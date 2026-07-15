<?php
declare(strict_types=1);

namespace plugin\base\controller\tools;

use plugin\base\model\BaseMp;
use plugin\base\model\BaseTools;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 工具列表
 * @class Index
 */
class Index extends Controller
{
    /**
     * 当前绑定的 AppID
     * @var string
     */
    public string $appid;

    /**
     * 工具列表
     * @auth true
     * @menu true
     */
    public function index(): void
    {
        $this->appid = (string) ($this->get['appid'] ?? '');
        BaseTools::mQuery()->layTable(function () {
            $this->title = '工具列表';
            $this->mps = $this->mps();
        }, function (QueryHelper $query) {
            $query->like('title')->like('desc')->like('to_appid');
            $query->equal('status');
            $query->where(['appid' => $this->appid]);
        });
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_page_filter(array &$data): void
    {
        foreach ($data as &$vo) {
            $vo['appid_name'] = $vo['appid'] ?: '通用工具';
        }
        unset($vo);
    }

    /**
     * 小程序列表
     * @return array
     */
    protected function mps(): array
    {
        return BaseMp::mk()->where(['status' => 1])->order('sort desc,id asc')->select()->toArray();
    }

    /**
     * 表单数据处理
     * @param array $data
     */
    protected function _form_filter(array &$data): void
    {
        if ($this->request->isGet()) {
            $this->mps = $this->mps();
            if (empty($data['appid']) && !empty($this->get['appid'])) {
                $data['appid'] = (string) $this->get['appid'];
            }
        } else {
            $data['appid'] = trim((string) ($data['appid'] ?? ''));
            $data['to_appid'] = trim((string) ($data['to_appid'] ?? ''));
        }
    }

    /**
     * 添加工具
     * @auth true
     */
    public function add(): void
    {
        $this->_applyFormToken();
        BaseTools::mForm('form');
    }

    /**
     * 编辑工具
     * @auth true
     */
    public function edit(): void
    {
        $this->_applyFormToken();
        BaseTools::mForm('form');
    }

    /**
     * 修改状态或排序
     * @auth true
     */
    public function state(): void
    {
        BaseTools::mSave($this->_vali([
            'status.in:0,1' => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除小程序
     * @auth true
     */
    public function remove(): void
    {
        BaseTools::mDelete();
    }

    /**
     * 导出数据
     * @auth true
     */
    public function export(): void
    {
        $appid = (string) ($this->request->get('appid', ''));
        $query = BaseTools::mQuery();
        $query->like('title')->like('desc')->like('to_appid')->equal('status');
        $fields = ['appid', 'title', 'desc', 'logo', 'to_appid', 'path', 'click_count', 'sort', 'status'];
        $list = $query->db()->where(['appid' => $appid])->field($fields)->order('sort desc,id asc')->select()->toArray();
        $data = json_encode($list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        $filename = 'tools_' . ($appid ? $appid : 'common') . '_' . date('YmdHis') . '.json';
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $data;
        exit;
    }

    /**
     * 导入数据
     * @auth true
     */
    public function import(): void
    {
        $appid = (string) ($this->request->get('appid', ''));
        if ($this->request->isGet()) {
            $this->appid = $appid;
            $this->fetch();
            return;
        }

        $jsonData = $this->request->post('json_data', '');
        if (empty($jsonData)) {
            $this->error('JSON数据不能为空！');
        }

        $data = json_decode($jsonData, true);
        if ($data === null) {
            $this->error('JSON解析失败，请检查格式是否正确！');
        }

        // 支持导入单条或多条数组
        $list = isset($data['title']) ? [$data] : $data;
        if (!is_array($list)) {
            $this->error('无效的JSON格式，必须是单个对象或数组！');
        }

        $successCount = 0;
        $failCount = 0;

        // 允许导入的字段列表
        $allowedFields = [
            'appid',
            'title',
            'desc',
            'logo',
            'to_appid',
            'path',
            'click_count',
            'sort',
            'status'
        ];

        try {
            foreach ($list as $item) {
                if (empty($item['title'])) {
                    $failCount++;
                    continue;
                }

                $title = trim((string) $item['title']);
                $toAppid = isset($item['to_appid']) ? trim((string) $item['to_appid']) : (isset($item['appid']) && $item['appid'] !== $appid ? trim((string) $item['appid']) : '');

                $updateData = [];
                foreach ($allowedFields as $field) {
                    if (isset($item[$field])) {
                        $updateData[$field] = $item[$field];
                    }
                }
                $updateData['appid'] = $appid;
                $updateData['to_appid'] = $toAppid;

                // 匹配规则：如果有 to_appid 则通过 appid 和 to_appid 查找，否则通过 appid 和 title 查找
                if (!empty($toAppid)) {
                    $tool = BaseTools::mk()->where(['appid' => $appid, 'to_appid' => $toAppid])->findOrEmpty();
                } else {
                    $tool = BaseTools::mk()->where(['appid' => $appid, 'title' => $title])->findOrEmpty();
                }

                if ($tool->isEmpty()) {
                    // 添加新记录
                    BaseTools::mk($updateData)->save();
                } else {
                    // 更新已存在记录
                    $tool->save($updateData);
                }
                $successCount++;
            }
        } catch (\think\exception\HttpResponseException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->error('导入失败：' . $e->getMessage());
        }
        $this->success("导入成功！已成功添加/更新了 {$successCount} 条，失败 {$failCount} 条。");
    }
}


