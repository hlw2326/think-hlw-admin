<?php
declare(strict_types=1);

namespace plugin\base\name\components;

use plugin\base\name\Name;

/**
 * 重名率评语计算组件
 */
class Rate implements BaseInterface
{
    public function calculate(Name $name): array
    {
        $count = $name->getCount();
        if ($count < 30) {
            return ['text' => '极低', 'desc' => '名字这么独一无二，肯定胜友如云~'];
        } elseif ($count < 150) {
            return ['text' => '较低', 'desc' => '重名率很低，独具个性的名字哦！'];
        } elseif ($count < 800) {
            return ['text' => '中等', 'desc' => '重名率适中，是个好听又亲切的名字~'];
        } else {
            return ['text' => '偏高', 'desc' => '名字非常受欢迎，可以说是家喻户晓！'];
        }
    }
}
