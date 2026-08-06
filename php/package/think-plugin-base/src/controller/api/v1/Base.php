<?php

declare(strict_types=1);

namespace plugin\base\controller\api\v1;

use plugin\base\model\BaseMp;
use plugin\base\model\BaseUser;
use think\admin\Controller;

/**
 * 接口基础 API 控制器
 */
class Base extends Controller
{
    /**
     * 当前小程序 AppId
     *
     * @var string
     */
    protected string $appid = '';

    /**
     * 当前小程序模型对象
     *
     * @var BaseMp
     */
    protected BaseMp $mp;

    /**
     * 当前登录用户模型对象
     *
     * @var BaseUser
     */
    protected BaseUser $user;

    /**
     * 当前登录用户 ID
     *
     * @var string
     */
    protected string $user_id = '';

    /**
     * 客户端设备信息参数
     *
     * @var array
     */
    protected array $device = [];

    /**
     * 控制器初始化
     *
     * @return void
     */
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

        // 检测并应用 @token true 注解方法验证登录状态
        $action = $this->request->action();
        if (method_exists($this, $action)) {
            $ref_method = new \ReflectionMethod($this, $action);
            $doc_comment = $ref_method->getDocComment() ?: '';
            if (preg_match('/@token\s+true/i', $doc_comment)) {
                $this->checkToken();
            }
        }
    }

    /**
     * 验证用户 Token 登录状态
     *
     * @return void
     */
    protected function checkToken(): void
    {
        $token = $this->request->header('X-Token', '');
        if ($token === '') {
            $this->error('请先登录', [], 401);
        }

        $user = BaseUser::mk()->where(['token' => $token, 'deleted' => 0])->findOrEmpty();
        if ($user->isEmpty()) {
            // 过渡期兼容：检测并允许 60 秒之内被置换的旧 Token 访问接口，避免并发请求竞态导致 401
            $user = BaseUser::mk()->where(['old_token' => $token, 'deleted' => 0])->findOrEmpty();
            if ($user->isEmpty() || (time() - intval($user->old_token_time)) > 60) {
                $this->error('登录已过期，请重新登录', [], 401);
            }
        }
        if (intval($user->status) !== 1) {
            $this->error('账号已被禁用', [], 403);
        }

        $this->user_id = (string) $user->id;
        $this->user = $user;
    }
}
