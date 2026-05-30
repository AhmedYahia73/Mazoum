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
        $row = CustomEventUsers::find($this->userId);
        $event = CustomEvent::find($this->eventId);

        if (!$row || !$row->mobile || !$event) {
            return;
        }

        $to = $row->mobile;
        $day_name = Carbon::parse($event->date)->locale('ar')->translatedFormat('l');

        $caption = $row->name . PHP_EOL . PHP_EOL .
            $event->title . PHP_EOL . PHP_EOL .
            "وذلك بمشيئة الله يوم " . $day_name ." الموافق" . PHP_EOL . PHP_EOL .
            $event->date . " 📆" . PHP_EOL . PHP_EOL .
            "⏱️الساعـة " . $event->time . " مساءاً" . PHP_EOL . PHP_EOL .
            "📍مكان الحفـل " . $event->address ;

        $confirm_link = url("confirm_custom_event/" . $row->id);
        $apologize_link = url("apologize_custom_event/" . $row->id);
        
        // استخدام المسار الداخلي المطلق للسيرفر
        $pdfFile = $event->getRawOriginal('pdf');
        $pdfPath = public_path('images/' . $pdfFile);
        if ($pdfFile && file_exists($pdfPath)) {
            $image = str_replace('\\', '/', $pdfPath);
        } else {
            $image = str_replace('\\', '/', public_path('img/no-image.png'));
        }

        // ضغط الصورة لتقليل حجم الـ PDF (لكي يفتحها WhatsApp مباشرة بدون تحميل)
        $compressedImagePath = null;
        if ($image && file_exists($image)) {
            try {
                $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                $srcImage = null;
                if ($ext === 'png') {
                    $srcImage = @imagecreatefrompng($image);
                } elseif (in_array($ext, ['jpg', 'jpeg'])) {
                    $srcImage = @imagecreatefromjpeg($image);
                }

                if ($srcImage) {
                    // نضغط إلى 794x1123 (A4 على 96 DPI) لتقليل الحجم
                    $targetW = 794;
                    $targetH = 1123;
                    $resized = imagecreatetruecolor($targetW, $targetH);
                    imagecopyresampled($resized, $srcImage, 0, 0, 0, 0,
                        $targetW, $targetH, imagesx($srcImage), imagesy($srcImage));
                    imagedestroy($srcImage);

                    $compressedImagePath = sys_get_temp_dir() . '/pdf_bg_' . uniqid() . '.jpg';
                    imagejpeg($resized, $compressedImagePath, 80); // جودة 80% كافية
                    imagedestroy($resized);

                    $image = $compressedImagePath;
                }
            } catch (\Throwable $ex) {
                // نستمر بالصورة الأصلية إذا فشل الضغط
            }
        }

        try {
            $config = [
                'margin_left'   => 0,
                'margin_right'  => 0,
                'margin_top'    => 0,
                'margin_bottom' => 0,
                'margin_header' => 0,
                'margin_footer' => 0,
                'format'        => 'A4',
            ];

            // إنشاء mPDF مباشرة
            $mpdf = new \Mpdf\Mpdf($config);

            // وضع الصورة كخلفية كاملة للصفحة باستخدام Image() API مباشرة
            // A4 = 210mm x 297mm
            if ($image && file_exists($image)) {
                $mpdf->Image($image, 0, 0, 210, 297, '', '', true, false);
            }

            // بناء HTML الأزرار فقط (بدون صورة) بـ CSS بسيط جداً
            $buttonsHtml = '
<html><head><meta charset="UTF-8">
<style>
body { margin: 0; padding: 0; font-family: Arial; }
table { margin: 0 auto; }
td { padding-left: 6mm; padding-right: 6mm; }
a { display: block; padding-top: 3mm; padding-bottom: 3mm; padding-left: 6mm; padding-right: 6mm; font-size: 12pt; font-weight: bold; text-decoration: none; color: #ffffff; }
.a1 { background-color: #1e6b40; }
.a2 { background-color: #8e2020; }
</style>
</head><body>
<table cellpadding="0" cellspacing="0">
<tr>
<td><a href="' . $confirm_link . '" class="a1">تأكيد الحضور</a></td>
<td><a href="' . $apologize_link . '" class="a2">الاعتذار عن الحضور</a></td>
</tr>
</table>
</body></html>';

            // تحديد موضع الأزرار من أعلى الصفحة (297mm - 35mm من الأسفل = 262mm)
            $mpdf->SetY(262);
            $mpdf->WriteHTML($buttonsHtml);

            $filename = 'invitation_' . uniqid() . '_' . $row->id . '.pdf';

            $directory = public_path('temp_pdfs');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $pdf_path = $directory . '/' . $filename;
            $mpdf->Output($pdf_path, 'F');
        } catch (\Exception $e) {
            Log::error("PDF Generation Error in Job: " . $e->getMessage());
            return;
        }
        
        $pdf_url = asset('temp_pdfs/' . $filename);

        $client = new \UltraMsg\WhatsAppApi($this->ultramsg_token, $this->instance_id);
        
        $priority = 0;
        $referenceId = "SDK";
        $nocache = true;
        
        $api = $client->sendDocumentMessage($to, 'invitation.pdf', $pdf_url, $caption, $priority, $referenceId, $nocache);
        
        // Wait a few seconds to ensure Ultramsg server downloaded the file
        sleep(5);

        // Delete the PDF after sending to save server space
        if (file_exists($pdf_path)) {
            @unlink($pdf_path);
        }
        // Delete compressed temp image if created
        if ($compressedImagePath && file_exists($compressedImagePath)) {
            @unlink($compressedImagePath);
        }
        
        if (!empty($api) && isset($api['sent']) && $api['sent'] == 'true' && isset($api['message']) && $api['message'] == 'ok') {
            // Success
            // $row->update(['is_new_sent' => 1]);
        } else {
            Log::error("Failed to send PDF to {$to} via Ultramsg.", ['response' => $api]);
            // $row->update(['is_new_sent' => 0]);
        }
    }
}
