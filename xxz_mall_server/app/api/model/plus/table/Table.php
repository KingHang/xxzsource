<?php

namespace app\api\model\plus\table;

use app\common\model\plugin\table\Table as TableModel;
use app\common\model\plugin\table\Record as RecordModel;
/**
 * 表单模型
 */
class Table extends TableModel
{
    /**
     * 签到
     */
    public function add($user, $data)
    {
        $this->startTrans();
        try {
            $data['content'] = $data['tableData'];
            $data['user_id'] = $user['user_id'];
            $data['app_id'] = self::$app_id;
            (new RecordModel())->save($data);
            $this->where('table_id', '=', $data['table_id'])->inc('total_count')->update();
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}
