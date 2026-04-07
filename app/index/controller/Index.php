<?php

namespace app\index\controller;

use hlw2326\collect\Config;
use hlw2326\collect\adapter\dy\Dy;
use hlw2326\collect\adapter\ks\Ks;
use hlw2326\collect\adapter\bili\Bili;
use hlw2326\collect\adapter\xhs\Xhs;
use hlw2326\collect\Collect;
use think\admin\Controller;
use think\response\Json;

class Index extends Controller
{
    public function index(): void
    {
        echo php_sapi_name();
        $this->fetch();
    }

    /**
     * think-collect-api 调用示例
     * @auth true
     */
    public function index3(): Json
    {
        // ═══════════════════════════════════════════════════════
        // 示例 1：最简调用 — 直接传 Cookie 字符串
        // ═══════════════════════════════════════════════════════
        $cookie = 'sessionid=xxx; passport_csrf_token=xxx';
        $res1 = Dy::web($cookie)->getUserInfo('sec_uid_xxx');

        // ═══════════════════════════════════════════════════════
        // 示例 2：传入配置数组（cookie / timeout / headers / proxy）
        // ═══════════════════════════════════════════════════════
        $res2 = Dy::web([
            'cookie'  => $cookie,
            'timeout' => 20,
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ])->getFeeds();

        // ═══════════════════════════════════════════════════════
        // 示例 3：链式调用 — 默认实例配置 + withXxx 累积
        // ═══════════════════════════════════════════════════════
        $api = Dy::web($cookie)
            ->withHttpProxy('127.0.0.1', 7890)       // HTTP 代理
            ->withHeaders(['Accept-Language' => 'zh-CN']) // 追加请求头
            ->withTimeout(30);                          // 超时 30 秒

        $res3 = $api->getUserInfo('sec_uid_xxx');

        // ═══════════════════════════════════════════════════════
        // 示例 4：SOCKS5 代理快捷设置
        // ═══════════════════════════════════════════════════════
        $res4 = Ks::web($cookie)
            ->withSocks5Proxy('192.168.1.100', 1080, 'user', 'pwd')
            ->getUserInfo('user_id_xxx');

        // ═══════════════════════════════════════════════════════
        // 示例 5：本次请求临时换配置（默认配置不变，返回新实例）
        // ═══════════════════════════════════════════════════════
        $baseApi = Dy::web($cookie)->withTimeout(15);
        $res5 = $baseApi->getUserInfo('sec_uid_xxx', [
            'proxy'   => ['type' => 'socks5', 'host' => '127.0.0.1', 'port' => 1080],
            'timeout' => 5,
        ]);

        // ═══════════════════════════════════════════════════════
        // 示例 6：通过 Config 对象精细化配置
        // ═══════════════════════════════════════════════════════
        $config = Config::make()
            ->withCookie($cookie)
            ->withHttpProxy('127.0.0.1', 8080)
            ->withHeaders([
                'Device-Fp' => 'xxx',
                'X-B3-Traceid' => 'xxx',
            ])
            ->withQuery(['count' => 20, 'max_cursor' => 0])
            ->withTimeout(20);

        $res6 = $config->withProxy(['type' => 'http', 'host' => '10.0.0.1', 'port' => 3128]);
        $res6 = Dy::web($config)->getFeeds();

        // ═══════════════════════════════════════════════════════
        // 示例 7：Collect::make 统一入口
        // ═══════════════════════════════════════════════════════
        $res7 = Collect::make('dy', 'web', $cookie)->getUserInfo('sec_uid_xxx');
        $res8 = Collect::make('ks', 'h5', $cookie)->getUserInfo('user_id_xxx');
        $res9 = Collect::make('bili', 'app', $cookie)->getFeeds();

        // ═══════════════════════════════════════════════════════
        // 示例 8：B站/XHS 等其他平台调用
        // ═══════════════════════════════════════════════════════
        $res10 = Bili::web($cookie)->getUserInfo('123456');
        $res11 = Bili::app($cookie)->getFeeds();
        $res12 = Xhs::web($cookie)->getContentInfo('note_id_xxx');

        // ═══════════════════════════════════════════════════════
        // 示例 9：verify 验证 Cookie 有效性
        // ═══════════════════════════════════════════════════════
        $res13 = Dy::web($cookie)->verify();
        if (!$res13['success']) {
            return json(['code' => 0, 'msg' => 'Cookie 已失效：' . $res13['message']]);
        }

        // ═══════════════════════════════════════════════════════
        // 示例 10：直接 HTTP 请求（绕过封装方法，直接调用底层 GET/POST）
        // ═══════════════════════════════════════════════════════
        $raw = Dy::web($cookie)
            ->withHttpProxy('127.0.0.1', 7890)
            ->get('https://www.douyin.com/aweme/v1/web/user/profile/other/', [
                'sec_user_id' => 'sec_uid_xxx',
                'count' => 20,
            ]);

        $raw2 = Dy::web($cookie)->post('https://api.example.com/submit', [
            'data' => ['uid' => '123', 'type' => 'video'],
        ]);

        return json(['code' => 1, 'msg' => 'ok', 'data' => [
            'res1'  => $res1,
            'res5'  => $res5,
            'res6'  => $res6,
            'res7'  => $res7,
            'res13' => $res13,
            'raw'   => $raw,
        ]]);
    }
}
