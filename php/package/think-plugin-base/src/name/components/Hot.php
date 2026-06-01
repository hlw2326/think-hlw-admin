<?php
declare(strict_types=1);

namespace plugin\base\name\components;

use plugin\base\name\Name;
use plugin\base\name\Parser;
use plugin\base\model\BaseSurnameProvince;

/**
 * 热门程度星级与Top 10省份分布计算组件
 */
class Hot implements BaseInterface
{
    public function calculate(Name $name): array
    {
        $surname = $name->getSurname();
        $givenName = $name->getGivenName();
        $count = $name->getCount();
        $hash = $name->getHash();

        if ($count >= 8000) {
            $popularityStars = 5;
        } elseif ($count >= 1000) {
            $popularityStars = 4;
        } else {
            $popularityStars = 3;
        }

        $selectedProvinces = [];
        $values = [];

        try {
            $records = BaseSurnameProvince::mk()
                ->where(['surname' => $surname])
                ->order('count', 'desc')
                ->limit(10)
                ->select();

            if (!$records->isEmpty()) {
                foreach ($records as $rec) {
                    $selectedProvinces[] = $rec->province;
                    $ratio = (float)$rec->ratio;
                    $val = (int)round($count * ($ratio / 100.0));
                    $values[] = max(1, $val);
                }
            }
        } catch (\Throwable $e) {
            // 容错
        }

        if (empty($selectedProvinces)) {
            $provinceList = [];
            try {
                $provinceRecords = BaseSurnameProvince::mk()
                    ->field('province')
                    ->group('province')
                    ->select();
                if (!$provinceRecords->isEmpty()) {
                    foreach ($provinceRecords as $rec) {
                        if (!empty($rec->province)) {
                            $provinceList[] = $rec->province;
                        }
                    }
                }
            } catch (\Throwable $e) {
            }

            if (empty($provinceList)) {
                $provinceList = ["北京", "天津", "河北", "山西", "辽宁", "吉林", "黑龙江", "上海", "江苏", "浙江", "安徽", "福建", "江西", "山东", "河南", "湖北", "湖南", "广东", "广西", "海南", "重庆", "四川", "贵州", "云南", "陕西", "甘肃", "青海", "宁夏", "新疆"];
            }

            $h = $hash;
            while (count($selectedProvinces) < 10) {
                $p = $provinceList[$h % count($provinceList)];
                if (!in_array($p, $selectedProvinces, true)) {
                    $selectedProvinces[] = $p;
                }
                $h = (int)($h / 3) + 7;
            }

            $remaining = $count;
            for ($i = 0; $i < 9; $i++) {
                $share = max(1, (int)($remaining * (0.22 - $i * 0.018) * (0.8 + (Parser::calcHash($name->getName() . $i) % 5) / 10)));
                $values[] = $share;
                $remaining -= $share;
            }
            $values[] = max(1, $remaining);
            rsort($values, SORT_NUMERIC);
        }

        $totalVal = array_sum($values);
        $segments = [];
        $offset = 0;
        $colors = ["#3084fe", "#60a5fa", "#93c5fd", "#bfdbfe", "#dbeafe", "#eab308", "#f97316", "#f43f5e", "#8b5cf6", "#94a3b8"];
        
        for ($i = 0; $i < count($selectedProvinces); $i++) {
            $percent = $totalVal > 0 ? $values[$i] / $totalVal : 0;
            $dashArray = $percent * 220;
            $segments[] = [
                'name' => $selectedProvinces[$i],
                'value' => $values[$i],
                'percent' => number_format($percent * 100, 1),
                'color' => $colors[$i % count($colors)],
                'dashArray' => round($dashArray, 2),
                'dashOffset' => round($offset, 2),
            ];
            $offset += $dashArray;
        }

        return [
            'givenName' => $givenName,
            'stars' => $popularityStars,
            'segments' => $segments,
        ];
    }
}
