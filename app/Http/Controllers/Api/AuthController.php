<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\APiResource\UserItemResource;
use App\Models\CustomEventUsers;
use Illuminate\Http\Request;
use App\Models\Qr_Code;
use App\Models\User;
use App\Models\EventUsers;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\Setting;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class AuthController extends Controller
{
    use GeneralTrait;

    public $user;
    public $lang;


    public function __construct()
    {
        if (array_key_exists('language', getallheaders())) {
          $this->lang = getallheaders()['language'];
        } elseif (array_key_exists('Language', getallheaders())) {
          $this->lang = getallheaders()['Language'];
        } else {
          $this->lang = 'ar';
        }
    }


    // register
    public function register(Request $request)
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        try {

            $validated_arr = [
                'name'      => 'required|max:200',
                'mobile'      => 'required|numeric|unique:admin|unique:users,mobile',
                'mobile_code' => 'required',
            ];

            if ($lang == 'ar') {
                $custom_messages = [
                    'name.required' => "الاسم مطلوب",
                    'name.max' => "الاسم يجب ان يحتوي علي الاكثر علي 200 حرف",

                    'mobile.required' => "الهاتف مطلوب",
                    'mobile.unique' => "الهاتف موجود",
                    'mobile.numeric' => "الهاتف يجب ان يحتوي علي ارقام",
                    'mobile.digits' => "الهاتف يجب ان يحتوي علي 12 رقم",
                    'mobile_code.required' => "كود الهاتف مطلوب",
                    'mobile_code.exists' => "عفوا كود الهاتف غير صحيح",
                ];
            } else {
                $custom_messages =  [
                    'name.required' => 'name is required',
                    'name.unique' => 'name must be unique',
                    'name.max' => 'name is must have at max 200 character',

                    'mobile.required' => 'mobile is required',
                    'mobile.numeric' => 'mobile must be numeric',
                    'mobile.unique' => 'mobile must be unique',
                    'mobile_code.required' => 'mobile code is required',
                    'mobile_code.exists' => 'sorry this mobile code not exist',
                ];
            }

            $validator = Validator::make($request->all(), $validated_arr, $custom_messages);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            if ($request->fcm_token != null) {
                $fcm_token = $request->fcm_token;
            } else {
                $fcm_token = null;
            }

            $data['name'] = $request->name;
            $data['mobile_code'] = $request->mobile_code;
            $data['mobile'] = $request->mobile;
            $data['device_token'] = $fcm_token;
            $data['password'] = Hash::make($request->password);
          	$data['pass'] = $request->password;
            $data['status'] = 1;

            //Request is valid, create new user
            $auth_user = User::create($data);

            $user = User::where('id', $auth_user->id)->first();

          	$order_number = Orders::max('order_number') + 1;

          	$offer = Packages::find(20);

            if ($offer != null) {

              $currency_id = $offer->currency_id;

                $order = Orders::create([
                  'order_number' => $order_number,
                  'user_id' => $user->id,
                  'type' => 'offer',
                  'offer_id' => 20,
                  'total' => $offer->price,
                  'users_count' => $offer->users_count,
                  'operation_date' => Carbon::now(),
                  'currency_id' => $currency_id
              ]);

              $user->update([
                  'order_id' => $order->id,
                  'offer_id' => $offer->id,
                  'full_balance' => $user->full_balance + $offer->users_count,
                  'balance' => $user->balance + $offer->users_count
              ]);
            }


            $setting = Setting::first();

            $template_name = 'mazoom_msg';
            $language = 'ar';

            $phone_numer_id = $setting->phone_numer_id;
            $token = $setting->access_token;
            $sender_id = $setting->sender_id;

            $to = $request->mobile_code.$request->mobile;

            $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name;

            $response = SendNewTemplateCodeV1($url);

            if ($response != null && $response->getStatusCode() == 200) {
                $body = $response->getBody();
                $data = json_decode($body, true);
            } else {
                info('error sending template register');
            }

            if ($lang == 'en') {
                return $this->returnData('user', UserItemResource::make($user), 'User created successfully');
            } else {
                return $this->returnData('user', UserItemResource::make($user), 'تم تسجيل عضوية بنجاح');
            }
        } catch(Exception $e) {
            //dd($e->getMessage());
          	info($e);
            if ($lang == 'en') {
                return $this->returnError('E200', 'sorry try again');
            } else {
                return $this->returnError('E200', 'عذرا حاول مرة أخرى');
            }
        }
    }


    // login
    public function login(Request $request)
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

      	info($request->all());

        $validated_arr = [
            'mobile' => 'required|exists:users,mobile',
            'user_type' => 'required'
        ];

        if($request->user_type == 'employee') {
			$validated_arr['password'] = 'required';
        }

        $custom_messages =  [
            'mobile.required' => 'رقم الهاتف مطلوب',
            'mobile.exists' => 'عفوا رقم الهاتف غير موجود مسبقا',
        ];

        if ($lang == 'en') {
            $validator = Validator::make($request->all(), $validated_arr);
        } else {
            $validator = Validator::make($request->all(), $validated_arr, $custom_messages);
        }


        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        //Request is validated

        $user = User::where('mobile', $request->mobile)->first();

      	//dd($user->password,$user->id);

        //Creat token and return user with token
        try {
            if (! $user) {
                if ($lang == 'en') {
                    return $this->returnError('401', 'Unauthorized User');
                } else {
                    return $this->returnError('401', 'مستخدم غير مصرح به');
                }
            } else {

              	if($request->user_type == 'employee' || $request->user_type == 'user') {

                  	if (Hash::check($request->password, $user->password)) {

                      if ($user->status == 1) {

                          $user->update([
                              'device_token' => $request->fcm_token,
                          ]);

                          if($user->token == null) {

                              $token = bcrypt(rand());

                              $user->update([
                                'token' => $token
                              ]);
                          }

                          if ($lang == 'en') {
                              return $this->returnData('user', UserItemResource::make($user), 'login successfully');
                          } else {
                              return $this->returnData('user', UserItemResource::make($user), 'تم تسجيل الدخول بنجاح ');
                          }

                      } else {

                          if ($lang == 'en') {
                              return $this->returnError('E100', 'sorry your membership is blocked please contact with owner of this app');
                          } else {
                              return $this->returnError('E100', 'آسف تم حظر عضويتك ، يرجى الاتصال بمالك هذا التطبيق');
                          }
                      }

                    } else {
                    	if ($lang == 'en') {
                          return $this->returnError('E100', 'please check your password again');
                        } else {
							return $this->returnError('E100', 'برجاء التحقق من كلمة المرور مره اخري');
                        }
                    }

                } else {
                	if ($lang == 'en') {
                      return $this->returnError('401', 'Unauthorized User');
                  } else {
                      return $this->returnError('401', 'مستخدم غير مصرح به');
                  }
                }

            }


        } catch (Exception $e) {
            if ($lang == 'en') {
                return $this->returnError('E200', 'sorry try again');
            } else {
                return $this->returnError('E200', 'عذرا حاول مرة أخرى');
            }
        }
    }




    public function update_profile(Request $request)
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;


        if (array_key_exists('token', getallheaders())) {
            $token = getallheaders()['token'];
        } elseif (array_key_exists('Token', getallheaders())) {
            $token = getallheaders()['Token'];
        } else {
            $token = null;
        }

        if ($token == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'token is required');
            } else {
                return $this->returnError('E100', 'التوكت مطلوب');
            }
        }

        $user = User::where('token', $token)->first();

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        $validated_arr = [
            'name'      => 'required|max:200',
            'mobile'      => 'required|numeric|unique:admin|unique:users,mobile,' . $user->id,
            'mobile_code' => 'required'
        ];

        if ($lang == 'ar') {
            $custom_messages = [
                'name.required' => "الاسم مطلوب",
                'name.max' => "الاسم يجب ان يحتوي علي الاكثر علي 200 حرف",

                'mobile.required' => "الهاتف مطلوب",
                'mobile.unique' => "الهاتف موجود",
                'mobile.numeric' => "الهاتف يجب ان يحتوي علي ارقام",
                'mobile.digits' => "الهاتف يجب ان يحتوي علي 12 رقم",
                'mobile_code.required' => "كود الهاتف مطلوب",
                'mobile_code.exists' => "عفوا كود الهاتف غير صحيح",
            ];
        } else {
            $custom_messages =  [
                'name.required' => 'name is required',
                'name.unique' => 'name must be unique',
                'name.max' => 'name is must have at max 200 character',

                'mobile.required' => 'mobile is required',
                'mobile.numeric' => 'mobile must be numeric',
                'mobile.unique' => 'mobile must be unique',
                'mobile_code.required' => 'mobile code is required',
                'mobile_code.exists' => 'sorry this mobile code not exist',
            ];
        }

        $validator = Validator::make($request->all(), $validated_arr, $custom_messages);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }


        $data['name'] = $request->name;
        $data['mobile_code'] = $request->mobile_code;
        $data['mobile'] = $request->mobile;

        $user->update($data);

        if ($lang == 'en') {
            return $this->returnData('user', $user, 'User updated successfully');
        } else {
            return $this->returnData('user', $user, 'تم تحديث الملف الشخصي بنجاح');
        }


    }



    // remove-user
    public function remove_user()
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;


        if (array_key_exists('token', getallheaders())) {
            $token = getallheaders()['token'];
        } elseif (array_key_exists('Token', getallheaders())) {
            $token = getallheaders()['Token'];
        } else {
            $token = null;
        }

        if ($token == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'token is required');
            } else {
                return $this->returnError('E100', 'التوكت مطلوب');
            }
        }

        $user = User::where('token', $token)->first();

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        // user status
        /*
            0 => hold
            1 => accept
            2 => block
            3 => request for remove user,
            4 => removed user
        */

        $user->delete();

        if ($lang == 'en') {
            return $this->returnSuccessMessage('user is deleted successfully');
        } else {
            return $this->returnSuccessMessage('تم حذف هذا المستخدم بنجاح');
        }

        // if ($user->status == 2) {
        //     if ($lang == 'en') {
        //         return $this->returnError('E200', 'sorry this user is already blocked');
        //     } else {
        //         return $this->returnError('E200', 'عذرا هذا المستخدم محظور بالفعل');
        //     }
        // } elseif ($user->status == 3) {
        //     if ($lang == 'en') {
        //         return $this->returnError('E200', 'sorry this user is already having request remove');
        //     } else {
        //         return $this->returnError('E200', 'عذرا تم تقديم طلب حذف لهذا المستخدم بالفعل');
        //     }
        // } elseif ($user->status == 4) {
        //     if ($lang == 'en') {
        //         return $this->returnError('E200', 'sorry this user is already removed');
        //     } else {
        //         return $this->returnError('E200', 'عذرا تم حذف هذا المستخدم بالفعل');
        //     }
        // } elseif ($user->status == 1) {
        //     $user->update([
        //         'status' => 3,
        //     ]);

        //     if ($lang == 'en') {
        //         return $this->returnSuccessMessage('request for removing user is created successfully');
        //     } else {
        //         return $this->returnSuccessMessage('تم تقديم طلب حذف لهذا العضويه بنجاح');
        //     }

        // } else {
        //     if ($lang == 'en') {
        //         return $this->returnError('E200', 'sorry try again in another time');
        //     } else {
        //         return $this->returnError('E200', 'عذرا حاول مرة أخرى في وقت لاحق');
        //     }
        // }
    }


    public function profile()
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;


        if (array_key_exists('token', getallheaders())) {
            $token = getallheaders()['token'];
        } elseif (array_key_exists('Token', getallheaders())) {
            $token = getallheaders()['Token'];
        } else {
            $token = null;
        }

        if ($token == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'token is required');
            } else {
                return $this->returnError('E100', 'التوكت مطلوب');
            }
        }

        $user = User::where('token', $token)->first();

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        if ($lang == 'en') {
            return $this->returnData('user', UserItemResource::make($user), '');
        } else {
            return $this->returnData('user', UserItemResource::make($user), '');
        }

    }



  	public function mobile_scan_qr($uu_id)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;


        if (array_key_exists('token', getallheaders())) {
            $token = getallheaders()['token'];
        } elseif (array_key_exists('Token', getallheaders())) {
            $token = getallheaders()['Token'];
        } else {
            $token = null;
        }

        if ($token == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'token is required');
            } else {
                return $this->returnError('E100', 'التوكت مطلوب');
            }
        }

        $user = User::where('token', $token)->first();

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }


      	$Item = Qr_Code::where('uu_id', $uu_id)->first();

        if($Item == null) {
			if ($lang == 'en') {
                return $this->returnError('E100', 'sorry this qr-code not found');
            } else {
                return $this->returnError('E100', 'عفوا هذا الكيو ار كود غير موجود');
            }
        }

        $user_event = EventUsers::where('id', $Item->event_user_id)->first();

      	if($user_event == null) {
			if ($lang == 'en') {
                return $this->returnError('E100', 'sorry this qr-code not found');
            } else {
                return $this->returnError('E100', 'عفوا هذا الكيو ار كود غير موجود');
            }
        }

        $now = Carbon::now();

        if($user_event->scan_count < $user_event->users_count) {
            $user_event->update(['scan' => 'yes','scan_at' => $now,'scan_count' => $user_event->scan_count + 1]);
            $Item->update(['counter' => $Item->counter + 1]);
            $msg = 'welcome '.$user_event->name;
          	$data['check'] = 'ok';
          	$data['users_count'] = $user_event->users_count;
			$data['scan_count'] = $user_event->scan_count;
            $data['user_event'] = $user_event;
          	$data['name'] = $user_event->name;
            $data['mobile'] = $user_event->mobile;
        } else {
            $msg = 'sorry this qr code is scaned before';
            $data['check'] = 'not-ok';
            $data['users_count'] = 0;
            $data['scan_count'] = 0;
            $data['user_event'] = $user_event;
          	$data['name'] = $user_event->name;
            $data['mobile'] = $user_event->mobile;
        }

      	if ($lang == 'en') {
            return $this->returnData('data', $data, $msg);
        } else {
            return $this->returnData('data', $data, $msg);
        }

    }



    public function mobile_custom_scan_qr($uu_id)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;


        if (array_key_exists('token', getallheaders())) {
            $token = getallheaders()['token'];
        } elseif (array_key_exists('Token', getallheaders())) {
            $token = getallheaders()['Token'];
        } else {
            $token = null;
        }

        if ($token == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'token is required');
            } else {
                return $this->returnError('E100', 'التوكت مطلوب');
            }
        }

        $user = User::where('token', $token)->first();

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }


      	$Item = CustomEventUsers::where('uu_id', $uu_id)->first();

        if($Item == null) {
			if ($lang == 'en') {
                return $this->returnError('E100', 'sorry this qr-code not found');
            } else {
                return $this->returnError('E100', 'عفوا هذا الكيو ار كود غير موجود');
            }
        }

        $user_event = $Item;

      	if($user_event == null) {
			if ($lang == 'en') {
                return $this->returnError('E100', 'sorry this qr-code not found');
            } else {
                return $this->returnError('E100', 'عفوا هذا الكيو ار كود غير موجود');
            }
        }

        $now = Carbon::now();

        if($user_event->scan_count < $user_event->users_count) {
            $user_event->update(['scan' => 'yes','scan_at' => $now,'scan_count' => $user_event->scan_count + 1]);
            $msg = 'welcome '.$user_event->name;
          	$data['check'] = 'ok';
          	$data['users_count'] = $user_event->users_count;
			$data['scan_count'] = $user_event->scan_count;
            $data['user_event'] = $user_event;
          	$data['name'] = $user_event->name;
            // $data['mobile'] = $user_event->mobile;
        } else {
            $msg = 'sorry this qr code is scaned before';
            $data['check'] = 'not-ok';
            $data['users_count'] = 0;
            $data['scan_count'] = 0;
            $data['user_event'] = $user_event;
          	$data['name'] = $user_event->name;
            // $data['mobile'] = $user_event->mobile;
        }

      	if ($lang == 'en') {
            return $this->returnData('data', $data, $msg);
        } else {
            return $this->returnData('data', $data, $msg);
        }

    }



    // logout
    public function logout(Request $request)
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;


        $validated_arr = [
            'token' => 'required'
        ];

        $custom_messages =  [
            'token.required' => 'معرف المستخدم مطلوب',
        ];

        if ($lang == 'en') {
            $validator = Validator::make($request->all(), $validated_arr);
        } else {
            $validator = Validator::make($request->all(), $validated_arr, $custom_messages);
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        //Request is validated, do logout
        try {
            $user = User::where('token', $request->token)->first();

            if ($user != null) {

                $user->update(['token' => null]);

                if ($lang == 'en') {
                    return $this->returnSuccessMessage('logout successfully');
                } else {
                    return $this->returnSuccessMessage('تم تسجيل الخروج بنجاح');
                }

            } else {

                if ($lang == 'en') {
                    return $this->returnError('E100', 'Unauthorized User');
                } else {
                    return $this->returnError('E100', 'مستخدم غير مصرح به');
                }
            }
        } catch (Exception $exception) {
            if ($lang == 'en') {
                return $this->returnError('E200', 'sorry try again');
            } else {
                return $this->returnError('E200', 'عذرا حاول مرة أخرى');
            }
        }
    }






}

