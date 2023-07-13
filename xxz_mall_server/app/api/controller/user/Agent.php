<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\plus\agent\Referee;
use app\api\model\plus\agent\Setting;
use app\api\model\plus\agent\User as AgentUserModel;
use app\api\model\plus\agent\Apply as AgentApplyModel;
use app\api\model\product\Goods as ProductModel;
use app\api\model\settings\Message as MessageModel;
use app\api\model\order\Order as OrderModel;
use app\common\model\settings\Settings as SettingModel;
use think\response\Json;
use app\api\model\plus\agent\Capital as CapitalModel;
use app\api\model\plus\agent\OrderDetail;

/**
 * 分销中心
 */
class Agent extends Controller
{
    // 用户
    private $user;
    // 分销商
    private $agent;
    // 分销设置
    private $setting;

    /**
     * 构造方法
     */
    public function initialize()
    {
        $action = Request()->action();
        // 用户信息
        $this->user = $this->getUser($action != 'check');
        // 分销商用户信息
        $this->agent = AgentUserModel::detail($this->user['user_id']);
        // 分销商设置
        $this->setting = Setting::getAll();
    }

    /**
     * 分销商中心
     */
    public function center()
    {
        // 获取待结算金额
        if (!empty($this->agent)){
            $this->agent['notSettledMoney'] = (new OrderDetail())->getNotSettledMoney($this->user['user_id']);
            $this->agent['notSettledMoney'] = number_format( $this->agent['notSettledMoney'] , 2, '.', '');
        }
        return $this->renderSuccess('', [
            // 当前是否为分销商
            'is_agent' => $this->isAgentUser(),
            // 当前是否在申请中
            'is_applying' => AgentApplyModel::isApplying($this->user['user_id']),
            // 当前用户信息
            'user' => $this->user,
            // 分销商用户信息
            'agent' => $this->agent,
            // 背景图
            'background' => $this->setting['background']['values']['index'],
            // 页面文字
            'words' => $this->setting['words']['values'],
        ]);
    }

    /**
     * 分销商申请状态
     * @param null $referee_id
     * @return Json
     */
    public function process($referee_id = null)
    {
        // 步骤进度
        $step = [];

        if ($referee_id) {
            $step[] = [
                'name' => '接受邀请',
                'type' => 0
            ];
            $setmsg = '我想要';
        } else {
            $step[] = [
                'name' => '填写邀请人',
                'type' => 1
            ];
            $setmsg = '我要申请';
        }

        $step[] = [
            'name' => '添加客服微信',
            'type' => 2
        ];

        // 获取分销商审核状态
        $isApplying = AgentApplyModel::isApplying($this->user['user_id']);

        $current = $isApplying ? 2 : 0;

        // 如果之前有关联分销商，则继续关联之前的分销商
        $has_referee_id = Referee::getRefereeUserId($this->user['user_id'], 1);

        if ($has_referee_id > 0) {
            $referee_id = $has_referee_id;
        }

        // 获取配置
        $config = SettingModel::getItem('store');

        // 获取分销商模式
        $basic = $this->setting['condition']['values'];

        switch ($basic['become']) {
            case '20':
                $step[] = [
                    'name' => '审核',
                    'type' => 3
                ];
                break;
            case '30':
                $step[] = [
                    'name' => '购买商品',
                    'type' => 5
                ];
                $setmsg = '购买指定商品';
                break;
            case '40':
                $step[] = [
                    'name' => '购物满'.$basic['meet_amount'].'元',
                    'type' => 4
                ];
                $setmsg = '完成购买任务';
                break;
        }

        $step[] = [
            'name' => '成为店主',
            'type' => 6
        ];

        // 指定商品数据
        $productData = [];
        $is_buy = false;

        if ($basic['become'] == '30' && !empty($basic['product_ids'])) {
            // 获取商品数据
            $productModel = new ProductModel;
            $productList = $productModel->getListByIdsFromApi($basic['product_ids'], $this->user);

            if (!$productList->isEmpty()) {
                foreach ($productList as $product) {
                    $show_sku = ProductModel::getShowSku($product);
                    $productData[] = [
                        'product_id' => $product['product_id'],
                        'product_name' => $product['product_name'],
                        'selling_point' => $product['selling_point'],
                        'image' => $product['image'][0]['file_path'],
                        'product_image' => $product['image'][0]['file_path'],
                        'product_price' => $show_sku['product_price'],
                        'line_price' => $show_sku['line_price'],
                        'product_sales' => $product['product_sales'],
                    ];
                }
            }

            // 判断用户有没有购买过
            $buyNum = OrderModel::getUserBuyProduct($this->user['user_id'], $basic['product_ids']);
            $is_buy = $buyNum > 0;
        }

        return $this->renderSuccess('', [
            // 是否邀请
            'is_referee' => $referee_id > 0,
            // 当前是否为分销商
            'is_agent' => $this->isAgentUser(),
            // 当前是否在申请中
            'is_applying' => $isApplying,
            // 当前用户信息
            'user' => $this->user,
            // 分销商用户信息
            'agent' => $this->agent ? $this->agent : (object)[],
            // 进度条信息
            'step' => $step,
            // 进度提示语
            'setmsg' => $setmsg,
            // 当前进度
            'current' => $current,
            // 客服二维码
            'kefu' => $config['kefu'],
            // 是否购买过商品
            'is_buy' => $is_buy,
            // 是否满足购买金额
            'is_meet' => $this->user['expend_money'] >= $basic['meet_amount'],
            'expend_money' => $this->user['expend_money'],
            'meet_amount' => $basic['meet_amount'],
            // 指定商品数据
            'product_list' => $productData,
        ]);
    }

    /**
     * 检测分销商状态
     */
    public function check()
    {
        $info = (new AgentApplyModel())->checkApply($this->user);
        return $this->renderSuccess('', $info);
    }

    /**
     * 分销商申请状态
     */
    public function apply($referee_id = null, $platform = '')
    {
        // 推荐人昵称
        $referee_name = '平台';
        // 如果之前有关联分销商，则继续关联之前的分销商
        $has_referee_id = Referee::getRefereeUserId($this->user['user_id'], 1);
        if ($has_referee_id > 0) {
            $referee_id = $has_referee_id;
        }
        if ($referee_id > 0 && ($referee = AgentUserModel::detail($referee_id))) {
            $referee_name = $referee['user']['nickName'];
        }
        return $this->renderSuccess('', [
            // 当前是否为分销商
            'is_agent' => $this->isAgentUser(),
            // 当前是否在申请中
            'is_applying' => AgentApplyModel::isApplying($this->user['user_id']),
            // 推荐人昵称
            'referee_name' => $referee_name,
            // 背景图
            'background' => $this->setting['background']['values']['apply'],
            // 页面文字
            'words' => $this->setting['words']['values'],
            // 申请协议
            'license' => $this->setting['license']['values']['license'],
            // 如果来源是小程序, 则获取小程序订阅消息id.获取售后通知.
            'template_arr' => MessageModel::getMessageByNameArr($platform, ['agent_apply_user']),
        ]);
    }

    /**
     * 分销商提现信息
     */
    public function cash($platform = '')
    {
        // 如果来源是小程序, 则获取小程序订阅消息id.获取售后通知.
        $template_arr = MessageModel::getMessageByNameArr($platform, ['agent_cash_user']);
        $settlement = $this->setting['settlement']['values'];
        $settlement['pay_type'] = $this->setting['basic']['values']['pay_type'];
        return $this->renderSuccess('', [
            // 分销商用户信息
            'agent' => $this->agent,
            // 结算设置
            'settlement' => $settlement,
            // 背景图
            'background' => $this->setting['background']['values']['cash_apply'],
            // 页面文字
            'words' => $this->setting['words']['values'],
            // 小程序消息
            'template_arr' => $template_arr,
            // 服务费
            'reduce_percent' => round($this->setting['basic']['values']['reduce_percent'], 2),
            //提现协议
            'basic'=>$this->setting['basic']['values'],
        ]);
    }

    /**
     * 当前用户是否为分销商
     */
    private function isAgentUser()
    {
        return !!$this->agent && !$this->agent['is_delete'];
    }

    public function getCapitalList()
    {
        $data = (new CapitalModel())->getCapitalList($this->user['user_id'], $this->request->post());
        return $this->renderSuccess('', $data);
    }
}
