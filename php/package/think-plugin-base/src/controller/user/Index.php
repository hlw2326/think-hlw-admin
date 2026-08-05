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
        $this->current = 'index';
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

    /**
     * 调整积分
     * @auth true
     */
    public function score(): void
    {
        $id = intval($this->request->get('id', $this->request->post('id', 0)));
        $user = BaseUser::mk()->where(['id' => $id, 'deleted' => 0])->findOrEmpty();
        if ($user->isEmpty()) {
            $this->error('用户不存在！');
        }

        if ($this->request->isGet()) {
            $this->vo = $user->toArray();
            $this->fetch();
            return;
        }

        $value = intval($this->request->post('value', 0));
        $action = $this->request->post('action', 'add');
        $remark = trim($this->request->post('remark', ''));

        if ($value <= 0) {
            $this->error('积分数值必须大于 0！');
        }

        $change_value = ($action === 'sub') ? -$value : $value;
        $new_score = intval($user->score) + $change_value;

        if ($new_score < 0) {
            $this->error('用户积分不足！');
        }

        $user->score_source = 'admin';
        $user->score_remark = $remark ?: '管理员后台手动调整';
        $user->score_change_value = $change_value;

        if ($user->save(['score' => $new_score])) {
            $this->success('积分调整成功！');
        } else {
            $this->error('积分调整失败！');
        }
    }

    /**
     * 调整会员时间
     * @auth true
     */
    public function vip(): void
    {
        $id = intval($this->request->get('id', $this->request->post('id', 0)));
        $user = BaseUser::mk()->where(['id' => $id, 'deleted' => 0])->findOrEmpty();
        if ($user->isEmpty()) {
            $this->error('用户不存在！');
        }

        if ($this->request->isGet()) {
            $this->vo = $user->toArray();
            $this->fetch();
            return;
        }

        $days = intval($this->request->post('days', 0));
        $action = $this->request->post('action', 'add');
        $remark = trim($this->request->post('remark', ''));

        if ($days <= 0) {
            $this->error('调整天数必须大于 0！');
        }

        $current_vip_time = intval($user->vip_time);
        $now = time();

        if ($current_vip_time > $now) {
            $base_time = $current_vip_time;
        } else {
            $base_time = $now;
        }

        if ($action === 'add') {
            $new_vip_time = $base_time + ($days * 86400);
            $change_days = $days;
        } else {
            $change_days = -$days;
            if ($current_vip_time <= $now) {
                $new_vip_time = 0;
            } else {
                $new_vip_time = $current_vip_time - ($days * 86400);
                if ($new_vip_time < $now) {
                    $new_vip_time = 0;
                }
            }
        }

        $user->vip_source = 'admin';
        $user->vip_remark = $remark ?: '管理员后台手动调整';
        $user->vip_change_days = $change_days;

        if ($user->save(['vip_time' => $new_vip_time])) {
            $this->success('会员时间调整成功！');
        } else {
            $this->error('会员时间调整失败！');
        }
    }
}


