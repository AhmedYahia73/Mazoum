<?php

namespace App\Http\Controllers\Assistant;

use App\Models\CustomEventUsers;
use App\Models\Qr_Code;
use Illuminate\Http\Request;
use App\Http\Requests\CustomEvent as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\CustomEvent as Model;
use App\Models\CustomEventFamily;
use Carbon\Carbon;
use Response;
use PDF;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Notifications;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;



class CustomEventController extends Controller
{
    private $view = 'assistant.custom_events.';
    private $redirect = 'assistant_panel/custom_events';








     // save_event_users
    public function save_event_users(Request $request)
    {

        $request->validate([
            'custom_event_id' => 'required|exists:custom_event,id',
            'event_users.*.name' => 'required',
          	'event_users.*.users_count' => 'required|numeric|min:1',
        ]);

        $custom_event_id = $request->custom_event_id;

        $event = Model::where('id', $custom_event_id)->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {

                if($arr['name'] != null && $arr['users_count'] != null && is_numeric($arr['users_count'])) {

                    $uu_id = uniqid();

                    $row = CustomEventUsers::withTrashed()->create([
                        'custom_event_id' => $custom_event_id,
                        'name' => $arr['name'],
                        'users_count' => $arr['users_count'],
                        'uu_id' => $uu_id
                    ]);

                    $this->update_qr($row,$uu_id);

                }
            }

        }

        return redirect()->back()->with('success', 'تم الحفظ بنجاح');

    }




     // update_event_users
    public function update_event_users(Request $request)
    {

        $request->validate([
            'old_event_users.*.name' => 'required',
            'old_event_users.*.users_count' => 'required|numeric|min:0',
        ]);

        if($request->old_event_users != null && ! empty($request->old_event_users)) {

            foreach ($request->old_event_users as $id => $arr) {

                $row = CustomEventUsers::withTrashed()->find($id);

                if($row != null && $arr['name'] != null && $arr['users_count'] != null && is_numeric($arr['users_count'])) {

                    $uu_id = uniqid();

                  	$row->update([
                        'name' => $arr['name'],
                        'users_count' => $arr['users_count'],
                        'uu_id' => $uu_id
                    ]);

                    $this->update_qr($row,$uu_id);

                }
            }

        }

        return redirect()->back()->with('success', 'تم التحديث بنجاح');

    }



    public function delete_event_users($id)
    {
        $Item = CustomEventUsers::findOrFail($id);
        $Item->delete();
        return redirect()->back()->with('error', trans('home.delete_msg'));
    }




    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'custom_event_id' => 'required|exists:custom_event,id'
        ]);

        $file = $request->file('file');

        if (!$file->isValid()) {
            return back()->with('error', 'Uploaded file is not valid.');
        }

        // dd($request->all(), $request->hasFile('file'), $request->file('file'));

        $file_path = $file->store('temp');

        //dd($file_path);

        $saved_path = storage_path('app/' . $file_path);

        if (!file_exists($saved_path)) {
            return back()->with('error', 'File not found.');
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
                        'uu_id'           => $uu_id
                    ]);

                    $this->update_qr($row,$uu_id);
                }

            }
        }

        return redirect()->back()->with('success', 'Imported successfully!');
    }


    private function update_qr($row, $uu_id) {
        $event = $row->event;
        $bg = $event->image;

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

        // إنشاء QR بخلفية شفافة
        $color = $this->hexToRgb($event_element->color);
        
        QrCode::format('png')
            ->size($qr_width > 0 ? $qr_width : 140) // كقيمة مبدئية لعرض الـ QR
            ->color($color[0], $color[1], $color[2])
            ->backgroundColor(0, 0, 0, 0)
            ->generate($link, $qr_temp_path);

        // افتح الخلفية
        $background = Image::make($bg);

        if ($image_width > 0 && $image_height > 0) {
            $background->resize($image_width, $image_height);
        }
        
        // افتح QR
        $qr = Image::make($qr_temp_path);

        if ($qr_width > 0 && $qr_height > 0) {
            $qr->resize($qr_width, $qr_height);
        }

        $x = ($qr_x > 0) ? $qr_x : intval(($background->width()  - $qr->width())  / 2);
        $y = ($qr_y > 0) ? $qr_y : intval(($background->height() - $qr->height()) / 2);

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

        // إضافة اسم الشخص (مربوط بالـ Boolean)
        if ($name_qr) {
            $background->text($name, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
                $font->file($font_path);
                $font->size(20);
                $font->color($text_color);
                $font->align('center');
                $font->valign('top');
            });
            
            // لو الاسم انطبع، ننزل السطر اللي بعده مسافة عشان العدد (لو موجود)
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
    public function index()
    {
        $admin = Auth::guard('assistant')->user();

        $Item = Model::where('assistant_id',$admin->id)->get();

        return view($this->view . 'index', compact('Item'));
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(modelRequest $request)
    {
        Model::create($this->gteInput($request, null));
        return redirect($this->redirect)->with('success', trans('home.save_msg'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = Auth::guard('assistant')->user();
        $Item = Model::where('assistant_id',$admin->id)->findOrFail($id);
        return view($this->view . 'edit', [ 'Item' => $Item ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(modelRequest $request, $id)
    {
        $Item = Model::findOrFail($id);
        $Item->update($this->gteInput($request, $Item));
        return redirect()->back()->with('info', trans('home.update_msg'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Item = Model::findOrFail($id);
        $Item->delete();
        return redirect()->back()->with('error', trans('home.delete_msg'));
    }


    private function gteInput($request, $modelClass)
    {
        $input = $request->only([
            'title', 'user_id', 'color' , 'assistant_id' , 'language'
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

        $request->validate([
            'event_id' => 'required|exists:custom_event,id',
            'event_users.*.name' => 'required',
            // 'event_users.*.mobile' => 'nullable|numeric',
        ]);

        $event_id = $request->event_id;

        $event = Model::where('id', $event_id)->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {
                if($arr['name'] != null) {

                  CustomEventFamily::create([
                    'event_id' => $event_id,
                    'name' => $arr['name'],
                    // 'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                    'scan_qr' => 'no'
                  ]);
                }
            }

        }

        return redirect()->back()->with('success', 'تم الحفظ بنجاح');

    }


    // update_event_family
    public function update_event_family(Request $request)
    {

        $request->validate([
            'old_event_users.*.name' => 'required',
            'old_event_users.*.mobile' => 'nullable|numeric',
        ]);

        if($request->old_event_users != null && ! empty($request->old_event_users)) {

            foreach ($request->old_event_users as $id => $arr) {

                $row = CustomEventFamily::find($id);

                if($row != null && $arr['name'] != null) {

                  	$row->update([
                        'name' => $arr['name'],
                    	// 'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                    ]);
                }
            }

        }

        return redirect()->back()->with('success', 'تم التحديث بنجاح');

    }



  	public function delete_event_family($id) {

        $user_event = CustomEventFamily::find($id);

        if($user_event != null) {
          $user_event->delete();
        }

        return redirect()->back()->with('success', 'تم الحذف بنجاح');

    }


  	public function open_event_family($id) {

        $user_event = CustomEventFamily::findOrFail($id);

        $user_event->update(['scan_qr' => 'yes']);

        return redirect()->back()->with('success', 'تم دخول الحفل بنجاح');

    }

  	///////////////////////////////////////////////////////////////////////////////////////

  	public function event_family_search(Request $request) {

        $request->validate([
          'event_id' => 'required'
        ]);

        $event_id = $request->event_id;

        $event_users = CustomEventFamily::where('event_id',$event_id)

        ->when($request->name,function($q) use($request) {

          $q->where('name','like','%' . $request->name . '%');

        })->when($request->mobile,function($q) use($request) {

          $q->where('mobile', $request->mobile);

        })->get();

        return view('admin.custom_events.event_family_search', compact('event_users','event_id'));

    }

  	///////////////////////////////////////////////////////////////////////////////////////






}
