<?php
declare(strict_types=1);

namespace app\mini\controller;

use app\mini\model\MiniMp;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 小程序管理
 * @class Mp
 * @package app\mini\controller
 */
class Mp extends Controller
{
    /**
     * 小程序列表
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        MiniMp::mQuery()->layTable(function () {
            $this->title = '小程序列表';
        }, function (QueryHelper $query) {
            $query->like('name,appid');
            $query->equal('status');
        });
    }

    /**
     * 添加小程序
     * @auth true
     */
    public function add(): void
    {
        $this->_applyFormToken();
        MiniMp::mForm('form');
    }

    /**
     * 编辑小程序
     * @auth true
     */
    public function edit(): void
    {
        $this->_applyFormToken();
        MiniMp::mForm('form');
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state(): void
    {
        MiniMp::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除小程序
     * @auth true
     */
    public function remove(): void
    {
        MiniMp::mDelete();
    }
}
