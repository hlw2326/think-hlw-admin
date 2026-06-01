<?php
declare(strict_types=1);

namespace plugin\base\name\components;

use plugin\base\service\AiService;
use plugin\base\name\Name;

/**
 * 职业与性格属性计算组件 (支持 AI 大模型深度分析与本地高保真双重引擎)
 */
class Career implements BaseInterface
{
    public function calculate(Name $name): array
    {
        // 尝试获取合并后的 AI 结果
        $aiResult = $name->getAiResult();
        if ($aiResult && isset($aiResult['career'])) {
            $tags = $aiResult['career'];
            if (is_array($tags) && count($tags) >= 4) {
                $colorPresets = [
                    ['color' => '#2563eb', 'bg_color' => '#eff6ff'], // 蓝色
                    ['color' => '#7c3aed', 'bg_color' => '#f5f3ff'], // 紫色
                    ['color' => '#059669', 'bg_color' => '#ecfdf5'], // 绿色
                    ['color' => '#d97706', 'bg_color' => '#fffbeb'], // 黄色
                    ['color' => '#db2777', 'bg_color' => '#fdf2f8']  // 粉色
                ];
                
                $personalityTags = [];
                for ($i = 0; $i < 4; $i++) {
                    $style = $colorPresets[$i % count($colorPresets)];
                    $personalityTags[] = [
                        'text'     => mb_substr(trim((string)$tags[$i]), 0, 4, 'UTF-8'),
                        'color'    => $style['color'],
                        'bg_color' => $style['bg_color']
                    ];
                }
                
                return [
                    'tags' => $personalityTags
                ];
            }
        }

        return ['tags' => []];
    }
}
