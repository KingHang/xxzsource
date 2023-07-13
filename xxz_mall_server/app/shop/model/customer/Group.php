<?php

namespace app\shop\model\customer;

use app\common\model\customer\Group as GroupModel;
use app\shop\model\customer\User as UserModel;
use app\shop\service\customer\ExportService;

/**
 * 用户会员等级模型
 */
class Group extends GroupModel
{
    /**
     * 获取列表记录
     */
    public function getList($data)
    {
        return $this->where('is_delete', '=', 0)
            ->order(['create_time' => 'asc'])
            ->paginate($data, false, [
                'query' => request()->request()
            ]);
    }

    /**
     * 获取列表记录
     */
    public function getLists()
    {
        return $this->field('group_id, group_name')
            ->order(['create_time' => 'asc'])
            ->select();
    }

    /**
     * 标签导出
     */
    public function exportGroup($query)
    {
        // 获取标签列表
        $list = $this->getGroupListAll($query);
        // 导出excel文件
        return (new Exportservice)->groupList($list);
    }

    /**
     * 标签列表(全部)
     */
    public function getGroupListAll($query = [])
    {
        // 获取数据列表
        return $this->field('group_id, group_name')
            ->where('is_delete', '=', 0)
            ->order(['create_time' => 'asc'])
            ->select();
    }

    /**
     * 新增记录
     */
    public function add($data)
    {
        $group = $this->detail(['group_name' => $data['group']['group_name']]);
        if ($group) {
            $this->error = '该标签已存在';
            return false;
        }
        $data['group']['app_id'] = self::$app_id;
        return $this->save($data['group']);
    }

    /**
     * 编辑记录
     */
    public function edit($data)
    {
        $group = $this->detail(['group_name' => $data['group']['group_name']]);
        if ($group && $group['group_id'] != $data['group_id']) {
            $this->error = '该标签已存在';
            return false;
        }
        return $this->save($data['group']);
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        // 判断该标签下是否存在会员
        if (UserModel::checkExistByGradeId($this['group_id'])) {
            return false;
        }
        return $this->save(['is_delete' => 1]);
    }

    /**
     * 软删除
     */
    public function onBatchDelete($customerIds)
    {
        $data = [
            'is_delete' => 1
        ];
        return $this->where('group_id', 'in', $customerIds)->save($data);
    }
}