<?php
declare(strict_types=1);

namespace plugin\base\service;

use plugin\base\model\BaseUser;
use plugin\base\model\BaseUserVipLog;
use think\facade\Db;

/**
 * 会员服务
 * @class UserVipService
 * @package plugin\base\service
 */
class UserVipService
{

    /**
     * 是否正在由服务处理会员变更（用于防止模型事件重复记录日志）
     * @var bool
     */
    public static $changing = false;

    /**
     * 变更用户会员天数
     *
     * @param int $user_id   用户ID
     * @param int $days      变更天数（正数增加，负数减少）
     * @param string $source 会员变更来源/操作类型 (例如：admin, consume, register, invite)
     * @param string $remark 备注说明
     * @return array         返回状态 ['status' => bool, 'msg' => string, 'data' => array]
     */
    public static function change(int $user_id, int $days, string $source, string $remark = ''): array
    {
        if ($days === 0) {
            return ['status' => true, 'msg' => '天数未发生变化', 'data' => []];
        }

        self::$changing = true;
        try {
            return Db::transaction(function () use ($user_id, $days, $source, $remark) {
                // 1. 获取并锁定用户记录，防止并发冲突
                $user = BaseUser::mk()->where(['id' => $user_id, 'deleted' => 0])->lock(true)->findOrEmpty();
                if ($user->isEmpty()) {
                    throw new \RuntimeException('用户不存在');
                }
                if (intval($user->status) !== 1) {
                    throw new \RuntimeException('用户账号已被禁用');
                }

                $before = intval($user->vip_time);
                $now = time();

                if ($days > 0) {
                    $base_time = $before > $now ? $before : $now;
                    $after = $base_time + ($days * 86400);
                } else {
                    $sub_days = abs($days);
                    if ($before <= $now) {
                        $after = 0;
                    } else {
                        $after = $before - ($sub_days * 86400);
                        if ($after < $now) {
                            $after = 0;
                        }
                    }
                }

                // 2. 更新用户会员到期时间
                $user->save(['vip_time' => $after]);

                // 3. 记录变更日志
                self::log($user_id, $days, $before, $after, $source, $remark);

                return [
                    'status' => true,
                    'msg'    => '会员时间变更成功',
                    'data'   => [
                        'user_id' => $user_id,
                        'before'  => $before,
                        'after'   => $after,
                        'days'    => $days,
                    ]
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => false,
                'msg'    => $e->getMessage(),
                'data'   => []
            ];
        } finally {
            self::$changing = false;
        }
    }

    /**
     * 写入会员记录日志
     *
     * @param int $user_id   用户ID
     * @param int $days      变更天数
     * @param int $before    变更前会员到期时间戳
     * @param int $after     变更后会员到期时间戳
     * @param string $source 会员变更来源
     * @param string $remark 备注说明
     * @return bool
     */
    public static function log(int $user_id, int $days, int $before, int $after, string $source, string $remark = ''): bool
    {
        $log = BaseUserVipLog::mk();
        return $log->save([
            'user_id'         => $user_id,
            'source'          => $source,
            'days'            => $days,
            'before_vip_time' => $before,
            'after_vip_time'  => $after,
            'remark'          => $remark,
            'status'          => 1,
        ]);
    }
}
