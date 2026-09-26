<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\EventUsers;
use App\Models\NewSetting;
use App\Models\Setting;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExtraTemplateController extends Controller
{
    /**
     * 1. رسالة تهنئة لصاحب المناسبة
     * Template: send_congratulation_ar_new
     * يحتوي على زري: yes-congrato / no-congrato
     */
    public function send_congratulation_ar_new(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_user_id' => 'required_without_all:phone,mobile,to',
            'phone' => 'required_without:event_user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $params = $this->resolveWhatsAppParams($request);
        $check = $this->validateCredentials($params, $request);
        if ($check !== null) {
            return $check;
        }

        $template_name = 'send_congratulation_ar_new';

        try {
            $response = SendCongratulationArNewTemplate(
                $params['phone'],
                $template_name,
                $params['language'],
                $params['phone_numer_id'],
                $params['token']
            );

            return $this->handleTemplateResponse($response, $params, $template_name, [
                'title' => 'رسالة تهنئة لصاحب المناسبة',
            ]);
        } catch (ClientException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (RequestException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (\Throwable $e) {
            Log::error("Error in send_congratulation_ar_new: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 2. اكتب رسالتك الأن
     * Template: wedding_data_v16_ar
     */
    public function wedding_data_v16_ar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_user_id' => 'required_without_all:phone,mobile,to',
            'phone' => 'required_without:event_user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $params = $this->resolveWhatsAppParams($request);
        $check = $this->validateCredentials($params, $request);
        if ($check !== null) {
            return $check;
        }

        $template_name = 'wedding_data_v16_ar';

        try {
            $response = SendMessageTemplate(
                $params['phone'],
                $template_name,
                $params['language'],
                $params['phone_numer_id'],
                $params['token']
            );

            return $this->handleTemplateResponse($response, $params, $template_name, [
                'title' => 'اكتب رسالتك الأن',
            ]);
        } catch (ClientException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (RequestException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (\Throwable $e) {
            Log::error("Error in wedding_data_v16_ar: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 3. تم إرسال رسالتك لصاحب المناسبة🌷
     * Template: wedding_data_v4_ar
     */
    public function wedding_data_v4_ar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_user_id' => 'required_without_all:phone,mobile,to',
            'phone' => 'required_without:event_user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $params = $this->resolveWhatsAppParams($request);
        $check = $this->validateCredentials($params, $request);
        if ($check !== null) {
            return $check;
        }

        $template_name = 'wedding_data_v4_ar';

        try {
            $response = SendMessageTemplate(
                $params['phone'],
                $template_name,
                $params['language'],
                $params['phone_numer_id'],
                $params['token']
            );

            return $this->handleTemplateResponse($response, $params, $template_name, [
                'title' => 'تم إرسال رسالتك لصاحب المناسبة🌷',
            ]);
        } catch (ClientException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (RequestException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (\Throwable $e) {
            Log::error("Error in wedding_data_v4_ar: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 4. الرد على الأعتذار
     * Template: wedding_data_v3_ar
     * يحتوي على زري: yes-apologize / no-apologize
     */
    public function wedding_data_v3_ar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_user_id' => 'required_without_all:phone,mobile,to',
            'phone' => 'required_without:event_user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $params = $this->resolveWhatsAppParams($request);
        $check = $this->validateCredentials($params, $request);
        if ($check !== null) {
            return $check;
        }

        $template_name = 'wedding_data_v3_ar';

        try {
            $response = SendApologizedTemplate(
                $params['phone'],
                $template_name,
                $params['language'],
                $params['phone_numer_id'],
                $params['token']
            );

            return $this->handleTemplateResponse($response, $params, $template_name, [
                'title' => 'الرد على الأعتذار',
            ]);
        } catch (ClientException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (RequestException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (\Throwable $e) {
            Log::error("Error in wedding_data_v3_ar: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 5. شكراً لك يسعـدنـا أن نراكـم من جديد🌷
     * Template: wedding_data_v11_ar_
     */
    public function wedding_data_v11_ar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_user_id' => 'required_without_all:phone,mobile,to',
            'phone' => 'required_without:event_user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $params = $this->resolveWhatsAppParams($request);
        $check = $this->validateCredentials($params, $request);
        if ($check !== null) {
            return $check;
        }

        $template_name = 'wedding_data_v11_ar_';

        try {
            $response = SendMessageTemplate(
                $params['phone'],
                $template_name,
                $params['language'],
                $params['phone_numer_id'],
                $params['token']
            );

            return $this->handleTemplateResponse($response, $params, $template_name, [
                'title' => 'شكراً لك يسعـدنـا أن نراكـم من جديد🌷',
            ]);
        } catch (ClientException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (RequestException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (\Throwable $e) {
            Log::error("Error in wedding_data_v11_ar: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 6. Flow Template (flow_1 إلى flow_10 حسب عدد الدعوات)
     * Template: flow_{count}
     */
    public function send_flow(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_user_id' => 'required_without_all:phone,mobile,to',
            'phone' => 'required_without:event_user_id',
            'count' => 'required|integer|min:1|max:10', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $params = $this->resolveWhatsAppParams($request);
        $check = $this->validateCredentials($params, $request);
        if ($check !== null) {
            return $check;
        }

        // تحديد عدد الدعوات من 1 إلى 10 (افتراضياً من المستخدم أو من الريكوست)
        $inputCount = $request->input('count') 
            ?? $request->input('users_count') 
            ?? $request->input('invitations_count')
            ?? ($params['user_event']?->users_count)
            ?? 1;

        $available = min(max(1, (int)$inputCount), 10);
        $template_name = 'flow_' . $available;
        $func = 'SendArFlowV' . $available . 'Template';

        if (!function_exists($func)) {
            return response()->json([
                'status' => 'error',
                'message' => "Function {$func} is not defined.",
            ], 500);
        }

        try {
            $response = $func(
                $params['phone'],
                $template_name,
                $params['language'],
                $params['phone_numer_id'],
                $params['token']
            );

            return $this->handleTemplateResponse($response, $params, $template_name, [
                'title' => 'قالب اختيار عدد الحضور (' . $available . ' دعوة)',
                'flow' => $template_name,
                'invitations_count' => $available,
            ]);
        } catch (ClientException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (RequestException $e) {
            return $this->handleGuzzleException($e, $template_name);
        } catch (\Throwable $e) {
            Log::error("Error in send_flow: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ==========================================
    // Aliases for convenience (camelCase & snake_case)
    // ==========================================

    public function wedding_data_v11_ar_(Request $request): JsonResponse
    {
        return $this->wedding_data_v11_ar($request);
    }

    public function sendCongratulationArNew(Request $request): JsonResponse
    {
        return $this->send_congratulation_ar_new($request);
    }

    public function sendWeddingDataV16Ar(Request $request): JsonResponse
    {
        return $this->wedding_data_v16_ar($request);
    }

    public function sendWeddingDataV4Ar(Request $request): JsonResponse
    {
        return $this->wedding_data_v4_ar($request);
    }

    public function sendWeddingDataV3Ar(Request $request): JsonResponse
    {
        return $this->wedding_data_v3_ar($request);
    }

    public function sendWeddingDataV11Ar(Request $request): JsonResponse
    {
        return $this->wedding_data_v11_ar($request);
    }

    public function sendFlowTemplate(Request $request): JsonResponse
    {
        return $this->send_flow($request);
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    /**
     * استخراج وتجهيز بيانات واتساب والمستقبل من خلال event_user_id أو phone
     */
    protected function resolveWhatsAppParams(Request $request): array
    {
        $user_event = null;
        $event = null;
        $rawPhone = $request->input('phone') ?? $request->input('mobile') ?? $request->input('to');

        // 1. إذا تم إرسال event_user_id يتم جلب المستخدم والـ mobile والمناسبة تلقائياً
        if ($request->filled('event_user_id')) {
            try {
                $user_event = EventUsers::with('event')->find($request->input('event_user_id'));
                if ($user_event) {
                    if (empty($rawPhone)) {
                        $rawPhone = $user_event->mobile ?: $user_event->phone_number;
                    }
                    if ($user_event->event) {
                        $event = $user_event->event;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Could not query EventUsers by event_user_id: " . $e->getMessage());
            }
        }

        $phone = preg_replace('/[^0-9]/', '', (string)$rawPhone);

        // 2. إذا لم يتوفر المستخدم من event_user_id ولكن تم تمرير رقم الهاتف
        if (!$user_event && $phone) {
            try {
                $user_event = EventUsers::with('event')->where(function ($q) use ($phone, $rawPhone) {
                    $q->where('mobile', $phone)
                      ->orWhere('mobile', '+' . $phone)
                      ->orWhere('mobile', $rawPhone)
                      ->orWhere('phone_number', $phone)
                      ->orWhere('phone_number', '+' . $phone);
                })->orderByDesc('id')->first();

                if ($user_event && $user_event->event) {
                    $event = $user_event->event;
                }
            } catch (\Throwable $e) {
                Log::warning("Could not query EventUsers by phone: " . $e->getMessage());
            }
        }

        if (!$event && $request->filled('event_id')) {
            try {
                $event = Events::find($request->input('event_id'));
            } catch (\Throwable $e) {
                Log::warning("Could not query Events by event_id: " . $e->getMessage());
            }
        }

        $name = $request->input('name') ?: $user_event?->name;
        $language = $request->input('language') ?: 'ar';

        $token = $request->input('token');
        $phone_numer_id = null;

        try {
            // 3. استخراج phone_numer_id
            if ($request->filled('phone_numer_id')) {
                $inputPhoneId = $request->input('phone_numer_id');
                if (is_numeric($inputPhoneId) && strlen((string)$inputPhoneId) <= 10) {
                    $new_setting = NewSetting::find($inputPhoneId);
                    $phone_numer_id = $new_setting ? $new_setting->phone_numer_id : $inputPhoneId;
                } else {
                    $phone_numer_id = $inputPhoneId;
                }
            } elseif ($request->filled('new_setting_id')) {
                $new_setting = NewSetting::find($request->input('new_setting_id'));
                $phone_numer_id = $new_setting?->phone_numer_id;
            }

            // 4. Fallback من إعدادات المناسبة
            if ((!$token || !$phone_numer_id) && $event && function_exists('get_whats_setting')) {
                $eventCreds = get_whats_setting($event);
                if (!$token && !empty($eventCreds['token'])) {
                    $token = $eventCreds['token'];
                }
                if (!$phone_numer_id && !empty($eventCreds['phone_numer_id'])) {
                    $phone_numer_id = $eventCreds['phone_numer_id'];
                }
            }

            // 5. Fallback من إعدادات النظام العامة
            $setting = Setting::first();
            if (!$token && $setting) {
                $token = $setting->access_token ?: $setting->sa_access_token;
            }
            if (!$phone_numer_id && $setting) {
                $phone_numer_id = $setting->sa_phone_numer_id ?: $setting->phone_numer_id;
            }
        } catch (\Throwable $e) {
            Log::warning("Could not query settings in resolveWhatsAppParams: " . $e->getMessage());
        }

        if (!$phone_numer_id && $request->filled('phone_numer_id')) {
            $phone_numer_id = $request->input('phone_numer_id');
        }

        return [
            'phone' => $phone,
            'name' => $name,
            'language' => $language,
            'phone_numer_id' => $phone_numer_id,
            'token' => $token,
            'user_event' => $user_event,
            'event' => $event,
        ];
    }

    /**
     * التحقق من توفر بيانات الاعتماد ورقم الهاتف
     */
    protected function validateCredentials(array $params, Request $request): ?JsonResponse
    {
        if ($request->filled('event_user_id') && !$params['user_event']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event user not found for event_user_id: ' . $request->input('event_user_id'),
            ], 404);
        }

        if (empty($params['phone'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mobile number could not be found for the event user, or phone parameter is missing.',
            ], 422);
        }

        if (empty($params['phone_numer_id']) || empty($params['token'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'WhatsApp credentials (phone_numer_id or access_token) are missing in settings and request.',
            ], 400);
        }

        return null;
    }

    /**
     * معالجة رد WhatsApp API وتوثيق الإرسال
     */
    protected function handleTemplateResponse($response, array $params, string $template_name, array $extra = []): JsonResponse
    {
        $statusCode = $response ? $response->getStatusCode() : 500;
        $body = $response ? json_decode($response->getBody()->getContents(), true) : null;
        $messageId = $body['messages'][0]['id'] ?? null;

        if ($statusCode == 200 || $statusCode == 201) {
            if (function_exists('log_sent_watts_message')) {
                log_sent_watts_message(
                    $params['phone'],
                    $template_name,
                    $messageId,
                    $params['name'],
                    $params['phone_numer_id']
                );
            }

            return response()->json(array_merge([
                'status' => 'success',
                'message' => 'Template sent successfully',
                'template' => $template_name,
                'event_user_id' => $params['user_event']?->id,
                'name' => $params['name'],
                'mobile' => $params['phone'],
                'message_id' => $messageId,
                'meta_response' => $body,
            ], $extra), 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send template',
            'template' => $template_name,
            'response' => $body,
        ], $statusCode);
    }

    /**
     * معالجة أخطاء Guzzle عند التواصل مع Meta Cloud API
     */
    protected function handleGuzzleException($e, string $template_name): JsonResponse
    {
        $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;
        $statusCode = $response ? $response->getStatusCode() : 400;
        $body = $response ? json_decode($response->getBody()->getContents(), true) : null;

        Log::error("WhatsApp API Error ({$template_name}): " . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'WhatsApp API Error',
            'template' => $template_name,
            'error' => $body ?? $e->getMessage(),
        ], $statusCode);
    }
}
