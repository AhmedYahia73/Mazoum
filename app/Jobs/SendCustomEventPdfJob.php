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
        
        // جلب الرابط الكامل للصورة (خلفية الدعوة) بناءً على طلبك من الـ Model
        $image = $event->pdf;

        try {
            // Generate PDF
            $pdf = PDF::loadView('PDF.customPDF', compact('confirm_link', 'apologize_link', 'image'));
            
            $filename = 'invitation_' . uniqid() . '_' . $row->id . '.pdf';
            
            // Ensure directory exists
            $directory = public_path('temp_pdfs');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $pdf_path = $directory . '/' . $filename;
            $pdf->save($pdf_path);
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
        
        if (!empty($api) && isset($api['sent']) && $api['sent'] == 'true' && isset($api['message']) && $api['message'] == 'ok') {
            // Success
            // $row->update(['is_new_sent' => 1]);
        } else {
            Log::error("Failed to send PDF to {$to} via Ultramsg.", ['response' => $api]);
            // $row->update(['is_new_sent' => 0]);
        }
    }
}
