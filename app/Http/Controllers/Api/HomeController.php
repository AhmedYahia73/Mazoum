<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desgins;
use App\Models\MobileCodes;
use App\Models\Packages;
use Illuminate\Http\Request;
use App\Models\Notifications;
use App\Http\Resources\APiResource\Notications_Data_R;
use App\Models\User;
use App\Models\Uses;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    use GeneralTrait;

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


  	// notifications
    public function notifications($id = null) {

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

        if ($token != null) {
            $user = User::where('token', $token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        if($id == null) {

            $notifications = Notifications::where('send_to_type','user')->where('send_to_id',$user->id)->orderBy('created_at','desc')->get();

            if($notifications != null && $notifications->count() > 0) {

                Notifications::where('send_to_type','user')->where('send_to_id',$user->id)->update([ 'seen' => 1 ]);
                $notifications = Notications_Data_R::collection($notifications);

            }

            return $this->returnData('data',$notifications,'');

        } else {

            $notification = Notifications::where('send_to_type','user')->where('send_to_id',$user->id)->where('id',$id)->first();

            if($notification != null ) {

                $notification = new Notications_Data_R($notification);

                $notification->update([ 'seen' => 1 ]);

                return $this->returnData('data',$notification,'');

            } else  {

                if($lang == 'en') {
                    return $this->returnError('404','sorry this notification not found');
                } else {
                    return $this->returnError('404','عفوا هذا الأشعار غير موجود');
                }
            }

        }


    }


    // home
    public function home()
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $desgins = Desgins::get(['id',$lang.'_name as name','file', 'type']);
        $packages = Packages::where('status',1)->get(['id',$lang.'_name as name','users_count', 'price','image']);
        $uses = Uses::get(['id',$lang.'_desc as desc','link']);

        $mobile_codes = MobileCodes::get(['id',$lang.'_country_name as name','code']);

        $data['mobile_codes'] = $mobile_codes;
        $data['desgins'] = $desgins;
        $data['packages'] = $packages;
        $data['uses'] = $uses;


        if ($lang == 'en') {
            return $this->returnData('data', $data);
        } else {
            return $this->returnData('data', $data);
        }

    }
  
  
  
    // how_to_use
    public function how_to_use()
    {
        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        
        $uses = Uses::get(['id',$lang.'_desc as desc','link']);
      

        if ($lang == 'en') {
            return $this->returnData('data', $uses);
        } else {
            return $this->returnData('data', $uses);
        }

    }



}
