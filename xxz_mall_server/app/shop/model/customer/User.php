<?php

namespace app\shop\model\customer;

use app\common\model\settings\Express as ExpressModel;
use app\shop\model\customer\LevelLog as LevelLogModel;
use app\shop\model\customer\BalanceLog as BalanceLogModel;
use app\common\model\customer\User as UserModel;
use app\common\enum\user\grade\ChangeTypeEnum;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum as SceneEnum;
use app\shop\model\customer\PointsLog as PointsLogModel;
use app\shop\model\customer\RechargeLog as RechargeLogModel;
use app\shop\model\plugin\agent\User as AgentUserModel;
use app\shop\service\customer\ExportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use think\facade\Filesystem;

/**
 * 用户模型
 */
class User extends UserModel

{
    /**
     * 获取当前用户总数
     */
    public function getUserTotal($day = null)
    {
        $model = $this;
        if (!is_null($day)) {
            $startTime = strtotime($day);
            $model = $model->where('create_time', '>=', $startTime)
                ->where('create_time', '<', $startTime + 86400);
        }
        return $model->where('is_delete', '=', '0')->count();
    }

    /**
     * 获取用户id
     * @return \think\Collection
     */
    public function getUsers($where = null)
    {
        // 获取用户列表
        return $this->where('is_delete', '=', '0')
            ->where($where)
            ->order(['customer_id' => 'asc'])
            ->field(['customer_id'])
            ->select();
    }

    /**
     * 获取用户列表
     */
    public static function getList($data)
    {
        $model = new static();
        //检索：用户名
        if ($data) {
            $model = $model->where('nickname|realname|mobile', 'like', '%' . $data['keyword'] . '%');
        }
        // 检索：会员等级
//        if ($level_id > 0) {
//            $model = $model->where('level_id', '=', (int)$level_id);
//        }
//        //检索：注册时间
//        if (!empty($reg_date[0])) {
//            $model = $model->whereTime('create_time', 'between', $reg_date);
//        }
//        // 检索：性别
//        if (!empty($gender) && $gender > -1) {
//            $model = $model->where('gender', '=', (int)$gender);
//        }
        // 获取用户列表
        return $model->with(['Level'])->where('is_delete', '=', '0')
            ->order(['create_time' => 'desc'])
            ->paginate($data, false, [
                'query' => \request()->request()
            ]);
    }

    /**
     * 软删除
     */
    public function onBatchDelete($customerIds)
    {
        $data = [
            'is_delete' => 1
        ];
        return $this->where('customer_id', 'in', $customerIds)->save($data);
    }


    /**
     * 新增记录
     */
    public function add($data)
    {
        $customer = $this->detail(['mobile' => $data['customer']['mobile']]);
        if ($customer) {
            $this->error = '该手机号已被注册';
            return false;
        }
        $data['customer']['app_id'] = self::$app_id;
        $data['customer']['birthday'] = (isset($data['customer']['birthday']) && $data['customer']['birthday']) ? strtotime($data['customer']['birthday']) : '';
        return $this->save($data['customer']);
    }

     public function savaInfo(){

         $id= input('post.customer_id');
         $remark = input('post.remark');
         if($remark){
             $data['remark'] = $remark;
         }
         $distRibutor = input('post.distributor');
         if($distRibutor){
             $data['distributor'] = $distRibutor;
         }
         $affiliation = input('post.affiliation');
         if($affiliation){
             $data['affiliation'] =$affiliation;
         }
         $wechat = input('post.wechat');
         if($wechat){
             $data['wechat'] =$wechat;
         }
         $birthday = input('post.birthday');
         if($birthday){
             $data['birthday'] =strtotime($birthday);
         }

         $gender = input('post.gender');
         if(is_numeric($gender)){
             $data['gender'] =$gender;
         }
         $province = input('post.province');
         if($province){
             $data['province'] =$province;
         }
         $city = input('post.city');
         if($city){
             $data['city'] =$city;
         }
         $area = input('post.area');
         if($area){
             $data['area'] =$area;
         }

        return $this->where('customer_id',  $id)->save($data);


     }
    /**
     * 修改记录
     */
    public function edit($data)
    {
        $data['customer']['birthday'] = (isset($data['customer']['birthday']) && $data['customer']['birthday']) ? strtotime($data['customer']['birthday']) : '';
        $data['customer']['update_time'] = time();
        return $this->save($data['customer']);
    }

    /**
     * 修改用户等级
     */
    public function updateLevel($data)
    {
        if (!isset($data['remark'])) {
            $data['remark'] = '';
        }
        // 变更前的等级id
        $oldGradeId = $this['level_id'];
        return $this->transaction(function () use ($oldGradeId, $data) {
            // 更新用户的等级
            $status = $this->save(['level_id' => $data['level_id']]);
            // 新增用户等级修改记录
            if ($status) {
                (new LevelLogModel)->save([
                    'customer_id' => $this['customer_id'],
                    'old_level_id' => $oldGradeId,
                    'new_level_id' => $data['level_id'],
                    'change_type' => ChangeTypeEnum::ADMIN_USER,
                    'remark' => $data['remark'],
                    'app_id' => $this['app_id']
                ]);
            }
            return $status !== false;
        });
    }

    public function onBatchUpdateLevel($customerIds, $levelId)
    {
        $data = [
            'level_id' => $levelId
        ];

        return $this->where('customer_id', 'in', $customerIds)->save($data);
    }

    public function updateGroup($data) {
        return $this->save($data);
    }

    public function onBatchUpdateGroup($customerIds, $groupId)
    {
        $data = [
            'group_id' => $groupId
        ];
        return $this->where('customer_id', 'in', $customerIds)->save($data);
    }

    public function onBatchSetBlack($customerIds, $type)
    {
        if ($type == 1) {
            $data = ['is_black' => 1];
        } else {
            $data = ['is_black' => 0];
        }

        return $this->where('customer_id', 'in', $customerIds)->save($data);
    }

    /**
     * 消减用户的实际消费金额
     */
    public function setDecUserExpend($userId, $expendMoney)
    {
        return $this->where(['user_id' => $userId])->dec('expend_money', $expendMoney)->update();
    }

    /**
     * 用户充值
     */
    public function recharge($storeUserName, $source, $data)
    {
        if ($source == 0) {
            return $this->rechargeToBalance($storeUserName, $data['balance']);
        } elseif ($source == 1) {
            return $this->rechargeToPoints($storeUserName, $data['exchangepurch']);
        } elseif ($source == 2) {
            return $this->rechargeToMCR($storeUserName, $data['MCR']);
        }
        return false;
    }

    /**
     * 用户充值：余额
     */
    private function rechargeToBalance($storeUserName, $data)
    {
        if (!isset($data['money']) || $data['money'] === '' || $data['money'] < 0) {
            $this->error = '请输入正确的金额';
            return false;
        }
        // 判断充值方式，计算最终金额
        $money = 0;
        if ($data['mode'] === 'inc') {
            $diffMoney = $this['balance'] + $data['money'];
            $money = $data['money'];

            $diffMoneyPresent = $this['present_balance'] + $data['present_money'];
            $moneyPresent = $data['present_money'];
        } elseif ($data['mode'] === 'dec') {
            $diffMoney = $this['balance'] - $data['money'] <= 0 ? 0 : $this['balance'] - $data['money'];
            $money = -$data['money'];

            $diffMoneyPresent = $this['present_balance'] - $data['present_money'] <= 0 ? 0 : $this['present_balance'] - $data['present_money'];
            $moneyPresent = -$data['present_money'];
        } else {
            $diffMoney = $data['money'];
            $money = $diffMoney - $this['present_balance'];

            $diffMoneyPresent = $data['present_money'];
            $moneyPresent = $diffMoney - $this['present_balance'];
        }
        // 更新记录
        $this->transaction(function () use ($storeUserName, $data, $diffMoney, $money, $diffMoneyPresent, $moneyPresent) {
            // 更新账户余额
            $this->where('customer_id', '=', $this['customer_id'])->update(['balance' => $diffMoney, 'present_balance' => $diffMoneyPresent]);
            // 新增余额变动记录
            BalanceLogModel::add(SceneEnum::ADMIN, [
                'operator_id' => $this['user_id'],
                'money' => $money,
                'present_money' => $moneyPresent,
                'old_money' => $this['balance'],
                'old_present_money' => $this['present_balance'],
                'remark' => $data['remark'],
            ], [$storeUserName]);
        });
        return true;
    }

    /**
     * 用户充值：积分
     */
    private function rechargeToPoints($storeUserName, $data)
    {
        if (!isset($data['value']) || $data['value'] === '' || $data['value'] < 0) {
            $this->error = '请输入正确的积分数量';
            return false;
        }
        $points = 0;
        // 判断充值方式，计算最终积分
        if ($data['mode'] === 'inc') {
            $diffMoney = $this['exchangepurch'] + $data['value'];
            $points = $data['value'];
        } elseif ($data['mode'] === 'dec') {
            $diffMoney = $this['exchangepurch'] - $data['value'] <= 0 ? 0 : $this['exchangepurch'] - $data['value'];
            $points = -$data['value'];
        } else {
            $diffMoney = $data['value'];
            $points = $data['value'] - $this['exchangepurch'];
        }
        // 更新记录
        $this->transaction(function () use ($storeUserName, $data, $diffMoney, $points) {
            $totalPoints = $this['total_points'] + $points <= 0? 0 : $this['total_points'] + $points;
            // 更新账户积分
            $this->where('customer_id', '=', $this['customer_id'])->update([
                'exchangepurch' => $diffMoney,
                'total_points' => $totalPoints
            ]);
            // 新增积分变动记录
            PointsLogModel::add([
                'operator_id' => $this['user_id'],
                'value' => $points,
                'des' => "后台管理员 [{$storeUserName}] 操作",
                'remark' => $data['remark'],
            ]);
        });
        event('UserGrade', $this['user_id']);
        return true;
    }

    /**
     * 用户充值：MCR
     */
    private function rechargeToMCR($storeUserName, $data)
    {
        if (!isset($data['value']) || $data['value'] === '' || $data['value'] < 0) {
            $this->error = '请输入正确的数量';
            return false;
        }
        $value = 0;
        // 判断充值方式，计算最终积分
        if ($data['mode'] === 'inc') {
            $diff = $this['MCR'] + $data['value'];
            $value = $data['value'];
        } elseif ($data['mode'] === 'dec') {
            $diff = $this['MCR'] - $data['value'] <= 0 ? 0 : $this['MCR'] - $data['value'];
            $value = -$data['value'];
        } else {
            $diff = $data['value'];
            $value = $data['value'] - $this['MCR'];
        }
        // 更新记录
        $this->transaction(function () use ($storeUserName, $data, $diff, $value) {
            $totalMCR = $this['total_mcr'] + $value <= 0? 0 : $this['total_mcr'] + $value;
            // 更新账户积分
            $this->where('customer_id', '=', $this['customer_id'])->update([
                'MCR' => $diff,
                'total_mcr' => $totalMCR
            ]);
            // 新增积分变动记录
            RechargeLogModel::add([
                'operator_id' => $this['user_id'],
                'add_value' => $data['value'],
                'old_value' => $this['MCR'],
                'des' => "后台管理员 [{$storeUserName}] 操作",
                'remark' => $data['remark'],
            ]);
        });
        return true;
    }

    /**
     * 获取用户统计数量
     */
    public function getUserData($startDate = null, $endDate = null, $type)
    {
        $model = $this;
        if(!is_null($startDate)){
            $model = $model->where('create_time', '>=', strtotime($startDate));
        }
        if(is_null($endDate)){
            $model = $model->where('create_time', '<', strtotime($startDate) + 86400);
        }else{
            $model = $model->where('create_time', '<', strtotime($endDate) + 86400);
        }
        if($type == 'user_total' || $type == 'user_add'){
            return $model->count();
        } else if($type == 'user_pay'){
            return $model->where('pay_money', '>', '0')->count();
        } else if($type == 'user_no_pay'){
            return $model->where('pay_money', '=', '0')->count();
        }
        return 0;
    }

    /**
     * 客户导出
     */
    public function exportList($query)
    {
        // 获取订单列表
        $list = $this->getListAll($query);
        // 导出excel文件
        return (new Exportservice)->customerList($list);
    }

    /**
     * 客户列表(全部)
     */
    public function getListAll($query = [])
    {
        $model = $this;
        // 检索查询条件
        $model = $model->setWhere($model, $query);
        // 获取数据列表
        return $model->with('level')
            ->alias('customer')
            ->field('customer.*')
            ->where('customer.is_delete', '=', 0)
            ->order(['customer.create_time' => 'desc'])
            ->select();
    }

    /**
     * 设置检索查询条件
     */
    private function setWhere($model, $data)
    {
        //搜索订单号
        if (isset($data['keyword']) && $data['keyword'] != '') {
            $model = $model->where('nickname', 'like', '%' . trim($data['keyword']) . '%');
        }

        //搜索配送方式
//        if (isset($data['style_id']) && $data['style_id'] != '') {
//            $model = $model->where('delivery_type', '=', $data['style_id']);
//        }
        //搜索时间段
        if (isset($data['create_time']) && $data['create_time'] != '') {
            $sta_time = array_shift($data['create_time']);
            $end_time = array_pop($data['create_time']);
            $model = $model->whereBetweenTime('create_time', $sta_time, $end_time);
        }
        return $model;
    }

    /**
     * 导入客户
     */
    public function uploadCustomer(){
        // 文件信息
        $fileInfo = request()->file('iFile');

        try {
            $saveName = Filesystem::disk('public')->putFile( '', $fileInfo);
            $savePath = public_path() . "uploads/{$saveName}";
            //载入excel表格
            $inputFileType = IOFactory::identify($savePath); //传入Excel路径
            $reader = IOFactory::createReader($inputFileType);
            $PHPExcel = $reader->load($savePath);

            $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
            // 遍历并记录订单信息
            $list = [];
            foreach ($sheet->toArray() as $key => $val) {
                if($key > 0 && $val[2]) {
                    // 查找客户是否存在
                    $customer = self::detail(['mobile' => trim($val[2])]);
                    $item = [
                        'nickname' => trim($val[0]),
                        'realname' => trim($val[1]),
                        'mobile' => trim($val[2]),
                        'open_id' => trim($val[3]),
                        'exchangepurch' => trim($val[4]),
                        'balance' => trim($val[5]),
                        'create_time' => $this->filterTime(trim($val[6])),
                    ];
                    if($customer) {
                        $list[] = [
                            'data' => $item,
                            'where' => [
                                'customer_id' => $customer['customer_id']
                            ],
                        ];
                    } else {
                        self::add(['customer' => $item]);
                    }
                }
            }
            if(count($list) > 0){
                $this->updateAll($list);
            }
            unlink($savePath);
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    private function filterTime($value)
    {
        if (!$value) return '';
        return strtotime($value);
    }
}
