<?php

declare(strict_types=1);

namespace plugin\base\controller\api\v1;

use plugin\base\model\BaseMp;
use plugin\base\service\CustomService;
use think\admin\Controller;
use WeChat\Contracts\Tools;
use WeChat\Receive;

/**
 * 客服消息 API 控制器
 */
class Custom extends Controller
{
    /**
     * 微信小程序客服消息回调接口
     *
     * @return string
     */
    public function index(): string
    {
        try {
            $mp = $this->mp();
            if (!$this->checkSignature((string) $mp->token)) {
                return '';
            }
            if ($this->request->isGet()) {
                return (string) $this->request->get('echostr', '');
            }
            if (empty($mp->custom_reply_enabled)) {
                return 'success';
            }
            $message = $this->receive($mp);
            $openid = (string) ($message['FromUserName'] ?? $message['fromusername'] ?? '');
            if ($openid !== '' && ($rule = CustomService::match($mp, $message))) {
                if (strtolower((string) $rule->reply_type) === 'transfer') {
                    return $this->transfer($message);
                }
                CustomService::send($mp, $openid, $rule);
            }
        } catch (\Throwable $exception) {
            $this->app->log->error("BASE mini custom reply failed: {$exception->getMessage()}");
        }
        return $this->request->isGet() ? '' : 'success';
    }

    /**
     * 构建转接人工客服报文
     *
     * @param array $message
     * @return string
     */
    private function transfer(array $message): string
    {
        $to_user = $message['FromUserName'] ?? $message['fromusername'] ?? '';
        $from_user = $message['ToUserName'] ?? $message['touser'] ?? '';
        $time = time();
        $raw = trim(Tools::getRawInput());
        if (str_starts_with($raw, '{')) {
            return (string) json_encode([
                'ToUserName' => $to_user,
                'FromUserName' => $from_user,
                'CreateTime' => $time,
                'MsgType' => 'transfer_customer_service'
            ]);
        }
        return <<<XML
<xml>
  <ToUserName><![CDATA[{$to_user}]]></ToUserName>
  <FromUserName><![CDATA[{$from_user}]]></FromUserName>
  <CreateTime>{$time}</CreateTime>
  <MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>
XML;
    }

    /**
     * 获取当前小程序配置对象
     *
     * @return BaseMp
     */
    private function mp(): BaseMp
    {
        $appid = (string) $this->request->get('appid', '');
        if ($appid === '') {
            throw new \RuntimeException('缺少 appid 参数');
        }
        $mp = BaseMp::mk()->where(['appid' => $appid, 'status' => 1])->findOrEmpty();
        if ($mp->isEmpty()) {
            throw new \RuntimeException('无效的小程序 appid');
        }
        if (empty($mp->token)) {
            throw new \RuntimeException('小程序未配置消息校验 Token');
        }
        return $mp;
    }

    /**
     * 读取并解密微信推送消息
     *
     * @param BaseMp $mp
     * @return array
     */
    private function receive(BaseMp $mp): array
    {
        $raw = trim(Tools::getRawInput());
        if ($raw === '') {
            return [];
        }

        if (str_starts_with($raw, '{')) {
            return $this->arrayChangeKeyCase(json_decode($raw, true) ?: []);
        }

        if ($this->request->get('encrypt_type') === 'aes') {
            Tools::setRawInput($raw);
            $receive = new Receive(CustomService::config($mp), false);
            return $this->arrayChangeKeyCase($receive->getReceive());
        }

        return $this->arrayChangeKeyCase(Tools::xml2arr($raw));
    }

    /**
     * 验证微信推送签名
     *
     * @param string $token
     * @return bool
     */
    private function checkSignature(string $token): bool
    {
        $nonce = (string) $this->request->get('nonce', '');
        $timestamp = (string) $this->request->get('timestamp', '');
        $signature = (string) ($this->request->get('msg_signature', '') ?: $this->request->get('signature', ''));
        $tmp_arr = [$token, $timestamp, $nonce, $this->request->get('msg_signature') ? $this->encryptPayload() : ''];
        sort($tmp_arr, SORT_STRING);
        return sha1(implode($tmp_arr)) === $signature;
    }

    /**
     * 获取加密消息体原始内容
     *
     * @return string
     */
    private function encryptPayload(): string
    {
        $raw = trim(Tools::getRawInput());
        if ($raw === '') {
            return '';
        }
        $data = Tools::xml2arr($raw);
        return (string) ($data['Encrypt'] ?? $data['encrypt'] ?? '');
    }

    /**
     * 数组键名统一转换为小写
     *
     * @param array $data
     * @return array
     */
    private function arrayChangeKeyCase(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->arrayChangeKeyCase($value);
            }
            $data[strtolower((string) $key)] = $value;
            if (strtolower((string) $key) !== (string) $key) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
