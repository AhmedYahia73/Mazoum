<?php

namespace App\Services\Agora;

use Exception;

class AgoraService
{
    /**
     * Get Agora App ID from environment.
     *
     * @return string|null
     */
    public function getAppId(): ?string
    {
        return env('AGORA_APP_ID');
    }

    /**
     * Get Agora App Certificate from environment.
     *
     * @return string|null
     */
    public function getAppCertificate(): ?string
    {
        return env('AGORA_APP_CERTIFICATE');
    }

    /**
     * Check if Agora credentials are configured in .env.
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->getAppId()) && !empty($this->getAppCertificate());
    }

    /**
     * Generate Agora RTC token for voice/video call.
     *
     * @param string $channelName
     * @param int|string $uid
     * @param int $role
     * @param int $expireSeconds
     * @return string
     * @throws Exception
     */
    public function generateToken(string $channelName, $uid = 0, int $role = RtcTokenBuilder::RolePublisher, int $expireSeconds = 3600): string
    {
        $appId = $this->getAppId();
        $appCertificate = $this->getAppCertificate();

        if (empty($appId) || empty($appCertificate)) {
            throw new Exception('مفاتيح Agora غير محددة في ملف .env (AGORA_APP_ID / AGORA_APP_CERTIFICATE are missing in .env)');
        }

        $privilegeExpireTs = time() + $expireSeconds;

        return RtcTokenBuilder::buildTokenWithUid(
            $appId,
            $appCertificate,
            $channelName,
            (int) $uid,
            $role,
            $privilegeExpireTs
        );
    }

    /**
     * Create a unique channel name for call session.
     *
     * @param int|string $userId
     * @return string
     */
    public function generateChannelName($userId): string
    {
        return 'call_u' . $userId . '_' . time() . '_' . rand(100, 999);
    }
}
