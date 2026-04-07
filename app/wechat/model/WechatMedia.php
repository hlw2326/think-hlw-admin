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
 * 微信媒体文件模型.
 *
 * @property int $id
 * @property string $appid 公众号ID
 * @property string $create_at 创建时间
 * @property string $local_url 本地文件链接
 * @property string $md5 文件哈希
 * @property string $media_id 永久素材MediaID
 * @property string $media_url 远程图片链接
 * @property string $type 媒体类型
 * @class WechatMedia
 */
class WechatMedia extends Model {}
