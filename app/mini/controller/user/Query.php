<?php
declare(strict_types=1);

namespace app\mini\controller\user;

use app\mini\model\MiniUserQuery;
use app\mini\model\MiniUser;
use app\mini\model\MiniUserScoreLog;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\admin\service\AdminService;

/**
 * 用户查询记录
 * @class Query
 * @package app\mini\controller\user
 */
class Query extends Controller
{
    /**
     * 查询记录列表
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        MiniUserQuery::mQuery()->layTable(function () {
            $this->title     = '查询记录';
            $this->platforms = MiniUserQuery::getPlatforms();
        }, function (QueryHelper $query) {
            $query->equal('user_id,platform,status,refunded');
            $query->dateBetween('create_at');
        });
    }

    /**
     * 退积分
     * @auth true
     */
    public function refund(): void
    {
        $this->_applyFormToken();

        if ($this->request->isGet()) {
            $id     = intval(input('id', 0));
            $record = MiniUserQuery::mk()->where(['id' => $id])->findOrEmpty();
            if (!$record->isExists()) {
                $this->error('查询记录不存在！');
            }
            $this->vo = $record->toArray();
            $this->fetch('refund');
            return;
        }

        $data = $this->_vali([
            'id.require'     => '查询记录ID不能为空！',
            'id.integer'     => '查询记录ID格式错误！',
            'remark.default' => '',
        ]);

        $record = MiniUserQuery::mk()->where(['id' => $data['id']])->findOrEmpty();
        if (!$record->isExists()) {
            $this->error('查询记录不存在！');
        }
        if ($record->cost_score <= 0) {
            $this->error('该记录为VIP免费查询，无需退积分！');
        }
        if ($record->refunded) {
            $this->error('该记录已退过积分，不能重复操作！');
        }

        $user = MiniUser::mk()->where(['id' => $record->user_id])->findOrEmpty();
        if (!$user->isExists()) {
            $this->error('用户不存在！');
        }

        try {
            \think\facade\Db::startTrans();

            $newBalance = intval($user->score) + intval($record->cost_score);
            MiniUser::mk()->where(['id' => $record->user_id])->update(['score' => $newBalance]);

            MiniUserScoreLog::record(
                intval($record->user_id),
                intval($record->cost_score),
                $newBalance,
                MiniUserScoreLog::SOURCE_QUERY_REFUND,
                intval($record->id),
                '查询退积分' . ($data['remark'] ? '：' . $data['remark'] : ''),
                AdminService::getUserId()
            );

            MiniUserQuery::mk()->where(['id' => $data['id']])->update(['refunded' => 1, 'refund_at' => date('Y-m-d H:i:s')]);

            \think\facade\Db::commit();
            $this->success('退积分成功，已退还 ' . $record->cost_score . ' 积分！');
        } catch (\Exception $e) {
            \think\facade\Db::rollback();
            $this->error('退积分失败：' . $e->getMessage());
        }
    }
}
