<?php

namespace App\Services\WhatsApp;

use App\Models\Events;
use App\Models\EventUsers;
use App\Models\NewSetting;
use App\Models\Qr_Code;
use App\Models\Setting;
use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use IntlDateFormatter;

class WhatsAppLinkInvitationService
{
    /**
     * أسماء القوالب المعتمدة في Meta حسب نوع الوسائط
     */
    public const TEMPLATE_IMAGE = 'invitation_link';
    public const TEMPLATE_VIDEO = 'video_link_invitation';
    public const TEMPLATE_PDF   = 'link_invitation_pdf';

    /**
     * الرابط الأساسي لصفحة تسجيل دخول المدعو
     */
    public const BASE_LOGIN_URL = 'https://www.mazoominvitations.com/event-login/';

    /**
     * تحديد اسم القالب ونوع الهيدر بناءً على نوع الوسائط
     *
     * @param string $type image|video|pdf|document
     * @return array ['template_name' => string, 'header_type' => string]
     */
    public static function resolveTemplateAndHeader(string $type = 'image'): array
    {
        $normalized = strtolower(trim($type));

        return match ($normalized) {
            'video' => [
                'template_name' => self::TEMPLATE_VIDEO,
                'header_type'   => 'video',
            ],
            'pdf', 'document' => [
                'template_name' => self::TEMPLATE_PDF,
                'header_type'   => 'document',
            ],
            default => [
                'template_name' => self::TEMPLATE_IMAGE,
                'header_type'   => 'image',
            ],
        };
    }

    /**
     * تنظيف واستخراج كود المدعو لاستخدامه في أزرار الرابط الديناميكي
     *
     * @param string|null $code
     * @return string
     */
    public static function sanitizeCode(?string $code): string
    {
        if (empty($code)) {
            return '';
        }

        $code = trim($code);

        // إذا تم تمرير الرابط كاملاً، نستخرج الكود فقط
        if (str_contains($code, 'event-login/')) {
            $parts = explode('event-login/', $code);
            $after = end($parts);
            $token = strtok($after, "/?#");
            return trim($token ?: $after, '/');
        }

        return trim($code, '/');
    }

    /**
     * تنظيف متغيرات النص لتفادي أخطاء Meta Cloud API
     *
     * @param mixed $val
     * @param string $default
     * @return string
     */
    public static function cleanParam($val, string $default = '-'): string
    {
        if ($val === null || $val === '') {
            return $default;
        }

        $str = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string) $val;
        // إزالة الفواصل السطرية وعلامات الجدولة لأن ميتا ترفضها في متغيرات المتن
        $str = preg_replace('/[\r\n\t]+/', ' ', $str);
        // دمج المسافات المتعددة
        $str = trim(preg_replace('/\s{2,}/u', ' ', $str));

        return $str !== '' ? $str : $default;
    }

    /**
     * بناء كائن المكونات (components) الخاص بالقالب لميتا
     *
     * @param string $headerType image|video|document
     * @param string $mediaUrl
     * @param array $bodyParams 7 parameters
     * @param string $code
     * @return array
     */
    public static function buildComponents(
        string $headerType,
        string $mediaUrl,
        array $bodyParams,
        string $code
    ): array {
        // 1. مكون الرأس (Header)
        $headerParam = [
            'type' => $headerType,
            $headerType => [
                'link' => $mediaUrl,
            ],
        ];

        if ($headerType === 'document') {
            $parsedPath = parse_url($mediaUrl, PHP_URL_PATH);
            $docName = $parsedPath ? basename($parsedPath) : 'invitation.pdf';
            $headerParam[$headerType]['filename'] = $docName;
        }

        $components = [
            [
                'type' => 'header',
                'parameters' => [$headerParam],
            ],
        ];

        // 2. مكون المتن (Body) - 7 متغيرات
        $bodyParameters = [];
        for ($i = 0; $i < 7; $i++) {
            $bodyParameters[] = [
                'type' => 'text',
                'text' => self::cleanParam($bodyParams[$i] ?? null),
            ];
        }

        $components[] = [
            'type' => 'body',
            'parameters' => $bodyParameters,
        ];

        // 3. مكونات الأزرار (Buttons)
        // زر 1 (Index 0): زيارة موقع الويب (ديناميكي) - قبول الدعوة
        $components[] = [
            'type' => 'button',
            'sub_type' => 'url',
            'index' => '0',
            'parameters' => [
                [
                    'type' => 'text',
                    'text' => $code,
                ],
            ],
        ];

        // زر 2 (Index 1): زيارة موقع الويب (ديناميكي) - الاعتذار عن الدعوة
        $components[] = [
            'type' => 'button',
            'sub_type' => 'url',
            'index' => '1',
            'parameters' => [
                [
                    'type' => 'text',
                    'text' => $code,
                ],
            ],
        ];

        // زر 3 (Index 2): رد سريع (Quick Reply) - تفاصيل المناسبة
        $components[] = [
            'type' => 'button',
            'sub_type' => 'quick_reply',
            'index' => '2',
            'parameters' => [
                [
                    'type' => 'payload',
                    'payload' => 'event_details',
                ],
            ],
        ];

        return $components;
    }

    /**
     * إرسال قالب الدعوة ذو الرابط المخصص إلى رقم هاتف
     *
     * @param string $to رقم الهاتف مع رمز الدولة
     * @param string $type image|video|pdf|document
     * @param string $mediaUrl رابط الوسائط (صورة/فيديو/ملف)
     * @param array $bodyParams 7 متغيرات نصية للمتن
     * @param string $code كود المدعو
     * @param string|null $phoneNumberId
     * @param string|null $token
     * @param string $language
     * @param int|null $phoneSettingId
     * @param Events|null $event
     * @return array
     */
    public static function sendMessage(
        string $to,
        string $type,
        string $mediaUrl,
        array $bodyParams,
        string $code,
        ?string $phoneNumberId = null,
        ?string $token = null,
        string $language = 'ar',
        ?int $phoneSettingId = null,
        ?Events $event = null
    ): array {
        $resolved = self::resolveTemplateAndHeader($type);
        $templateName = $resolved['template_name'];
        $headerType   = $resolved['header_type'];

        // تحسين صورة الرأس إن وُجدت الخدمة
        if ($headerType === 'image' && !empty($mediaUrl) && class_exists(WhatsAppMediaOptimizerService::class)) {
            $mediaUrl = WhatsAppMediaOptimizerService::optimizeHeaderImageUrl($mediaUrl);
        }

        $cleanTo = preg_replace('/[^0-9]/', '', $to);
        $cleanCode = self::sanitizeCode($code);

        // حل بيانات الاعتماد مع إعطاء الأولوية لـ phone_setting_id
        $credentials = self::resolveCredentials($event, $phoneSettingId, $phoneNumberId, $token);
        $resolvedPhoneId = $credentials['phone_number_id'];
        $resolvedToken   = $credentials['token'];
        $usedSettingId   = $credentials['phone_setting_id'];

        if (empty($resolvedPhoneId) || empty($resolvedToken)) {
            return [
                'success'               => false,
                'message'               => 'بيانات اعتماد واتساب (phone_number_id أو token) غير متوفرة',
                'template'              => $templateName,
                'phone_setting_id_used' => $usedSettingId,
            ];
        }

        $components = self::buildComponents($headerType, $mediaUrl, $bodyParams, $cleanCode);

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $cleanTo,
            'type'              => 'template',
            'template'          => [
                'name'       => $templateName,
                'language'   => [
                    'code' => $language,
                ],
                'components' => $components,
            ],
        ];

        $fullUrl = 'https://graph.facebook.com/v18.0/' . $resolvedPhoneId . '/messages';
        $client = new Client();

        try {
            $response = $client->post($fullUrl, [
                'http_errors' => false,
                'headers' => [
                    'Authorization' => 'Bearer ' . $resolvedToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode((string) $response->getBody(), true);

            if ($statusCode === 200 || $statusCode === 201) {
                $messageId = $body['messages'][0]['id'] ?? null;

                if (function_exists('log_sent_watts_message')) {
                    log_sent_watts_message(
                        $cleanTo,
                        $templateName,
                        $messageId,
                        $bodyParams[0] ?? null,
                        $resolvedPhoneId
                    );
                }

                return [
                    'success'          => true,
                    'status_code'      => $statusCode,
                    'message_id'       => $messageId,
                    'template'         => $templateName,
                    'header_type'      => $headerType,
                    'code'             => $cleanCode,
                    'login_url'        => self::BASE_LOGIN_URL . $cleanCode,
                    'phone_setting_id' => $usedSettingId,
                    'phone_number_id'  => $resolvedPhoneId,
                    'data'             => $body,
                ];
            }

            Log::error("WhatsAppLinkInvitationService Error ({$statusCode}): " . json_encode($body, JSON_UNESCAPED_UNICODE));

            return [
                'success'               => false,
                'status_code'           => $statusCode,
                'message'               => $body['error']['message'] ?? 'فشل إرسال القالب من خلال ميتا',
                'error'                 => $body['error'] ?? $body,
                'template'              => $templateName,
                'phone_setting_id_used' => $usedSettingId,
                'phone_numer_id_used'   => $resolvedPhoneId,
            ];
        } catch (\Throwable $e) {
            Log::error("WhatsAppLinkInvitationService Exception: " . $e->getMessage());

            return [
                'success'               => false,
                'message'               => $e->getMessage(),
                'template'              => $templateName,
                'phone_setting_id_used' => $usedSettingId,
                'phone_numer_id_used'   => $resolvedPhoneId,
            ];
        }
    }

    /**
     * إرسال دعوة الرابط مباشرة لسجل مدعو (EventUsers) مع تجهيز البيانات تلقائياً من قاعدة البيانات
     *
     * @param EventUsers $eventUser
     * @param Events|null $event
     * @param string|null $type image|video|pdf
     * @param int|null $phoneSettingId
     * @return array
     */
    public static function sendForEventUser(
        EventUsers $eventUser,
        ?Events $event = null,
        ?string $type = null,
        ?int $phoneSettingId = null
    ): array {
        if (!$event) {
            $event = Events::withoutGlobalScopes()->find($eventUser->event_id);
        }

        if (!$event) {
            return [
                'success' => false,
                'message' => 'المناسبة غير موجودة لهذا المدعو',
            ];
        }

        $mobile = $eventUser->mobile;
        if (empty($mobile)) {
            $eventUser->update(['status' => 'failed', 'error' => 'رقم الهاتف فارغ']);
            return [
                'success' => false,
                'message' => 'رقم الهاتف الخاص بالمدعو فارغ',
            ];
        }

        // التأكد من وجود كود للمستخدم
        if (empty($eventUser->code)) {
            $eventUser->code = EventUsers::generateUniqueCode();
            $eventUser->save();
        }

        // تحديد نوع الإرسال
        $sendType = $type ?: ($event->send_type ?: 'image');
        $resolved = self::resolveTemplateAndHeader($sendType);
        $headerType = $resolved['header_type'];

        // تحديد ملف الهيدر
        $mediaUrl = match ($headerType) {
            'video'    => $event->video,
            'document' => $event->pdf,
            default    => $event->file,
        };

        if ($headerType === 'image') {
            if (class_exists(WhatsAppMediaOptimizerService::class)) {
                $optimized = WhatsAppMediaOptimizerService::getOptimizedEventHeaderImage($event);
                if (!empty($optimized)) {
                    $mediaUrl = $optimized;
                }
            }
        }

        if (empty($mediaUrl)) {
            $mediaUrl = asset('11.png');
        }

        // تجهيز المتغيرات السبعة
        // 1. اسم المدعو
        $param1 = $eventUser->name ?: 'ضيفنا الكريم';

        // 2. نص الدعوة وعنوان المناسبة
        $param2 = $event->title ?: 'حفل زفاف مبارك';

        // 3. اليوم والتاريخ الميلادي
        $param3 = self::formatGregorianDate($event->date);

        // 4. التاريخ الهجري
        $param4 = self::formatHijriDate($event->date);

        // 5. المكان والعنوان
        $param5 = $event->address ?: 'قاعة الحفل';

        // 6. وقت المناسبة
        $param6 = self::formatTime($event->time);

        // 7. عدد الدعوات المصرح بها
        $param7 = (string) ($eventUser->users_count ?: 1);

        $bodyParams = [$param1, $param2, $param3, $param4, $param5, $param6, $param7];

        $targetPhoneSettingId = $phoneSettingId ?: ($event->phone_setting_id ?? null);

        // تحديث رقم الهاتف في سجل المدعو إن وُجد في NewSetting
        if (!empty($targetPhoneSettingId)) {
            $phoneSetting = NewSetting::find($targetPhoneSettingId);
            if ($phoneSetting && !empty($phoneSetting->phone_number)) {
                $eventUser->update(['phone_number' => $phoneSetting->phone_number]);
            }
        }

        $result = self::sendMessage(
            $mobile,
            $sendType,
            $mediaUrl,
            $bodyParams,
            $eventUser->code,
            null,
            null,
            'ar',
            $targetPhoneSettingId,
            $event
        );

        if ($result['success']) {
            $eventUser->update([
                'status'     => 'hold',
                'is_sent'    => 'yes',
                'sent_from'  => 'api_link_template',
                'message_id' => $result['message_id'] ?? null,
                'send_type'  => $sendType,
            ]);
        } else {
            $eventUser->update([
                'status' => 'failed',
                'error'  => $result['message'] ?? 'فشل إرسال الدعوة',
            ]);
        }

        return $result;
    }

    /**
     * تنسيق التاريخ الميلادي واسم اليوم باللغة العربية
     * مثال: السبت الموافق 17/02/2025
     */
    public static function formatGregorianDate($date): string
    {
        if (empty($date)) {
            return '-';
        }

        try {
            $carbon = Carbon::parse($date)->locale('ar');
            $dayName = $carbon->translatedFormat('l');
            $formatted = $carbon->format('d/m/Y');
            return "{$dayName} الموافق {$formatted}";
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }

    /**
     * تنسيق التاريخ الهجري
     * مثال: 18 جمادى الآخرة 1448 هـ
     */
    public static function formatHijriDate($date): string
    {
        if (empty($date)) {
            return '-';
        }

        try {
            $dateObj = $date instanceof DateTime ? $date : new DateTime($date);
            $formatter = new IntlDateFormatter(
                'ar_SA@calendar=islamic-umalqura',
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                'Asia/Riyadh',
                IntlDateFormatter::TRADITIONAL,
                'd MMMM yyyy'
            );
            return $formatter->format($dateObj) . ' هـ';
        } catch (\Throwable $e) {
            return '-';
        }
    }

    /**
     * تنسيق توقيت المناسبة
     * مثال: 07:30
     */
    public static function formatTime(?string $time): string
    {
        if (empty($time)) {
            return '07:30';
        }

        try {
            return date('h:i', strtotime($time));
        } catch (\Throwable $e) {
            return (string) $time;
        }
    }

    /**
     * استخراج بيانات الاعتماد (phone_number_id و token)
     * يعطي الأولوية القصوى لـ phone_setting_id المحدد ثم إعدادات المناسبة
     */
    public static function resolveCredentials(
        ?Events $event = null,
        ?int $phoneSettingId = null,
        ?string $phoneNumberId = null,
        ?string $token = null
    ): array {
        // 1. استخدام القيم المباشرة إن وُجدت
        if (!empty($phoneNumberId) && !empty($token)) {
            return [
                'phone_number_id'  => $phoneNumberId,
                'token'            => $token,
                'phone_setting_id' => $phoneSettingId,
            ];
        }

        // 2. تحديد phone_setting_id المستهدف (إما الممرر صراحة أو من المناسبة)
        $targetPhoneSettingId = $phoneSettingId ?: ($event?->phone_setting_id);

        // 3. إذا تم تحديد phone_setting_id، نسحب phone_numer_id من NewSetting كأولوية أولى
        if (empty($phoneNumberId) && $targetPhoneSettingId) {
            try {
                $newSetting = NewSetting::find($targetPhoneSettingId);
                if ($newSetting && !empty($newSetting->phone_numer_id)) {
                    $phoneNumberId = $newSetting->phone_numer_id;
                }
            } catch (\Throwable $e) {
                Log::warning('WhatsAppLinkInvitationService: NewSetting resolve error: ' . $e->getMessage());
            }
        }

        // 4. استخراج الـ Token من إعدادات المناسبة
        if ($event && function_exists('get_whats_setting')) {
            try {
                $whatsSetting = get_whats_setting($event);
                if (empty($token)) {
                    $token = $whatsSetting['token'] ?? null;
                }
                // إذا لم يتحدد phoneNumberId من NewSetting ولم يُمرر، نأخذه كـ fallback من إعدادات المناسبة
                if (empty($phoneNumberId)) {
                    $phoneNumberId = $whatsSetting['phone_numer_id'] ?? null;
                }
            } catch (\Throwable $e) {
                Log::warning('WhatsAppLinkInvitationService: get_whats_setting resolve error: ' . $e->getMessage());
            }
        }

        // 5. إذا لم يتحدد phoneNumberId بعد، نبحث عن أول NewSetting نشط (status = 1)
        if (empty($phoneNumberId)) {
            try {
                $activeNewSetting = NewSetting::where('status', 1)->first() ?? NewSetting::first();
                if ($activeNewSetting && !empty($activeNewSetting->phone_numer_id)) {
                    $phoneNumberId = $activeNewSetting->phone_numer_id;
                    $targetPhoneSettingId = $targetPhoneSettingId ?: $activeNewSetting->id;
                }
            } catch (\Throwable $e) {
                Log::warning('WhatsAppLinkInvitationService: active NewSetting resolve error: ' . $e->getMessage());
            }
        }

        // 6. استخراج الـ Token و phoneNumberId من Setting العام كـ fallback
        if (empty($phoneNumberId) || empty($token)) {
            try {
                $setting = Setting::first();
                if ($setting) {
                    if (empty($token)) {
                        $token = $setting->access_token ?: $setting->sa_access_token;
                    }
                    if (empty($phoneNumberId)) {
                        $phoneNumberId = $setting->phone_numer_id ?: $setting->sa_phone_numer_id;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('WhatsAppLinkInvitationService: Setting fallback warning: ' . $e->getMessage());
            }
        }

        // 7. استخراج من متغيرات البيئة .env
        if (empty($token)) {
            $token = env('WHATSAPP_ACCESS_TOKEN');
        }
        if (empty($phoneNumberId)) {
            $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        }

        return [
            'phone_number_id'  => $phoneNumberId,
            'token'            => $token,
            'phone_setting_id' => $targetPhoneSettingId,
        ];
    }
}
