<?php

declare(strict_types=1);

use think\admin\extend\PhinxExtend;
use think\migration\Migrator;

/**
 * 升级表：base_mp_reply（添加客服消息新回复类型字段）
 */
class AddBaseMpReplyNewFields extends Migrator
{
    public function getName(): string
    {
        return 'AddBaseMpReplyNewFields';
    }

    public function change(): void
    {
        $table = $this->table('base_mp_reply');
        PhinxExtend::upgrade($table, [
            ['title', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '标题']],
            ['pagepath', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '小程序路径']],
            ['url', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '跳转链接']],
        ], [], true);
    }
}
