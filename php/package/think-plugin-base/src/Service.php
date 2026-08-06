<?php

declare(strict_types=1);

namespace plugin\base;

use plugin\base\exception\ApiExceptionHandle;
use think\admin\Plugin;
use think\exception\Handle;

/**
 * 通用基础插件入口服务
 */
class Service extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $appName = '通用插件';

    /**
     * 包名
     *
     * @var string
     */
    protected $package = 'hlw2326/think-plugin-base';

    /**
     * 注册服务绑定
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(Handle::class, ApiExceptionHandle::class);
    }

    /**
     * 启动插件初始化
     *
     * @return void
     */
    public function boot(): void
    {
        if (class_exists(\WeChat\Contracts\Tools::class)) {
            \WeChat\Contracts\Tools::$cache_path = $this->app->getRuntimePath() . 'wechat' . DIRECTORY_SEPARATOR;
        }
    }

    /**
     * 定义后台管理菜单项
     *
     * @return array
     */
    public static function menu(): array
    {
        $code = static::getAppCode();
        return [
            ['name' => '系统统计', 'icon' => 'layui-icon layui-icon-chart-screen', 'node' => "{$code}/main.index/index"],
            ['name' => '系统参数', 'icon' => 'layui-icon layui-icon-set', 'node' => "{$code}/config.index/index"],
            ['name' => '用户列表', 'icon' => 'layui-icon layui-icon-user', 'node' => "{$code}/user.index/index"],
            ['name' => '微信程序', 'icon' => 'layui-icon layui-icon-app', 'node' => "{$code}/mp.index/index"],
            ['name' => '客服回复', 'icon' => 'layui-icon layui-icon-dialogue', 'node' => "{$code}/mp.reply/index"],
            ['name' => '工具列表', 'icon' => 'layui-icon layui-icon-release', 'node' => "{$code}/tools.index/index"],
            ['name' => '帮助列表', 'icon' => 'layui-icon layui-icon-help', 'node' => "{$code}/help.index/index"],
        ];
    }
}
