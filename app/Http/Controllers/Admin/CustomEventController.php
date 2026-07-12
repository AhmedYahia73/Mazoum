<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomEvent as modelRequest;
use App\Jobs\SendCustomEventPdfJob;
use App\Models\CustomEvent as Model;
use App\Models\CustomEvent;
use App\Models\CustomEventFamily; 
use App\Models\CustomEventUsers;
use App\Models\CustomMessage;
use App\Models\EnterUserCustomEvent;
use App\Models\Notifications;
use App\Models\Qr_Code;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
 
    public function excel_event_users(Request $request) {

        $validator = Validator::make($request->all(), [
          'custom_event_id' => 'required|exists:custom_event,id'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        $custom_event_id = $request->custom_event_id;

        $event_users = CustomEventUsers::
        where('custom_event_id', $custom_event_id)
        ->get(); // عدد النتائج في الصفحة

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

                if(isset($arr['mobile'])){
                    $event_users = CustomEventUsers::
                    where('mobile', $arr['mobile'])
                    ->where("custom_event_id", $event->id)
                    ->first();
                    if($event_users){
                        continue; 
                    }
                }
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

     // save_event_users
    public function save_host_event_users(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            'user_id' => 'required|exists:users,id',
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
        $user_id = $request->user_id;
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
                        'uu_id' => $uu_id,
                        "user_id" => $user_id
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
        $user_event = $row;
        $image_name = $event->image;
        $color = $this->hexToRgb($event->color);

        $name_qr      = $event->name_qr;
        $number_qr    = $event->number_qr;
        $qr_height    = $event->qr_height;
        $qr_width     = $event->qr_width;
        $qr_x         = $event->qr_x;
        $qr_y         = $event->qr_y;
        $image_height = $event->image_height;
        $image_width  = $event->image_width;
        $text_color   = $event->text_color ?: '#000';

        if ($event->getRawOriginal('image') != null && file_exists(public_path('images/' . $event->getRawOriginal('image')))) {

            $image_name  = $uu_id . '-test-qr.png';
            $link        = asset('scan-custom-qr/' . $uu_id);
            $qr_tmp_path = public_path('custom_event_qr_code/tmp_' . $image_name);
            $final_path  = public_path('custom_event_qr_code/' . $image_name);
 
            $qr_size = ($qr_width > 0 && $qr_height > 0) ? $qr_width : 300;

            QrCode::format('png')
                ->size($qr_size)
                ->color($color[0], $color[1], $color[2])
                ->backgroundColor(0, 0, 0, 0)
                ->generate($link, $qr_tmp_path);

            $background = Image::make(public_path('images/' . $event->getRawOriginal('image')));

            if ($image_width > 0 && $image_height > 0) {
                $background->resize($image_width, $image_height);
            }

            $qr = Image::make($qr_tmp_path);

            
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

            $center_x = intval($background->width() / 2);
            $text_y   = $y + $qr->height() + 15;

            if ($event->language == 'ar') {
                $Arabic    = new \ArPHP\I18N\Arabic('Glyphs');
                $font_path = public_path('font/DroidArabicKufiRegular.ttf');
                $name      = $Arabic->utf8Glyphs($user_event->name);
                $Arabic2   = new \ArPHP\I18N\Arabic('Glyphs');
                $name2     = $Arabic2->utf8Glyphs('عدد الضيوف ' . $user_event->confirm_count);    
                if($user_event->suit_num && $user_event->suit_num != 0){
                    $Arabic3   = new \ArPHP\I18N\Arabic('Glyphs');
                    $name3     = $Arabic3->utf8Glyphs('رقم الكرسى ' . $user_event->suit_num);
                }
            } else {
                $font_path = public_path('font/LuxuriousRoman-Regular.ttf');
                $name      = $user_event->name;
                $name2     = 'Entered Users ' . $user_event->confirm_count;
                if($user_event->suit_num && $user_event->suit_num != 0){
                    $name3     = "Suit Num " . $user_event->suit_num;
                }
            }

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

            if ($number_qr && $user_event->confirm_count > 1) {
                $background->text($name2, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
                    $font->file($font_path);
                    $font->size(20);
                    $font->color($text_color);
                    $font->align('center');
                    $font->valign('top');
                });
                $text_y += 25;
            }

            if ($name3) {
                $background->text($name3, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
                    $font->file($font_path);
                    $font->size(20);
                    $font->color($text_color);
                    $font->align('center');
                    $font->valign('top');
                }); 
            }

            $background->save($final_path, 100);

            if (file_exists($qr_tmp_path)) {
                unlink($qr_tmp_path);
            }

        } else {

            $bg           = 'qr-image-v9.jpg';
            $link         = asset('scan-qr/' . $uu_id);
            $qr_code_path = 'qr_code/' . $image_name;

            QrCode::format('png')
            ->size(450)
            ->color($color[0], $color[1], $color[2])
            ->backgroundColor(255, 255, 255) // تم التعديل هنا للون الأبيض ليطابق خلفية الكارت
            ->generate($link, $qr_code_path);
            
            // يمكنك الاستغناء عن دالة الشفافية اليدوية إذا لم تعد بحاجة لها
            // make_qr_transparent(public_path($qr_code_path)); 
            
            Image::make($bg)->insert($qr_code_path, 'left', 320, 0)->widen(450)->save($qr_code_path, 100);

            $destination = public_path($qr_code_path);
            $new_img     = Image::make($destination);

            if ($user_event->accept_count > 1) {
                $new_img->text($user_event->accept_count, 115, 412, function ($font) {
                    $font->file(public_path('font/OpenSans-Italic.ttf'));
                    $font->size(25);
                    $font->color('#000');
                });
            }

            $new_img->save($destination);
            return $destination;
        }
        $row->update(["qr" => $image_name]);
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
        $query = Model::
        with("user:id,name");

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
        $query = Model::
        with("user:id,name")
        ->onlyTrashed();

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
    
    public function restore_deleted($id)
    {
        // بنستخدم onlyTrashed علشان ندور في الحاجات اللي في الـ Trash بس
        $Item = Model::onlyTrashed()->findOrFail($id);

        // استرجاع البيانات كأنها مش ممسوحة
        $Item->restore();

        return response()->json([
            'success' => 'تم استرجاع البيانات بنجاح', 
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
            "qr_width", "qr_x", "qr_y", "lat", "lng", "pdf_bottom",
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

  	public function excel_event_family(Request $request) {

        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $event_id = $request->custom_event_id;

        $event_users = CustomEventFamily::
        where('event_id', $event_id)
        ->get(); // عدد النتائج في الصفحة

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
        ->orWhere("id", $Item->user_id)
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

    public function event_host_report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        if($user_status){ 
            $visitors_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->sum('users_count');
            $qr_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->where('scan','yes')
            ->sum('scan_count'); 
            $congratulation_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "congratulation")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->count();
            $apologize_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "apologize")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->count();
            $apologize_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->sum("apologize_count");
            $confirm_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->sum("confirm_count"); 
        }
        else{
            $visitors_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->sum('users_count');
            $qr_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->where('scan','yes')
            ->sum('scan_count'); 
            $congratulation_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "congratulation")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->count();
            $apologize_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "apologize")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->count();
            $apologize_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->sum("apologize_count");
            $confirm_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->sum("confirm_count"); 
        }

        return response()->json([
            'Item' =>  $Item, 
            'visitors_count' =>  $visitors_count, 
            'qr_count' =>  $qr_count,  
            'congratulation_msg' =>  $congratulation_msg, 
            'apologize_msg' =>  $apologize_msg, 
            'apologize_count' =>  $apologize_count, 
            'confirm_count' =>  $confirm_count,  
        ]); 
    }

    public function event_host_visitor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
            'search' => 'sometimes|nullable|string',
            'per_page' => 'sometimes|nullable|integer|min:1'
        ]); 
        if ($validator->fails()) { 
            return response()->json(['errors' => $validator->errors()], 400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        $perPage = $request->get('per_page', 10);

        $query = CustomEventUsers::where('custom_event_id', $Item->id);

        if($user_status){ 
            $query->where(function($q) use($request){ 
                $q->where("user_id", $request->user_id)->orWhereNull("user_id");
            }); 
        } else {
            $query->where("user_id", $request->user_id);
        }

        // Search Filter
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        $visitors_count = $query->paginate($perPage);

        return response()->json([
            'Item' =>  $Item, 
            'visitors_count' =>  $visitors_count, 
        ]); 
    }

    public function event_host_qr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
            'search' => 'sometimes|nullable|string',
            'per_page' => 'sometimes|nullable|integer|min:1'
        ]); 
        if ($validator->fails()) { 
            return response()->json(['errors' => $validator->errors()], 400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        $perPage = $request->get('per_page', 10);

        $query = CustomEventUsers::where('custom_event_id', $Item->id)->where('scan', 'yes');

        if($user_status){  
            $query->where(function($q) use($request){ 
                $q->where("user_id", $request->user_id)->orWhereNull("user_id");
            });
        } else {
            $query->where("user_id", $request->user_id);
        }

        // Search Filter
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        $qr_count = $query->paginate($perPage);

        return response()->json([
            'Item' =>  $Item, 
            'qr_count' =>  $qr_count,
        ]); 
    }

    public function event_host_congrate_msg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
            'search' => 'sometimes|nullable|string',
            'per_page' => 'sometimes|nullable|integer|min:1'
        ]); 
        if ($validator->fails()) { 
            return response()->json(['errors' => $validator->errors()], 400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        $perPage = $request->get('per_page', 10);

        $query = CustomMessage::where("custom_event_id", $Item->id)->where("type", "congratulation");

        if($user_status){ 
            $query->whereHas("user", function($q) use($request){ 
                $q->where("user_id", $request->user_id)->orWhereNull("user_id");
            });
        } else {
            $query->whereHas("user", function($q) use($request){ 
                $q->where("user_id", $request->user_id);
            });
        }

        // Search Filter (البحث بمحتوى الرسالة أو اسم المرسل)
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('msg', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($qu) use ($request) {
                      $qu->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $congratulation_msg = $query->paginate($perPage);

        return response()->json([
            'Item' =>  $Item,
            'congratulation_msg' =>  $congratulation_msg, 
        ]); 
    }

    public function event_host_apologize_msg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
            'search' => 'sometimes|nullable|string',
            'per_page' => 'sometimes|nullable|integer|min:1'
        ]); 
        if ($validator->fails()) { 
            return response()->json(['errors' => $validator->errors()], 400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        $perPage = $request->get('per_page', 10);

        $query = CustomMessage::where("custom_event_id", $Item->id)->where("type", "apologize");

        if($user_status){  
            $query->whereHas("user", function($q) use($request){ 
                $q->where("user_id", $request->user_id)->orWhereNull("user_id");
            });
        } else {
            $query->whereHas("user", function($q) use($request){ 
                $q->where("user_id", $request->user_id);
            });
        }

        // Search Filter
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('msg', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($qu) use ($request) {
                      $qu->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $apologize_msg = $query->paginate($perPage);

        return response()->json([
            'Item' =>  $Item, 
            'apologize_msg' =>  $apologize_msg,
        ]); 
    }

    public function event_host_apologize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
            'search' => 'sometimes|nullable|string',
            'per_page' => 'sometimes|nullable|integer|min:1'
        ]); 
        if ($validator->fails()) { 
            return response()->json(['errors' => $validator->errors()], 400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        $perPage = $request->get('per_page', 10);

        $query = CustomEventUsers::where("custom_event_id", $Item->id);

        if($user_status){ 
            $query->where(function($q) use($request){ 
                $q->where("user_id", $request->user_id)->orWhereNull("user_id");
            });
        } else {
            $query->where(function($q) use($request){ 
                $q->where("user_id", $request->user_id);
            });
        }

        // Search Filter
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        $apologize_count = $query->paginate($perPage);

        return response()->json([
            'Item' =>  $Item, 
            'apologize_count' =>  $apologize_count, 
        ]); 
    }

    public function event_host_confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
            'search' => 'sometimes|nullable|string',
            'per_page' => 'sometimes|nullable|integer|min:1'
        ]); 
        if ($validator->fails()) { 
            return response()->json(['errors' => $validator->errors()], 400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        $perPage = $request->get('per_page', 10);

        $query = CustomEventUsers::where("custom_event_id", $Item->id);

        if($user_status){
            $query->where(function($q) use($request){ 
                $q->where("user_id", $request->user_id)->orWhereNull("user_id");
            }); 
        } else {
            $query->where(function($q) use($request){ 
                $q->where("user_id", $request->user_id);
            }); 
        }

        // Search Filter
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        $confirm_count = $query->paginate($perPage);

        return response()->json([
            'Item' =>  $Item,   
            'confirm_count' =>  $confirm_count,  
        ]); 
    }

    public function qr_count(Request $request, $id){// استقبال كلمة البحث من الـ Request (مثال: ?search=أحمد)
        $search = $request->input('search'); 
        
        // تحديد عدد العناصر في كل صفحة (افتراضي 10)
        $perPage = $request->input('per_page', 10); 

        $custom_event_users = CustomEventUsers::where('custom_event_id', $id)
            ->where('scan', 'yes')
            ->when($search, function ($query, $search) {
                // البحث في حقول الاسم، الهاتف، أو الـ uuid
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage); // استخدام التقسيم بدلاً من get()

        return response()->json([
            "status" => true,
            "custom_event_users" => $custom_event_users
        ]);
    } 

    public function excel_event_host_visitor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        if($user_status){ 
            $visitors_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->get(); 
        }
        else{
            $visitors_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->get(); 
        }

        return response()->json([
            'Item' =>  $Item, 
            'visitors_count' =>  $visitors_count, 
        ]); 
    }

    public function excel_event_host_qr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        if($user_status){  
            $qr_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->where('scan','yes')
            ->get();
        }
        else{
            $qr_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->where('scan','yes')
            ->get(); 
        }

        return response()->json([
            'Item' =>  $Item, 
            'qr_count' =>  $qr_count,
        ]); 
    }

    public function excel_event_host_congrate_msg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        if($user_status){ 
            $congratulation_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "congratulation")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->get();
        }
        else{
            $congratulation_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "congratulation")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->get();
        }

        return response()->json([
            'Item' =>  $Item,
            'congratulation_msg' =>  $congratulation_msg, 
        ]); 
    }

    public function excel_event_host_apologize_msg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        if($user_status){  
            $apologize_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "apologize")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->get();
        }
        else{
            $apologize_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "apologize")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->get();
        }

        return response()->json([
            'Item' =>  $Item, 
            'apologize_msg' =>  $apologize_msg,
        ]); 
    }

    public function excel_event_host_apologize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        if($user_status){ 
            $apologize_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->get();
        }
        else{
            $apologize_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->get();
        }

        return response()->json([
            'Item' =>  $Item, 
            'apologize_count' =>  $apologize_count, 
        ]); 
    }

    public function excel_event_host_confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            "user_id" => 'required|exists:users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = Model::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == $request->user_id;
        if($user_status){
            $confirm_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id)
                ->orWhereNull("user_id");
            })
            ->get(); 
        }
        else{
            $confirm_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", $request->user_id);
            })
            ->get(); 
        }

        return response()->json([
            'Item' =>  $Item,   
            'confirm_count' =>  $confirm_count,  
        ]); 
    }

    public function excel_qr_count($id){
        $custom_event_users = CustomEventUsers::
        where('custom_event_id',$id)
        ->where('scan','yes')
        ->get();

        return response()->json([
            "custom_event_users" => $custom_event_users
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

    public function send_event_location($id) {

        $user_event = CustomEventUsers::
        withTrashed()
        ->findOrFail($id);

        $event = $user_event->event;

        $mobile = ltrim($user_event->mobile,"+");

        $setting = Setting::first();

 
        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);  

        // $api=$client->sendChatMessage($to,$body);
        $api2 = $client->sendLocationMessage($mobile,$event->address,$event->lat,$event->lng,$priority=0,$referenceId="SDK");
        $response = ["success"];

        return response()->json([
            'success' => 'تم الارسال بنجاح', 
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
                    \App\Jobs\SendCustomEventPdfJob::dispatch($row->id, $event->id, $ultramsg_token, $instance_id, $event->pdf_bottom);
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

    public function delete_messages(Request $request)
    {
        // 1. التحقق من أن المدخلات عبارة عن مصفوفة (Array)
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        if (!empty($request->items)) {
            // 2. استخراج الـ IDs من الـ Array
            $ids = collect($request->items)->pluck('id')->toArray();

            // 3. حذف كل الرسائل اللي نوعها تهنئة أو اعتذار وموجودة في الـ IDs بـ Query واحدة
            CustomMessage::whereIn('id', $ids) 
                ->delete(); // أو forceDelete() لو عاوز تحذف نهائي من الـ DB مباشرة

            return response()->json([
                "success" => "تم حذف الرسائل المختارة بنجاح",
            ]);
        }

        return response()->json([
            "error" => "من فضلك اختر عنصر واحد على الأقل",
        ], 400);
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
    
    public function excel_confirm_count(Request $request, $id){
    
        $Item = Model::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->where("confirm_count", ">", 0) 
        ->get();

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
    
    public function excel_apologize_count(Request $request, $id){
        
        $Item = Model::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->where("apologize_count", ">", 0) 
        ->get();

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
        ->orWhere("id", $Item->user_id)
        ->get();

        return response()->json([ 
            "Item" => $Item,
            "event_host" => $event_host
        ]);
    }
    
    public function host_custom_create(Request $request){
        $validator = Validator::make($request->all(), [
          'mobile_code' => 'required|exists:mobile_codes,id',
          'mobile' => 'required',
          'name' => 'required',
          'custom_invetaion' => 'required|numeric',
          "custom_event_id" => "required|exists:custom_event,id",
          "password" => "required",
          "user_id" => "required|exists:users,id",
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        if(!$request->custom_event_id && !$request->event_user_id){
            return response()->json([
                "errors" => "custom_event_id or event_user_id is required"
            ]);

        } 

        $user = User::findOrFail($request->user_id);
        $user->custom_invetaion -= $request->custom_invetaion;
        $user->balance -= $request->custom_invetaion;
        $user->save();
        User::create([
            "mobile_code" => $request->mobile_code,
            "mobile" => $request->mobile,
            "name" => $request->name,
            "custom_invetaion" => $request->custom_invetaion,
            "user_id" => $user->id,
            "custom_event_id" => $request->custom_event_id ?? null,
            "event_id" => $request->event_user_id ?? null,
            "password" => Hash::make($request->password),
            "balance" => $request->custom_invetaion
        ]);

        return response()->json([
            "success" => "You add data success"
        ]);
    }

    public function host_custom_update(Request $request, $id){
        $validator = Validator::make($request->all(), [
          "password" => "required"
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
  
        User::
        where("id", $id) 
        ->update([
            "password" => Hash::make($request->password)
        ]);

        return response()->json([
            "success" => "You update data success"
        ]);
    }

    public function remember_users_to_event(Request $request)
    {
       $validator = Validator::make($request->all(), [ 
            'message' => 'required',
            'custom_event_id' => 'required|exists:custom_event,id',
            'users' => 'required|array',
            'users.*' => 'required|exists:custom_event_users,id',
          	'file'  => 'nullable',
            'date' => 'required',
            'time' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   

        $setting = Setting::first(); 

        $event_id = $request->custom_event_id;

        $event = Model::where('id', $event_id)->firstOrFail();

      	$message = $request->message;

        $url_button = '?q=' . $event->lat . ',' . $event->lng;

      	$path = 'images';
      	$filename = '';

        if($request->file('file') != null && $request->file != null) {

            $extension = $request->file('file')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('file')->move($path, $filename);

            $url_image = asset('images/'.$filename);

        } else {
            $url_image = $event->image;
        }

        /* ***************************************************************************** */

        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true;

        /* ***************************************************************************** */

        try {

            $errors = 0;

            foreach($request->users as $item) {

                $user_event = CustomEventUsers::withTrashed()->find($item);

                $url_button = '?q=' . $event->lat . ',' . $event->lng;

                $user_name = $user_event->name;

                $mobile = $user_event->mobile;

                //$to = $code.$mobile;
                $to = $mobile;
                $to = str_replace("+","",$to);

                //$time = $event->time . ' مساءًً';
                $date = $request->date;
                $time = $request->time;

                $template_name = 'car_msg3_';

                $caption = "ضيفتنـا الغاليـة , ننتظـرك يوم ". $date ." في تمــام الساعة "  . $time . "  تشرفينــا لحضور " . $request->message2 . ' 🌺🌺 ';

                // $caption2 = 'تحرص الشركة على تقديم المساعدة للضيف حتى لا توجه اي صعوبات في دخول المناسبة تم ارسال الكود مره ثانية ,يرجى العلم ان الكود نفس الكود المرسل في السابق وليس كودا جديداً ';

                // $api=$client->sendChatMessage($to,$body);
                $api = $client->sendImageMessage($to,$url_image,$caption,$priority,$referenceId,$nocache);
                $api2 = $client->sendLocationMessage($to,$event->address,$event->lat,$event->lng,$priority=0,$referenceId="SDK");

                // $qr_code_row = Qr_Code::where('event_user_id',$arr['id'])->latest()->first();

                // if($qr_code_row) {
                //     $image_link = url('qr_code/' . $qr_code_row->qr);
                //     // $api3 = $client->sendImageMessage($to,$image_link,$caption2,$priority,$referenceId,$nocache);
                // }

                if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {
                    // dd('ok');
                    info('error sending');
                } else {
                    // dd('not ok',$api);
                    $errors = $errors + 1;
                } 
            }

            return response()->json([
                'success' => 'تم الأرسال بنجاح..', 
            ]); 
            

        } catch(\Exception $e) {
            dd($e,$e->getMessage(), $e->getLine());
        }

        dd('error-v2'); 

    }

    public function scan_data(Request $request){ 
       $validator = Validator::make($request->all(), [
            'qr_id' => 'required|exists:custom_event_users,uu_id',
        ]); 
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
        // qr_id  
        $Item = CustomEventUsers::
        where("uu_id",$request->qr_id)
        ->with("event")
        ->firstOrFail(); 
 
        return response()->json([
            "id" => $Item?->id,
            "custom_event_id" => $Item?->custom_event_id,
            "user_name" => $Item?->name,
            "user_mobile" => $Item?->mobile, 
            "custom_event_name" => $Item?->event?->title,
            "scan_count" => $Item?->scan_count,
            "users_count" => $Item?->users_count,
            "available" => $Item?->users_count - $Item?->scan_count,
            "apologize_count" => $Item?->apologize_count,
            "confirm_count" => $Item?->confirm_count,
        ]);
    }

    public function scan_qr(Request $request){
       $validator = Validator::make($request->all(), [
            'qr_id' => 'required|exists:custom_event_users,uu_id',
            "users_count" => 'required|numeric',
        ]); 
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        // qr_id
        
        $Item = CustomEventUsers::
        where("uu_id",$request->qr_id)
        ->firstOrFail(); 
        $user_data = User::
        where("id", $Item->user_id)
        ->first();
        if(!$user_data){
            $user_data = User::
            where("id", $Item?->event?->user_id)
            ->first();
        }
        $available = $user_data->custom_invetaion - $user_data->send_custom_invetaion;
        if($request->users_count >= $available){
            return response()->json([
                "errors" => "لا تمتلك كل هذا العدد من الدعوات تم ارسال البعض و ليس الكل"
            ], 400);
        }
        if(!$Item || $Item?->users_count < $Item?->scan_count + $request->users_count || $Item?->is_refused == 'yes' || $Item?->accept_count < 1) {
            return response()->json([
                'errors' => 'عفوا هذا QR غير متاح', 
            ],400);

        }
        $user_data->send_custom_invetaion += $request->users_count;
        $user_data->save();
        EnterUserCustomEvent::create([
            "custom_user_id" => $Item->id,
            "count" => $request->users_count
        ]);
         

      	$now = Carbon::now(); 

        $Item->update(['scan' => 'yes',
        'scan_at' => $now,
        'scan_count' => $request->users_count + $Item->scan_count]);

 
        return response()->json([
            'success' => 'تم عمل QR Scan  بنجاح', 
        ]);
    }
    
    public function re_send_custom_qr(Request $request)
    {
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
  
        $event_id = $request->custom_event_id;

        $event = Model::where('id', $event_id)->firstOrFail();
 

        /* ***************************************************************************** */

        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true;
 
        try {

            $errors = 0; 
            $user_event = CustomEventUsers::
            withTrashed()
            ->whereIn("id", $request->users)
            ->get();
            foreach($user_event as $item) {
                $user_name = $item->name;

                $mobile = $item->mobile;

                //$to = $code.$mobile;
                $to = $mobile;
                $to = str_replace("+","",$to);
                $url_image = $item->qr;
                $caption = 'باركود الدخـول الخـاص بـك, فضـلاً تأكد من حفـظ الصـورة في هاتفك لإبرازهــا عند دخـول المناسبة.'
                // . $user_name 
                . PHP_EOL . PHP_EOL .
                ' عدد الدعوات (' . $item->confirm_count . ")";

                // $api=$client->sendChatMessage($to,$body);
                $api = $client->sendImageMessage($to,$url_image,$caption,$priority,$referenceId,$nocache);
  
            }

            return response()->json([
                'success' => 'تم الأرسال بنجاح', 
            ]); 

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

        dd('error-v2'); 
    }

    public function custom_event_login($uu_id){
        $user = CustomEventUsers::
        where("uu_id", $uu_id)
        ->with("event", "congratulation_msg", "apologize_msg")
        ->with("event")
        ->firstOrFail();

        return response()->json([
            "id" => $user->id,
            "uu_id" => $user->uu_id,
            "name" => $user->name,
            "mobile" => $user->mobile,
            "qr" => $user->qr,
            "scan" => $user->scan,
            "users_count" => $user->users_count,
            "confirm_count" => $user->confirm_count,
            "apologize_count" => $user->apologize_count,
            "map" => $user?->event?->map,
            "congratulation_msg" => count($user->congratulation_msg) > 0 ? 
            $user->congratulation_msg[0] : null,
            "apologize_msg" => count($user->apologize_msg) > 0 ? 
            $user->apologize_msg[0] : null,
        ]);
    }

    public function custom_event_applogize_count(Request $request, $id){
        $user = CustomEventUsers::
        where("id", $id) 
        ->firstOrFail();
        $max_count = $user->users_count -$user->confirm_count -$user->apologize_count;
       $validator = Validator::make($request->all(), [  
            'apologize' => 'required|numeric|max:' . $max_count,
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 

        $user->update([
            "apologize_count" => $user->apologize_count + $request->apologize
        ]);
        return response()->json([
            "success" => "You update data success"
        ]);
    }

    public function custom_event_confirm_count(Request $request, $id){
        $user = CustomEventUsers::
        where("id", $id) 
        ->firstOrFail();
        $max_count = $user->users_count -$user->confirm_count -$user->apologize_count;
       $validator = Validator::make($request->all(), [  
            'confirm_count' => 'required|numeric|max:' . $max_count,
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 

        $user->update([
            "confirm_count" => $user->confirm_count + $request->confirm_count
        ]);
        return response()->json([
            "success" => "You update data success"
        ]);
    }

    public function custom_event_congratulation_msg(Request $request, $id){

       $validator = Validator::make($request->all(), [  
            'msg' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
        $user = CustomEventUsers::
        where("id", $id) 
        ->firstOrFail(); 
        $old_msg = CustomMessage::
        where("custom_user_id", $id)
        ->where("type", "congratulation")
        ->first();
        if($old_msg){
            return response()->json([
                "errors" => "You add msg before you can not add new msg"
            ], 400);
        }
        CustomMessage::create([
            'custom_event_id' => $user->custom_event_id,
            'custom_user_id' => $user->id,
            'msg' => $request->msg,
            'type' => "congratulation",
        ]);
        
        return response()->json([
            "success" => "You add data success"
        ]);
    }

    public function custom_event_apologize_msg(Request $request, $id){

       $validator = Validator::make($request->all(), [  
            'msg' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
        $user = CustomEventUsers::
        where("id", $id) 
        ->firstOrFail(); 
        $old_msg = CustomMessage::
        where("custom_user_id", $id)
        ->where("type", "apologize")
        ->first();
        if($old_msg){
            return response()->json([
                "errors" => "You add msg before you can not add new msg"
            ], 400);
        }
        CustomMessage::create([
            'custom_event_id' => $user->custom_event_id,
            'custom_user_id' => $user->id,
            'msg' => $request->msg,
            'type' => "apologize",
        ]);
        
        return response()->json([
            "success" => "You add data success"
        ]);
    }

    public function my_package(Request $request, $id){
          
        $Item = Model::
        with("user.order")
        ->withTrashed()->findOrFail($id);
        $arr = [ 
            "id"=> $Item->id,
            "title"=> $Item->title,
            "image"=> $Item->image,  
            "map"=> $Item->map,
            "lat"=> $Item->lat,
            "long"=> $Item->lng,
            "address"=> $Item->address, 
            "date"=> $Item->date,
            "time"=> $Item->time, 
            "user_id"=> $Item->user_id,
            "user_name"=> $Item?->user?->name ?? null,
            "assistant_id"=> $Item->assistant_id,
            "phone"=> $Item?->user?->mobile_code . $Item?->user?->mobile,
            "invitation_count"=> $Item?->user?->order?->users_count,
            "reservation_date"=> $Item?->user?->order?->start_subscription_date,
            "package_price"=> $Item?->user?->order?->total,
            "payment_type"=> $Item?->user?->order?->payment_type,
            "is_paid"=> $Item?->user?->order?->is_paid,
            "employee_gender"=> $Item?->user?->employee_gender == 'male' ? 'رجل' : 'مرأة',
            "color"=> $Item->color,
            "video"=> $Item->video,
            "created_at"=> $Item->created_at,
            "updated_at"=> $Item->updated_at,
        ];

        return response()->json([
            "Item" => $arr
        ]);
    }
                    
    // // send_event_users
    public function send_custom_message(Request $request)
    {
       $validator = Validator::make($request->all(), [ 
            'message' => 'required',
            'file'  => 'nullable',
            'custom_event_id' => 'required|exists:custom_event,id',
            'users' => 'required|array',
            'users.*' => 'required|exists:custom_event_users,id',
            "type" => "required|in:image,pdf,video"
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }    
  
        $event_id = $request->custom_event_id;

        $event = CustomEvent::where('id', $event_id)->firstOrFail();

      	$path = 'images';
      	$filename = '';

        if($request->file('file') != null && $request->file != null) {

            $extension = $request->file('file')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('file')->move($path, $filename);

            $url_image = asset('images/'.$filename);

        } else {
            
            if($request->type == "video"){
                $url_image = $event->video;
            }
            elseif($request->type != "pdf"){
                $url_image = $event->image;
            }
        }

        /* ***************************************************************************** */

        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true;

        /* ***************************************************************************** */

        try {

            $errors = 0;

            if($request->users != null && ! empty($request->users)) {

                foreach($request->users as $item) {
  
                    $user_event = CustomEventUsers::withTrashed()->find($item);
  
                    $user_name = $user_event->name;

                    $mobile = $user_event->mobile;
 
                    $to = $mobile;
                    $to = str_replace("+","",$to);
  
                    $caption = '' . $user_name . PHP_EOL . PHP_EOL .
                    ' ' . $request->message;

                    // $api=$client->sendChatMessage($to,$body);
                    if($request->type == "pdf"){ 
                        SendCustomEventPdfJob::dispatch($item, $event->id, $ultramsg_token, $instance_id, $event->pdf_bottom, $caption);
                    }
                    elseif($request->type == "video"){ 
                        $api = $client->sendVideoMessage($to, $url_image,$caption,$priority,$referenceId,$nocache);
                    }
                    else{
                        $api = $client->sendImageMessage($to,$url_image,$caption,$priority,$referenceId,$nocache);
                    }  
                }

                return response()->json([
                    'success' => 'تم الأرسال بنجاح', 
                ]);
            }

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

        dd('error-v2');

    }

    // send_congratulation_messages
    public function send_congratulation_messages(Request $request)
    {
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

        $event_id = $request->custom_event_id;

        $event = CustomEvent::where('id', $event_id)->firstOrFail();

        /* ***************************************************************************** */

        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true;

        /* ***************************************************************************** */

        try {

            $errors = 0;
 
            foreach($request->users as $item) {
 
                $user_event = CustomEventUsers::withTrashed()->find($item); 
                $user_name = $user_event->name;

                $mobile = $user_event->mobile;

                //$to = $code.$mobile;
                $to = $mobile;
                $to = str_replace("+","",$to);
  
                $caption = 'حياكم الله ،،' .
                'اكتمل حفلنا بحضوركم نتمنى لكم ليلة ممتعة🌹';

                // $api=$client->sendChatMessage($to,$body);
                $api = $client->sendChatMessage($to,$caption,$priority,$referenceId,$nocache);
            }


            return response()->json([
                'success' => 'تم الأرسال بنجاح', 
            ]); 
        

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

        dd('error-v2');

    }
}