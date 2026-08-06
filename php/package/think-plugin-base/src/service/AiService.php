<?php

declare(strict_types=1);

namespace plugin\base\service;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Throwable;

/**
 * AI 大模型服务
 */
class AiService
{
    /**
     * 供应商预设配置列表
     *
     * @return array
     */
    public static function providers(): array
    {
        return [
            'qwen' => [
                'label' => '通义千问',
                'base_url' => 'https://dashscope.aliyuncs.com/compatible-mode/v1',
                'model' => 'qwen3.7-max',
                'note' => '阿里云百炼 / DashScope OpenAI 兼容接口',
                'models' => ['qwen3.7-max', 'qwen3.6-72b-instruct', 'qwen3.6-14b-instruct', 'qwen-turbo'],
            ],
            'doubao' => [
                'label' => '豆包',
                'base_url' => 'https://ark.cn-beijing.volces.com/api/v3',
                'model' => 'seed-2.0-pro',
                'note' => '火山方舟 OpenAI 兼容接口，model 可填写模型名或接入点 ID',
                'models' => ['seed-2.0-pro', 'seed-2.0-lite', 'seed-2.0-mini', 'doubao-seed-1-6-250615'],
            ],
            'deepseek' => [
                'label' => 'DeepSeek',
                'base_url' => 'https://api.deepseek.com',
                'model' => 'deepseek-v4-pro',
                'note' => 'DeepSeek OpenAI 兼容接口，调用接口需要 DeepSeek API Key',
                'models' => ['deepseek-v4-pro', 'deepseek-v4-flash', 'deepseek-chat', 'deepseek-reasoner'],
            ],
            'kimi' => [
                'label' => 'Kimi',
                'base_url' => 'https://api.moonshot.ai/v1',
                'model' => 'kimi-k2.6',
                'note' => '月之暗面 Moonshot / Kimi OpenAI 兼容接口',
                'models' => ['kimi-k2.6', 'kimi-k2.5', 'kimi-k2-thinking-turbo', 'moonshot-v1-8k'],
            ],
            'hunyuan' => [
                'label' => '腾讯混元',
                'base_url' => 'https://api.hunyuan.cloud.tencent.com/v1',
                'model' => 'hunyuan-hy3-preview',
                'note' => '腾讯混元 OpenAI 兼容接口',
                'models' => ['hunyuan-hy3-preview', 'hunyuan-large', 'hunyuan-standard'],
            ],
            'qianfan' => [
                'label' => '百度千帆/文心',
                'base_url' => 'https://qianfan.baidubce.com/v2',
                'model' => 'ernie-5.1-turbo-128k',
                'note' => '百度智能云千帆 OpenAI 兼容接口',
                'models' => ['ernie-5.1-turbo-128k', 'ernie-5.1-8k-preview', 'ernie-4.5-turbo-128k', 'ernie-4.0-turbo-8k'],
            ],
            'zhipu' => [
                'label' => '智谱 GLM',
                'base_url' => 'https://open.bigmodel.cn/api/paas/v4',
                'model' => 'glm-5.1-plus',
                'note' => '智谱大模型开放平台 OpenAI 兼容接口',
                'models' => ['glm-5.1-plus', 'glm-5.1-air', 'glm-5.1-flash', 'glm-4-plus', 'glm-4-flash'],
            ],
            'minimax' => [
                'label' => 'MiniMax',
                'base_url' => 'https://api.minimax.io/v1',
                'model' => 'MiniMax-M2.7',
                'note' => 'MiniMax OpenAI 兼容接口',
                'models' => ['MiniMax-M2.7', 'MiniMax-Text-01', 'MiniMax-M1'],
            ],
            'stepfun' => [
                'label' => '阶跃星辰',
                'base_url' => 'https://api.stepfun.com/v1',
                'model' => 'step-3.7-flash',
                'note' => '阶跃星辰 StepFun OpenAI 兼容接口',
                'models' => ['step-3.7-flash', 'step-2-mini', 'step-1-8k'],
            ],
            'xunfei' => [
                'label' => '讯飞星火',
                'base_url' => 'https://spark-api-open.xf-yun.com/v1',
                'model' => 'generalv3.5',
                'note' => '讯飞星火 OpenAI 兼容接口',
                'models' => ['generalv3.5', 'spark-x2', 'generalv3', '4.0Ultra'],
            ],
            'sensenova' => [
                'label' => '商汤日日新',
                'base_url' => 'https://api.sensenova.cn/compatible-mode/v1',
                'model' => 'SenseChat-5',
                'note' => '商汤日日新 OpenAI 兼容接口',
                'models' => ['SenseChat-5', 'SenseChat-Turbo'],
            ],
            'baichuan' => [
                'label' => '百川智能',
                'base_url' => 'https://api.baichuan-ai.com/v1',
                'model' => 'Baichuan4-Turbo',
                'note' => '百川智能 OpenAI 兼容接口',
                'models' => ['Baichuan4-Turbo', 'Baichuan4-Air'],
            ],
            'yi' => [
                'label' => '零一万物',
                'base_url' => 'https://api.lingyiwanwu.com/v1',
                'model' => 'yi-lightning',
                'note' => '零一万物 OpenAI 兼容接口',
                'models' => ['yi-lightning', 'yi-large', 'yi-medium'],
            ],
            'mimo' => [
                'label' => '小米 MiMo',
                'base_url' => '',
                'model' => '',
                'note' => '小米 MiMo，按开放平台提供的 OpenAI 兼容地址和模型名填写',
                'models' => [],
            ],
            'pangu' => [
                'label' => '华为盘古',
                'base_url' => '',
                'model' => '',
                'note' => '华为盘古，按实际接入网关填写 OpenAI 兼容地址和模型名',
                'models' => [],
            ],
            'tiangong' => [
                'label' => '天工大模型',
                'base_url' => '',
                'model' => '',
                'note' => '昆仑万维天工，按开放平台提供的兼容接口填写',
                'models' => [],
            ],
            'brain360' => [
                'label' => '360 智脑',
                'base_url' => '',
                'model' => '',
                'note' => '360 智脑，按开放平台提供的兼容接口填写',
                'models' => [],
            ],
            'siliconflow' => [
                'label' => '硅基流动',
                'base_url' => 'https://api.siliconflow.cn/v1',
                'model' => 'deepseek-ai/DeepSeek-V3',
                'note' => '国产模型推理平台，可接入多种国产模型',
                'models' => ['deepseek-ai/DeepSeek-V3', 'deepseek-ai/DeepSeek-R1', 'Qwen/Qwen2.5-72B-Instruct'],
            ],
            'openrouter' => [
                'label' => 'OpenRouter',
                'base_url' => 'https://openrouter.ai/api/v1',
                'model' => 'openai/gpt-5.4-mini',
                'note' => 'OpenRouter 聚合模型接口，模型列表可无密钥公开获取，调用模型仍需 API Key',
                'models' => ['openai/gpt-5.4-mini', 'openai/gpt-5.5', 'openai/gpt-5.4', 'deepseek/deepseek-chat'],
            ],
            'openai' => [
                'label' => 'OpenAI (GPT)',
                'base_url' => 'https://api.openai.com/v1',
                'model' => 'gpt-5.5-instant',
                'note' => 'OpenAI 官方接口，国内使用通常需要配置代理地址',
                'models' => ['gpt-5.5-instant', 'gpt-5.5-pro', 'gpt-5.5', 'gpt-4o-mini', 'gpt-4o'],
            ],
            'claude' => [
                'label' => 'Anthropic (Claude)',
                'base_url' => 'https://api.anthropic.com/v1',
                'model' => 'claude-opus-4-8',
                'note' => 'Anthropic 官方接口，使用 OpenAI 兼容格式时可能需要搭配代理网关或中转接口',
                'models' => ['claude-opus-4-8', 'claude-sonnet-4-6', 'claude-haiku-4-5'],
            ],
            'gemini' => [
                'label' => 'Google (Gemini)',
                'base_url' => 'https://generativelanguage.googleapis.com/v1beta/openai',
                'model' => 'gemini-3.5-flash',
                'note' => 'Google Gemini OpenAI 兼容接口，使用官方 OpenAI 格式调用',
                'models' => ['gemini-3.5-flash', 'gemini-3.1-pro', 'gemini-3.1-flash-lite', 'gemini-2.5-flash', 'gemini-2.5-pro', 'gemini-2.5-flash-lite'],
            ],
            'custom' => [
                'label' => '自定义兼容接口',
                'base_url' => '',
                'model' => '',
                'note' => '手动填写任意 OpenAI 兼容 base_url 和模型名',
                'models' => [],
            ],
        ];
    }

    /**
     * 获取 AI 配置并规范化
     *
     * @param array|null $data
     * @return array
     */
    public static function config(?array $data = null): array
    {
        if ($data === null) {
            $data = [
                'enabled'       => sysconf('base.ai_enabled', '0'),
                'provider'      => sysconf('base.ai_provider', 'qwen'),
                'api_key'       => sysconf('base.ai_api_key', ''),
                'base_url'      => sysconf('base.ai_base_url', ''),
                'model'         => sysconf('base.ai_model', ''),
                'temperature'   => sysconf('base.ai_temperature', '0.3'),
                'max_tokens'    => sysconf('base.ai_max_tokens', '1200'),
                'system_prompt' => sysconf('base.ai_system_prompt', '你是一个AI分析助手，请基于输入的数据输出中文分析建议，内容要具体、克制、可执行。'),
            ];
        }

        $providers = self::providers();
        $code = strtolower(trim((string) ($data['provider'] ?? 'qwen')));
        $provider = $providers[$code] ?? $providers['qwen'];

        $base_url = trim((string) ($data['base_url'] ?? ''));
        $model = trim((string) ($data['model'] ?? ''));
        $api_key = trim((string) ($data['api_key'] ?? ''));

        $mask = '';
        if ($api_key !== '') {
            $mask = mb_strlen($api_key) <= 8 ? str_repeat('*', mb_strlen($api_key)) : mb_substr($api_key, 0, 4) . '****' . mb_substr($api_key, -4);
        }

        return [
            'enabled'       => in_array(strtolower((string) ($data['enabled'] ?? '')), ['1', 'true', 'on', 'yes'], true),
            'provider'      => $code,
            'label'         => $provider['label'],
            'api_key'       => $api_key,
            'api_key_mask'  => $mask,
            'base_url'      => rtrim(trim($base_url !== '' ? $base_url : $provider['base_url']), '/'),
            'model'         => $model !== '' ? $model : $provider['model'],
            'temperature'   => max(0.0, min(2.0, round((float) ($data['temperature'] ?? 0.3), 2))),
            'max_tokens'    => max(1, min(128000, (int) ($data['max_tokens'] ?? 1200))),
            'system_prompt' => trim((string) ($data['system_prompt'] ?? '你是一个AI分析助手，请基于输入的数据输出中文分析建议，内容要具体、克制、可执行。')),
            'note'          => $provider['note'],
        ];
    }

    /**
     * 在线或回退获取可用的 AI 模型列表
     *
     * @param array $data
     * @return array
     */
    public static function models(array $data): array
    {
        $config = self::config($data);
        $provider = self::providers()[$config['provider']] ?? [];
        $fallback = array_values(array_unique(array_filter([
            $config['model'],
            $provider['model'] ?? '',
            ...($provider['models'] ?? []),
        ])));

        $base_url = (string) $config['base_url'];
        if ($base_url === '') {
            return ['online' => false, 'models' => $fallback, 'message' => '未配置 Base URL，已显示内置推荐模型', 'endpoint' => ''];
        }

        $endpoint = $base_url . '/models';
        try {
            $headers = ['Accept' => 'application/json'];
            if ($config['api_key'] !== '') {
                $headers['Authorization'] = 'Bearer ' . $config['api_key'];
            }
            $client = new Client(['timeout' => 12, 'http_errors' => true, 'verify' => false]);
            $response = $client->get($endpoint, ['headers' => $headers]);
            $payload = json_decode((string) $response->getBody(), true);
            if (!is_array($payload)) {
                throw new InvalidArgumentException('返回不是 JSON 对象');
            }

            $extracted = [];
            foreach (['data', 'models'] as $key) {
                foreach ($payload[$key] ?? [] as $item) {
                    $id = is_string($item) ? $item : ($item['id'] ?? $item['name'] ?? '');
                    if ($id !== '') $extracted[] = $id;
                }
            }

            $list = array_values(array_unique(array_filter([...$extracted, ...$fallback])));
            return ['online' => true, 'models' => $list, 'message' => '已获取模型列表', 'endpoint' => $endpoint];
        } catch (Throwable $e) {
            return ['online' => false, 'models' => $fallback, 'message' => '获取模型列表失败，已显示推荐模型：' . $e->getMessage(), 'endpoint' => $endpoint];
        }
    }

    /**
     * 测试 AI 模型连通性
     *
     * @param array $data
     * @return array
     */
    public static function test(array $data): array
    {
        $config = self::config($data);
        if ($config['api_key'] === '' || $config['base_url'] === '' || $config['model'] === '') {
            throw new InvalidArgumentException('请先配置 API Key、接口地址和模型名称');
        }

        $client = new Client(['timeout' => 60, 'http_errors' => true, 'verify' => false]);
        $response = $client->post($config['base_url'] . '/chat/completions', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $config['api_key'],
            ],
            'json' => [
                'model' => $config['model'],
                'messages' => [['role' => 'user', 'content' => '请只回复：连接正常']],
                'temperature' => 0,
                'max_tokens' => 16,
            ],
        ]);

        $payload = json_decode((string) $response->getBody(), true);
        $reply = $payload['choices'][0]['message']['content'] ?? '';
        return ['reply' => is_string($reply) ? trim($reply) : ''];
    }

    /**
     * 发起大模型对话或分析请求
     *
     * @param string $content
     * @param array $messages
     * @param array|null $config
     * @return string
     */
    public static function chat(string $content, array $messages = [], ?array $config = null): string
    {
        $config = self::config($config);
        if (empty($config['enabled'])) {
            throw new InvalidArgumentException('AI 模型分析未启用');
        }
        if ($config['api_key'] === '' || $config['base_url'] === '' || $config['model'] === '') {
            throw new InvalidArgumentException('请先配置 API Key、接口地址和模型名称');
        }

        if ($messages === []) {
            $messages = [
                ['role' => 'system', 'content' => $config['system_prompt']],
                ['role' => 'user', 'content' => $content],
            ];
        }

        $client = new Client(['timeout' => 60, 'http_errors' => true, 'verify' => false]);
        $response = $client->post($config['base_url'] . '/chat/completions', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $config['api_key'],
            ],
            'json' => [
                'model' => $config['model'],
                'messages' => $messages,
                'temperature' => $config['temperature'],
                'max_tokens' => $config['max_tokens'],
            ],
        ]);

        $payload = json_decode((string) $response->getBody(), true);
        $reply = $payload['choices'][0]['message']['content'] ?? '';
        return is_string($reply) ? trim($reply) : '';
    }
}
