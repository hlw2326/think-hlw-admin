<?php

declare(strict_types=1);

if (!function_exists('base_admin_view')) {
    /**
     * 获取基础插件后台视图模板绝对路径
     *
     * @param string $template 模板相对名称
     * @return string 模板绝对路径
     */
    function base_admin_view(string $template): string
    {
        return app()->getBasePath() . 'admin' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR .
            str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $template) . '.html';
    }
}
