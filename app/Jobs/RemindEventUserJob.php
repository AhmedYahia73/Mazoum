<?php

namespace App\Jobs;

use App\Models\Events;
use App\Models\EventUsers;
use App\Models\NewSetting;
use App\Models\Setting;
use App\Models\WattsChat as WattsChatModel;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemindEventUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $eventUserId;
    public $eventId;
    public $sendingType;
    public $message;
    public $date;
    public $time;
    public $phoneSettingId;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        $eventUserId,
        $eventId,
        $sendingType,
        $message,
        $date,
        $time,
        $phoneSettingId
    ) {
        $this->eventUserId = $eventUserId;
        $this->eventId = $eventId;
        $this->sendingType = $sendingType;
        $this->message = $message;
        $this->date = $date;
        $this->time = $time;
        $this->phoneSettingId = $phoneSettingId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("RemindEventUserJob: بدء إرسال تذكير - EventUser ID: {$this->eventUserId}, Event ID: {$this->eventId}");

        $eventUser = EventUsers::withTrashed()->find($this->eventUserId);
        $event = Events::withoutGlobalScopes()->find($this->eventId);

        if (!$eventUser) {
            Log::error("RemindEventUserJob: لم يتم العثور على EventUser ID: {$this->eventUserId}");
            return;
        }

        if (!$event) {
            Log::error("RemindEventUserJob: لم يتم العثور على Event ID: {$this->eventId}");
            return;
        }

        $mobile = $eventUser->mobile;
        if (empty($mobile)) {
            Log::warning("RemindEventUserJob: رقم الموبايل فارغ - EventUser ID: {$this->eventUserId}");
            return;
        }

        $to = str_replace("+", "", $mobile);

        $url_image = $event->file;

        $template_name = 'car_msg3_';

        if ($this->sendingType === 'old_send') {

            // إرسال عبر Meta (Cloud API)
            $language = 'ar';

            $token = get_whats_setting($event)['token'];
            $phone_numer_id = $this->getPhoneId($this->phoneSettingId);

            $param_1 = $this->message;
            $param_2 = $this->time;
            $param_3 = $this->date;

            try {
                $response = SendCarMsgTemplate(
                    $to,
                    $template_name,
                    $language,
                    $url_image,
                    $param_1,
                    $param_2,
                    $param_3,
                    $phone_numer_id,
                    $token
                );

                if ($response != null && $response->getStatusCode() == 200) {

                    $eventUser->update([
                        'remember' => 1,
                    ]);

                    $body = $response->getBody();
                    $data = json_decode($body, true);

                    WattsChatModel::create([
                        'phone'          => $to,
                        'name'           => "Admin",
                        'message'        => $template_name,
                        'is_sent_by_me'  => true,
                        'message_id'     => 0,
                        'from'           => "Admin",
                        "template_name"  => $template_name,
                        "event_user_id"  => $eventUser->id,
                        "event_id"       => $event->id,
                        "phone_numer_id" => $phone_numer_id,
                    ]);

                    Log::info("RemindEventUserJob: تم الإرسال بنجاح (old_send) - EventUser ID: {$this->eventUserId}");

                } else {
                    $eventUser->update([
                        'status' => 'failed-v2',
                    ]);
                    Log::error("RemindEventUserJob: فشل الإرسال (old_send) - EventUser ID: {$this->eventUserId}");
                }

            } catch (\Exception $e) {
                Log::error("RemindEventUserJob: خطأ في الإرسال (old_send) - EventUser ID: {$this->eventUserId} - Error: {$e->getMessage()}");
                $eventUser->update([
                    'status' => 'failed-v2',
                ]);
            }

        } else {

            // إرسال عبر UltraMsg (new_send / link)
            $ultramsg_token = "7ye6ifujyug0u46g";
            $instance_id = "instance109805";
            $client = new \UltraMsg\WhatsAppApi($ultramsg_token, $instance_id);

            $priority = 0;
            $referenceId = "SDK";
            $nocache = true;

            $caption = "ضيفتنـا الغاليـة , ننتظـرك يوم " . $this->date . " في تمــام الساعة " . $this->time . "  تشرفينــا لحضور " . $this->message . ' 🌺🌺 ';

            try {
                $api = $client->sendImageMessage($to, $url_image, $caption, $priority, $referenceId, $nocache);
                $api2 = $client->sendLocationMessage($to, $event->address, $event->lat, $event->long, $priority = 0, $referenceId = "SDK");

                if (!empty($api) && isset($api['sent']) && $api['sent'] == 'true' && isset($api['message']) && $api['message'] == 'ok') {
                    $eventUser->update([
                        'remember' => 1,
                    ]);
                    Log::info("RemindEventUserJob: تم الإرسال بنجاح (new_send) - EventUser ID: {$this->eventUserId}");
                } else {
                    Log::error("RemindEventUserJob: فشل الإرسال (new_send) - EventUser ID: {$this->eventUserId} - Response: " . json_encode($api));
                }

            } catch (\Exception $e) {
                Log::error("RemindEventUserJob: خطأ في الإرسال (new_send) - EventUser ID: {$this->eventUserId} - Error: {$e->getMessage()}");
            }
        }
    }

    /**
     * Get phone_numer_id from NewSetting
     */
    private function getPhoneId($id)
    {
        $data = NewSetting::where("id", $id)->first();

        if (empty($data)) {
            return Setting::first()?->phone_numer_id ?? null;
        }

        return $data->phone_numer_id;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error("RemindEventUserJob: فشل نهائي - EventUser ID: {$this->eventUserId} - Error: {$exception->getMessage()}");
    }
}
