<?php

namespace App\Jobs;

use App\Models\CustomEvent;
use App\Models\CustomEventUsers;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class SendCustomEventPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $eventId;
    public $ultramsg_token;
    public $instance_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $eventId, $ultramsg_token, $instance_id)
    {
        $this->userId = $userId;
        $this->eventId = $eventId;
        $this->ultramsg_token = $ultramsg_token;
        $this->instance_id = $instance_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('PDF Job Started - User ID: ' . $this->userId . ', Event ID: ' . $this->eventId);
        $row = CustomEventUsers::find($this->userId);
        $event = CustomEvent::find($this->eventId);

        if (!$row) {
            Log::error('PDF Job Error - CustomEventUsers row not found for ID: ' . $this->userId);
            return;
        }
        if (!$event) {
            Log::error('PDF Job Error - CustomEvent row not found for ID: ' . $this->eventId);
            return;
        }
        if (!$row->mobile) {
            Log::warning('PDF Job Warning - CustomEventUsers mobile is empty for ID: ' . $this->userId);
            return;
        }

        $to = $row->mobile;
        $day_name = Carbon::parse($event->date)->locale('ar')->translatedFormat('l');

        if($event->show_data_pdf){
            $caption = $row->name . PHP_EOL . PHP_EOL .
                $event->title . PHP_EOL . PHP_EOL .
                "وذلك بمشيئة الله تعالى يوم " . $day_name ." الموافق"  . $event->date . " 📆" 
                . PHP_EOL . PHP_EOL .
                "وقت الاستقبال ⏱️الساعـة " . $event->time . " مساءاً" . PHP_EOL . PHP_EOL .
                "📍مكان الحفـل " . $event->address  . PHP_EOL . PHP_EOL .
                "عدد الدعوات " . $row->users_count . PHP_EOL . PHP_EOL .
                "تم إرسـال هذه الرسالة من خـــلال تطبيق معزوم للدعوات الإلكترونية";
        } else { 
            $caption = $row->name . PHP_EOL . PHP_EOL .
                "عدد الدعوات " . $row->users_count . PHP_EOL . PHP_EOL .
                "تم إرسـال هذه الرسالة من خـــلال تطبيق معزوم للدعوات الإلكترونية";
        }

        $confirm_link = url("confirm_custom_event/" . $row->id);
        $apologize_link = url("apologize_custom_event/" . $row->id);
        
        // استخدام المسار الداخلي المطلق للسيرفر
        $pdfFile = $event->getRawOriginal('pdf');
        $pdfPath = public_path('images/' . $pdfFile);

        Log::info('PDF Job - pdfFile: ' . $pdfFile);
        Log::info('PDF Job - pdfPath: ' . $pdfPath);
  
        if ($pdfFile && file_exists($pdfPath)) {
            $imagePathForRender = $pdfPath;
        } else {
            $imagePathForRender = public_path('img/no-image.png');
            Log::warning('PDF Job - background image not found, using fallback no-image.png');
        }

        // ضغط الصورة لتقليل حجم الـ PDF
        $compressedImagePath = null;
        if (file_exists($imagePathForRender)) {
            Log::info('PDF Job - Starting image compression for image: ' . $imagePathForRender . ' (Size: ' . filesize($imagePathForRender) . ' bytes)');
            try {
                $ext = strtolower(pathinfo($imagePathForRender, PATHINFO_EXTENSION));
                $srcImage = null;
                if ($ext === 'png') {
                    $srcImage = @imagecreatefrompng($imagePathForRender);
                } elseif (in_array($ext, ['jpg', 'jpeg'])) {
                    $srcImage = @imagecreatefromjpeg($imagePathForRender);
                }

                if ($srcImage) {
                    $targetW = 794; // A4 Width at 96 DPI
                    $targetH = 1123; // A4 Height at 96 DPI
                    $resized = imagecreatetruecolor($targetW, $targetH);
                    
                    // التعامل مع شفافية PNG إذا وجدت
                    if ($ext === 'png') {
                        imagealphablending($resized, false);
                        imagesavealpha($resized, true);
                        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                        imagefilledrectangle($resized, 0, 0, $targetW, $targetH, $transparent);
                    }

                    imagecopyresampled($resized, $srcImage, 0, 0, 0, 0,
                        $targetW, $targetH, imagesx($srcImage), imagesy($srcImage));
                    imagedestroy($srcImage);

                    $compressedImagePath = sys_get_temp_dir() . '/pdf_bg_' . uniqid() . '.jpg';
                    $quality = 80; // جودة عالية مع ضغط جيد
                    if (imagejpeg($resized, $compressedImagePath, $quality)) {
                        Log::info('PDF Job - Image compressed successfully.');
                        $imagePathForRender = $compressedImagePath;
                    }
                    imagedestroy($resized);
                }
            } catch (\Throwable $ex) {
                Log::error('PDF Job - Exception in image compression: ' . $ex->getMessage());
            }
        }

        // --- الحل الجذري لمشكلة اختفاء الصورة: تحويلها لـ Base64 ---
        $base64Image = '';
        try {
            if (file_exists($imagePathForRender)) {
                $imageData = file_get_contents($imagePathForRender);
                $mimeType = mime_content_type($imagePathForRender);
                $base64Image = 'data:' . $mimeType . ';base64,' . base6464_encode($imageData);
                Log::info('PDF Job - Image converted to Base64 successfully.');
            }
        } catch (\Exception $e) {
            Log::error('PDF Job - Failed to convert image to Base64: ' . $e->getMessage());
        }
        // --------------------------------------------------------

        try {
            $config = [
                'margin_left'   => 0,
                'margin_right'  => 0,
                'margin_top'    => 0,
                'margin_bottom' => 0,
                'margin_header' => 0,
                'margin_footer' => 0,
                'format'        => 'A4',
                // إعدادات افتراضية أفضل للغة العربية
                'mode'          => 'utf-8',
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
            ];
            Log::info('PDF Job - Initializing mPDF');

            $mpdf = new \Mpdf\Mpdf($config);
            $mpdf->showImageErrors = true;
            $mpdf->SetDirectionality('rtl'); // تحديد الاتجاه الافتراضي للمستند

            // دمج كود الـ HTML والـ CSS
            // ملاحظة: تم إزالة @import الخاص بـ Google Fonts لضمان سرعة التحميل وعدم تعليق الخلفية
            // واستخدام خطوط نظام عربية احترافية وجميلة متوفرة دائماً
            $html = '
            <style>
            @page {
                background-image: url("' . $base64Image . '");
                background-image-resize: 6;
                background-repeat: no-repeat;
                margin: 0;
            }
            body { 
                background-color: transparent; 
                /* استخدام خطوط نظام احترافية وبديلة (تاهوما، سيجو، أريال) */
                font-family: "Segoe UI", Tahoma, "Arial", sans-serif; 
                margin: 0; 
                padding: 0; 
            }
            /* الحاوية التي تجبر الأزرار على البقاء بالأسفل */
            .buttons-container {
                position: absolute;
                bottom: 35mm; /* يمكنك تعديل هذا الرقم لرفع أو خفض الأزرار */
                width: 100%;
                text-align: center;
            }
            table.buttons-table { 
                margin: 0 auto; 
                border-collapse: separate; 
                border-spacing: 20px 0; /* مسافة أفقية أنيقة بين الزرين */
            }
            table.buttons-table td { 
                padding: 0; 
                width: 75mm; /* عرض مناسب للزر الواحد */
            }
            a.btn { 
                display: block; 
                padding: 18px 10px; /* Padding رأسي كبير لإعطاء حجم ضخم للزر */
                font-size: 14pt; /* خط أصغر متناسق مع حجم الزر الضخم */
                font-weight: bold; 
                text-decoration: none; 
                color: #ffffff; 
                border-radius: 50px; /* حواف دائرية بالكامل لتصميم عصري */
                text-align: center; 
                line-height: 1.2;
            }
            .btn-confirm { 
                background-color: #10B981; 
                border: 2px solid #047857;
            }
            .btn-apologize { 
                background-color: #EF4444; 
                border: 2px solid #B91C1C;
            }
            </style>
            
            <div class="buttons-container">
                <table class="buttons-table" cellpadding="0" cellspacing="0" dir="rtl">
                    <tr>
                        <td><a href="' . $confirm_link . '" class="btn btn-confirm">تأكيد الحضور ✓</a></td>
                        <td><a href="' . $apologize_link . '" class="btn btn-apologize">الاعتذار ✕</a></td>
                    </tr>
                </table>
            </div>';

            Log::info('PDF Job - Writing HTML content');
            $mpdf->WriteHTML($html);

            $filename = 'invitation_' . uniqid() . '_' . $row->id . '.pdf';
            $directory = public_path('temp_pdfs');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $pdf_path = $directory . '/' . $filename;
            Log::info('PDF Job - Outputting PDF file');
            $mpdf->Output($pdf_path, 'F');
            
        } catch (\Exception $e) {
            Log::error("PDF Job - Exception in PDF Generation: " . $e->getMessage());
            // تنظيف الصورة المضغوطة المؤقتة حتى لو فشل الـ PDF
            if ($compressedImagePath && file_exists($compressedImagePath)) {
                @unlink($compressedImagePath);
            }
            return;
        }
        
        $pdf_url = asset('temp_pdfs/' . $filename);

        Log::info('PDF Job - Initializing UltraMsg API');
        $client = new \UltraMsg\WhatsAppApi($this->ultramsg_token, $this->instance_id);
        
        $priority = 0;
        $referenceId = "SDK";
        $nocache = true;
        
        Log::info('PDF Job - Sending document via WhatsApp to: ' . $to);
        $api = $client->sendDocumentMessage($to, 'invitation.pdf', $pdf_url, $caption, $priority, $referenceId, $nocache);
        Log::info('PDF Job - WhatsApp API response: ' . json_encode($api));
        
        Log::info('PDF Job - Sleeping 5 seconds before clean up...');
        sleep(5);

        // تنظيف الملفات
        if (file_exists($pdf_path)) {
            @unlink($pdf_path);
            Log::info('PDF Job - Cleaned up PDF file');
        }
        if ($compressedImagePath && file_exists($compressedImagePath)) {
            @unlink($compressedImagePath);
            Log::info('PDF Job - Cleaned up compressed image file');
        }
        
        if (!empty($api) && isset($api['sent']) && $api['sent'] == 'true') {
            Log::info('PDF Job - Success message sent to ' . $to);
        } else {
            Log::error("Failed to send PDF via Ultramsg.", ['response' => $api]);
        }
    }
}