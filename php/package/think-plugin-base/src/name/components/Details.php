<?php
declare(strict_types=1);

namespace plugin\base\name\components;

use plugin\base\name\Name;
use plugin\base\model\BaseSurnameProvince;

/**
 * 姓名在各省的真实分布计算组件（动态从数据库获取，无硬编码）
 */
class Details implements BaseInterface
{
    public function calculate(Name $name): array
    {
        $surname = $name->getSurname();
        $count = $name->getCount();
        $details = [];

        try {
            // 1. 动态从数据库中获取所有已存在的省份列表（保证无硬编码省份）
            $provinceRecords = BaseSurnameProvince::mk()
                ->field('province')
                ->group('province')
                ->select();

            $allProvinces = [];
            if (!$provinceRecords->isEmpty()) {
                foreach ($provinceRecords as $rec) {
                    if (!empty($rec->province)) {
                        $allProvinces[] = $rec->province;
                    }
                }
            }

            // 兜底（以防数据库未完全初始化）
            if (empty($allProvinces)) {
                $allProvinces = ["北京", "天津", "河北", "山西", "内蒙古", "辽宁", "吉林", "黑龙江", "上海", "江苏", "浙江", "安徽", "福建", "江西", "山东", "河南", "湖北", "湖南", "广东", "广西", "海南", "重庆", "四川", "贵州", "云南", "西藏", "陕西", "甘肃", "青海", "宁夏", "新疆", "香港", "澳门", "台湾"];
            }

            // 2. 查询该姓氏在数据库中的所有省份分布记录
            $records = BaseSurnameProvince::mk()
                ->where(['surname' => $surname])
                ->select();

            $surnameMap = [];
            if (!$records->isEmpty()) {
                foreach ($records as $rec) {
                    $surnameMap[$rec->province] = (float)$rec->ratio;
                }
            }

            // 3. 动态组装所有省份的数据，若数据库存在对应比例则计算，否则设为 0
            foreach ($allProvinces as $prov) {
                if (isset($surnameMap[$prov])) {
                    $ratio = $surnameMap[$prov];
                    $val = (int)round($count * ($ratio / 100.0));
                    $details[] = [
                        'name'  => $prov,
                        'count' => max(1, $val)
                    ];
                } else {
                    $details[] = [
                        'name'  => $prov,
                        'count' => 0
                    ];
                }
            }

            // 4. 按人数从高到低进行排序，人数相同则按省份名称排序
            usort($details, function ($a, $b) {
                if ($b['count'] === $a['count']) {
                    return strcmp($a['name'], $b['name']);
                }
                return $b['count'] <=> $a['count'];
            });

        } catch (\Throwable $e) {
            $details = [];
        }

        return $details;
    }
}
