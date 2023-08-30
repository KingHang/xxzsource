<template>
	<view class="index"  v-if="!loading">

        <view style="padding: 30rpx">
            <view >
                <image src="https://img.dfhlyl.com/20220629235732998c26829.png"  style="width: 100%" mode="widthFix"></image>
            </view>

            <view style="width: 83%;margin: auto;color: white;position: absolute;top: 50rpx;left: 60rpx">
                <view style="display: flex;justify-content: space-between">
                    <view>
                        当前余额
                    </view>
                    <navigator @click="gotoList('all')">
                        查看明细<u-icon name="arrow-right" color="white" size="28"></u-icon>
                    </navigator>
                </view>
                <view style="display: flex;align-items: center">
                    <view style="width: 50%">
                        <text style="font-size: 22rpx;margin: 20rpx">¥</text> <text style="font-size: 50rpx">{{balance}}</text>
                    </view>
                    <view style="width: 50%;font-size: 22rpx">
                        <view style="display: flex;justify-content: space-between">
                            <view style="font-size: 22rpx">
                                本金：
                            </view>
                            <view style="font-size: 22rpx">
                                ¥{{balance||0}}
                            </view>
                        </view>
                         <view style="display: flex;justify-content: space-between;margin-top: 30rpx">
                              <view style="font-size: 22rpx">
                                  赠送金额：
                              </view>
                              <view style="font-size: 22rpx">
                                  ¥{{data.balance||0}}
                              </view>

                          </view>

                    </view>
                </view>

            </view>


            <view style="margin-top: 50rpx">
                <u-row gutter="2">
                    <u-col v-for="(item,index) in tableData" span="4">
                        <view @click="select(index)"  :class="active==index?'chose':''" style="margin: 10rpx;border: 1px #F0F0F0 solid;height: 150rpx;border-radius: 15rpx;align-items: center;display: flex;justify-content: center">

                            <view style="padding: 30rpx;text-align: center">
                                <view >
                                    充{{item.money}}元
                                </view>
                                <view v-if="item.give_money>0" style="font-size: 22rpx;color: #999999">
                                    立即送{{item.give_money}}元
                                </view>
                            </view>
                        </view>
                    </u-col>
                    <block v-if="settings.is_plan == 1">
                        <u-col span="4" v-if="active!=-1">
                            <view @click="select(-1)" :class="active==-1?'chose':''" style="margin: 10rpx;border: 1px #F0F0F0 solid;height: 150rpx;border-radius: 15rpx;align-items: center;display: flex;justify-content: center">
                                <view style="padding: 30rpx;text-align: center">
                                    <view style="font-weight: bold">
                                        其他金额
                                    </view>
                                </view>
                            </view>
                        </u-col>
                        <u-col span="4" v-else>
                            <view  class="chose" style="margin: 10rpx;border: 1px #F0F0F0 solid;height: 150rpx;border-radius: 15rpx;align-items: center;display: flex;justify-content: center">
                                <u-input :focus="focus" v-model="user_money" type="number" style="text-align: center;width: 80rpx;margin: auto" placeholder=" "    />
                            </view>
                        </u-col>
                    </block>

                </u-row>

            </view>

        </view>



	<!--	<view class="index-head">
			<view class="index-head-top">
				<view class="f32">请选择充值金额:</view>
			</view>
			<view class="index-head-bottom">
				<view :class="active==index?'index-head-bottom-item-active':'index-head-bottom-item'" @click="select(index)" v-for="(item,index) in tableData"
				 :key="index">
					<text class="f40">{{item.money}}</text><text class="f22">元</text>
					<view class="givemoney" v-if="item.give_money > 0">送{{item.give_money}}元</view>
				</view>
			</view>
			<view class="head-top-botm">注：充值金额只能消费，不能提现;</view>
		</view>-->
		<view class="index-body hlbblock" style="display: none">
			<!--<template v-if="settings.is_plan == 1">
				<view class="index-head-top">
					<view class="f32 p-0-20">可自定义充值金额:</view>
				</view>
				<view class="index-body-top" @click="select(-1)">
					<view class="d-s-c flex-1 border-b-d9">
						<view>￥</view><input type="text" v-model="user_money" />
					</view>
				</view>
				<view class="gray p-0-30">最低充值金额：{{settings.min_money}}元</view>
			</template>-->
			<!--支付方式-->
			<view class="buy-checkout">
				<view :class="pay_type == 20 ? 'item active ' : 'item'" @tap="payTypeFunc(20)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-weixin"></span></view>
						<text class="key">微信支付：</text>
					</view>
					<view class="icon-box d-c-c"><span class="icon iconfont icon-xuanze"></span></view>
				</view>
				<view v-if="showAlipay"  :class="pay_type == 30 ? 'item active' : 'item'" @click="payTypeFunc(30)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-zhifubao"></span></view>
						<text class="key">支付宝支付：</text>
					</view>
					<view class="icon-box d-c-c"><span class="icon iconfont icon-xuanze"></span></view>
				</view>
			</view>
			<!--<view class="index-body-bottom">
				<view class="index-body-bottom-info">
					<view class="f32 mb23">充值说明</view>
					<text class="gray f26">{{settings.describe}}</text>
				</view>
			</view>-->
		</view>
		<view class="btn">
			<button type="default" @click="payFunc">确认支付</button>
		</view>
	</view>
</template>

<script>
	import {
		pay
	} from '@/common/pay.js';
	export default {
		data() {
			return {
				tableData: [],
				settings: {},
				active: 0,
				/*套餐id*/
				plan_id: 0,
				/*自定义金额*/
				user_money: '',
				loading: true,
				/*是否显示支付宝支付，只有在h5，非微信内打开才显示*/
				showAlipay: false,
                balance:0,
                pay_type:20
			}
		},
		watch: {
			user_money(val) {
				if (val != '') {
					this.active = -1;
					this.plan_id = 0;
				}
			}
		},
		mounted() {
			/*获取数据*/
			this.getData();
		},
		methods: {

            gotoList(type) {
                this.gotoPage('/pages/user/my-wallet/my-balance?type=' + type);
            },
			/*获取数据*/

			getData() {
				let self = this;
				self.loading = true;
				uni.showLoading({
					title: '加载中'
				})
				self._get(
					'balance.plan/index', {
						pay_source: self.getPlatform()
					},
					function(data) {
					    console.log(data)
						self.loading = false;
						uni.hideLoading();
						self.tableData = data.data.list;
						self.settings = data.data.settings;
					}
				);
                self._get(
                    'balance.log/index', {},
                    function(res) {
                        console.log(res)
                        self.loading = false;
                        self.balance = res.data.balance;
                        if (!res.data.balance_open){
                            uni.showModal({
                                title: '提示',
                                content: '未开放充值',
                                success: function (res) {
                                    if (res.confirm) {
                                        uni.navigateBack();
                                    } else if (res.cancel) {
                                        uni.navigateBack();
                                    }
                                }
                            });
                        }
                    }
                );



			},
			//选择套餐
			select(index) {
				this.active = index;
				this.user_money = '';
			},
			payTypeFunc(e){
				this.pay_type = e;
			},
			payFunc() {
				let self = this;
				uni.showLoading({
					title: '加载中',
					mask: true
				});
				

				if (self.active != -1) {
					self.plan_id = self.tableData[self.active]['plan_id'];
				}

				if (self.plan_id == 0 && self.user_money == '') {
					uni.showToast({
						icon: 'none',
						title: '请选择充值套餐或输入要充值的金额'
					});
					return;
				}
				
				uni.login({
					provider: 'weixin',
					success: function(loginRes) {
						self._post('balance.plan/submit', {
							code: loginRes.code,
							plan_id: self.plan_id,
							user_money: self.user_money,
							pay_source: self.getPlatform()
						}, function(result) {
							uni.hideLoading();
							pay(result, self, function() {
								uni.showToast({
									title: '充值成功'
								}, 1000);
								setTimeout(function() {
									uni.navigateBack();
								}, 1000);
							}, function() {
								uni.showToast({
									icon: 'none',
									title: '充值失败'
								});
							});
						});
					}
				})
			},
		}
	}
</script>

<style>
    .chose{
        color: #F63E36 ;
        background-color: #FFEBE9;
        border:1px #F63E36 solid !important;
    }
	page {
		background: #FFFFFF;
	}

	.gray {
		color: #808080;
	}

	.font-28 {
		font-size: 28rpx;
	}

	.font-36 {
		font-size: 36rpx;
	}

	.index {
		width: 750rpx;
	}

	.index-head {
		width: 750rpx;
		margin: 0 auto;
		padding: 30rpx 20rpx;
		padding-bottom: 50rpx;
		box-sizing: border-box;
		background-color: white;
		border-top: 16rpx solid #f2f2f2;
	}

	.index-head-top {
		display: flex;
		justify-content: space-between;
		align-items: baseline;
		flex-direction: column;
	}

	.head-top-botm {
		color: #999999;
		font-size: 26rpx;
		margin-top: 20rpx;
	}

	.index-head-bottom {
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-wrap: wrap;
	}

	.index-head-bottom-item {
		position: relative;
		width: 220rpx;
		height: 128rpx;
		border: 1rpx solid #999999;
		background-color: #FFFFFF;
		color: #333333;
		text-align: center;
		border-radius: 15rpx;
		line-height: 128rpx;
		margin-top: 20rpx;
		font-size: 48rpx;
	}

	.index-head-bottom-item-active {
		position: relative;
		width: 220rpx;
		height: 128rpx;
		color: #323333;
		background: #FFE6E3;
		border: 1rpx solid #F6220C;
		text-align: center;
		border-radius: 12rpx;
		line-height: 128rpx;
		margin-top: 20rpx;
		font-size: 48rpx;
	}

	.givemoney {
		position: absolute;
		top: 0;
		left: 0;
		width: 90rpx;
		height: 33rpx;
		background-color: #F6220C;
		color: #FFFFFF;
		font-size: 20rpx;
		line-height: 33rpx;
		text-align: center;
		border-top-left-radius: 12rpx;
		border-bottom-right-radius: 12rpx;
	}

	.index-body {
		width: 750rpx;
	}

	.index-body-top {
		/* width: 660rpx; */
		height: 80rpx;
		padding: 0 20rpx;
		margin: 0 auto;
		display: flex;
		justify-content: flex-start;
		align-items: flex-end;
		color: #4b4b4b;
		margin-bottom: 15rpx;
		margin-top: 30rpx;
		padding-bottom: 10rpx;
		font-size: 28rpx;

	}

	.active {
		background-color: #FDE34880;
		color: #323333;
	}
    .ts {
        background-color: #FDE34880;
        color: #323333;
    }

	.index-body-top input {
		flex: 1;
		font-size: 48rpx;
		font-weight: bold;
	}

	.index-body-bottom {
		width: 750rpx;
		padding: 30rpx 20rpx;
		background-color: white;
		border-top: 16rpx solid #f2f2f2;
		margin-top: 20rpx;
	}

	.checkbox {
		display: flex;
		align-items: center;
	}

	.index-body-bottom-info {
		/* margin-top: 65rpx; */
	}

	.index-body-bottom-info view {
		margin-top: 10rpx;
	}

	.btn {
		margin-top: 50rpx;
	}

	.btn button {
		width: 710rpx;
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 40rpx;
		margin: 0 auto;
		color: white;
		font-size: 32rpx;
		background: linear-gradient(90deg, #FF6B6B 4%, #F6220C 100%);
	}

	.rule text {
		margin-left: 15rpx;
		color: #88B5D1;
	}
</style>
