<?php

declare(strict_types=1);

namespace plugin\base\service;

use plugin\base\model\BaseMp;
use plugin\base\model\BaseUser;

/**
 * 广告服务
 */
class AdService
{
    /**
     * 获取小程序广告配置
     *
     * @param BaseMp $mp
     * @return array
     */
    public static function config(BaseMp $mp): array
    {
        $global_on = self::enabled($mp, 'ad_global_enabled');

        $unit = static function (string $type) use ($mp, $global_on): string {
            if (!$global_on) {
                return '';
            }
            if (!self::enabled($mp, "ad_enabled_{$type}")) {
                return '';
            }
            return (string) ($mp->{"{$type}_unit_id"} ?? '');
        };

        return [
            'ad_global_enabled' => $global_on ? 1 : 0,
            'ad_enabled_banner' => self::enabled($mp, 'ad_enabled_banner') ? 1 : 0,
            'ad_enabled_grid' => self::enabled($mp, 'ad_enabled_grid') ? 1 : 0,
            'ad_enabled_custom' => self::enabled($mp, 'ad_enabled_custom') ? 1 : 0,
            'ad_enabled_video' => self::enabled($mp, 'ad_enabled_video') ? 1 : 0,
            'ad_enabled_reward' => self::enabled($mp, 'ad_enabled_reward') ? 1 : 0,
            'ad_enabled_popup' => self::enabled($mp, 'ad_enabled_popup') ? 1 : 0,
            'banner_unit_id' => $unit('banner'),
            'grid_unit_id' => $unit('grid'),
            'custom_unit_id' => $unit('custom'),
            'video_unit_id' => $unit('video'),
            'reward_unit_id' => $unit('reward'),
            'popup_unit_id' => $unit('popup'),
            'vip_no_ad' => (int) ($mp->vip_no_ad ?? 0),
        ];
    }

    /**
     * 奖励发放（如观看激励视频广告）
     *
     * @param int $user_id
     * @return array
     */
    public static function grant(int $user_id): array
    {
        $user = BaseUser::mk()->where('id', $user_id)->find();

        if (empty($user)) {
            return ['state' => false, 'msg' => '发放失败'];
        }

        $reward_score = intval(sysconf('base.ad_reward_score') !== '' ? sysconf('base.ad_reward_score') : 10);
        $res = UserScoreService::change($user_id, $reward_score, 'video', '观看广告获得奖励积分');

        return [
            'state' => true,
            'msg' => '观看广告奖励已发放',
            'data' => [
                'reward' => 1,
                'balance' => 0,
            ],
        ];
    }

    /**
     * 判断指定广告配置项是否开启
     *
     * @param BaseMp $mp
     * @param string $field
     * @return bool
     */
    private static function enabled(BaseMp $mp, string $field): bool
    {
        $value = $mp->{$field} ?? 1;
        if ($value === '' || $value === null) {
            return true;
        }
        return intval($value) === 1;
    }
}
