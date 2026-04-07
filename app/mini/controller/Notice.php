<?php
declare(strict_types=1);

namespace app\mini\controller;

use app\mini\model\MiniMp;
use app\mini\model\MiniNotice;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 通知管理
 * @class Notice
 * @package app\mini\controller
 */
class Notice extends Controller
{
    /**
     * 通知列表
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        MiniNotice::mQuery()->layTable(function () {
            $this->title   = '通知列表';
            $this->types   = MiniNotice::getTypes();
            $this->mp_list = MiniMp::mk()->where('status', 1)->column('name', 'appid');
        }, function (QueryHelper $query) {
            $query->like('title,content,appid');
            $query->equal('type,status');
            $query->dateBetween('create_at');
        });
    }

    /**
     * 添加通知
     * @auth true
     */
    public function add(): void
    {
        $this->_applyFormToken();
        $this->title = '添加通知';
        MiniNotice::mForm('form');
    }

    /**
     * 编辑通知
     * @auth true
     */
    public function edit(): void
    {
        $this->_applyFormToken();
        $this->title = '编辑通知';
        MiniNotice::mForm('form');
    }

    /**
     * 表单数据处理
     */
    protected function _form_filter(array &$data): void
    {
        $this->types      = MiniNotice::getTypes();
        $this->jump_types = MiniNotice::getJumpTypes();
        $this->mp_list    = MiniMp::mk()->where('status', 1)->column('name', 'appid');

        if ($this->request->isPost()) {
            if (!empty($data['appid_all'])) {
                $data['appid'] = '*';
            } else {
                $appids = array_filter((array)($data['appid'] ?? []));
                $data['appid'] = implode(',', $appids) ?: '*';
            }
            unset($data['appid_all']);
        } else {
            $this->appid_list = $data['appid'] ?? '';
        }
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state(): void
    {
        MiniNotice::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除通知
     * @auth true
     */
    public function remove(): void
    {
        MiniNotice::mDelete();
    }
}
