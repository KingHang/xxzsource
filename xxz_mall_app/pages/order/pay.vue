<template>
	<view class="">
		<view style="text-align: center;margin: 60rpx">
			<text style="color:#999999;font-size: 25rpx">支付金额</text>
			<view>
				<text style="font-size: 25rpx">¥</text>  <text style="font-size: 60rpx;font-weight: bold;margin-left: 20rpx">{{orderInfo.pay_price}}</text>
			</view>
			<text style="color:#999999;font-size: 25rpx">订单编号：{{orderInfo.order_no}}</text>
		</view>

		<view class="pay-type-list hlbblock" style="">
			<view style="padding: 30rpx 0">
				选择支付方式
			</view>
			<view v-if="showmp" class="type-item" @click="changePayType(20)">
				<image src="/static/images/order/pay/wechat.png" style="width: 50rpx" mode="widthFix"></image>
				<view class="con" style="margin-left: 20rpx">
					<text class="tit">微信支付</text>
					<view>
						<text style="padding: 5rpx;color: #F63E36;border: 1px #F63E36 solid;font-size: 23rpx">微信安全支付</text>
					</view>
				</view>
				<label class="radio">
					<radio value="" color="#fa436a" :checked='payType == 20'></radio>
				</label>
			</view>
			<view v-if="ish5" class="type-item" @click="changePayType(20)">
				<image src="/static/images/order/pay/wechat.png" style="width: 50rpx" mode="widthFix"></image>
				<view class="con" style="margin-left: 20rpx">
					<text class="tit">微信支付</text>
					<view>
						<text style="padding: 5rpx;color: #F63E36;border: 1px #F63E36 solid;font-size: 23rpx">微信H5安全支付</text>
					</view>
				</view>
				<label class="radio">
					<radio value="" color="#fa436a" :checked='payType == 20'></radio>
				</label>
			</view>
			<!--	<view class="type-item b-b" @click="changePayType(2)">
					<text class="icon yticon icon-alipay"></text>
					<view class="con">
						<text class="tit">支付宝支付</text>
					</view>
					<label class="radio">
						<radio value="" color="#fa436a" :checked='payType == 2' />
						</radio>
					</label>
				</view>-->
			<view  class="type-item" @click="changePayType(10)">
				<image src="/static/images/order/pay/credit.png" style="width: 50rpx" mode="widthFix"></image>
				<view class="con" style="margin-left: 20rpx">
					<text class="tit">余额支付</text>
					<text>可用余额 ¥{{balance}}</text>
				</view>
				<label class="radio">
					<radio value="" color="#fa436a" :checked='payType == 10'></radio>
				</label>
			</view>
		</view>
		
		<view @click="confirm" style="width: 85%;position: fixed;bottom: 80rpx;left: 50rpx; text-align: center;color: #FFFFFF;line-height: 70rpx;border-radius: 35rpx;background-color: #F63E36;" >
			确认支付
		</view>
	</view>
</template>

<script>
    var _self;
    import {
        pay
    } from '@/common/pay.js';
	export default {
		data() {
			return {
				payType: 20,
				orderInfo: [],
                res:{
				    order:{
				      price: 0
                    },
				    wechat:{
				        success: false
                    },
                    credit:{
                        success: false
                    }
                },
                id: 0,
                teamid: 0,
                groupbuy: 0,
                ish5: false,
                showmp: false,
                paying: 0,
                success: 0,
                order: [],
                successData: {},
                balance: 0,
				// 是否正在支付
				is_pay: false
			};
		},
		computed: {
		},
		onLoad(e) {
            _self = this;
            _self.order_id = e.orderid;
            //#ifdef H5
            _self.ish5 = true
            //#endif
            //#ifdef MP
            //如果是小程序  例如显示返回按钮
            _self.showmp = true

            // #endIf
		},
        onShow() {
            this.getdata();
        },
		methods: {
			//选择支付方式
			changePayType(type) {
				this.payType = type;
			},
            getdata(){
                uni.showLoading({
                    title: '加载中',
                    mask:true
                });
                _self._get('user.order/getPayOrder', {
                    order_id: _self.order_id,
                    pay_source: _self.getPlatform(),
                }, function(result) {
                    console.log(result)
                    _self.orderInfo  = result.data.order
                    _self.balance=result.data.balance;
                });
            },
            //确认支付
			confirm: async function() {
				if (_self.is_pay) {
					return false;
				}
				_self.is_pay = true;
				uni.showLoading({
				    title: '加载中',
				    mask: true
				});
				_self.code = '';
				uni.login({
				    provider: 'weixin',
				    success: function(loginRes) {
				       _self.code = loginRes.code;
				    },
				});
				_self._post('user.userweb/pay', {
					code: _self.code,
				    order_id: _self.order_id,
				    pay_source: _self.getPlatform(),
				    payType: _self.payType,
				}, function(result) {
				    console.log(result)
					_self.is_pay = false;
					uni.hideLoading();
					result.data.order_type = 10;
				    pay(result, _self, null, function() {
				        if (result.data.order_id.length > 1) {
				            console.log(123)
				            // _self.gotoPage('/pages/index/order/myorder');
				            _self.gotoPage('/pages/order/order-detail?order_id=' + result
				                .data.order_id);
				        } else {
				            console.log(12333)
				            // _self.gotoPage('/pages/order/order-detail?order_id=' + result
				            //     .data.order_id[0]);
				        }
				    })
				});
			},
            complete: function(t) {
                var a = this;
                uni.showLoading({
                    title: '加载中',
                    mask:true
                });
                if (_self.groupbuy==1){
                    this.$http.post('groupbuy.pay.complete', {
                        type: t,
                        id:_self.id
                    }).then(function (response) {
                        console.log(response)
                        uni.hideLoading()
                        if (response.data.error>0){
                            uni.showModal({
                                title: '提示',
                                content: response.data.message,
                                showCancel: false,

                            })
                        } else{
                            uni.redirectTo({
                                url: '/pages/groupbuy/success?id='+_self.id
                            })
                        }
                    }).catch(function (error) {
                    });
                }else{
                    this.$http.post('groupearn.pay.complete', {
                        type: t,
                        id:_self.id
                    }).then(function (response) {
                        console.log(response)
                        uni.hideLoading()
                        if (response.data.error>0){
                            uni.showModal({
                                title: '提示',
                                content: response.data.message,
                                showCancel: false,
                            })
                        } else{
                            if (response.data.order.isgroupearn==0){
                                uni.setNavigationBarTitle({
                                    title: "支付成功"
                                });
								_self.success= 1,
								_self.successData= response.data,
								_self.order= response.data.order,
								_self.ordervirtual= response.data.ordervirtual,
								_self.ordervirtualtype= Array.isArray(response.data.ordervirtual)
                                console.log(_self.order)
                            }else{
                                uni.redirectTo({
                                    url: '/pages/money/paySuccess?id='+_self.id
                                })
                            }
                        }
                    }).catch(function (error) {
                    });
                }
            },
		}
	}
</script>

<style lang='scss'>
	.app {
		width: 100%;
	}

	.price-box {
		background-color: #fff;
		height: 265upx;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		font-size: 28upx;
		color: #909399;

		.price{
			font-size: 50upx;
			color: #303133;
			margin-top: 12upx;
			&:before{
				content: '￥';
				font-size: 40upx;
			}
		}
	}

	.pay-type-list {
		margin-top: 20upx;
		background-color: #fff;
		padding-left: 60upx;
		
		.type-item{
			height: 120upx;
			padding: 20upx 0;
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding-right: 60upx;
			font-size: 30upx;
			position:relative;
		}
		
		.icon{
			width: 100upx;
			font-size: 52upx;
		}
		.icon-erjiye-yucunkuan {
			color: #fe8e2e;
		}
		.icon-weixinzhifu {
			color: #36cb59;
		}
		.icon-alipay {
			color: #01aaef;
		}
		.tit{
			font-size: $font-lg;
			color: $font-color-dark;
			margin-bottom: 4upx;
		}
		.con{
			flex: 1;
			display: flex;
			flex-direction: column;
			font-size: $font-sm;
			color: $font-color-light;
		}
	}
	.mix-btn {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 630upx;
		height: 80upx;
		margin: 80upx auto 30upx;
		font-size: $font-lg;
		color: #fff;
		background-color: $base-color;
		border-radius: 10upx;
		box-shadow: 1px 2px 5px rgba(219, 63, 96, 0.4);
	}

</style>
