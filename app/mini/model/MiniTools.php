<?php
declare(strict_types=1);

namespace app\mini\model;

use think\admin\Model;

/**
 * 工具箱模型
 * @property int $id
 * @property string $name 工具名称
 * @property string $logo 工具Logo
 * @property string $desc 工具描述
 * @property string $jump_type 跳转类型
 * @property string $jump_value 跳转值
 * @property int $click_count 点击次数
 * @property int $sort 排序权重
 * @property int $status 状态(0禁用,1启用)
 * @property string $create_at 创建时间
 * @property string $update_at 更新时间
 * @class MiniTools
 * @package app\mini\model
 */
class MiniTools extends Model
{
    /**
     * 获取跳转类型列表
     */
    public static function getJumpTypes(): array
    {
        return [
            'navigateTo'  => ['label' => 'navigateTo ' . lang('普通跳转'),   'class' => 'layui-bg-blue'],
            'redirectTo'  => ['label' => 'redirectTo ' . lang('重定向'),     'class' => 'layui-bg-cyan'],
            'switchTab'   => ['label' => 'switchTab '  . lang('底部菜单'),   'class' => 'layui-bg-green'],
            'reLaunch'    => ['label' => 'reLaunch '   . lang('重启跳转'),   'class' => 'layui-bg-orange'],
            'webview'     => ['label' => 'webview H5'  . lang('网页'),       'class' => 'layui-bg-red'],
            'miniprogram' => ['label' => 'miniprogram '. lang('其他小程序'), 'class' => 'layui-bg-gray'],
        ];
    }
}
