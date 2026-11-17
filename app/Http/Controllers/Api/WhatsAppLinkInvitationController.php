<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\EventUsers;
use App\Services\WhatsApp\WhatsAppLinkInvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WhatsAppLinkInvitationController extends Controller
{
    /**
     * إرسال دعوة الرابط التفاعلية (صورة / فيديو / PDF)
     *
     * القوالب المعتمدة في Meta:
     * - إذا كان نوع الوسائط صورة: invitation_link
     * - إذا كان نوع الوسائط فيديو: video_link_invitation
     * - إذا كان نوع الوسائط PDF: link_invitation_pdf
     *
     * الأزرار:
     * - الزر الأول: زيارة موقع الويب ديناميكي -> https://www.mazoominvitations.com/event-login/{code}
     * - الزر الثاني: زيارة موقع الويب ديناميكي -> https://www.mazoominvitations.com/event-login/{code}
     * - الزر الثالث: رد سريع -> value/payload = event_details
     */
    public function send(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_user_id'    => 'required|exists:event_users,id',
            'type'             => 'required|string|in:image,video,pdf,document',
            'phone_setting_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات الطلب غير صالحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        // =========================================================
        // المسار الأول: الإرسال عبر معرف سجل المدعو (event_user_id)
        // =========================================================
        if ($request->filled('event_user_id')) {
            $eventUser = EventUsers::with('event')->find($request->input('event_user_id'));

            if (!$eventUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على المدعو المطلوب برقم: ' . $request->input('event_user_id'),
                ], 404);
            }

            $type = $request->input('type');
            $phoneSettingId = $request->input('phone_setting_id');

            $result = WhatsAppLinkInvitationService::sendForEventUser(
                $eventUser,
                $eventUser->event,
                $type,
                $phoneSettingId
            );

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'تم إرسال دعوة الرابط بنجاح',
                    'data' => [
                        'event_user_id'    => $eventUser->id,
                        'name'             => $eventUser->name,
                        'phone'            => $eventUser->mobile,
                        'template'         => $result['template'] ?? null,
                        'header_type'      => $result['header_type'] ?? null,
                        'message_id'       => $result['message_id'] ?? null,
                        'code'             => $result['code'] ?? $eventUser->code,
                        'login_url'        => $result['login_url'] ?? (WhatsAppLinkInvitationService::BASE_LOGIN_URL . $eventUser->code),
                        'phone_setting_id' => $result['phone_setting_id'] ?? $phoneSettingId,
                        'phone_number_id'  => $result['phone_number_id'] ?? null,
                    ],
                ]);
            }

            return response()->json([
                'status'                => 'error',
                'message'               => $result['message'] ?? 'فشل إرسال الدعوة عبر واتساب',
                'template'              => $result['template'] ?? null,
                'phone_setting_id_used' => $result['phone_setting_id_used'] ?? $phoneSettingId,
                'phone_numer_id_used'   => $result['phone_numer_id_used'] ?? null,
                'error'                 => $result['error'] ?? null,
            ], 500);
        }

        // =========================================================
        // المسار الثاني: الإرسال بالمتغيرات اليدوية المباشرة
        // =========================================================
        $to = $request->input('to') ?: ($request->input('phone') ?: $request->input('mobile'));
        $type = $request->input('type', 'image');
        $code = $request->input('code') ?: strtolower(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));

        // الوسائط
        $mediaUrl = $request->input('media_url') ?: asset('11.png');

        // المتغيرات السبعة
        $param1 = $request->input('param_1') ?: ($request->input('name') ?: 'المكرم سعيد البياع');
        $param2 = $request->input('param_2') ?: ($request->input('title') ?: 'بكل الود والحب نتشرف بدعوتكم لحضور الحفل');
        $param3 = $request->input('param_3') ?: ($request->input('date') ?: WhatsAppLinkInvitationService::formatGregorianDate(now()));
        $param4 = $request->input('param_4') ?: ($request->input('hijri_date') ?: WhatsAppLinkInvitationService::formatHijriDate(now()));
        $param5 = $request->input('param_5') ?: ($request->input('address') ?: ($request->input('hall') ?: 'فندق الجميرا قاعة المسيلة'));
        $param6 = $request->input('param_6') ?: ($request->input('time') ?: '07:30');
        $param7 = (string) ($request->input('param_7') ?: ($request->input('users_count') ?: '2'));

        $bodyParams = [$param1, $param2, $param3, $param4, $param5, $param6, $param7];

        $phoneNumberId = $request->input('phone_number_id') ?: $request->input('phone_numer_id');
        $token = $request->input('token');

        $result = WhatsAppLinkInvitationService::sendMessage(
            $to,
            $type,
            $mediaUrl,
            $bodyParams,
            $code,
            $phoneNumberId,
            $token
        );

        if ($result['success']) {
            return response()->json([
                'status'  => 'success',
                'message' => 'تم إرسال دعوة الرابط بنجاح',
                'data'    => [
                    'to'          => $to,
                    'template'    => $result['template'],
                    'header_type' => $result['header_type'],
                    'message_id'  => $result['message_id'],
                    'code'        => $result['code'],
                    'login_url'   => $result['login_url'],
                ],
            ]);
        }

        return response()->json([
            'status'   => 'error',
            'message'  => $result['message'] ?? 'فشل إرسال الدعوة عبر واتساب',
            'template' => $result['template'] ?? null,
            'error'    => $result['error'] ?? null,
        ], 500);
    }
}
