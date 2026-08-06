<?php

declare(strict_types=1);

namespace plugin\base\controller\api\v1;

use plugin\base\service\AdService;

/**
 * 广告相关 API
 */
class Ad extends Base
{
    /**
     * 获取广告配置
     *
     * @return void
     */
    public function config(): void
    {
        $this->success('获取成功', AdService::config($this->mp));
    }

    /**
     * 激励广告奖励发放
     *
     * @token true
     * @return void
     */
    public function reward(): void
    {
        if (!$this->request->isPost()) {
            $this->error('请求方式不支持');
        }

        $result = AdService::grant(intval($this->user->id));
        if (!$result['state']) {
            $this->error($result['msg'] ?: '发放失败');
        }
        $this->success($result['msg'] ?: '领取成功', $result['data'] ?? []);
    }
}
