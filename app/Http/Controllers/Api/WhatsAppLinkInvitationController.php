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
            'event_user_id'    => 'required_without:event_user_ids',
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
        // المسار الأول: الإرسال عبر معرف سجل المدعو (event_user_id) يدعم قيمة مفردة أو مصفوفة
        // =========================================================
        $rawIds = $request->input('event_user_id', $request->input('event_user_ids'));

        $isArray = is_array($rawIds);
        if ($isArray) {
            $userIds = $rawIds;
        } elseif (is_string($rawIds) && str_contains($rawIds, ',')) {
            $userIds = array_map('trim', explode(',', $rawIds));
            $isArray = true;
        } elseif (!empty($rawIds)) {
            $userIds = [$rawIds];
        } else {
            $userIds = [];
        }

        $userIds = array_values(array_filter(array_map('intval', $userIds), fn($id) => $id > 0));

        if (empty($userIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'يجب تزويد معرف مدعو واحد على الأقل في event_user_id',
            ], 422);
        }

        $type = $request->input('type');
        $phoneSettingId = $request->input('phone_setting_id');

        // جلب جميع المدعوين دفعة واحدة مع بيانات الحفل المرتبطة
        $eventUsers = EventUsers::with('event')->whereIn('id', $userIds)->get()->keyBy('id');

        // إذا تم تمرير معرّف واحد فقط ولم يكن مصفوفة
        if (!$isArray && count($userIds) === 1) {
            $id = $userIds[0];
            $eventUser = $eventUsers->get($id);

            if (!$eventUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على المدعو المطلوب برقم: ' . $id,
                ], 404);
            }

            $effectiveSettingId = $phoneSettingId ?: ($eventUser->event?->phone_setting_id);

            $result = WhatsAppLinkInvitationService::sendForEventUser(
                $eventUser,
                $eventUser->event,
                $type,
                $effectiveSettingId ? (int)$effectiveSettingId : null
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
                        'phone_setting_id' => $result['phone_setting_id'] ?? $effectiveSettingId,
                        'phone_number_id'  => $result['phone_number_id'] ?? null,
                    ],
                ]);
            }

            return response()->json([
                'status'                => 'error',
                'message'               => $result['message'] ?? 'فشل إرسال الدعوة عبر واتساب',
                'template'              => $result['template'] ?? null,
                'phone_setting_id_used' => $result['phone_setting_id_used'] ?? $effectiveSettingId,
                'phone_numer_id_used'   => $result['phone_numer_id_used'] ?? null,
                'error'                 => $result['error'] ?? null,
            ], 500);
        }

        // =========================================================
        // معالجة المصفوفة (Bulk / Array of event_user_id)
        // =========================================================
        $results = [];
        $successCount = 0;
        $failedCount = 0;
        $templateUsed = null;

        foreach ($userIds as $id) {
            $eventUser = $eventUsers->get($id);

            if (!$eventUser) {
                $failedCount++;
                $results[] = [
                    'event_user_id' => $id,
                    'status'        => 'failed',
                    'message'       => 'المدعو غير موجود برقم ' . $id,
                ];
                continue;
            }

            $effectiveSettingId = $phoneSettingId ?: ($eventUser->event?->phone_setting_id);

            $result = WhatsAppLinkInvitationService::sendForEventUser(
                $eventUser,
                $eventUser->event,
                $type,
                $effectiveSettingId ? (int)$effectiveSettingId : null
            );

            if ($result['success']) {
                $successCount++;
                $templateUsed = $result['template'] ?? $templateUsed;
                $results[] = [
                    'event_user_id'    => $eventUser->id,
                    'name'             => $eventUser->name,
                    'phone'            => $eventUser->mobile,
                    'status'           => 'sent',
                    'message_id'       => $result['message_id'] ?? null,
                    'code'             => $result['code'] ?? $eventUser->code,
                    'login_url'        => $result['login_url'] ?? (WhatsAppLinkInvitationService::BASE_LOGIN_URL . $eventUser->code),
                    'phone_setting_id' => $result['phone_setting_id'] ?? $effectiveSettingId,
                ];
            } else {
                $failedCount++;
                $results[] = [
                    'event_user_id' => $eventUser->id,
                    'name'          => $eventUser->name,
                    'phone'         => $eventUser->mobile,
                    'status'        => 'failed',
                    'message'       => $result['message'] ?? 'فشل الإرسال',
                    'error'         => $result['error'] ?? null,
                ];
            }

            if (count($userIds) > 1) {
                usleep(50000); // 50ms pause between sends
            }
        }

        $overallStatus = ($failedCount === 0) ? 'success' : (($successCount > 0) ? 'partial' : 'error');
        $message = "تمت معالجة إرسال {$successCount} من أصل " . count($userIds) . " دعوة بنجاح";

        return response()->json([
            'status'           => $overallStatus,
            'message'          => $message,
            'total'            => count($userIds),
            'success_count'    => $successCount,
            'failed_count'     => $failedCount,
            'template'         => $templateUsed,
            'phone_setting_id' => $phoneSettingId,
            'data'             => $results,
        ], $overallStatus === 'error' ? 500 : 200);
    }
}
