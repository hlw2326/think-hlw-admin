<?php

declare(strict_types=1);

namespace plugin\base\controller\api\v1;

use plugin\base\model\BaseUser;
use plugin\base\service\UserScoreService;

/**
 * 需授权登录的 API 基类控制器
 */
class Auth extends Base
{
    /**
     * 控制器初始化并校验登录状态
     *
     * @return void
     */
    protected function initialize(): void
    {
        parent::initialize();
        $this->checkToken();
    }

    /**
     * 检查当前用户是否 VIP，非 VIP 则扣除对应积分
     *
     * @param string $config_key 配置项名称（如 'search_score', 'weight_score'）
     * @param string $remark     扣除积分说明
     * @param int    $default_score 默认扣除积分
     * @return bool 返回当前用户是否为 VIP
     */
    protected function checkScore(string $config_key = 'search_score', string $remark = '查询扣除积分', int $default_score = 10): bool
    {
        $is_vip = intval($this->user->vip_time ?? 0) > time();
        if (!$is_vip) {
            $score = intval(sysconf("base.{$config_key}") !== '' ? sysconf("base.{$config_key}") : $default_score);
            if ($score > 0) {
                $score_res = UserScoreService::change((int) $this->user->id, -$score, 'search', $remark);
                if (!($score_res['status'] ?? false)) {
                    $this->error('操作失败：' . ($score_res['msg'] ?? '积分不足'));
                }
            }
        }

        return $is_vip;
    }
}
