<?php
declare(strict_types=1);

namespace plugin\base\name\components;

use plugin\base\name\Name;

/**
 * 姓名属性计算分析组件接口
 */
interface BaseInterface
{
    /**
     * 计算特定维度的属性
     * @param Name $name
     * @return mixed
     */
    public function calculate(Name $name);
}
