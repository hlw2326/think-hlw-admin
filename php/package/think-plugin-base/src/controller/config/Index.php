<?php
declare(strict_types=1);

namespace plugin\base\controller\config;

use plugin\base\service\AiService;
use think\admin\Controller;
use think\exception\HttpResponseException;
use Throwable;

/**
 * 系统参数配置
 * @class Index
 */
class Index extends Controller
{
    /**
     * 基础配置
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            sysconf('base.help_steps', (string) ($data['help_steps'] ?? ''));
            $this->success('基础配置已保存');
        }

        $this->title = '基础配置';
        $this->current = 'index';
        $this->base = [
            'help_steps' => (string) (sysconf('base.help_steps') ?: ""),
        ];
        $this->fetch();
    }

    /**
     * 分享配置
     * @auth true
     */
    public function share(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            sysconf('base.share_title', (string) ($data['title'] ?? ''));
            sysconf('base.share_path', (string) ($data['path'] ?? '/pages/index/index'));
            sysconf('base.share_image', (string) ($data['image'] ?? ''));
            $this->success('分享配置已保存');
        }

        $this->title = '分享配置';
        $this->current = 'share';
        $this->share = [
            'title' => (string) sysconf('base.share_title'),
            'path' => (string) (sysconf('base.share_path') ?: '/pages/index/index'),
            'image' => (string) sysconf('base.share_image'),
        ];
        $this->fetch();
    }

    /**
     * 客服配置
     * @auth true
     */
    public function contact(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            sysconf('base.contact_send_message_title', (string) ($data['send_message_title'] ?? ''));
            sysconf('base.contact_send_message_path', (string) ($data['send_message_path'] ?? ''));
            sysconf('base.contact_send_message_img', (string) ($data['send_message_img'] ?? ''));
            sysconf('base.contact_show_message_card', (string) ($data['show_message_card'] ?? '0'));
            sysconf('base.contact_official_qrcode', (string) ($data['official_qrcode'] ?? ''));
            $this->success('客服配置已保存');
        }

        $this->title = '客服配置';
        $this->current = 'contact';
        $this->contact = [
            'send_message_title' => (string) sysconf('base.contact_send_message_title'),
            'send_message_path' => (string) sysconf('base.contact_send_message_path'),
            'send_message_img' => (string) sysconf('base.contact_send_message_img'),
            'show_message_card' => (string) (sysconf('base.contact_show_message_card') ?: '0'),
            'official_qrcode' => (string) sysconf('base.contact_official_qrcode'),
        ];
        $this->fetch();
    }

    /**
     * AI模型配置
     *
     * @auth true
     */
    public function ai(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $provider = (string) ($data['provider'] ?? 'qwen');
            if (!isset(AiService::providers()[$provider])) {
                $provider = 'qwen';
            }

            sysconf('base.ai_enabled', (int) ($data['enabled'] ?? 0));
            sysconf('base.ai_provider', $provider);
            sysconf('base.ai_base_url', trim((string) ($data['base_url'] ?? '')));
            sysconf('base.ai_model', trim((string) ($data['model'] ?? '')));
            sysconf('base.ai_temperature', (string) ($data['temperature'] ?? '0.3'));
            sysconf('base.ai_max_tokens', (string) ($data['max_tokens'] ?? '1200'));
            sysconf('base.ai_system_prompt', trim((string) ($data['system_prompt'] ?? '')));

            $apiKey = trim((string) ($data['api_key'] ?? ''));
            if ($apiKey !== '') {
                sysconf('base.ai_api_key', $apiKey);
            }

            $this->success('AI模型配置已保存');
        }

        $this->title = 'AI模型配置';
        $this->current = 'ai';
        $this->providers = AiService::providers();
        $this->ai = AiService::config();
        $this->fetch();
    }

    /**
     * 获取当前供应商可用模型列表
     *
     * @auth true
     */
    public function models(): void
    {
        $result = AiService::listModels($this->currentAiConfigInput());
        $this->success((string) $result['message'], $result);
    }

    /**
     * 测试当前模型配置是否可连接
     *
     * @auth true
     */
    public function test(): void
    {
        try {
            $result = AiService::testConnection($this->currentAiConfigInput());
            $reply = trim((string) ($result['reply'] ?? ''));
            $this->success($reply !== '' ? "连接成功：{$reply}" : '连接成功', $result);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            $this->error(self::formatExceptionMessage($exception));
        }
    }

    private static function formatExceptionMessage(Throwable $exception): string
    {
        $message = trim($exception->getMessage());
        if ($message !== '') {
            return $message;
        }

        $previous = $exception->getPrevious();
        if ($previous !== null) {
            $message = trim($previous->getMessage());
            if ($message !== '') {
                return $message;
            }
        }

        return '连接失败：' . $exception::class;
    }

    /**
     * 读取当前 AI 表单配置，API Key 留空时使用已保存的密钥
     *
     * @return array<string,mixed>
     */
    private function currentAiConfigInput(): array
    {
        $data = $this->request->post();
        if (trim((string) ($data['api_key'] ?? '')) === '') {
            $data['api_key'] = (string) (AiService::config()['api_key'] ?? '');
        }

        return $data;
    }
}
