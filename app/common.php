<?php

if (!function_exists('sig')) {
    /**
     * 构建 URL sig 签名（与前端 @hlw-uni/mp-vue 的 buildSignString 算法对齐）。
     *
     * 前端：md5(sortedQueryPairs.join('&') + '&' + secret)，pair 按 raw 字符串字典序排。
     * 后端：拿 raw QUERY_STRING 按相同规则重算。
     *
     * 配置在 config/sig.php；enabled=false 或 secret 为空时返回空字符串。
     */
    function sig(string $queryString, array $reserved = ['sig', 's', '_t']): string
    {
        $secret = (string)config('sig.secret', '');
        if (!config('sig.enabled', false) || $secret === '') {
            return '';
        }

        $pairs = array_filter(explode('&', $queryString), static function (string $pair) use ($reserved): bool {
            if ($pair === '') {
                return false;
            }

            $eqPos = strpos($pair, '=');
            $key = $eqPos === false ? $pair : substr($pair, 0, $eqPos);

            return !in_array($key, $reserved, true);
        });

        sort($pairs, SORT_STRING);
        $signStr = implode('&', $pairs) . '&';

        return md5($signStr . $secret);
    }
}

if (!function_exists('verify_sig')) {
    /**
     * 校验 URL sig 签名。
     *
     * @return array{0: bool, 1: string, 2: array}
     */
    function verify_sig(\think\Request $request): array
    {
        $expected = sig((string)$request->server('QUERY_STRING', ''));
        if ($expected === '') {
            return [true, '', []];
        }

        $sig = (string)$request->get('sig', '');
        if ($sig === '') {
            return [false, '缺少签名', []];
        }

        return hash_equals($expected, $sig) ? [true, '', []] : [false, '签名校验失败', []];
    }
}
