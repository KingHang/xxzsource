<?php


namespace app\common\library\agora\token;

class TokenService
{
    public static function getRtcToken($user_id, $channel, $appId, $appCertificate, $isCaster){
        include("RtcTokenBuilder.php");
        $channelName = $channel;
        $role = $isCaster?RtcTokenBuilder::RolePublisher:RtcTokenBuilder::RoleSubscriber;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = time();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        return RtcTokenBuilder::buildTokenWithUid($appId, $appCertificate, $channelName, intval($user_id), $role, $privilegeExpiredTs);
    }

    public static function getRtmToken($user_id, $appId, $appCertificate){
        include("RtmTokenBuilder.php");
        $role = RtmTokenBuilder::RoleRtmUser;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = time();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        return RtmTokenBuilder::buildToken($appId, $appCertificate, $user_id, $role, $privilegeExpiredTs);
    }
}