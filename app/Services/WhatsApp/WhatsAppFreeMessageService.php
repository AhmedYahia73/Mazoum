<?php

namespace App\Services\WhatsApp;

use App\Models\NewSetting;
use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class WhatsAppFreeMessageService
{
    /**
     * إرسال رسالة نصية عادية مجانية (Text Message) داخل نافذة الـ 24 ساعة
     */
    public static function sendText(
        string $to,
        string $text,
        ?string $phoneNumberId = null,
        ?string $token = null,
        bool $previewUrl = true
    ): ResponseInterface {
        $cleanTo = preg_replace('/[^0-9]/', '', $to);

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanTo,
            'type' => 'text',
            'text' => [
                'preview_url' => $previewUrl,
                'body' => $text,
            ],
        ];

        return self::sendPayload($payload, $phoneNumberId, $token);
    }

    /**
     * إرسال رسالة وسائط / صورة مجانية مع نص توضيحي (Image Message)
     */
    public static function sendImage(
        string $to,
        string $imageUrl,
        ?string $caption = null,
        ?string $phoneNumberId = null,
        ?string $token = null
    ): ResponseInterface {
        $cleanTo = preg_replace('/[^0-9]/', '', $to);
        $resolvedImageUrl = self::resolvePublicMediaUrl($imageUrl);

        $imageData = [
            'link' => $resolvedImageUrl,
        ];

        if (!empty($caption)) {
            $imageData['caption'] = mb_substr(trim($caption), 0, 1024);
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanTo,
            'type' => 'image',
            'image' => $imageData,
        ];

        return self::sendPayload($payload, $phoneNumberId, $token);
    }

    /**
     * إرسال رسالة تفاعلية بأزرار مجانية بدون تمبلت (Interactive Button Message)
     * تدعم هيدر نصي أو هيدر صورة
     */
    public static function sendInteractiveButtons(
        string $to,
        string $bodyText,
        array $buttons,
        ?string $phoneNumberId = null,
        ?string $token = null,
        string|array|null $header = null,
        ?string $footerText = null
    ): ResponseInterface {
        $cleanTo = preg_replace('/[^0-9]/', '', $to);

        // تنسيق الأزرار (بحد أقصى 3 أزرار مسموح بها من ميتا)
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

        $interactive = [
            'type' => 'button',
            'body' => [
                'text' => mb_substr(trim($bodyText), 0, 1024),
            ],
            'action' => [
                'buttons' => $formattedButtons,
            ],
        ];

        // معالجة الهيدر (صورة أو نص)
        if (!empty($header)) {
            if (is_string($header)) {
                $interactive['header'] = [
                    'type' => 'text',
                    'text' => mb_substr(trim($header), 0, 60),
                ];
            } elseif (is_array($header)) {
                $headerType = $header['type'] ?? 'text';
                if ($headerType === 'image' && !empty($header['link'])) {
                    $interactive['header'] = [
                        'type' => 'image',
                        'image' => [
                            'link' => self::resolvePublicMediaUrl($header['link']),
                        ],
                    ];
                } elseif ($headerType === 'text' && !empty($header['text'])) {
                    $interactive['header'] = [
                        'type' => 'text',
                        'text' => mb_substr(trim($header['text']), 0, 60),
                    ];
                }
            }
        }

        if (!empty($footerText)) {
            $interactive['footer'] = [
                'text' => mb_substr(trim($footerText), 0, 60),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanTo,
            'type' => 'interactive',
            'interactive' => $interactive,
        ];

        return self::sendPayload($payload, $phoneNumberId, $token);
    }

    /**
     * إرسال قائمة تفاعلية مجانية (Interactive List Message) حتى 10 عناصر
     */
    public static function sendInteractiveList(
        string $to,
        string $bodyText,
        string $buttonLabel,
        array $rows,
        ?string $phoneNumberId = null,
        ?string $token = null,
        ?string $headerText = null,
        ?string $footerText = null,
        string $sectionTitle = 'الخيارات المتاحة'
    ): ResponseInterface {
        $cleanTo = preg_replace('/[^0-9]/', '', $to);

        $formattedRows = [];
        foreach (array_slice($rows, 0, 10) as $index => $row) {
            if (is_scalar($row)) {
                $formattedRows[] = [
                    'id' => (string) $row,
                    'title' => mb_substr((string) $row, 0, 24),
                ];
            } elseif (is_array($row)) {
                $r = [
                    'id' => (string) ($row['id'] ?? ($index + 1)),
                    'title' => mb_substr((string) ($row['title'] ?? ($index + 1)), 0, 24),
                ];
                if (!empty($row['description'])) {
                    $r['description'] = mb_substr((string) $row['description'], 0, 72);
                }
                $formattedRows[] = $r;
            }
        }

        $interactive = [
            'type' => 'list',
            'body' => [
                'text' => mb_substr(trim($bodyText), 0, 1024),
            ],
            'action' => [
                'button' => mb_substr(trim($buttonLabel), 0, 20),
                'sections' => [
                    [
                        'title' => mb_substr(trim($sectionTitle), 0, 24),
                        'rows' => $formattedRows,
                    ],
                ],
            ],
        ];

        if (!empty($headerText)) {
            $interactive['header'] = [
                'type' => 'text',
                'text' => mb_substr(trim($headerText), 0, 60),
            ];
        }

        if (!empty($footerText)) {
            $interactive['footer'] = [
                'text' => mb_substr(trim($footerText), 0, 60),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanTo,
            'type' => 'interactive',
            'interactive' => $interactive,
        ];

        return self::sendPayload($payload, $phoneNumberId, $token);
    }

    /**
     * إرسال كارت الدعوة التفاعلي (بديل مجاني لـ wedding_data_v25 / wedding_data_v1_ar)
     */
    public static function sendInvitationMessage(
        string $to,
        array $params,
        ?string $phoneNumberId = null,
        ?string $token = null
    ): ResponseInterface {
        $guestName = $params['param_1'] ?? ($params['name'] ?? 'ضيفنا الكريم');
        $eventTitle = $params['param_2'] ?? ($params['title'] ?? 'حفل زفاف');
        $dateAr = $params['param_3'] ?? '';
        $hijriDate = $params['param_4'] ?? '';
        $address = $params['param_5'] ?? ($params['address'] ?? '');
        $time = $params['param_6'] ?? ($params['time'] ?? '07:00 مساءً');
        $seatsCount = $params['param_7'] ?? ($params['users_count'] ?? 1);
        $imageUrl = $params['image_url'] ?? null;

        $body = "مرحباً بك يا {$guestName} 🌸\n"
              . "يسرنا دعوتكم لحضور: *{$eventTitle}*\n\n"
              . (!empty($dateAr) ? "📅 *اليوم*: {$dateAr}\n" : '')
              . (!empty($hijriDate) ? "🌙 *الموافق*: {$hijriDate}\n" : '')
              . (!empty($address) ? "🏛 *المكان*: {$address}\n" : '')
              . (!empty($time) ? "⏰ *الوقت*: {$time}\n" : '')
              . "👥 *عدد المقاعد*: {$seatsCount}\n\n"
              . "يسعدنا ويشرفنا حضوركم ومشاركتكم فرحتنا 🌹";

        $buttons = [
            ['id' => 'attend', 'title' => 'تأكيد الحضور'],
            ['id' => 'not-attend', 'title' => 'اعتذار'],
            ['id' => 'event_details', 'title' => 'تفاصيل المناسبة'],
        ];

        $header = null;
        if (!empty($imageUrl)) {
            $header = [
                'type' => 'image',
                'link' => $imageUrl,
            ];
        }

        return self::sendInteractiveButtons(
            $to,
            $body,
            $buttons,
            $phoneNumberId,
            $token,
            $header,
            'منصة معزوم'
        );
    }

    /**
     * إرسال تفاصيل المناسبة ورابط الخريطة (بديل مجاني لـ wedding___details)
     */
    public static function sendEventDetailsMessage(
        string $to,
        $event,
        ?string $phoneNumberId = null,
        ?string $token = null
    ): ResponseInterface {
        $address = $event->address ?? 'الموقع المحدد في الدعوة';
        $date = $event->date ?? '';
        $dayName = !empty($event->date) ? \Carbon\Carbon::parse($event->date)->locale('ar')->translatedFormat('l') : '';
        $time = $event->time ?? '07:00 مساءً';
        if (!empty($event->time) && str_contains($event->time, ':')) {
            $timeFormatted = \Carbon\Carbon::createFromFormat('H:i', substr($event->time, 0, 5))->format('g:i A');
            $time = str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], $timeFormatted);
        }

        $mapUrl = (!empty($event->lat) && !empty($event->long))
            ? "https://www.google.com/maps?q={$event->lat},{$event->long}"
            : '';

        $text = "📍 *تفاصيل المناسبة*:\n\n"
              . "🏛 *المكان*: {$address}\n"
              . (!empty($date) ? "📅 *التاريخ*: {$date} ({$dayName})\n" : '')
              . "⏰ *الوقت*: {$time}\n";

        if (!empty($mapUrl)) {
            $text .= "\n🗺 *موقع الحفل على خريطة جوجل*:\n{$mapUrl}";
        }

        return self::sendText($to, $text, $phoneNumberId, $token, true);
    }

    /**
     * إرسال رمز الـ QR Code كصورة مباشرة مع نص توضيحي (بديل مجاني لـ wedding_data_v2_ar / wedding_data90)
     */
    public static function sendQrCodeMessage(
        string $to,
        string $qrImageUrl,
        int|string $attendCount = 1,
        ?string $userName = null,
        ?string $phoneNumberId = null,
        ?string $token = null
    ): ResponseInterface {
        $nameText = !empty($userName) ? "* {$userName}*\n" : '';
        $caption = "🎫 *رمز الدخول الخاص بك (QR Code)*\n"
                 . $nameText
                 . " عدد الأفراد المصرح لهم: *{$attendCount}*\n\n"
                 . "يرجى إبراز هذا الرمز عند بوابة الدخول 🌷";

        return self::sendImage($to, $qrImageUrl, $caption, $phoneNumberId, $token);
    }

    /**
     * إرسال سؤال التهنئة بأزرار نعم / لا (بديل مجاني لـ send_congratulation_ar_new)
     */
    public static function sendCongratulationPromptMessage(
        string $to,
        ?string $phoneNumberId = null,
        ?string $token = null
    ): ResponseInterface {
        $body = "تهانينا! تم تأكيد حضورك بنجاح 🎉\n\nهل ترغب في إرسال رسالة تهنئة لصاحب المناسبة؟";

        $buttons = [
            ['id' => 'yes-congrato', 'title' => 'نعم'],
            ['id' => 'no-congrato', 'title' => 'لا'],
        ];

        return self::sendInteractiveButtons(
            $to,
            $body,
            $buttons,
            $phoneNumberId,
            $token,
            null,
            'منصة معزوم'
        );
    }

    /**
     * إرسال سؤال الاعتذار بأزرار نعم / لا (بديل مجاني لـ wedding_data_v3_ar)
     */
    public static function sendApologyPromptMessage(
        string $to,
        ?string $phoneNumberId = null,
        ?string $token = null
    ): ResponseInterface {
        $body = "نأسف لعدم تمكنك من الحضور، ونتمنى أن نراك في مناسبات قادمة بإذن الله 🌸\n\nهل ترغب في ترك رسالة اعتذار لصاحب المناسبة؟";

        $buttons = [
            ['id' => 'yes-apologize', 'title' => 'نعم'],
            ['id' => 'no-apologize', 'title' => 'لا'],
        ];

        return self::sendInteractiveButtons(
            $to,
            $body,
            $buttons,
            $phoneNumberId,
            $token,
            null,
            'منصة معزوم'
        );
    }

    /**
     * إرسال خيارات اختيار عدد الحضور (بديل مجاني لـ flow_1 إلى flow_10)
     * إذا كان العدد <= 3 يرسل أزرار، وإذا كان أكثر يرسل قائمة تفاعلية
     */
    public static function sendAttendeesCountSelectionMessage(
        string $to,
        int $available,
        ?string $phoneNumberId = null,
        ?string $token = null
    ): ResponseInterface {
        $maxCount = min(max(1, $available), 10);

        if ($maxCount <= 3) {
            $buttons = [];
            for ($i = 1; $i <= $maxCount; $i++) {
                $title = match ($i) {
                    1 => '1 فرد',
                    2 => '2 أفراد',
                    3 => '3 أفراد',
                    default => "{$i} أفراد",
                };
                $buttons[] = [
                    'id' => (string) $i,
                    'title' => $title,
                ];
            }

            return self::sendInteractiveButtons(
                $to,
                'يرجى تحديد عدد الأفراد المؤكد حضورهم:',
                $buttons,
                $phoneNumberId,
                $token,
                'تحديد عدد الحضور',
                'منصة معزوم'
            );
        }

        // أكثر من 3 عناصر حتى 10 عناصر يتم إرسالها كقائمة تفاعلية (Interactive List)
        $rows = [];
        for ($i = 1; $i <= $maxCount; $i++) {
            $title = match ($i) {
                1 => '1 فرد',
                2 => '2 أفراد',
                default => "{$i} أفراد",
            };
            $rows[] = [
                'id' => (string) $i,
                'title' => $title,
                'description' => "تأكيد حضور {$i} " . ($i === 1 ? 'شخص' : 'أشخاص'),
            ];
        }

        return self::sendInteractiveList(
            $to,
            'يرجى اختيار عدد الأفراد المؤكد حضورهم من القائمة أدناه:',
            'اختر عدد الحضور',
            $rows,
            $phoneNumberId,
            $token,
            'تحديد عدد الحضور',
            'منصة معزوم',
            'خيارات عدد الحضور'
        );
    }

    /**
     * إرسال موقع المناسبة (بديل مجاني لـ wedding_data_v7_ar)
     */
    public static function sendLocationMessage(
        string $to,
        $event,
        ?string $userName = null,
        ?string $phoneNumberId = null,
        ?string $token = null
    ): ResponseInterface {
        $nameText = !empty($userName) ? "مرحباً بك يا {$userName} 📍\n" : "مرحباً بك 📍\n";
        $mapUrl = (!empty($event->lat) && !empty($event->long))
            ? "https://www.google.com/maps?q={$event->lat},{$event->long}"
            : '';

        $text = "{$nameText}موقع المناسبة على خريطة جوجل:\n{$mapUrl}";

        return self::sendText($to, $text, $phoneNumberId, $token, true);
    }

    /**
     * المحرك الأساسي للإرسال إلى Meta Cloud API
     */
    public static function sendPayload(
        array $payload,
        ?string $phoneNumberId = null,
        ?string $token = null
    ): ResponseInterface {
        $credentials = self::resolveCredentials($phoneNumberId, $token);
        $resolvedPhoneId = $credentials['phone_number_id'];
        $resolvedToken = $credentials['token'];

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
            $responseBody = (string) $response->getBody();
            Log::error('WhatsApp Free Message Error: ' . $responseBody, [
                'to' => $payload['to'] ?? null,
                'type' => $payload['type'] ?? null,
                'phone_number_id' => $resolvedPhoneId,
                'status_code' => $statusCode,
            ]);
        }

        return $response;
    }

    /**
     * حل وتجهيز الروابط لتكون رابطاً عاماً يقبله سيرفر ميتا (استبدال localhost بنطاق الموقع)
     */
    public static function resolvePublicMediaUrl(?string $url): string
    {
        if (empty($url)) {
            return asset('11.png');
        }

        $cleanUrl = preg_replace('#^https?://(localhost|127\.0\.0\.1)(:\d+)?#i', 'https://mazoom.online', $url);

        return $cleanUrl;
    }

    /**
     * جلب بيانات الاعتماد (Phone Number ID و Access Token) من المدخلات أو قاعدة البيانات أو الـ .env
     */
    public static function resolveCredentials(?string $phoneNumberId = null, ?string $token = null): array
    {
        // 1. استخراج الـ Token
        if (empty($token)) {
            try {
                $setting = Setting::first();
                $token = $setting?->access_token ?: $setting?->sa_access_token;
            } catch (\Throwable $e) {
                Log::warning('WhatsAppFreeMessageService: Failed to get token from Setting: ' . $e->getMessage());
            }

            if (empty($token)) {
                $token = env('WHATSAPP_ACCESS_TOKEN');
            }
        }

        // 2. استخراج phone_number_id
        if (empty($phoneNumberId)) {
            try {
                $activeNewSetting = NewSetting::where('status', 1)->first() ?? NewSetting::first();
                $phoneNumberId = $activeNewSetting?->phone_numer_id;
            } catch (\Throwable $e) {
                Log::warning('WhatsAppFreeMessageService: Failed to get phone_numer_id from NewSetting: ' . $e->getMessage());
            }

            if (empty($phoneNumberId)) {
                try {
                    $setting = Setting::first();
                    $phoneNumberId = $setting?->phone_numer_id ?: $setting?->sa_phone_numer_id;
                } catch (\Throwable $e) {
                    Log::warning('WhatsAppFreeMessageService: Failed to get phone_numer_id from Setting: ' . $e->getMessage());
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
