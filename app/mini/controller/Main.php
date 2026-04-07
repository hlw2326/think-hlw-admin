<?php
declare(strict_types=1);

namespace app\mini\controller;

use app\mini\model\MiniAd;
use app\mini\model\MiniCardLog;
use app\mini\model\MiniCardScore;
use app\mini\model\MiniCardVip;
use app\mini\model\MiniMp;
use app\mini\model\MiniNotice;
use app\mini\model\MiniUser;
use app\mini\model\MiniUserQuery;
use think\admin\Controller;
use think\facade\Cache;

/**
 * 数据概览
 * @class Main
 * @package app\mini\controller
 */
class Main extends Controller
{
    /**
     * 数据概览首页
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        $this->title = '数据概览';
        $data = Cache::remember('mini_dashboard', function () {
            $now = time();

            // ── 用户统计 ──────────────────────────────────────
            $totalUsers  = MiniUser::mk()->where('deleted', 0)->count();
            $totalScore  = MiniUser::mk()->where('deleted', 0)->sum('score');

            // 性别分布
            $genderRaw  = MiniUser::mk()->where('deleted', 0)->field('gender, count(*) as cnt')->group('gender')->select()->toArray();
            $genderMap  = [0 => '未知', 1 => '男', 2 => '女'];
            $genderData = [];
            foreach ($genderRaw as $row) {
                $genderData[] = ['name' => $genderMap[$row['gender']] ?? '未知', 'value' => (int)$row['cnt']];
            }

            // 用户状态分布
            $userStatusData = [
                ['name' => '正常', 'value' => MiniUser::mk()->where('deleted', 0)->where('status', 1)->count()],
                ['name' => '禁用', 'value' => MiniUser::mk()->where('deleted', 0)->where('status', 0)->count()],
            ];

            // ── 广告类型分布 ──────────────────────────────────
            $adTypeRaw  = MiniAd::mk()->field('ad_type, count(*) as cnt')->group('ad_type')->select()->toArray();
            $adTypes    = MiniAd::getAdTypes();
            $adTypeData = [];
            foreach ($adTypeRaw as $row) {
                $adTypeData[] = ['name' => $adTypes[$row['ad_type']]['label'] ?? $row['ad_type'], 'value' => (int)$row['cnt']];
            }

            // 广告按应用分布
            $adByAppRaw  = MiniAd::mk()->field('appid, count(*) as cnt')->group('appid')->select()->toArray();
            $mpNames     = MiniMp::mk()->column('name', 'appid');
            $adByAppData = [];
            foreach ($adByAppRaw as $row) {
                $adByAppData[] = ['name' => $mpNames[$row['appid']] ?? $row['appid'], 'value' => (int)$row['cnt']];
            }

            // ── 通知类型分布 ──────────────────────────────────
            $noticeTypeRaw  = MiniNotice::mk()->field('type, count(*) as cnt')->group('type')->select()->toArray();
            $noticeTypes    = MiniNotice::getTypes();
            $noticeTypeData = [];
            foreach ($noticeTypeRaw as $row) {
                $noticeTypeData[] = ['name' => $noticeTypes[$row['type']]['label'] ?? $row['type'], 'value' => (int)$row['cnt']];
            }

            // ── 卡密统计 ──────────────────────────────────────
            $totalScoreCards  = MiniCardScore::mk()->count();
            $activeScoreCards = MiniCardScore::mk()->where('status', 1)->count();
            $usedScoreCards   = MiniCardLog::mk()->where('card_type', MiniCardLog::TYPE_SCORE)->count();
            $voidScoreCards   = MiniCardScore::mk()->where('status', 0)->count();

            $totalVipCards  = MiniCardVip::mk()->count();
            $activeVipCards = MiniCardVip::mk()->where('status', 1)->count();
            $usedVipCards   = MiniCardLog::mk()->where('card_type', MiniCardLog::TYPE_VIP)->count();
            $voidVipCards   = MiniCardVip::mk()->where('status', 0)->count();

            $scoreCardStatusData = [
                ['name' => '可用', 'value' => $activeScoreCards],
                ['name' => '已用', 'value' => $usedScoreCards],
                ['name' => '禁用', 'value' => $voidScoreCards],
            ];
            $vipCardStatusData = [
                ['name' => '可用', 'value' => $activeVipCards],
                ['name' => '已用', 'value' => $usedVipCards],
                ['name' => '禁用', 'value' => $voidVipCards],
            ];

            // ── 查询平台分布 ──────────────────────────────────
            $queryPlatformRaw  = MiniUserQuery::mk()->field('platform, count(*) as cnt')->group('platform')->select()->toArray();
            $platforms         = MiniUserQuery::getPlatforms();
            $queryPlatformData = [];
            foreach ($queryPlatformRaw as $row) {
                $queryPlatformData[] = ['name' => $platforms[$row['platform']]['label'] ?? $row['platform'], 'value' => (int)$row['cnt']];
            }

            // ── 30天注册趋势 ──────────────────────────────────
            $trendDates  = [];
            $trendCounts = [];
            for ($i = 29; $i >= 0; $i--) {
                $date          = date('Y-m-d', strtotime("-{$i} days"));
                $trendDates[]  = $date;
                $trendCounts[] = MiniUser::mk()->where('deleted', 0)
                    ->whereTime('create_at', 'between', [$date . ' 00:00:00', $date . ' 23:59:59'])
                    ->count();
            }
            $trendData = json_encode(['dates' => $trendDates, 'counts' => $trendCounts], JSON_UNESCAPED_UNICODE);

            // ── 12个月用户增长趋势 ────────────────────────────
            $monthlyMonths = [];
            $monthlyCounts = [];
            for ($i = 11; $i >= 0; $i--) {
                $month           = date('Y-m', strtotime("-{$i} months"));
                $monthlyMonths[] = $month;
                $monthlyCounts[] = MiniUser::mk()->where('deleted', 0)
                    ->whereTime('create_at', 'between', [
                        $month . '-01 00:00:00',
                        date('Y-m-t 23:59:59', strtotime($month . '-01')),
                    ])
                    ->count();
            }
            $monthlyTrendData = json_encode(['months' => $monthlyMonths, 'counts' => $monthlyCounts], JSON_UNESCAPED_UNICODE);

            return [
                'totalUsers'         => $totalUsers,
                'vipUsers'           => MiniUser::mk()->where('deleted', 0)->where('vip_time', '>', $now)->count(),
                'newUsersToday'      => MiniUser::mk()->where('deleted', 0)->whereTime('create_at', '>=', date('Y-m-d'))->count(),
                'newUsersMonth'      => MiniUser::mk()->where('deleted', 0)->whereTime('create_at', '>=', date('Y-m-01'))->count(),
                'usersWithPhone'     => MiniUser::mk()->where('deleted', 0)->where('phone', '<>', '')->count(),
                'activeUsersToday'   => MiniUser::mk()->where('deleted', 0)->whereTime('login_at', '>=', date('Y-m-d'))->count(),
                'activeUsersMonth'   => MiniUser::mk()->where('deleted', 0)->whereTime('login_at', '>=', date('Y-m-01'))->count(),
                'vipExpireSoon'      => MiniUser::mk()->where('deleted', 0)->where('vip_time', '>', $now)->where('vip_time', '<=', $now + 86400 * 7)->count(),
                'vipExpired'         => MiniUser::mk()->where('deleted', 0)->where('vip_time', '>', 0)->where('vip_time', '<=', $now)->count(),
                'totalScore'         => $totalScore,
                'avgScore'           => $totalUsers > 0 ? round($totalScore / $totalUsers, 1) : 0,
                'totalApps'          => MiniMp::mk()->count(),
                'activeApps'         => MiniMp::mk()->where('status', 1)->count(),
                'inactiveApps'       => MiniMp::mk()->where('status', 0)->count(),
                'totalAds'           => MiniAd::mk()->count(),
                'activeAds'          => MiniAd::mk()->where('status', 1)->count(),
                'inactiveAds'        => MiniAd::mk()->where('status', 0)->count(),
                'totalNotices'       => MiniNotice::mk()->count(),
                'publishedNotices'   => MiniNotice::mk()->where('status', 1)->count(),
                'draftNotices'       => MiniNotice::mk()->where('status', 0)->count(),
                'totalScoreCards'    => $totalScoreCards,
                'activeScoreCards'   => $activeScoreCards,
                'usedScoreCards'     => $usedScoreCards,
                'unusedScoreCards'   => max(0, $totalScoreCards - $usedScoreCards),
                'voidScoreCards'     => $voidScoreCards,
                'totalVipCards'      => $totalVipCards,
                'activeVipCards'     => $activeVipCards,
                'usedVipCards'       => $usedVipCards,
                'unusedVipCards'     => max(0, $totalVipCards - $usedVipCards),
                'voidVipCards'       => $voidVipCards,
                'totalQueries'       => MiniUserQuery::mk()->count(),
                'todayQueries'       => MiniUserQuery::mk()->whereTime('create_at', '>=', date('Y-m-d'))->count(),
                'monthQueries'       => MiniUserQuery::mk()->whereTime('create_at', '>=', date('Y-m-01'))->count(),
                'refundedQueries'    => MiniUserQuery::mk()->where('refunded', 1)->count(),
                'trendData'          => $trendData,
                'monthlyTrendData'   => $monthlyTrendData,
                'genderData'         => json_encode($genderData, JSON_UNESCAPED_UNICODE),
                'userStatusData'     => json_encode($userStatusData, JSON_UNESCAPED_UNICODE),
                'adTypeData'         => json_encode($adTypeData, JSON_UNESCAPED_UNICODE),
                'adByAppData'        => json_encode($adByAppData, JSON_UNESCAPED_UNICODE),
                'noticeTypeData'     => json_encode($noticeTypeData, JSON_UNESCAPED_UNICODE),
                'scoreCardStatusData'=> json_encode($scoreCardStatusData, JSON_UNESCAPED_UNICODE),
                'vipCardStatusData'  => json_encode($vipCardStatusData, JSON_UNESCAPED_UNICODE),
                'queryPlatformData'  => json_encode($queryPlatformData, JSON_UNESCAPED_UNICODE),
            ];
        }, 60);

        // ── 传递给视图 ────────────────────────────────────
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }

        $this->fetch('index');
    }
}
