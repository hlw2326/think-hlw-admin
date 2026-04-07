<?php
declare(strict_types=1);

namespace app\mini\controller\card;

use app\mini\model\MiniCardLog;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 卡密使用记录
 * @class Log
 * @package app\mini\controller\card
 */
class Log extends Controller
{
    /**
     * 卡密记录
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        MiniCardLog::mQuery()->layTable(function () {
            $this->title = '卡密记录';
        }, function (QueryHelper $query) {
            $query->like('card_code');
            $query->equal('card_type,user_id');
            $query->dateBetween('create_at');
        });
    }
}
