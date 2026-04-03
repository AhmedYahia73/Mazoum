<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomEvent as modelRequest;
use App\Models\CustomEvent as Model;
use App\Models\CustomEventFamily; 
use App\Models\CustomEventUsers;
use App\Models\Notifications;
use App\Models\Qr_Code;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;



class CustomEventController extends Controller
{
    private $view = 'admin.custom_events.';
    private $redirect = 'admin/custom_events';




    public function event_users_search(Request $request) {

        $validator = Validator::make($request->all(), [
          'custom_event_id' => 'required|exists:custom_event,id'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        $custom_event_id = $request->custom_event_id;

        $event_users = CustomEventUsers::where('custom_event_id', $custom_event_id)
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%");
                });
            })
            ->paginate(15); // عدد النتائج في الصفحة

        return response()->json([
            'event_users' => $event_users,
        ]); 
    }




     // save_event_users
    public function save_event_users(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            'event_users' => 'required|array',
            'event_users.*.name' => 'required',
          	'event_users.*.users_count' => 'required|numeric|min:1',
          	'event_users.*.mobile' => 'sometimes',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   

        $custom_event_id = $request->custom_event_id;

        $event = Model::where('id', $custom_event_id)->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {

                if($arr['name'] != null && $arr['users_count'] != null && is_numeric($arr['users_count'])) {

                    $uu_id = uniqid();

                    $row = CustomEventUsers::create([
                        'custom_event_id' => $custom_event_id,
                        'name' => $arr['name'],
                        'users_count' => $arr['users_count'],
                        'mobile' => isset($arr['mobile']) ? $arr['mobile'] : null,
                        'uu_id' => $uu_id
                    ]);

                    $this->update_qr($row,$uu_id);

                }
            }

        }
        $users_count = collect($request->event_users)->sum('users_count');
        $user = User::
        where("id", $event->user_id)
        ->first();
        $user->update([
            "send_custom_invetaion" => $user->send_custom_invetaion + $users_count
        ]);

        return response()->json([
            'success' =>  'تم الحفظ بنجاح', 
        ]);  

    }




     // update_event_users
    public function update_event_users(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_users' => 'required|array',
            'event_users.*.id' => 'required',
            'event_users.*.name' => 'required',
            'event_users.*.users_count' => 'required|numeric|min:0',
            'event_users.*.mobile' => 'nullable|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $key => $arr) {
                $id = $request->event_users[$key]['id'];
                $row = CustomEventUsers::find($id);

                if($row != null && $arr['name'] != null && $arr['users_count'] != null && is_numeric($arr['users_count'])) {

                    $users_count = $arr['users_count'] - $row->users_count;
                    $user = User::
                    where("id", $row->user_id)
                    ->first();
                    $user->update([
                        "send_custom_invetaion" => $user->send_custom_invetaion + $users_count
                    ]);

                    $uu_id = uniqid();

                  	$row->update([
                        'name' => $arr['name'],
                        'users_count' => $arr['users_count'],
                        'mobile' => isset($arr['mobile']) ? $arr['mobile'] : null,
                        'uu_id' => $uu_id
                    ]);

                    $this->update_qr($row,$uu_id);


                }
            }

        }

        return response()->json([
            'success' =>  'تم التحديث بنجاح', 
        ]);  

    }



    public function delete_event_users($id)
    {
        $Item = CustomEventUsers::findOrFail($id);
        $Item->delete();
        return response()->json([
            'success' =>  'تم الحذف بنجاح', 
        ]); 
    }




    public function import(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
            'custom_event_id' => 'required|exists:custom_event,id'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  

        $file = $request->file('file');

        if (!$file->isValid()) {
        return response()->json([
            'errors' =>  'Uploaded file is not valid.', 
        ]); 
        }

        // dd($request->all(), $request->hasFile('file'), $request->file('file'));

        $file_path = $file->store('temp');

        //dd($file_path);

        $saved_path = storage_path('app/' . $file_path);

        if (!file_exists($saved_path)) {
            return response()->json([
                'errors' =>  'File not found.', 
            ]); 
        }

        $data = Excel::toArray([], $saved_path);

        if (!empty($data)) {
            $data = array_slice($data[0], 1); // تخطي رأس الجدول
        }

      	//dd($data);

        if ($data && count($data) > 0) {
            foreach ($data as $row) {

              	if($row[0] && $row[1]) {

                	$uu_id =  uniqid();

                    $row = CustomEventUsers::create([
                        'custom_event_id' => $request->custom_event_id,
                        'name'            => $row[0],
                        'users_count'     => $row[1],
                        'mobile'          => $row[2],
                        'uu_id'           => $uu_id
                    ]);

                    $this->update_qr($row,$uu_id);
                }

            }
        }

        return response()->json([
            'success' =>  'Imported successfully!', 
        ]);  
    }


    private function update_qr($row, $uu_id) {
        $event = $row->event;
        $bg = $event->image;

        // تأكد من وجود المجلد
        $directory = public_path('custom_event_qr_code');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $image_name = $uu_id . '-custom-event-qr.png';
        $link = asset('scan-custom-event-qr/' . $uu_id);
        $qr_temp_path = public_path('custom_event_qr_code/temp_qr_' . $image_name);

        // إنشاء QR بخلفية شفافة
        $color = $this->hexToRgb($row->event->color);
        
        QrCode::format('png')
            ->size(140)
            ->color($color[0], $color[1], $color[2])
            ->backgroundColor(0, 0, 0, 0)
            ->generate($link, $qr_temp_path);

        // افتح الخلفية
        $background = Image::make($bg);
        
        // افتح QR
        $qr = Image::make($qr_temp_path);

        // احسب الإحداثيات لتوسيط QR
        $x = intval(($background->width() - $qr->width()) / 2);
        $y = $background->height() - $qr->height() - 280;

        // أدرج QR مرة واحدة بس!
        $background->insert($qr, 'top-left', $x, $y);

        // إعداد النصوص
        if ($event->language == 'ar') {
            $Arabic = new \ArPHP\I18N\Arabic('Glyphs');
            $name = $Arabic->utf8Glyphs($row->name);
            
            $user_count_label = 'عدد الضيوف ' . $row->users_count;
            $Arabic2 = new \ArPHP\I18N\Arabic('Glyphs');
            $name2 = $Arabic2->utf8Glyphs($user_count_label);
            
            $font_path = public_path('font/DroidArabicKufiRegular.ttf');
        } else {
            $name = $row->name;
            $name2 = 'Entered Users ' . $row->users_count;
            $font_path = public_path('font/LuxuriousRoman-Regular.ttf');
        }

        // مركز الصورة للنص
        $center_x = intval($background->width() / 2);
        $text_y = $y + $qr->height() + 15;

        // إضافة اسم الشخص
        $background->text($name, $center_x, $text_y, function ($font) use ($font_path) {
            $font->file($font_path);
            $font->size(20);
            $font->color('#000');
            $font->align('center');
            $font->valign('top');
        });

        // إضافة عدد المستخدمين
        if ($row->users_count > 1) {
            $text_y2 = $text_y + 25;
            $background->text($name2, $center_x, $text_y2, function ($font) use ($font_path) {
                $font->file($font_path);
                $font->size(20);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
        }

        // حفظ الصورة النهائية
        $final_path = public_path('custom_event_qr_code/' . $image_name);
        
        try {
        
            $background = Image::canvas($background->width(), $background->height())
                        ->insert($background);
            // ⭐ الحل السحري: encode قبل save
            $encoded = $background->encode('png', 100);
            // حفظ الصورة المشفرة
            file_put_contents($final_path, $encoded);
            
            // تحديث قاعدة البيانات
            $row->update([
                'qr' => $image_name
            ]);
            
            // حذف QR المؤقت
            @unlink($qr_temp_path);
            
            // تدمير الصورة من الذاكرة
            $background->destroy();
            
            return true;
        } catch (\Exception $e) {
            \Log::error("فشل حفظ QR: " . $e->getMessage());
            return false;
        }
    }


  	private function hexToRgb(string $hex): array
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return [$r, $g, $b];
    }


	/*
  	private function update_qr($row,$uu_id) {

        $bg = $row->event->image;

      	//dd($bg);

      	$image_name = $uu_id . '-custom-event-qr.png';
        $link = asset('scan-custom-event-qr/' . $uu_id);
        $qr_code_path = 'custom_event_qr_code/' . $image_name;

        // إنشاء QR كـ صورة مؤقتة
        $qr_temp_path = public_path('custom_event_qr_code/temp_' . $image_name);
        QrCode::size(300)->format('png')->generate($link, $qr_temp_path);

        // افتح الخلفية
        $background = Image::make($bg);

        // افتح QR
        $qr = Image::make($qr_temp_path);

        // احسب الإحداثيات لتوسيط QR في الأسفل
        $x = intval(($background->width() - $qr->width()) / 2); // مركز أفقي
        $y = $background->height() - $qr->height() - 180; // من الأسفل

        // أدرج QR
        //$background->insert($qr, 'top-left', $x, $y - 350);
        $background->insert($qr, 'top-left', $x, $y - 420);

        // احسب مركز الصورة للنص
        $center_x = intval($background->width() / 2);
        $text_y = $y + $qr->height() - 390; // أسفل QR بـ 20px

        $Arabic = new \ArPHP\I18N\Arabic('Glyphs');
        $name = $Arabic->utf8Glyphs($row->name);

        // أضف النص في وسط الصورة أفقيًا وأسفل QR
        $background->text($name, $center_x, $text_y, function ($font) {
            $font->file(public_path('font/DroidArabicKufiRegular.ttf'));
            $font->size(26);
            $font->color('#000');
            $font->align('center');
            $font->valign('top');
        });



       	// احسب مركز الصورة للنص
        $text_y2 = $y + $qr->height() - 340; // أسفل QR بـ 20px

        $Arabic2 = new \ArPHP\I18N\Arabic('Glyphs');
       	$user_count_label = 'عدد الدخول ' . $row->users_count . '';
        $name2 = $Arabic2->utf8Glyphs($user_count_label);

        // أضف النص في وسط الصورة أفقيًا وأسفل QR
        $background->text($name2, $center_x, $text_y2, function ($font) {
            $font->file(public_path('font/DroidArabicKufiRegular.ttf'));
            $font->size(26);
            $font->color('#000');
            $font->align('center');
            $font->valign('top');
        });



        // حفظ النتيجة
        $background->save(public_path($qr_code_path), 100);

      	$row->update([
            'qr' => $image_name
        ]);

        // حذف QR المؤقت
        @unlink($qr_temp_path);

    }
    */



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Model::query();

        // Search
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                ->orWhere('address', 'like', "%$search%");
            });
        }

        // Pagination
        $Item = $query->paginate(15); // عدد العناصر في الصفحة

        return response()->json([
            'Item' => $Item,
        ]); 
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->view . 'create');
    }

    public function lists(Request $request)
    {
        return view($this->view . 'create');
    }
 
    public function store(modelRequest $request)
    {
        Model::create($this->gteInput($request, null));
        return response()->json([
            'success' =>  'تم تخزين البيانات بنجاح', 
        ]);  
    }
    
    public function show($id)
    {
        $Item = Model::findOrFail($id);
        return response()->json([
            'Item' =>  $Item, 
        ]);  
    }
    
    public function edit($id)
    {
        $Item = Model::findOrFail($id);
        return response()->json([
            'Item' =>  $Item, 
        ]);  
    }
    
    public function update(modelRequest $request, $id)
    {
        $Item = Model::findOrFail($id);
        $Item->update($this->gteInput($request, $Item));
        return response()->json([
            'success' =>  'تم تحديث البيانات بنجاح', 
        ]); 
    }
    
    public function destroy($id)
    {
        $Item = Model::findOrFail($id);
        $Item->delete();
        return response()->json([
            'success' =>  'تم حذف البيانات بنجاح', 
        ]); 
    }


    private function gteInput($request, $modelClass)
    {
        $input = $request->only([
            'title', 'user_id', 'color' , 'assistant_id' , 'language' ,
            'address' , 'date' , 'time', 'scan_assistant_id'
        ]);

        $path = 'images';

        if($request->file('image') != null) {

            $extension = $request->file('image')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('image')->move($path, $filename);

            $input['image'] = $filename;
        }

        return  $input;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////////////////

  	// save_event_family
    public function save_event_family(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            'event_users' => 'required',
            'event_users.*.id' => 'required|exists:event_users,id',
            'event_users.*.name' => 'required',
            'event_users.*.mobile' => 'nullable|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $event_id = $request->custom_event_id;

        $event = Model::where('id', $event_id)->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {
                if($arr['name'] != null) {

                  CustomEventFamily::create([
                    'event_id' => $event_id,
                    'name' => $arr['name'],
                    'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                    'scan_qr' => 'no'
                  ]);
                }
            }

        }

        return response()->json([
            'success' =>  'تم الحفظ بنجاح', 
        ]);  

    }


    // update_event_family
    public function update_event_family(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_users' => 'required|array',
            'event_users.*.id' => 'required|exists:custom_event_family,id',
            'event_users.*.name' => 'required',
            'event_users.*.mobile' => 'nullable|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
 

        foreach ($request->event_users as $key => $arr) {
            $id = $request->event_users[$key]['id'];
            $row = CustomEventFamily::find($id);

            if($row != null && $arr['name'] != null) {

                $row->update([
                    'name' => $arr['name'],
                    'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                ]);
            }
        } 

        return response()->json([
            'success' =>  'تم التحديث بنجاح', 
        ]);  

    }



  	public function delete_event_family($id) {

        $user_event = CustomEventFamily::find($id);

        if($user_event != null) {
          $user_event->delete();
        }

        return response()->json([
            'success' =>  'تم الحذف بنجاح', 
        ]); 

    }


  	public function open_event_family($id) {

        $user_event = CustomEventFamily::findOrFail($id);

        $user_event->update(['scan_qr' => 'yes']);

        return response()->json([
            'success' =>  'تم دخول الحفل بنجاح', 
        ]);  

    }

  	///////////////////////////////////////////////////////////////////////////////////////

  	public function event_family_search(Request $request) {

        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $event_id = $request->custom_event_id;

        $event_users = CustomEventFamily::where('event_id', $event_id)
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%")
                    ->orWhere('mobile', $search);
                });
            })
            ->paginate(15); // عدد النتائج في الصفحة

        return response()->json([
            'event_users' => $event_users,
            'event_id' => $event_id,
        ]);


    }

  	///////////////////////////////////////////////////////////////////////////////////////



    public function event_visitors(Request $request, $id)
    {
        $Item = Model::findOrFail($id);
        $event_users = CustomEventUsers::where('custom_event_id', $Item->id)
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        return response()->json([
            'Item' =>  $Item, 
            'event_users' =>  $event_users, 
        ]);  
    }


    public function send_events(Request $request, $id)
    {
        $Item = Model::findOrFail($id);
        $event_users = CustomEventUsers::where('custom_event_id', $Item->id)
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        return response()->json([
            'Item' =>  $Item, 
            'event_users' =>  $event_users, 
        ]); 
    }

    public function users(Request $request, $id)
    {
        $Item = Model::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);
        $invetations = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->sum('users_count');
        $attendance = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->sum('scan_count');

        return response()->json([
            'Item' =>  $Item, 
            'user_events' =>  $user_events, 
            'invetations' =>  $invetations, 
            'attendance' =>  $attendance, 
        ]); 
    }


    public function event_report($id)
    {
        $Item = Model::findOrFail($id);
        $visitors_count = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->sum('users_count');
        $qr_count = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where('scan','yes')
        ->sum('scan_count');
        return response()->json([
            'Item' =>  $Item, 
            'visitors_count' =>  $visitors_count, 
            'qr_count' =>  $qr_count, 
        ]); 
    }


    public function all_event_users(Request $request, $id)
    {
        $Item = Model::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->get();
        $invetations = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->sum('users_count');
        $attendance = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->sum('scan_count');

        return response()->json([
            'Item' =>  $Item, 
            'user_events' =>  $user_events, 
            'invetations' =>  $invetations, 
            'attendance' =>  $attendance, 
        ]); 
    }


    public function event_users(Request $request, $id)
    {
        $Item = Model::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);
        $invetations = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->sum('users_count');
        $attendance = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->sum('scan_count');

        return response()->json([
            'Item' =>  $Item, 
            'user_events' =>  $user_events, 
            'invetations' =>  $invetations, 
            'attendance' =>  $attendance, 
        ]); 
    }



    public function enter_event($id)
    {
        $Item = Model::findOrFail($id);

        return response()->json([
            'Item' =>  $Item, 
        ]); 
    }

    // new-send-event-invitation
    public function new_send_event_invitation(Request $request) {
        $validator = Validator::make($request->all(), [
        	'custom_event_id' => 'required|exists:custom_event,id',
            'users' => 'required|array',
            'users.*' => 'required|exists:custom_event_users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true;

      	$event = Model::where('id',$request->custom_event_id)->firstOrFail();

        if($request->users != null && ! empty($request->users)) {

            $error_count = 0;

          	foreach($request->users as $item) {

              if(isset($item)) {

                $row = CustomEventUsers::where('id',$item)->first();

                if($row != null && $row->mobile != null && $event != null) {

                  $to = $row->mobile;

                  $image = $row->qr;

                  $day_name   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l');

                  $caption = $row->name . PHP_EOL . PHP_EOL .
                    $event->title . PHP_EOL . PHP_EOL .
                    "وذلك بمشيئة الله يوم " . $day_name ." الموافق" . PHP_EOL . PHP_EOL .
                    $event->date . " 📆" . PHP_EOL . PHP_EOL .
                    "⏱️الساعـة " . $event->time . " مساءاً" . PHP_EOL . PHP_EOL .
                    "📍مكان الحفـل " . $event->address ;

                  // $api=$client->sendChatMessage($to,$body);
                  $api = $client->sendImageMessage($to,$image,$caption,$priority,$referenceId,$nocache);

                //   dd($api);

                  if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {

                    // dd('ok');
                    // $row->update(['is_new_sent' => 1]);

                  } else {

                    $error_count = $error_count + 1;

                    // dd('not ok',$api);
                    // $row->update(['is_new_sent' => 0]);
                  }

                }

              }

            }

            if($error_count == 0) {
                return response()->json([
                    'success' =>  'تم ارسال الرسائل بنجاح', 
                ]);  
            } else {
                return response()->json([
                    'errors' =>  'عفوا فشل ارسال ' .$error_count . ' أرقام ', 
                ], 400);  
            }

        } else {
            return response()->json([
                'errors' =>  'من فضلك اختر عنصر واحد علي الاقل', 
            ], 400);  
        }

    }


    // delete_selected_event_users
    public function delete_selected_event_users(Request $request) {

        $validator = Validator::make($request->all(), [
            'users' => 'required|array',
            'users.*' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
        if($request->users != null && ! empty($request->users)) {

            foreach($request->users as $item) {

              	if(isset($item)) {
                	CustomEventUsers::where('id',$item)->delete();
                }

            }

            return response()->json([
                'success' =>  'تم حذف العناصر المختاره', 
            ]);  

        } else {
            return response()->json([
                'errors' =>  'من فضلك اختر عنصر واحد علي الاقل', 
            ]);  
        }

    } 
}
