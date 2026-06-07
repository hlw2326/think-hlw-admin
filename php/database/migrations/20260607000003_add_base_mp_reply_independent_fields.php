<?php

declare(strict_types=1);

use think\admin\extend\PhinxExtend;
use think\migration\Migrator;

/**
 * 升级表：base_mp_reply（添加回复规则类型独立字段）
 */
class AddBaseMpReplyIndependentFields extends Migrator
{
    public function getName(): string
    {
        return 'AddBaseMpReplyIndependentFields';
    }

    public function change(): void
    {
        $table = $this->table('base_mp_reply');
        PhinxExtend::upgrade($table, [
            ['text_content', 'text', ['default' => null, 'null' => true, 'comment' => '文本内容']],
            ['image_image_url', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '图片链接']],
            ['link_title', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '图文标题']],
            ['link_content', 'text', ['default' => null, 'null' => true, 'comment' => '图文描述']],
            ['link_url', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '图文链接']],
            ['link_image_url', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '图文封面']],
            ['page_title', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '卡片标题']],
            ['page_pagepath', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '卡片路径']],
            ['page_image_url', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '卡片封面']],
            ['page_appid', 'string', ['limit' => 100, 'default' => '', 'null' => true, 'comment' => '卡片AppID']],
            ['voice_voice_url', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '语音链接']],
            ['video_title', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '视频标题']],
            ['video_content', 'text', ['default' => null, 'null' => true, 'comment' => '视频描述']],
            ['video_video_url', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '视频链接']],
            ['music_title', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '音乐标题']],
            ['music_content', 'text', ['default' => null, 'null' => true, 'comment' => '音乐描述']],
            ['music_url', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '音乐链接']],
            ['music_hqurl', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '高质链接']],
            ['music_image_url', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '音乐封面']],
        ], [], true);
    }
}
