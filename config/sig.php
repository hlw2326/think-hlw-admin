<?php
/**
 * API 请求签名配置 
 *
 * 前端规则（@hlw-uni/mp-vue setupInterceptors）：
 *   1. 取 URL query，按 `&` 拆成 key=value 对
 *   2. 整个 pair 字符串字典序排序
 *   3. 排序后用 `&` 拼回，末尾再加一个 `&`
 *   4. 计算 md5(signStr + secret) → 追加 `&sig=xxx`
 *
 * 后端按相同规则重算 sig 比对，失败直接 4xx。
 *
 * 注意：secret 必须跟 uni/.env.dev / .env.prod 的 VITE_SIG_SECRET 完全一致，
 * 任何一端改动后另一端要同步，否则所有请求 4xx。
 */
return [
    // 校验开关
    // - true:  enabled=true 且 secret 非空时启用
    // - false: 完全跳过（迁移 / 排查时临时关）
    'enabled' => true,

    // 签名密钥；改动后立即同步到前端 uni/.env.dev / .env.prod 的 VITE_SIG_SECRET
    'secret'  => 'your-production-secret-key-here',
];
