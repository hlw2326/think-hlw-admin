<?php
declare(strict_types=1);

namespace app\mini\controller\user;

use app\mini\model\MiniUser;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 用户管理
 * @class Index
 * @package app\mini\controller\user
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
        MiniUser::mQuery()->layTable(function () {
            $this->title = '用户列表';
        }, function (QueryHelper $query) {
            $query->equal('id');
            $query->like('nickname,phone,openid');
            $query->like('device_model#model,sdk_version#sdk,app_version#ver,app_channel#channel');
            $query->equal('status');
            $query->equal('pid');
            $query->dateBetween('create_at');
        });
    }

    /**
     * 编辑用户
     * @auth true
     */
    public function edit(): void
    {
        $this->_applyFormToken();
        $this->title = '编辑用户';
        MiniUser::mForm('form');
    }

    /**
     * 表单数据处理
     */
    protected function _form_filter(array &$data): void
    {
        if ($this->request->isPost()) {
            $allowed = ['id', 'nickname', 'phone', 'avatar_url', 'remark', 'pid',
                'device_model', 'device_system', 'screen_width', 'screen_height',
                'sdk_version', 'app_version', 'app_channel'];
            $data = array_intersect_key($data, array_flip($allowed));
        }
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state(): void
    {
        MiniUser::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除用户
     * @auth true
     */
    public function remove(): void
    {
        MiniUser::mDelete();
    }
}
