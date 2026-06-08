<?php
declare(strict_types=1);

namespace plugin\base\controller\api\v1;

use plugin\base\model\BaseMp;
use think\admin\Controller;

/**
 * 接口基础 API
 * @class Base
 */
class Base extends Controller
{
    protected string $appid = '';

    protected BaseMp $mp;

    protected array $device = [];

    protected function initialize(): void
    {
        parent::initialize();

        [$state, $msg, $data] = verify_sig($this->request);
        if (!$state) {
            $this->error($msg, $data);
        }

        $this->appid = $this->request->header('X-Appid', '') ?: $this->request->get('appid', '');
        if ($this->appid === '') {
            $this->error('缺少 appid 参数');
        }

        $mp = BaseMp::mk()->where(['appid' => $this->appid, 'status' => 1])->findOrEmpty();
        if ($mp->isEmpty()) {
            $this->error('无效的 appid');
        }
        $this->mp = $mp;

        $this->device = [
            'app_name' => $this->request->get('app_name', ''),
            'version' => $this->request->get('version', ''),
            'version_code' => $this->request->get('version_code', ''),
            'channel' => $this->request->get('channel', ''),
            'device_brand' => $this->request->get('device_brand', ''),
            'device_model' => $this->request->get('device_model', ''),
            'device_id' => $this->request->get('device_id', ''),
            'device_type' => $this->request->get('device_type', ''),
            'device_orientation' => $this->request->get('device_orientation', ''),
            'device_system' => $this->request->get('system', ''),
            'os' => $this->request->get('os', ''),
            'screen_width' => intval($this->request->get('screen_width', 0)),
            'screen_height' => intval($this->request->get('screen_height', 0)),
            'window_width' => intval($this->request->get('window_width', 0)),
            'window_height' => intval($this->request->get('window_height', 0)),
            'pixel_ratio' => floatval($this->request->get('pixel_ratio', 0)),
            'status_bar_height' => intval($this->request->get('status_bar_height', 0)),
            'sdk_version' => $this->request->get('sdk_version', ''),
            'host_name' => $this->request->get('host_name', ''),
            'host_version' => $this->request->get('host_version', ''),
            'platform' => $this->request->get('platform', ''),
            'language' => $this->request->get('language', ''),
            'brand' => $this->request->get('brand', ''),
            'model' => $this->request->get('model', ''),
        ];
    }
}
