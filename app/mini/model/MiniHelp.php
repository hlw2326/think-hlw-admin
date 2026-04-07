<?php
declare(strict_types=1);

namespace app\mini\model;

use think\admin\Model;

/**
 * 帮助文章模型
 * @property int $id
 * @property int $cate_id 分类ID
 * @property string $title 文章标题
 * @property string $content 文章内容
 * @property int $sort 排序权重
 * @property int $status 状态(0禁用,1启用)
 * @property string $create_at 创建时间
 * @property string $update_at 更新时间
 * @class MiniHelp
 * @package app\mini\model
 */
class MiniHelp extends Model
{
}
