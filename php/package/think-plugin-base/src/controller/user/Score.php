<?php

declare(strict_types=1);

namespace plugin\base\controller\user;

use plugin\base\model\BaseUser;
use plugin\base\model\BaseUserScoreLog;
use plugin\base\service\UserScoreService;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 积分记录管理控制器
 */
class Score extends Controller
{
    /**
     * 积分记录列表
     *
     * @auth true
     * @menu true
     * @return void
     */
    public function index(): void
    {
        $this->types = UserScoreService::TYPES;
        $this->current = 'score';
        BaseUserScoreLog::mQuery()->layTable(function () {
            $this->title = '积分记录';
        }, function (QueryHelper $query) {
            $query->equal('s.user_id#user_id,s.status#status');
            $query->like('s.source#source,s.remark#remark');
            $query->dateBetween('s.create_at#create_at');

            $db = $query->db();
            $db->alias('s')
               ->join('base_user u', 's.user_id = u.id')
               ->field('s.*, u.nickname, u.avatar_url');
        });
    }

    /**
     * 回滚积分记录（5分钟之内）
     *
     * @auth true
     * @return void
     */
    public function rollback(): void
    {
        $id = intval($this->request->post('id', 0));
        $log = BaseUserScoreLog::mk()->where(['id' => $id])->field('user_id, value, create_at, NOW() as db_now')->findOrEmpty();
        if ($log->isEmpty()) {
            $this->error('记录不存在！');
        }

        $diff = strtotime((string)$log->getAttr('db_now')) - strtotime((string)$log->create_at);
        if ($diff > 300) {
            $this->error('该记录已超过 5 分钟，不允许回滚！');
        }

        $user_id = intval($log->user_id);
        $rollback_value = -intval($log->value);

        if ($rollback_value === 0) {
            $this->error('该记录变化值为 0，无需回滚！');
        }

        $user = BaseUser::mk()->where(['id' => $user_id, 'deleted' => 0])->findOrEmpty();
        if ($user->isEmpty()) {
            $this->error('用户不存在！');
        }

        $new_score = intval($user->score) + $rollback_value;
        if ($new_score < 0) {
            $this->error('回滚后积分不足，无法回滚！');
        }

        $user->score_source = 'rollback';
        $user->score_remark = "回滚记录 #{$id}";
        $user->score_change_value = $rollback_value;

        if ($user->save(['score' => $new_score])) {
            $this->success('回滚成功！');
        } else {
            $this->error('回滚失败！');
        }
    }
}
