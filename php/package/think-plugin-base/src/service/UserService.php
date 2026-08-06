<?php

declare(strict_types=1);

namespace plugin\base\service;

use plugin\base\model\BaseUser;

/**
 * 用户服务
 */
class UserService
{
    /**
     * 获取规范化用户 Profile 数组
     *
     * @param BaseUser $user
     * @return array
     */
    public static function profile(BaseUser $user): array
    {
        return [
            'id' => intval($user->id),
            'nickname' => (string) $user->nickname,
            'avatar_url' => (string) $user->avatar_url,
            'phone' => (string) $user->phone,
            'vip_time' => intval($user->vip_time),
            'gender' => intval($user->gender),
            'birthday' => (string) ($user->birthday ?? ''),
            'region' => (string) ($user->region ?? ''),
            'signature' => (string) ($user->signature ?? ''),
        ];
    }

    /**
     * 同步登录用户信息（注册或更新）
     *
     * @param string $openid
     * @param string $unionid
     * @param array $profile
     * @param array $device
     * @param string $ip
     * @param string $invite_uid
     * @param string $appid
     * @return BaseUser
     */
    public static function sync(
        string $openid,
        string $unionid,
        array $profile,
        array $device,
        string $ip,
        string $invite_uid = '',
        string $appid = ''
    ): BaseUser {
        $user = BaseUser::mk()->where('openid', $openid)->findOrEmpty();

        if ($user->isEmpty()) {
            return static::register($openid, $unionid, $profile, $device, $ip, $invite_uid, $appid);
        }

        return static::refresh($user, $unionid, $device, $ip);
    }

    /**
     * 新用户注册入库
     *
     * @param string $openid
     * @param string $unionid
     * @param array $profile
     * @param array $device
     * @param string $ip
     * @param string $invite_uid
     * @param string $appid
     * @return BaseUser
     */
    private static function register(
        string $openid,
        string $unionid,
        array $profile,
        array $device,
        string $ip,
        string $invite_uid,
        string $appid
    ): BaseUser {
        $pid = 0;
        $invite_user_id = intval($invite_uid);
        if ($invite_user_id > 0) {
            $inviter = BaseUser::mk()->where(['id' => $invite_user_id, 'deleted' => 0, 'status' => 1])->findOrEmpty();
            if ($inviter->isExists()) {
                $pid = intval($inviter->id);
            }
        }

        $user = BaseUser::mk();
        $user->save([
            'openid' => $openid,
            'appid' => $appid,
            'pid' => $pid,
            'unionid' => $unionid,
            'nickname' => $profile['nickname'] ?? '',
            'avatar_url' => $profile['avatar_url'] ?? '',
            'device_model' => $device['device_model'] ?? '',
            'device_system' => $device['device_system'] ?? '',
            'screen_width' => intval($device['screen_width'] ?? 0),
            'screen_height' => intval($device['screen_height'] ?? 0),
            'sdk_version' => $device['sdk_version'] ?? '',
            'version' => $device['version'] ?? '',
            'channel' => $device['channel'] ?? '',
            'login_ip' => $ip,
            'login_at' => date('Y-m-d H:i:s'),
            'status' => 1,
        ]);

        return $user;
    }

    /**
     * 刷新已有用户登录及设备信息
     *
     * @param BaseUser $user
     * @param string $unionid
     * @param array $device
     * @param string $ip
     * @return BaseUser
     */
    private static function refresh(BaseUser $user, string $unionid, array $device, string $ip): BaseUser
    {
        if (intval($user->status) !== 1) {
            throw new \RuntimeException('账号已被禁用');
        }

        $update = [
            'last_login_ip' => $user->login_ip,
            'last_login_at' => $user->login_at,
            'login_ip' => $ip,
            'login_at' => date('Y-m-d H:i:s'),
            'device_model' => $device['device_model'] ?? '',
            'device_system' => $device['device_system'] ?? '',
            'screen_width' => intval($device['screen_width'] ?? 0),
            'screen_height' => intval($device['screen_height'] ?? 0),
            'sdk_version' => $device['sdk_version'] ?? '',
            'version' => $device['version'] ?? '',
            'channel' => $device['channel'] ?? '',
        ];

        if ($unionid !== '' && empty($user->unionid)) {
            $update['unionid'] = $unionid;
        }

        $user->save($update);
        return $user;
    }
}
