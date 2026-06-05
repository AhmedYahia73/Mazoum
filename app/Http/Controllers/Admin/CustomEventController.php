<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomEvent as modelRequest;
use App\Models\CustomEvent as Model;
use App\Models\CustomEventFamily; 
use App\Models\CustomEventUsers;
use App\Models\EnterUserCustomEvent;
use App\Models\Notifications;
use App\Models\Qr_Code;
use App\Models\CustomMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $bg = public_path('images/' . $event->getRawOriginal('image'));

        // تأكد من وجود المجلد
        $directory = public_path('custom_event_qr_code');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        $event_element = $row->event;
        $name_qr      = $row->event?->name_qr; 
        $number_qr    = $row->event?->number_qr; 
        $qr_height    = $row->event?->qr_height; 
        $qr_width     = $row->event?->qr_width; 
        $qr_x         = $row->event?->qr_x; 
        $qr_y         = $row->event?->qr_y; 
        $image_height = $row->event?->image_height;
        $image_width  = $row->event?->image_width;
        $text_color   = $row->event?->text_color ?: '#000';
        $user_name = $row->name;
        $users_count = $row->users_count;
        $image_name = $uu_id . '-custom-event-qr.png';
        $link = asset('scan-custom-event-qr/' . $uu_id);
        $qr_temp_path = public_path('custom_event_qr_code/temp_qr_' . $image_name);

        // إنشاء QR
        $color = $this->hexToRgb($event_element->color);

        generate_qr_png($link, $qr_temp_path, ($qr_width > 0 ? $qr_width : 140), $color);

        // افتح الخلفية
        $background = Image::make($bg);

        if ($image_width > 0 && $image_height > 0) {
            $background->resize($image_width, $image_height);
        }
        
        // افتح QR
        $qr = Image::make($qr_temp_path);

        // // تعديل أبعاد الـ QR بناءً على الطول والعرض من الداتابيز
        // if ($qr_width > 0 && $qr_height > 0) {
        //     $qr->resize($qr_width, $qr_height);
        // }

        // // origin: bottom-right — qr_x/qr_y = pixels from bottom-right corner
        // if ($qr_x > 0 || $qr_y > 0) {
        //     $x = $background->width()  - $qr->width()  - $qr_x;
        //     $y = $background->height() - $qr->height() - $qr_y;
        // } else {
        //     $x = intval(($background->width()  - $qr->width())  / 2);
        //     $y = intval(($background->height() - $qr->height()) / 2);
        // }

        // // أدرج QR مرة واحدة بس!
        // $background->insert($qr, 'top-left', $x, $y);
        
            if ($qr_width > 0 && $qr_height > 0) {
                $qr->resize($qr_width, $qr_height);
            }

            // تعديل نقطة البداية (Origin) لتكون Top-Left مباشرة
            if ($qr_x > 0 || $qr_y > 0) {
                $x = $qr_x; 
                $y = $qr_y;
            } else {
                $x = intval(($background->width()  - $qr->width())  / 2);
                $y = intval(($background->height() - $qr->height()) / 2);
            }

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

        // إضافة اسم الشخص (مربوط بالـ Boolean)
        if ($name_qr) {
            $background->text($name, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
                $font->file($font_path);
                $font->size(20);
                $font->color($text_color);
                $font->align('center');
                $font->valign('top');
            });
            
            $text_y += 25; 
        }

        // إضافة عدد المستخدمين (مربوط بالـ Boolean)
        if ($number_qr && $row->users_count > 1) {
            $background->text($name2, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
                $font->file($font_path);
                $font->size(20);
                $font->color($text_color);
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
            Log::error("فشل حفظ QR: " . $e->getMessage());
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
    
    public function deleted_custom_events(Request $request)
    {
        $query = Model::onlyTrashed();

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

    public function multi_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'   => 'required|array',
            'items.*' => 'required|exists:custom_event,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        Model::whereIn('id', $request->items)->delete();
        return response()->json(['success' => 'تم حذف البيانات بنجاح']);
    } 
    
    public function force_destroy($id)
    {
        // نستخدم withTrashed() تحسباً لو كان العنصر محذوفاً ناعماً بالفعل وتريد حذفه نهائياً
        $Item = Model::withTrashed()->findOrFail($id);
        
        $Item->forceDelete(); // حذف نهائي من قاعدة البيانات
        
        return response()->json([
            'success' => 'تم حذف البيانات نهائياً بنجاح', 
        ]); 
    }

    public function force_multi_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'   => 'required|array',
            'items.*' => 'required|exists:custom_event,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        
        // استخدام forceDelete للحذف الجماعي النهائي
        Model::withTrashed()->whereIn('id', $request->items)->forceDelete();
        
        return response()->json(['success' => 'تم حذف البيانات المحددة نهائياً بنجاح']);
    }

    private function gteInput($request, $modelClass)
    {
        $input = $request->only([
            'title', 'user_id', 'color' , 'assistant_id' , 'language' ,
            'address' , 'date' , 'time', 'scan_assistant_id',
            "name_qr", "number_qr", "qr_height", "send_type",
            "qr_width", "qr_x", "qr_y", "lat", "lng",
            'image_height', 'image_width', 'text_color', "show_data_pdf",
        ]);

        $path = 'images';

        if($request->file('image') != null) {

            $extension = $request->file('image')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('image')->move($path, $filename);

            $input['image'] = $filename;
        }

        if($request->file('video') != null) {

            $extension = $request->file('video')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('video')->move($path, $filename);

            $input['video'] = $filename;
        }

        if($request->file('pdf') != null) {

            $extension = $request->file('pdf')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('pdf')->move($path, $filename);

            $input['pdf'] = $filename;
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
        $event_host = User::
        where("user_id", $Item->user_id)
        ->orWhereHas("sub_users", function($query) use($Item){
            $query->where("users.id", $Item->user_id);
        })
        ->count();
        $congratulation_msg = CustomMessage::
        where("custom_event_id", $Item->id)
        ->where("type", "congratulation")
        ->count();
        $apologize_msg = CustomMessage::
        where("custom_event_id", $Item->id)
        ->where("type", "apologize")
        ->count();
        $apologize_count = CustomEventUsers::
        where("custom_event_id", $Item->id) 
        ->sum("apologize_count");
        $confirm_count = CustomEventUsers::
        where("custom_event_id", $Item->id) 
        ->sum("confirm_count");
        $event_host = User::
        where("user_id", $Item->user_id)
        ->whereHas("event_users", function($query) use($Item){
            $query->where("custom_event_id", $Item->id);
        })
        ->count();

        return response()->json([
            'Item' =>  $Item, 
            'visitors_count' =>  $visitors_count, 
            'qr_count' =>  $qr_count, 
            'event_host' =>  $event_host, 
            'congratulation_msg' =>  $congratulation_msg, 
            'apologize_msg' =>  $apologize_msg, 
            'apologize_count' =>  $apologize_count, 
            'confirm_count' =>  $confirm_count, 
            'event_host' =>  $event_host, 
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
            "type" => "required|in:pdf,image"
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

                if($request->type == "pdf"){
                    \App\Jobs\SendCustomEventPdfJob::dispatch($row->id, $event->id, $ultramsg_token, $instance_id);
                    // Mock API response since it's processed in background
                    $api = ['sent' => 'true', 'message' => 'ok'];
                }
                else{
                    info('sendImageMessage to: ' . $to . ' image: ' . $image);
                    $api = $client->sendImageMessage($to,$image,$caption,$priority,$referenceId,$nocache);
                    info('sendImageMessage response: ' . json_encode($api));
                }

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

    public function custom_open_users(Request $request, $id){
        $enter_event = EnterUserCustomEvent::
        where("custom_user_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "count" => $item->count,
                "date" => $item->created_at->format("Y-m-d"),
                "time" => $item->created_at->format("H:i A"),
            ];
        });

        return response()->json([
            "enter_event" => $enter_event
        ]);
    } 
    
    public function congratulation_msg($id){
        $messages = CustomMessage::
        where("custom_event_id", $id)
        ->where("type", "congratulation")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "msg" => $item->msg,
                "name" => $item?->user?->name,
                "mobile" => $item?->user?->mobile,
            ];
        });

        return response()->json([
            "messages" => $messages
        ]);
    } 
    
    public function apologize_msg($id){
        // status
        $messages = CustomMessage::
        where("custom_event_id", $id)
        ->where("type", "apologize")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "msg" => $item->msg,
                "name" => $item?->user?->name,
                "mobile" => $item?->user?->mobile,
            ];
        });

        return response()->json([
            "messages" => $messages
        ]);
    } 
    
    public function confirm_count(Request $request, $id){
    
        $Item = Model::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->where("confirm_count", ">", 0)
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
            'user_events' =>  $user_events,  
        ]); 
    } 
    
    public function apologize_count(Request $request, $id){
        
        $Item = Model::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->where("apologize_count", ">", 0)
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
            'user_events' =>  $user_events,  
        ]); 
    } 
    
    public function send_message(Request $request){
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            'custom_user_id' => 'required|exists:custom_event_users,id',
            "type" => "required|in:congratulation,apologize",
            "msg" => "required"
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 

        $messages = CustomMessage::create([
            'custom_event_id' => $request->custom_event_id,
            'custom_user_id' => $request->custom_user_id,
            'msg' => $request->msg,
            'type' => $request->type,
        ]);

        return response()->json([
            "message" => $messages,
            "success" => "You add data success",
        ]);
    }

    public function status(Request $request, $id){
    
        $custom_event = CustomEventUsers::
        findOrFail($id);
        $count = $custom_event->users_count - $custom_event->confirm_count - $custom_event->apologize_count;
        $validator = Validator::make($request->all(), [
            "status" => "required|in:confirm,apologize",
            "count" => "required|integer|max:" . $count
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  

        if($request->status == "confirm"){
            $custom_event
            ->update([
                "confirm_count" => $request->count,
            ]);
        }
        else{
            $custom_event
            ->update([
                "apologize_count" => $request->count,
            ]);
        }

        return response()->json([ 
            "success" => "You update data success",
        ]);
    }


    public function confirm_custom_event(Request $request, $id){
    
        $custom_event = CustomEventUsers::
        findOrFail($id);
        $count = $custom_event->users_count - $custom_event->confirm_count - $custom_event->apologize_count; 
        $custom_event
        ->update([
            "confirm_count" => $count,
        ]); 

        return response()->json([ 
            "success" => "You update data success",
        ]);
    }

    public function apologize_custom_event(Request $request, $id){
    
        $custom_event = CustomEventUsers::
        findOrFail($id);
        $count = $custom_event->users_count - $custom_event->confirm_count - $custom_event->apologize_count; 
        $custom_event
        ->update([
            "apologize_count" => $count,
        ]); 

        return response()->json([ 
            "success" => "You update data success",
        ]);
    }
    
    public function host_custom_event(Request $request, $id){
        $Item = Model::findOrFail($id);
        $event_host = User::
        where("user_id", $Item->user_id)
        ->whereHas("event_users", function($query) use($Item){
            $query->where("custom_event_id", $Item->id);
        })
        ->get();

        return response()->json([ 
            "Item" => $Item,
            "event_host" => $event_host
        ]);
    }

}