<?php
declare(strict_types=1);

namespace plugin\base\controller\api\v1;

use plugin\base\service\AdService;

/**
 * 系统配置 API
 * @class Config
 * @package plugin\base\controller\api\v1
 */
class Config extends Base
{
    public function index(): void
    {
        $this->success('获取成功', [
            'share' => [
                'title' => (string) sysconf('base.share_title'),
                'path' => (string) (sysconf('base.share_path') ?: '/pages/index/index'),
                'image_url' => (string) sysconf('base.share_image'),
            ],
            'contact' => [
                'send_message_title' => (string) sysconf('base.contact_send_message_title'),
                'send_message_path' => (string) sysconf('base.contact_send_message_path'),
                'send_message_img' => (string) sysconf('base.contact_send_message_img'),
                'show_message_card' => (int) (sysconf('base.contact_show_message_card') ?: 0) === 1,
                'official_qrcode' => (string) sysconf('base.contact_official_qrcode'),
            ],
            'ad' => AdService::mpConfig($this->mp),
        ]);
    }
}
