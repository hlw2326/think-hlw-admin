<?php

declare(strict_types=1);

namespace plugin\base\controller\api\v1;

use plugin\base\model\BaseTools;

/**
 * 工具列表 API 控制器
 */
class Tools extends Base
{
    /**
     * 获取推荐工具列表
     *
     * @return void
     */
    public function list(): void
    {
        $list = BaseTools::mk()
            ->field('id,title,desc,logo,to_appid as appid,path,click_count,sort,status')
            ->where(['status' => 1])
            ->where(function ($query) {
                $query->whereOr([
                    ['appid', '=', $this->appid],
                    ['appid', '=', '']
                ]);
            })
            ->order('sort desc, id asc')
            ->select()
            ->toArray();

        $this->success('获取成功', ['list' => $list]);
    }

    /**
     * 增加工具点击计数
     *
     * @return void
     */
    public function click(): void
    {
        $id = intval($this->request->get('id', $this->request->post('id', 0)));
        if ($id <= 0) {
            $this->error('工具 ID 不能为空');
        }

        BaseTools::mk()->where(['id' => $id, 'status' => 1])->inc('click_count')->update();
        $this->success('记录成功');
    }
}
