<?php
declare(strict_types=1);

namespace plugin\base\name\components;

use plugin\base\name\Name;

/**
 * 姓名五维评分计算组件
 */
class Score implements BaseInterface
{
    public function calculate(Name $name): array
    {
        $hash = $name->getHash();
        $overallScore = ($hash % 15) + 84;
        if ($overallScore >= 95) {
            $scoreGrade = "神完气足 (SS)";
            $scoreComment = "字形沉稳大气，音律铿锵有力，蕴含高雅志趣，是非常罕见的佳名！";
        } elseif ($overallScore >= 90) {
            $scoreGrade = "天造地设 (S)";
            $scoreComment = "格局开阔，字形优美和谐，字义蕴含深厚，极具现代高级质感！";
        } else {
            $scoreGrade = "朗朗上口 (A)";
            $scoreComment = "音调朗朗上口，结构匀称，亲切自然，是一个好听又亲和的优秀名字！";
        }
        return [
            'value' => $overallScore,
            'grade' => $scoreGrade,
            'comment' => $scoreComment
        ];
    }
}
