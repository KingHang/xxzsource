<?php

namespace app\mall\model\setting;

use app\common\model\setting\Printer as PrinterModel;

class Printer extends PrinterModel
{
    /**
     * 添加新记录
     */
    public function add($data)
    {
        $data['purveyor_id'] = 10001;
        $data['printer_config'] = json_encode($data[$data['printer_type']]);
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 编辑记录
     */
    public function edit($data)
    {
        $data['printer_config'] = json_encode($data[$data['printer_type']]);
        return $this->save($data);
    }

    /**
     * 删除记录
     * @return bool|int
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

}