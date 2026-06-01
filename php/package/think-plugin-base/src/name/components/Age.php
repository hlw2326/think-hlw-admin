<?php
declare(strict_types=1);

namespace plugin\base\name\components;

use plugin\base\name\Name;

/**
 * 年龄分布计算组件
 */
class Age implements BaseInterface
{
    public function calculate(Name $name): array
    {
        $hash = $name->getHash();
        $brackets = ["0-10", "11-20", "21-30", "31-40", "41-50", "50+"];
        $h1 = ($hash % 20) + 5;
        $h2 = (($hash >> 1) % 25) + 10;
        $h3 = (($hash >> 2) % 30) + 15;
        $h4 = (($hash >> 3) % 25) + 10;
        $h5 = (($hash >> 4) % 15) + 5;
        $h6 = 100 - ($h1 + $h2 + $h3 + $h4 + $h5);
        if ($h6 < 2) {
            $diff = 2 - $h6;
            $h3 = max(5, $h3 - $diff);
            $h6 = 100 - ($h1 + $h2 + $h3 + $h4 + $h5);
        }
        $ageValues = [$h1, $h2, $h3, $h4, $h5, $h6];
        $age = [];
        for ($i = 0; $i < 6; $i++) {
            $age[] = ['label' => $brackets[$i], 'value' => $ageValues[$i]];
        }
        return $age;
    }
}
