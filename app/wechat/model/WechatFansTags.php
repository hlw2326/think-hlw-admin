<?php

declare(strict_types=1);
/**
 * +----------------------------------------------------------------------
 * | HlwAdmin
 * +----------------------------------------------------------------------
 * | 邮箱: 1608626143@qq.com
 * | 官方网站: https://www.hlw2326.com
 * +----------------------------------------------------------------------
 */

namespace app\wechat\model;

use think\admin\Model;

/**
 * 微信粉丝标签模型.
 *
 * @property int $count 粉丝总数
 * @property int $id
 * @property string $appid 公众号APPID
 * @property string $create_at 创建日期
 * @property string $name 标签名称
 * @class WechatFansTags
 */
class WechatFansTags extends Model {}
