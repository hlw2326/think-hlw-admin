<?php

declare(strict_types=1);
/**
 * +----------------------------------------------------------------------
 * | HlwAdmin
 * +----------------------------------------------------------------------
 * | 邮箱: 1608626143@qq.com
 * | 官方网站: https://www.hlw2326.com
 * +----------------------------------------------------------------------
 */

namespace app\wechat\service;

use app\wechat\model\WechatFans;
use think\admin\Library;
use think\admin\Service;

/**
 * 微信粉丝信息.
 * @class FansService
 */
class FansService extends Service
{
    /**
     * 增加或更新粉丝信息.
     * @param array $user 粉丝信息
     * @param string $appid 微信APPID
     */
    public static function set(array $user, string $appid = ''): bool
    {
        if (isset($user['subscribe_time'])) {
            $user['subscribe_at'] = date('Y-m-d H:i:s', $user['subscribe_time']);
        }
        if (isset($user['tagid_list']) && is_array($user['tagid_list'])) {
            $user['tagid_list'] = arr2str($user['tagid_list']);
        }
        if ($appid !== '') {
            $user['appid'] = $appid;
        }
        unset($user['privilege'], $user['groupid']);
        foreach ($user as $k => $v) {
            if ($v === '') {
                unset($user[$k]);
            }
        }
        Library::$sapp->event->trigger('WechatFansUpdate', $user);
        return (bool)WechatFans::mUpdate($user, 'openid');
    }

    /**
     * 获取粉丝信息.
     */
    public static function get(string $openid): array
    {
        return WechatFans::mk()->where(['openid' => $openid])->findOrEmpty()->toArray();
    }
}
