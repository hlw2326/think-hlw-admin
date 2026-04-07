<?php
declare(strict_types=1);

namespace app\mini\service;

use app\mini\model\MiniCardScore;
use app\mini\model\MiniCardVip;
use app\mini\model\MiniCardLog;
use app\mini\model\MiniUser;
use app\mini\model\MiniUserScoreLog;
use think\admin\Service;

/**
 * 卡密使用服务
 * @class CardUseService
 * @package plugin\qz\service
 */
class CardUseService extends Service
{
    /**
     * 使用积分卡密
     */
    public static function useScoreCard(string $cardCode, int $userId): array
    {
        $canUse = MiniCardLog::canUseCard(MiniCardLog::TYPE_SCORE, $cardCode, $userId);
        if (!$canUse['can']) {
            return ['success' => false, 'message' => $canUse['reason']];
        }

        $card = MiniCardScore::mk()->where(['card_code' => $cardCode])->findOrEmpty();
        if (!$card->isExists()) {
            return ['success' => false, 'message' => '卡密不存在！'];
        }

        try {
            \think\facade\Db::startTrans();

            $logResult = MiniCardLog::recordUse(
                MiniCardLog::TYPE_SCORE,
                $card->id,
                $cardCode,
                $userId,
                ['score' => $card->score]
            );

            if (!$logResult) {
                throw new \Exception('记录使用日志失败');
            }

            $user = MiniUser::mk()->where(['id' => $userId])->findOrEmpty();
            if (!$user->isExists()) {
                throw new \Exception('用户不存在');
            }
            $newBalance = intval($user->score) + intval($card->score);
            MiniUser::mk()->where(['id' => $userId])->update(['score' => $newBalance]);

            MiniUserScoreLog::record(
                $userId,
                intval($card->score),
                $newBalance,
                MiniUserScoreLog::SOURCE_CARD_SCORE,
                intval($card->id),
                '积分卡密充值：' . $cardCode
            );

            \think\facade\Db::commit();

            $stats = MiniCardLog::getCardUseStats($cardCode, MiniCardLog::TYPE_SCORE);

            return [
                'success' => true,
                'message' => '使用成功',
                'data'    => [
                    'score'        => $card->score,
                    'use_count'    => $stats['total_uses'],
                    'unique_users' => $stats['unique_users'],
                ],
            ];
        } catch (\Exception $e) {
            \think\facade\Db::rollback();
            return ['success' => false, 'message' => '使用失败：' . $e->getMessage()];
        }
    }

    /**
     * 使用会员卡密
     */
    public static function useVipCard(string $cardCode, int $userId): array
    {
        $canUse = MiniCardLog::canUseCard(MiniCardLog::TYPE_VIP, $cardCode, $userId);
        if (!$canUse['can']) {
            return ['success' => false, 'message' => $canUse['reason']];
        }

        $card = MiniCardVip::mk()->where(['card_code' => $cardCode])->findOrEmpty();
        if (!$card->isExists()) {
            return ['success' => false, 'message' => '卡密不存在！'];
        }

        try {
            \think\facade\Db::startTrans();

            $logResult = MiniCardLog::recordUse(
                MiniCardLog::TYPE_VIP,
                $card->id,
                $cardCode,
                $userId,
                ['vip_days' => $card->vip_days]
            );

            if (!$logResult) {
                throw new \Exception('记录使用日志失败');
            }

            \think\facade\Db::commit();

            $stats = MiniCardLog::getCardUseStats($cardCode, MiniCardLog::TYPE_VIP);

            return [
                'success' => true,
                'message' => '使用成功',
                'data'    => [
                    'vip_days'     => $card->vip_days,
                    'use_count'    => $stats['total_uses'],
                    'unique_users' => $stats['unique_users'],
                ],
            ];
        } catch (\Exception $e) {
            \think\facade\Db::rollback();
            return ['success' => false, 'message' => '使用失败：' . $e->getMessage()];
        }
    }

    /**
     * 获取用户卡密使用记录
     */
    public static function getUserUseHistory(int $userId, int $cardType = 0, int $page = 1, int $limit = 20): array
    {
        $query = MiniCardLog::mk()->where(['user_id' => $userId]);
        if ($cardType > 0) {
            $query->where(['card_type' => $cardType]);
        }
        $total = $query->count();
        $logs  = $query->order('id', 'desc')->page($page, $limit)->select()->toArray();
        return ['total' => $total, 'page' => $page, 'limit' => $limit, 'logs' => $logs];
    }

    /**
     * 获取卡密使用统计
     */
    public static function getCardStatistics(string $cardCode, int $cardType): array
    {
        $stats      = MiniCardLog::getCardUseStats($cardCode, $cardType);
        $recentUses = MiniCardLog::mk()
            ->where(['card_code' => $cardCode, 'card_type' => $cardType])
            ->order('id', 'desc')
            ->limit(10)
            ->select()
            ->toArray();
        return [
            'total_uses'   => $stats['total_uses'],
            'unique_users' => $stats['unique_users'],
            'recent_uses'  => $recentUses,
        ];
    }
}
