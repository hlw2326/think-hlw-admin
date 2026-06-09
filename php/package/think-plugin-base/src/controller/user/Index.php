<?php
declare(strict_types=1);

namespace plugin\base\controller\user;

use plugin\base\model\BaseUser;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 用户列表
 * @class Index
 */
class Index extends Controller
{
    /**
     * 用户列表
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        BaseUser::mQuery()->layTable(function () {
            $this->title = '用户列表';
        }, function (QueryHelper $query) {
            $query->equal('id');
            $query->like('nickname')->like('phone')->like('openid')->like('device_model');
            $query->equal('status,appid,vip_no_ad');
            $query->dateBetween('create_at');
            $query->where(['deleted' => 0]);
        });
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state(): void
    {
        BaseUser::mSave($this->_vali([
            'status.in:0,1' => '状态值范围异常！',
            'vip_no_ad.in:0,1' => '免广告状态异常！',
        ]));
    }

    /**
     * 退出登录
     * @auth true
     */
    public function logout(): void
    {
        $id = intval($this->request->post('id', $this->request->get('id', 0)));
        if ($id <= 0) {
            $this->error('用户 ID 不能为空！');
        }

        BaseUser::mk()->where(['id' => $id, 'deleted' => 0])->update(['token' => '']);
        $this->success('已退出登录！');
    }

    /**
     * 用户详细信息
     * @auth true
     */
    public function info(): void
    {
        $id = intval($this->request->get('id', 0));
        $this->vo = BaseUser::mk()->where(['id' => $id, 'deleted' => 0])->findOrEmpty()->toArray();
        if (empty($this->vo)) {
            $this->error('用户不存在！');
        }
        $this->fetch();
    }
}


