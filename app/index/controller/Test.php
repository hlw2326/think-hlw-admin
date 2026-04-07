<?php
namespace app\index\controller;

use app\mini\model\MiniUserScoreLog;
use app\mini\model\MiniUserToken;
use app\mini\service\QueryService;
use think\admin\Controller;
use think\admin\extend\CodeExtend;
use think\response\Json;

class Test extends Controller
{

    public function seedScoreLog(): Json
    {
        $userId  = 1;
        $balance = 500;

        $fixtures = [
            ['change' => 200,  'source' => MiniUserScoreLog::SOURCE_CARD_SCORE,   'source_id' => 1,  'remark' => '积分卡密充值：CARD2024001',  'admin_id' => 0],
            ['change' => 100,  'source' => MiniUserScoreLog::SOURCE_CARD_SCORE,   'source_id' => 2,  'remark' => '积分卡密充值：CARD2024002',  'admin_id' => 0],
            ['change' => -20,  'source' => MiniUserScoreLog::SOURCE_QUERY_COST,   'source_id' => 0,  'remark' => '查询扣除：douyin',           'admin_id' => 0],
            ['change' => -20,  'source' => MiniUserScoreLog::SOURCE_QUERY_COST,   'source_id' => 0,  'remark' => '查询扣除：kuaishou',         'admin_id' => 0],
            ['change' => 50,   'source' => MiniUserScoreLog::SOURCE_ADMIN_ADJUST, 'source_id' => 0,  'remark' => '管理员手动调整',             'admin_id' => 1],
            ['change' => -20,  'source' => MiniUserScoreLog::SOURCE_QUERY_COST,   'source_id' => 0,  'remark' => '查询扣除：bilibili',         'admin_id' => 0],
            ['change' => 20,   'source' => MiniUserScoreLog::SOURCE_QUERY_REFUND, 'source_id' => 3,  'remark' => '查询退积分：链接无效退还',   'admin_id' => 1],
            ['change' => -20,  'source' => MiniUserScoreLog::SOURCE_QUERY_COST,   'source_id' => 0,  'remark' => '查询扣除：weibo',            'admin_id' => 0],
            ['change' => 300,  'source' => MiniUserScoreLog::SOURCE_CARD_SCORE,   'source_id' => 3,  'remark' => '积分卡密充值：CARD2024003',  'admin_id' => 0],
            ['change' => -20,  'source' => MiniUserScoreLog::SOURCE_QUERY_COST,   'source_id' => 0,  'remark' => '查询扣除：xiaohongshu',      'admin_id' => 0],
            ['change' => -100, 'source' => MiniUserScoreLog::SOURCE_ADMIN_ADJUST, 'source_id' => 0,  'remark' => '管理员手动扣除：违规处理',   'admin_id' => 1],
            ['change' => 20,   'source' => MiniUserScoreLog::SOURCE_QUERY_REFUND, 'source_id' => 5,  'remark' => '查询退积分：用户申诉通过',   'admin_id' => 1],
        ];

        foreach ($fixtures as $item) {
            $balance += $item['change'];
            MiniUserScoreLog::record(
                $userId,
                $item['change'],
                $balance,
                $item['source'],
                $item['source_id'],
                $item['remark'],
                $item['admin_id']
            );
        }

        return json(['msg' => 'ok', 'count' => count($fixtures), 'final_balance' => $balance]);
    }

    public function seedQuery(): Json
    {
        $fixtures = [
            ['platform' => 'douyin',      'url' => 'https://v.douyin.com/abc123/'],
            ['platform' => 'douyin',      'url' => 'https://v.douyin.com/xyz456/'],
            ['platform' => 'douyin',      'url' => 'https://www.douyin.com/user/MS4wLjAB'],
            ['platform' => 'kuaishou',    'url' => 'https://www.kuaishou.com/profile/3x9y2z'],
            ['platform' => 'kuaishou',    'url' => 'https://v.kuaishou.com/abc789'],
            ['platform' => 'bilibili',    'url' => 'https://space.bilibili.com/123456'],
            ['platform' => 'bilibili',    'url' => 'https://space.bilibili.com/789012'],
            ['platform' => 'weibo',       'url' => 'https://weibo.com/u/1234567890'],
            ['platform' => 'weibo',       'url' => 'https://weibo.com/n/testuser'],
            ['platform' => 'xiaohongshu', 'url' => 'https://www.xiaohongshu.com/user/profile/abc123'],
            ['platform' => 'other',       'url' => 'https://example.com/user/test'],
        ];

        $results = [];
        foreach ($fixtures as $item) {
            $success = rand(0, 4) > 0;
            $ret = QueryService::record(
                1,
                $item['platform'],
                $item['url'],
                $success ? ['name' => '测试账号', 'fans' => rand(100, 99999)] : [],
                $success ? '' : '链接解析失败'
            );
            $results[] = $ret;
        }

        $ok   = count(array_filter($results, fn($r) => $r['success']));
        $fail = count($results) - $ok;

        return json(['msg' => 'ok', 'total' => count($results), 'success' => $ok, 'fail' => $fail]);
    }

    /**
     * 生成用户登录 Token 测试数据
     */
    public function seedToken(): Json
    {
        $userIds = [1, 2, 3];
        $fixtures = [
            [
                'device_model'  => 'iPhone 15 Pro',
                'device_system' => 'iOS 17.3.1',
                'screen_width'   => 393,
                'screen_height'  => 852,
                'sdk_version'   => '3.2.2',
                'app_version'   => '1.9.8',
                'app_channel'   => 'product',
                'client_ip'     => '117.136.45.201',
            ],
            [
                'device_model'  => 'Xiaomi 14 Pro',
                'device_system' => 'Android 14',
                'screen_width'   => 412,
                'screen_height'  => 915,
                'sdk_version'   => '3.2.1',
                'app_version'   => '1.9.7',
                'app_channel'   => 'huawei',
                'client_ip'     => '223.104.18.55',
            ],
            [
                'device_model'  => 'HUAWEI Mate 60',
                'device_system' => 'Android 13',
                'screen_width'   => 430,
                'screen_height'  => 932,
                'sdk_version'   => '3.2.0',
                'app_version'   => '1.9.6',
                'app_channel'   => 'oppo',
                'client_ip'     => '36.40.75.122',
            ],
            [
                'device_model'  => 'vivo X100 Pro',
                'device_system' => 'Android 14',
                'screen_width'   => 480,
                'screen_height'  => 1040,
                'sdk_version'   => '3.1.9',
                'app_version'   => '1.9.5',
                'app_channel'   => 'xiaomi',
                'client_ip'     => '180.97.33.10',
            ],
            [
                'device_model'  => 'iPhone 14',
                'device_system' => 'iOS 17.2.0',
                'screen_width'   => 390,
                'screen_height'  => 844,
                'sdk_version'   => '3.2.2',
                'app_version'   => '1.9.8',
                'app_channel'   => 'product',
                'client_ip'     => '112.97.56.88',
            ],
            [
                'device_model'  => 'OPPO Find X7',
                'device_system' => 'Android 14',
                'screen_width'   => 430,
                'screen_height'  => 920,
                'sdk_version'   => '3.2.1',
                'app_version'   => '1.9.7',
                'app_channel'   => 'vivo',
                'client_ip'     => '59.36.12.44',
            ],
            [
                'device_model'  => 'Samsung Galaxy S24 Ultra',
                'device_system' => 'Android 14',
                'screen_width'   => 500,
                'screen_height'  => 1100,
                'sdk_version'   => '3.2.0',
                'app_version'   => '1.9.6',
                'app_channel'   => 'samsung',
                'client_ip'     => '27.19.190.33',
            ],
            [
                'device_model'  => 'realme GT5 Pro',
                'device_system' => 'Android 14',
                'screen_width'   => 450,
                'screen_height'  => 980,
                'sdk_version'   => '3.1.8',
                'app_version'   => '1.9.4',
                'app_channel'   => 'jd',
                'client_ip'     => '119.147.22.17',
            ],
        ];

        $inserted = 0;
        $now = time();

        foreach ($userIds as $userId) {
            foreach ($fixtures as $idx => $device) {
                $minutesAgo = rand(0, 30 * 24 * 60);
                $loginAt = date('Y-m-d H:i:s', $now - $minutesAgo);
                $status = rand(0, 9) > 2 ? 1 : 0;
                $token = CodeExtend::random(40, 3) . CodeExtend::uuid();

                MiniUserToken::mk()->insert([
                    'user_id'       => (string) $userId,
                    'token'         => $token,
                    'device_model'  => $device['device_model'],
                    'device_system' => $device['device_system'],
                    'screen_width'  => $device['screen_width'],
                    'screen_height' => $device['screen_height'],
                    'sdk_version'   => $device['sdk_version'],
                    'app_version'   => $device['app_version'],
                    'app_channel'   => $device['app_channel'],
                    'client_ip'     => $device['client_ip'],
                    'login_at'      => $loginAt,
                    'expire_time'   => 0,
                    'status'        => $status,
                ]);
                $inserted++;
            }
        }

        return json(['msg' => 'ok', 'count' => $inserted, 'user_ids' => $userIds]);
    }
}
