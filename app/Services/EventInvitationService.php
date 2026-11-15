<?php

namespace App\Services;

use App\Models\Events;
use App\Models\EventUsers;
use App\Models\NewSetting;
use App\Models\Qr_Code;
use App\Models\Setting;
use App\Models\WattsChat as WattsChatModel;
use Carbon\Carbon;
use DateTime;
use IntlDateFormatter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class EventInvitationService
{
    /**
     * Generate a unique numeric code for QR code.
     */
    public static function generateUniqueUuId(): int
    {
        do {
            $uu_id = random_int(10000, 99999);
        } while (Qr_Code::where('uu_id', $uu_id)->exists());

        return $uu_id;
    }

    /**
     * Get sender phone number id by phone_setting_id with fallbacks.
     */
    public static function getPhoneId(?int $phoneSettingId, ?string $fallback = null): ?string
    {
        if ($phoneSettingId) {
            $setting = NewSetting::find($phoneSettingId);
            if ($setting && !empty($setting->phone_numer_id)) {
                return $setting->phone_numer_id;
            }
        }

        if (!empty($fallback)) {
            return $fallback;
        }

        $default = Setting::first();
        return $default ? $default->phone_numer_id : null;
    }

    /**
     * Get sender phone number string by phone_setting_id.
     */
    public static function getPhoneNumber(?int $phoneSettingId): ?string
    {
        if ($phoneSettingId) {
            $setting = NewSetting::find($phoneSettingId);
            if ($setting && !empty($setting->phone_number)) {
                return $setting->phone_number;
            }
        }

        return null;
    }

    /**
     * Send an invitation to a single EventUser record.
     *
     * @param  EventUsers  $userEvent
     * @param  Events|null $event
     * @param  int|null    $phoneSettingId
     * @param  string|null $type
     * @param  string      $sentFrom
     * @return array
     */
    public static function sendInvitationToUser(
        EventUsers $userEvent,
        ?Events $event = null,
        ?int $phoneSettingId = null,
        ?string $type = null,
        string $sentFrom = 'schedule'
    ): array {
        if (!$event) {
            $event = Events::withoutGlobalScopes()->find($userEvent->event_id);
        }

        if (!$event) {
            return [
                'status'  => false,
                'message' => 'Event not found for user: ' . $userEvent->id,
            ];
        }

        $mobile = $userEvent->mobile;
        if (empty($mobile)) {
            $userEvent->update(['status' => 'failed']);
            return [
                'status'  => false,
                'message' => 'Empty mobile number for user: ' . $userEvent->id,
            ];
        }

        $to = str_replace(['+', ' '], '', trim($mobile));

        $was_sent = ($userEvent->is_sent === 'yes');
        $users_count = (int) ($userEvent->users_count ?: 1);

        // Reset user state to hold while sending
        $userEvent->update([
            'status'        => 'hold',
            'users_count'   => $users_count,
            'scan'          => null,
            'scan_at'       => null,
            'get_location'  => null,
            'message_id'    => null,
            'is_sent'       => null,
            'sent_from'     => null,
            'is_delivered'  => null,
            'is_read'       => null,
            'qr_sent'       => null,
            'is_accepted'   => null,
            'is_refused'    => null,
            'error_title'   => null,
            'error'         => null,
            'log'           => null,
        ]);

        // Get or create QR record
        $qrRecord = Qr_Code::where('event_user_id', $userEvent->id)->latest()->first();
        if ($qrRecord) {
            $uuId = $qrRecord->uu_id;
            $imageName = $qrRecord->qr;
        } else {
            $uuId = self::generateUniqueUuId();
            $imageName = $uuId . '-test-qr.png';
            Qr_Code::create([
                'event_user_id' => $userEvent->id,
                'event_id'      => $userEvent->event_id,
                'qr'            => $imageName,
                'uu_id'         => $uuId,
                'counter'       => 0,
            ]);
        }

        // Determine template and media according to send_type
        $sendType = $type ?: ($event->send_type ?: 'image');
        $language = 'ar';

        if ($sendType === 'video') {
            $template_name = 'wedding__video';
            $image_path    = $event->video;
            $header_type   = 'video';
        } elseif ($sendType === 'pdf') {
            $template_name = 'wedding__pdf';
            $image_path    = $event->pdf;
            $header_type   = 'document';
        } else {
            $template_name = 'wedding_data_v25';
            $image_path    = $event->file;
            $header_type   = 'image';
        }

        $whatsSetting = get_whats_setting($event);
        $token = $whatsSetting['token'] ?? null;

        $targetPhoneSettingId = $phoneSettingId ?: $event->phone_setting_id;
        $phone_numer_id = self::getPhoneId($targetPhoneSettingId, $whatsSetting['phone_numer_id'] ?? null);
        $phone_number   = self::getPhoneNumber($targetPhoneSettingId);

        if (!empty($phone_number)) {
            $userEvent->update(['phone_number' => $phone_number]);
        }

        // Parameters
        $param_1 = $userEvent->name;
        $param_2 = $event->title;
        $param_3 = Carbon::parse($event->date)->locale('ar')->translatedFormat('l') . ' ' . Carbon::parse($event->date)->locale('ar')->translatedFormat('j F Y');

        $dateObj = $event->date instanceof DateTime ? $event->date : new DateTime($event->date);
        $formatter = new IntlDateFormatter(
            'ar_SA@calendar=islamic-umalqura',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            'Asia/Riyadh',
            IntlDateFormatter::TRADITIONAL,
            'd MMMM yyyy'
        );
        $param_4 = $formatter->format($dateObj) . 'هـ';
        $param_5 = $event->address;
        $param_6 = !empty($event->time)
            ? date('h:i', strtotime($event->time)) . ' ' . (date('a', strtotime($event->time)) === 'am' ? 'صباحاً' : 'مساءً')
            : '07:00 مساءً';
        $param_7 = $users_count > 10 ? 10 : $users_count;

        // Send via WhatsApp Helper
        try {
            if ($header_type === 'image') {
                $optimizedImage = \App\Services\WhatsApp\WhatsAppMediaOptimizerService::getOptimizedEventHeaderImage($event);
                if (!empty($optimizedImage)) {
                    $image_path = $optimizedImage;
                } elseif (!empty($image_path)) {
                    $image_path = \App\Services\WhatsApp\WhatsAppMediaOptimizerService::optimizeHeaderImageUrl($image_path);
                }

                $response = SendWeddingDataV1ArImageTemplate(
                    $to,
                    $template_name,
                    $language,
                    $param_1,
                    $param_2,
                    $param_3,
                    $param_4,
                    $param_5,
                    $param_6,
                    $param_7,
                    $image_path,
                    $phone_numer_id,
                    $token,
                    $header_type
                );
            } else {
                $response = SendWeddingDataV1ArTemplate(
                    $to,
                    $template_name,
                    $language,
                    $param_1,
                    $param_2,
                    $param_3,
                    $param_4,
                    $param_5,
                    $param_6,
                    $param_7,
                    $image_path,
                    $phone_numer_id,
                    $token,
                    $header_type,
                    true
                );
            }
        } catch (\Throwable $e) {
            Log::error("EventInvitationService Exception: {$e->getMessage()}", [
                'event_user_id' => $userEvent->id,
                'mobile'        => $to,
            ]);
            $userEvent->update(['status' => 'failed', 'error' => $e->getMessage()]);
            return [
                'status'  => false,
                'message' => 'حدث استثناء أثناء الإرسال',
                'error'   => $e->getMessage(),
            ];
        }

        if ($response != null && $response->getStatusCode() == 200) {
            $messageId = 0;
            try {
                $responseBody = (string) $response->getBody();
                $responseData = json_decode($responseBody, true);
                if (isset($responseData['messages'][0]['id'])) {
                    $messageId = $responseData['messages'][0]['id'];
                }
            } catch (\Throwable $e) {
                Log::warning("EventInvitationService: Failed parsing Meta response message_id: {$e->getMessage()}");
            }

            WattsChatModel::create([
                'phone'          => $to,
                'name'           => 'Admin',
                'message'        => $template_name,
                'is_sent_by_me'  => true,
                'message_id'     => $messageId,
                'from'           => 'Admin',
                'template_name'  => $template_name,
                'event_user_id'  => $userEvent->id,
                'event_id'       => $event->id,
                'phone_numer_id' => $phone_numer_id,
            ]);

            // Deduct balance and update user stats
            $user = $event->user;
            if ($user) {
                $updateData = [];
                if ($userEvent->status === 'hold' || $userEvent->status === 'failed') {
                    $updateData['balance'] = $user->balance - $users_count;
                }
                if (!$was_sent) {
                    $updateData['send_custom_invetaion'] = ($user->send_custom_invetaion ?? 0) + $users_count;
                }
                if (!empty($updateData)) {
                    $user->update($updateData);
                }
            }

            $userEvent->update([
                'is_sent'      => 'yes',
                'sent_from'    => $sentFrom,
                'status'       => 'sent',
                'message_id'   => $messageId,
                'send_type'    => 'meta',
                'phone_number' => $phone_number,
            ]);

            return [
                'status'     => true,
                'message'    => 'تم الإرسال بنجاح',
                'message_id' => $messageId,
            ];
        } else {
            $userEvent->update([
                'status' => 'failed',
            ]);

            $rawError = $response ? (string) $response->getBody() : 'No response from WhatsApp gateway';
            Log::warning("EventInvitationService Failed: User ID {$userEvent->id}, Response: {$rawError}");

            return [
                'status'  => false,
                'message' => 'فشل الإرسال عبر البوابة',
                'error'   => $rawError,
            ];
        }
    }

    /**
     * Send invitations to all pending hold users for a given event.
     *
     * @param  Events   $event
     * @param  int      $delaySeconds
     * @return array
     */
    public static function sendPendingInvitationsForEvent(Events $event, int $delaySeconds = 1): array
    {
        $eventUsers = EventUsers::where('event_id', $event->id)
            ->where('status', 'hold')
            ->where('is_new_sent', 0)
            ->whereNull('is_sent')
            ->get();

        $total  = $eventUsers->count();
        $sent   = 0;
        $failed = 0;

        Log::info("EventInvitationService: Starting batch for Event ID: {$event->id} ({$event->title}), pending: {$total}");

        foreach ($eventUsers as $userEvent) {
            $result = self::sendInvitationToUser(
                $userEvent,
                $event,
                $event->phone_setting_id,
                $event->send_type,
                'schedule'
            );

            if ($result['status']) {
                $sent++;
            } else {
                $failed++;
            }

            if ($delaySeconds > 0) {
                sleep($delaySeconds);
            }
        }

        Log::info("EventInvitationService: Finished Event ID: {$event->id}. Sent: {$sent}, Failed: {$failed}");

        return [
            'total'  => $total,
            'sent'   => $sent,
            'failed' => $failed,
        ];
    }

    /**
     * Send all scheduled invitations whose send_date has arrived.
     *
     * @param  int  $delaySeconds
     * @return array
     */
    public static function sendAllScheduledInvitations(int $delaySeconds = 1): array
    {
        $appUrl = config('app.url');
        if (empty($appUrl) || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            URL::forceRootUrl('https://mazoom.online');
            URL::forceScheme('https');
        }

        $now = Carbon::now();

        $events = Events::withoutGlobalScopes()
            ->whereNotNull('send_date')
            ->where('send_date', '<=', $now)
            ->whereHas('event_users', function ($q) {
                $q->where('status', 'hold')
                  ->where('is_new_sent', 0)
                  ->whereNull('is_sent');
            })
            ->get();

        Log::info("EventInvitationService: Checked scheduled events at {$now->toDateTimeString()}. Found: {$events->count()} events needing send.");

        $results = [];
        foreach ($events as $event) {
            $results[$event->id] = self::sendPendingInvitationsForEvent($event, $delaySeconds);
        }

        return $results;
    }
}
