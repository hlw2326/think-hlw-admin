<?php
declare(strict_types=1);

namespace app\mini\controller\api\v1;

use app\mini\model\MiniMp;
use think\admin\Controller;
use WeMini\Crypt;

/**
 * 登录接口
 * @class Login
 * @package plugin\qz\controller\api\v1
 */
class Login extends Controller
{
    public function in(): void
    {
        if ($this->request->isPost()) {
            $code  = $this->request->post('code');
            $appid = $this->request->get('appid');
            $mp = MiniMp::mk()->where(['appid' => $appid])->findOrEmpty();
            $config = [
                'appid'          => $mp->appid,
                'appsecret'      => $mp->appsecret,
                'encodingaeskey' => '',
            ];
            $crypt = Crypt::instance($config);
            $this->success('登录成功', $crypt);
        }
    }
}
