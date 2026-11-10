<?php

namespace App\Services\WhatsApp;

use App\Models\NewSetting;
use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppInteractiveService
{
    /**
     * إرسال رسالة تفاعلية بأزرار بدون الحاجة لقالب معتمد في ميتا (Interactive Button Message)
     *
     * @param string $to رقم الهاتف المستلم مع كود الدولة
     * @param string $bodyText نص الرسالة الرئيسي
     * @param array $buttons مصفوفة الأزرار (نصوص أو مصفوفات تحتوي id و title) - حد أقصى 3 أزرار
     * @param string|null $phoneNumberId معرف رقم الواتساب في ميتا (phone_number_id)
     * @param string|null $token رمز الوصول الخاص بميتا (Access Token)
     * @param string|null $headerText نص اختياري للرأس (Header)
     * @param string|null $footerText نص اختياري للتذييل (Footer)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function sendInteractiveButtons(
        string $to,
        string $bodyText,
        array $buttons,
        ?string $phoneNumberId = null,
        ?string $token = null,
        ?string $headerText = null,
        ?string $footerText = null
    ) {
        $credentials = self::resolveCredentials($phoneNumberId, $token);
        $resolvedPhoneId = $credentials['phone_number_id'];
        $resolvedToken = $credentials['token'];

        // تنظيف رقم الهاتف وإزالة العلامات غير الرقمية
        $cleanTo = preg_replace('/[^0-9]/', '', $to);

        // تنسيق الأزرار بما يتوافق مع متطلبات Meta Cloud API
        $formattedButtons = [];
        foreach (array_slice($buttons, 0, 3) as $index => $btn) {
            if (is_string($btn)) {
                $formattedButtons[] = [
                    'type' => 'reply',
                    'reply' => [
                        'id' => 'btn_' . ($index + 1),
                        'title' => mb_substr(trim($btn), 0, 20),
                    ],
                ];
            } elseif (is_array($btn)) {
                $id = $btn['id'] ?? ('btn_' . ($index + 1));
                $title = $btn['title'] ?? ($btn['text'] ?? ('زر ' . ($index + 1)));
                $formattedButtons[] = [
                    'type' => 'reply',
                    'reply' => [
                        'id' => (string) $id,
                        'title' => mb_substr(trim((string) $title), 0, 20),
                    ],
                ];
            }
        }

        // بناء جسم الرسالة التفاعلية
        $interactive = [
            'type' => 'button',
            'body' => [
                'text' => $bodyText,
            ],
            'action' => [
                'buttons' => $formattedButtons,
            ],
        ];

        if (!empty($headerText)) {
            $interactive['header'] = [
                'type' => 'text',
                'text' => mb_substr($headerText, 0, 60),
            ];
        }

        if (!empty($footerText)) {
            $interactive['footer'] = [
                'text' => mb_substr($footerText, 0, 60),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanTo,
            'type' => 'interactive',
            'interactive' => $interactive,
        ];

        $fullUrl = 'https://graph.facebook.com/v19.0/' . $resolvedPhoneId . '/messages';

        $client = new Client();
        $response = $client->post($fullUrl, [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $resolvedToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            Log::error('WhatsApp Interactive Button Message Error: ' . $response->getBody()->getContents(), [
                'to' => $cleanTo,
                'phone_number_id' => $resolvedPhoneId,
            ]);
        }

        return $response;
    }

    /**
     * إرسال رسالة الاختبار المحددة:
     * النص: مرحبا بك استاذ سعيد فى التمبلت الجديد
     * الأزرار: [ تم بنجاح , الله ينورر ]
     *
     * @param string $to رقم المستلم
     * @param string|null $phoneNumberId
     * @param string|null $token
     * @param string $name اسم الشخص (افتراضياً: استاذ سعيد)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function sendTestInteractiveMessage(
        string $to,
        ?string $phoneNumberId = null,
        ?string $token = null,
        string $name = 'استاذ سعيد'
    ) {
        $body = "مرحبا بك {$name} فى التمبلت الجديد";

        $buttons = [
            [
                'id' => 'btn_success',
                'title' => 'تم بنجاح',
            ],
            [
                'id' => 'btn_well_done',
                'title' => 'الله ينورر',
            ],
        ];

        return self::sendInteractiveButtons(
            $to,
            $body,
            $buttons,
            $phoneNumberId,
            $token
        );
    }

    /**
     * جلب بيانات الاعتماد (Phone Number ID و Access Token) من المدخلات أو قاعدة البيانات أو الـ .env
     */
    public static function resolveCredentials(?string $phoneNumberId = null, ?string $token = null): array
    {
        // 1. تحديد الـ Token
        if (empty($token)) {
            try {
                $setting = Setting::first();
                $token = $setting?->access_token ?: $setting?->sa_access_token;
            } catch (\Throwable $e) {
                Log::warning('WhatsAppInteractiveService: Failed to get token from Setting: ' . $e->getMessage());
            }

            if (empty($token)) {
                $token = env('WHATSAPP_ACCESS_TOKEN');
            }
        }

        // 2. تحديد phone_number_id
        if (empty($phoneNumberId)) {
            try {
                $activeNewSetting = NewSetting::where('status', 1)->first() ?? NewSetting::first();
                $phoneNumberId = $activeNewSetting?->phone_numer_id;
            } catch (\Throwable $e) {
                Log::warning('WhatsAppInteractiveService: Failed to get phone_numer_id from NewSetting: ' . $e->getMessage());
            }

            if (empty($phoneNumberId)) {
                try {
                    $setting = Setting::first();
                    $phoneNumberId = $setting?->phone_numer_id ?: $setting?->sa_phone_numer_id;
                } catch (\Throwable $e) {
                    Log::warning('WhatsAppInteractiveService: Failed to get phone_numer_id from Setting: ' . $e->getMessage());
                }
            }

            if (empty($phoneNumberId)) {
                $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
            }
        }

        return [
            'phone_number_id' => $phoneNumberId,
            'token' => $token,
        ];
    }
}
