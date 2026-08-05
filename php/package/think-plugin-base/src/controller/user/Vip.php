<?php
declare(strict_types=1);

namespace plugin\base\controller\user;

use plugin\base\model\BaseUser;
use plugin\base\model\BaseUserVipLog;
use plugin\base\service\UserVipService;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 会员记录管理
 * @class Vip
 * @package plugin\base\controller\user
 */
class Vip extends Controller
{
    /**
     * 会员记录
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        $this->types = [];
        $this->current = 'vip';
        BaseUserVipLog::mQuery()->layTable(function () {
            $this->title = '会员记录';
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
     * 回滚会员记录
     * @auth true
     */
    public function rollback(): void
    {
        $id = intval($this->request->post('id', 0));
        $log = BaseUserVipLog::mk()->where(['id' => $id])->field('user_id, days, before_vip_time, after_vip_time, create_at, NOW() as db_now')->findOrEmpty();
        if ($log->isEmpty()) {
            $this->error('记录不存在！');
        }

        $diff = strtotime((string)$log->getAttr('db_now')) - strtotime((string)$log->create_at);
        if ($diff > 300) {
            $this->error('该记录已超过 5 分钟，不允许回滚！');
        }

        $user_id = intval($log->user_id);
        $rollback_days = -intval($log->days);

        if ($rollback_days === 0) {
            $this->error('该记录变化天数为 0，无需回滚！');
        }

        $user = BaseUser::mk()->where(['id' => $user_id, 'deleted' => 0])->findOrEmpty();
        if ($user->isEmpty()) {
            $this->error('用户不存在！');
        }

        $current_vip_time = intval($user->vip_time);
        $now = time();

        if ($rollback_days > 0) {
            // 原记录是扣除，回滚是增加
            $base_time = $current_vip_time > $now ? $current_vip_time : $now;
            $new_vip_time = $base_time + ($rollback_days * 86400);
        } else {
            // 原记录是增加，回滚是扣除
            $sub_days = abs($rollback_days);
            if ($current_vip_time <= $now) {
                $new_vip_time = 0;
            } else {
                $new_vip_time = $current_vip_time - ($sub_days * 86400);
                if ($new_vip_time < $now) {
                    $new_vip_time = 0;
                }
            }
        }

        $user->vip_source = 'rollback';
        $user->vip_remark = "回滚记录 #{$id}";
        $user->vip_change_days = $rollback_days;

        if ($user->save(['vip_time' => $new_vip_time])) {
            $this->success('回滚成功！');
        } else {
            $this->error('回滚失败！');
        }
    }
}
