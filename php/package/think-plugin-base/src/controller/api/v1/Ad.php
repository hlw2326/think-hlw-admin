<?php
declare(strict_types=1);

namespace plugin\base\controller\api\v1;

use plugin\base\service\AdService;

/**
 * 广告相关 API
 * @class Ad
 */
class Ad extends Base
{
    public function config(): void
    {
        $this->success('获取成功', AdService::mpConfig($this->mp));
    }

    /**
     * @token true
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
