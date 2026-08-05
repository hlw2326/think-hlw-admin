<?php

declare(strict_types=1);

namespace plugin\base\service;

use plugin\base\model\BaseMp;
use plugin\base\model\BaseMpReply;
use think\admin\Storage;
use WeChat\Contracts\Tools;
use WeMini\Custom;
use WeMini\Media;

/**
 * 小程序客服消息服务
 */
class CustomService
{
    /**
     * 小程序微信 SDK 配置
     */
    public static function config(BaseMp $mp): array
    {
        return [
            'appid'          => (string) $mp->appid,
            'appsecret'      => (string) $mp->appsecret,
            'token'          => (string) $mp->token,
            'encodingaeskey' => (string) $mp->encodingaeskey,
            'cache_path'     => syspath('runtime/wechat'),
        ];
    }

    /**
     * 匹配客服回复规则
     */
    public static function match(BaseMp $mp, array $message): ?BaseMpReply
    {
        $msg_type = strtolower((string) ($message['MsgType'] ?? $message['msgtype'] ?? ''));
        $content = trim((string) ($message['Content'] ?? $message['content'] ?? ''));
        $event = trim((string) ($message['Event'] ?? $message['event'] ?? ''));
        $target = $msg_type === 'event' ? $event : $content;

        foreach ([(string) $mp->appid, ''] as $appid) {
            $default = null;
            foreach (BaseMpReply::mk()->where(['appid' => $appid, 'status' => 1])->order('sort desc,id asc')->cursor() as $rule) {
                $rule_msg_type = strtolower((string) $rule->msg_type);
                if ($rule_msg_type !== 'all' && $rule_msg_type !== $msg_type) continue;

                $match_type = strtolower((string) $rule->match_type);
                $keyword = trim((string) $rule->keyword);

                if ($match_type === 'default') {
                    if ($msg_type !== 'event' || $target !== 'user_enter_tempsession') $default ??= $rule;
                    continue;
                }
                if ($match_type === 'enter') {
                    if ($msg_type === 'event' && $target === 'user_enter_tempsession') return $rule;
                    continue;
                }
                if ($target === '' || $keyword === '') continue;
                if ($match_type === 'exact' && $target === $keyword) return $rule;
                if ($match_type === 'contains' && stripos($target, $keyword) !== false) return $rule;
            }
            if ($default) return $default;
        }

        return null;
    }

    /**
     * 发送客服消息
     */
    public static function send(BaseMp $mp, string $openid, BaseMpReply $rule): array
    {
        $type = strtolower((string) $rule->reply_type);
        $payload = match ($type) {
            'image' => [
                'msgtype' => 'image',
                'image'   => ['media_id' => self::uploadMedia($mp, (string) ($rule->image_image_url ?: $rule->image_url))],
            ],
            'link' => [
                'msgtype' => 'link',
                'link'    => [
                    'title'       => (string) ($rule->link_title ?: $rule->title),
                    'description' => (string) ($rule->link_content ?: $rule->content),
                    'url'         => (string) ($rule->link_url ?: $rule->url),
                    'thumb_url'   => (string) ($rule->link_image_url ?: $rule->image_url),
                ],
            ],
            'miniprogrampage' => [
                'msgtype'         => 'miniprogrampage',
                'miniprogrampage' => [
                    'title'          => (string) ($rule->page_title ?: $rule->title),
                    'appid'          => (string) ($rule->page_appid ?: $rule->url) ?: $mp->appid,
                    'pagepath'       => (string) ($rule->page_pagepath ?: $rule->pagepath),
                    'thumb_media_id' => self::uploadMedia($mp, (string) ($rule->page_image_url ?: $rule->image_url)),
                ],
            ],
            'voice' => [
                'msgtype' => 'voice',
                'voice'   => ['media_id' => self::uploadMedia($mp, (string) ($rule->voice_voice_url ?: $rule->image_url), 'voice')],
            ],
            'video' => [
                'msgtype' => 'video',
                'video'   => [
                    'media_id'    => self::uploadMedia($mp, (string) ($rule->video_video_url ?: $rule->image_url), 'video'),
                    'title'       => (string) ($rule->video_title ?: $rule->title),
                    'description' => (string) ($rule->video_content ?: $rule->content),
                ],
            ],
            default => [
                'msgtype' => 'text',
                'text'    => ['content' => (string) ($rule->text_content ?: $rule->content)],
            ],
        };

        $access_token = Custom::instance(self::config($mp))->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
        $res = Tools::json2arr(Tools::post($url, Tools::arr2json(['touser' => $openid] + $payload), ['headers' => ['Content-Type: application/json']]));

        if (isset($res['errcode']) && $res['errcode'] === 0) {
            $rule->inc('reply_count')->save();
        }

        return $res;
    }

    /**
     * 上传素材并返回 media_id
     */
    private static function uploadMedia(BaseMp $mp, string $url, string $type = 'image'): string
    {
        if ($url === '') return '';
        $file = is_file($url) ? $url : (is_file($f = syspath('public/' . ltrim(parse_url($url, PHP_URL_PATH) ?: '', '/'))) ? $f : Storage::down($url)['file']);
        $res = Media::instance(self::config($mp))->upload($file, $type);
        return (string) ($res['media_id'] ?? '');
    }
}
