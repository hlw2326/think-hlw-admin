<?php
declare(strict_types=1);

namespace plugin\base\service;

use plugin\base\name\Name;

/**
 * 姓名重名查询与多维属性分析服务
 */
class NameService
{
    /**
     * 分析姓名，通过多维计算类库协同进行分析
     * @param string $name
     * @return array
     */
    public static function analyze(string $name): array
    {
        return Name::calculate($name);
    }
}
