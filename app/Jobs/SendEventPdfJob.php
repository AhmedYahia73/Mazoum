<?php

namespace App\Jobs;

use App\Models\Events;
use App\Models\EventUsers;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class SendEventPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $eventId;
    public $ultramsg_token;
    public $instance_id;
    public $pdf_bottom;
    public $caption;
    
    public function __construct($userId, $eventId, $ultramsg_token, $instance_id, $pdf_bottom, $caption = null)
    {
        $this->userId = $userId;
        $this->eventId = $eventId;
        $this->ultramsg_token = $ultramsg_token;
        $this->instance_id = $instance_id;
        $this->pdf_bottom = $pdf_bottom;
        $this->caption = $caption;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('PDF Job Started - User ID: ' . $this->userId . ', Event ID: ' . $this->eventId);
        $row = EventUsers::find($this->userId);
        $event = Events::find($this->eventId);

        if (!$row) {
            Log::error('PDF Job Error - EventUsers row not found for ID: ' . $this->userId);
            return;
        }
        if (!$event) {
            Log::error('PDF Job Error - Events row not found for ID: ' . $this->eventId);
            return;
        }
        if (!$row->mobile) {
            Log::warning('PDF Job Warning - EventUsers mobile is empty for ID: ' . $this->userId);
            return;
        }

        $to = $row->mobile;
        $day_name = Carbon::parse($event->date)->locale('ar')->translatedFormat('l');

        if($event->show_data_pdf){
            $caption = empty($this->caption) ? $row->name . PHP_EOL . PHP_EOL .
                $event->title . PHP_EOL . PHP_EOL .
                "وذلك بمشيئة الله تعالى يوم " . $day_name ." الموافق  📆 "  . $event->date
                . PHP_EOL . PHP_EOL .
                "وقت الاستقبال ⏱️الساعـة " . $event->time . " مساءاً" . PHP_EOL . PHP_EOL .
                "📍مكان الحفـل " . $event->address  . PHP_EOL . PHP_EOL .
                "عدد الدعوات " . $row->users_count . PHP_EOL . PHP_EOL .
                "تم إرسـال هذه الرسالة من خـــلال تطبيق معزوم للدعوات الإلكترونية" :
                $this->caption;
        } else { 
            $caption = empty($this->caption) ? $row->name . PHP_EOL . PHP_EOL .
                "عدد الدعوات " . $row->users_count . PHP_EOL . PHP_EOL .
                "تم إرسـال هذه الرسالة من خـــلال تطبيق معزوم للدعوات الإلكترونية" :
                $this->caption;
        }

        $confirm_link = "https://mazoominvitations.com/event-login/" . $row->code;
        $apologize_link = "https://mazoominvitations.com/event-login/" . $row->code;
        
        // استخدام المسار الداخلي المطلق للسيرفر للخلفية
        $pdfFile = $event->getRawOriginal('file');
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
                    $targetW = 794; 
                    $targetH = 1123; 
                    $resized = imagecreatetruecolor($targetW, $targetH);
                    
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
                    $quality = 80; 
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

        // تحويل الخلفية لـ Base64 لضمان ظهورها
        $base64Image = '';
        try {
            if (file_exists($imagePathForRender)) {
                $imageData = file_get_contents($imagePathForRender);
                $mimeType = mime_content_type($imagePathForRender);
                $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                Log::info('PDF Job - Image converted to Base64 successfully.');
            }
        } catch (\Exception $e) {
            Log::error('PDF Job - Failed to convert image to Base64: ' . $e->getMessage());
        }

        // تجهيز مسار صورة الأزرار الجديدة وتأمينها
        $buttonsLocalPath = public_path('11.png');
        if (file_exists($buttonsLocalPath)) {
            $buttonsImage = 'data:image/png;base64,' . base64_encode(file_get_contents($buttonsLocalPath));
        } else {
            $buttonsImage = 'https://mazoom.online/11.png';
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
                'mode'          => 'utf-8',
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
            ];
            Log::info('PDF Job - Initializing mPDF');

            $mpdf = new \Mpdf\Mpdf($config);
            $mpdf->showImageErrors = true;
            $mpdf->SetDirectionality('rtl'); 

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
                margin: 0; 
                padding: 0; 
            }
            </style>
            
            <div style="position: absolute; bottom: ' . $this->pdf_bottom . 'px; width: 100%; text-align: center;">
                <a href="' . $confirm_link . '" style="text-decoration: none; display: inline-block;">
                    <img src="' . $buttonsImage . '" style="width: 130mm; height: auto; border: none; padding: 0; margin: 0;" />
                </a>
            </div>';

            Log::info('PDF Job - Writing HTML content');
            $mpdf->WriteHTML($html);

            $filename = $event->title . '.pdf';
            $directory = public_path('temp_pdfs');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $pdf_path = $directory . '/' . $filename;
            Log::info('PDF Job - Outputting PDF file');
            $mpdf->Output($pdf_path, 'F');
            
        } catch (\Exception $e) {
            Log::error("PDF Job - Exception in PDF Generation: " . $e->getMessage());
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
        $api = $client->sendDocumentMessage($to, $event->title . '.pdf', $pdf_url, $caption, $priority, $referenceId, $nocache);
        Log::info('PDF Job - WhatsApp API response: ' . json_encode($api));
        
        if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {

        // dd('ok');
        $row->update([
            'is_new_sent' => 1, 
            'status' => "sent", 
            'is_delivered' => "yes", 
        ]);
        $user = $event->user;

        $update_data = [
            'balance' => $user->balance - $row->users_count,
        ];
        $was_sent = $row != null && ($row->is_sent == 'yes' || $row->is_new_sent == 1);
        if (!$was_sent) {
            $update_data['send_custom_invetaion'] = $user->send_custom_invetaion + $row->users_count;
        }
        $user->update($update_data);

        } else {
        // dd('not ok',$api);
        $row->update(['is_new_sent' => 0]);
        }
        Log::info('PDF Job - Sleeping 5 seconds before clean up...');
        sleep(5);

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
