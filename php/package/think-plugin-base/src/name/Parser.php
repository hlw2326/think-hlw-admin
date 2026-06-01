<?php
declare(strict_types=1);

namespace plugin\base\name;

use plugin\base\model\BaseSurname;

/**
 * 姓名解析器与基础数据算法类
 */
class Parser
{
    /**
     * 32位有符号整型姓名 HASH 算法
     */
    public static function calcHash(string $str): int
    {
        $hash = 0;
        $len = mb_strlen($str, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($str, $i, 1, 'UTF-8');
            $utf16 = mb_convert_encoding($char, 'UTF-16BE', 'UTF-8');
            $code = (ord($utf16[0]) << 8) + ord($utf16[1]);
            
            $hash = $code + (($hash << 5) - $hash);
            $hash = ($hash & 0xFFFFFFFF);
            if ($hash & 0x80000000) {
                $hash = $hash - 0x100000000;
            }
        }
        return abs($hash);
    }

    /**
     * 解析姓名，获取姓氏和名字
     */
    public static function parse(string $name): array
    {
        $surname = '';
        $givenName = '';
        $len = mb_strlen($name, 'UTF-8');
        if ($len <= 1) {
            return ['surname' => $name, 'givenName' => '', 'record' => null];
        }

        // 1. 优先尝试匹配双字复姓
        $first2 = mb_substr($name, 0, 2, 'UTF-8');
        try {
            $record = BaseSurname::mk()->where(['surname' => $first2])->findOrEmpty();
            if (!$record->isEmpty()) {
                $surname = $first2;
                $givenName = mb_substr($name, 2, null, 'UTF-8');
                return ['surname' => $surname, 'givenName' => $givenName, 'record' => $record];
            }
        } catch (\Throwable $e) {
            // 容错
        }

        // 2. 匹配单字姓氏
        $first1 = mb_substr($name, 0, 1, 'UTF-8');
        try {
            $record = BaseSurname::mk()->where(['surname' => $first1])->findOrEmpty();
            if (!$record->isEmpty()) {
                $surname = $first1;
                $givenName = mb_substr($name, 1, null, 'UTF-8');
                return ['surname' => $surname, 'givenName' => $givenName, 'record' => $record];
            }
        } catch (\Throwable $e) {
            // 容错
        }

        // 3. 后备兜底
        $surname = $first1;
        $givenName = mb_substr($name, 1, null, 'UTF-8');
        return ['surname' => $surname, 'givenName' => $givenName, 'record' => null];
    }

    /**
     * 根据真实数据库及 HASH 估算全国重名人数
     */
    public static function estimateCount(string $name, int $hash, array $parsed): int
    {
        $surname = $parsed['surname'];
        $givenName = $parsed['givenName'];
        $record = $parsed['record'];

        // 获取该姓氏的全国人口总数
        $surnamePop = 0;
        if ($record && !$record->isEmpty()) {
            $surnamePop = (int)$record->population;
        } else {
            $surnamePop = 100000;
        }

        if ($givenName === '') {
            return $surnamePop;
        }

        $commonNames = ["张伟", "王伟", "李娜", "刘洋", "李静", "张敏", "李杰", "王静", "张丽", "王强"];
        if (in_array($name, $commonNames, true)) {
            return (int)round($surnamePop * 0.008) + ($hash % 1000);
        }

        $len = mb_strlen($givenName, 'UTF-8');
        if ($len === 1) {
            $ratio = 0.0005 + ($hash % 20) * 0.0001;
            $count = $surnamePop * $ratio;
            return (int)max(300, min(290000, round($count)));
        } elseif ($len === 2) {
            $ratio = 0.00002 + ($hash % 20) * 0.000005;
            $count = $surnamePop * $ratio;
            return (int)max(15, min(15000, round($count)));
        } else {
            $ratio = 0.000001 + ($hash % 10) * 0.0000002;
            $count = $surnamePop * $ratio;
            return (int)max(1, min(500, round($count)));
        }
    }
}
