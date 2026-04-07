<?php
declare(strict_types=1);

namespace app\mini\controller\user;

use app\mini\model\MiniUserScoreLog;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 积分流水
 * @class ScoreLog
 * @package app\mini\controller\user
 */
class ScoreLog extends Controller
{
    /**
     * 积分流水
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        MiniUserScoreLog::mQuery()->layTable(function () {
            $this->title   = '积分流水';
            $this->sources = MiniUserScoreLog::getSources();
        }, function (QueryHelper $query) {
            $query->equal('user_id,source');
            $query->dateBetween('create_at');
        });
    }
}
