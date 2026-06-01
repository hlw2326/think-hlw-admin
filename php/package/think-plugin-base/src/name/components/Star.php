<?php
declare(strict_types=1);

namespace plugin\base\name\components;

use plugin\base\name\Name;

/**
 * 星座分布计算组件
 */
class Star implements BaseInterface
{
    public function calculate(Name $name): array
    {
        $hash = $name->getHash();
        $constellations = ["白羊座", "金牛座", "双子座", "巨蟹座", "狮子座", "处女座", "天秤座", "天蝎座", "射手座", "摩羯座", "水瓶座", "双鱼座"];
        $star = [];
        $ch = $hash;
        while (count($star) < 3) {
            $nameIdx = $ch % count($constellations);
            $constName = $constellations[$nameIdx];
            $isExist = false;
            foreach ($star as $sc) {
                if ($sc['name'] === $constName) {
                    $isExist = true;
                    break;
                }
            }
            if (!$isExist) {
                $percent = count($star) === 0 ? 45 : (count($star) === 1 ? 35 : 20);
                $star[] = ['name' => $constName, 'percent' => $percent];
            }
            $ch = (int)($ch / 2) + 5;
        }
        return $star;
    }
}
