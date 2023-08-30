<template >
	<view class="pay-success" v-if="!loadding">

        <view style="background-color: #F63E36;height: 350rpx;padding: 30rpx;text-align: center;color: white">
            <view  style="color: white;align-items: center;display: flex;justify-content: center">
                <u-icon name="checkmark-circle-fill" color="white" size="40"></u-icon> <text style="margin: 20rpx">支付成功</text>
            </view>
            <view >
                实付 ¥{{detail.pay_price}}
            </view>
            <view style="display: flex;justify-content: space-between;width: 60%;margin: 20rpx auto;">
                <navigator @click="goMyorder"  class="hlbbutton" style="width: 45%;border:1px white solid">
                    订单详情
                </navigator>
                <navigator  @click="goHome()" class="hlbbutton" style="width: 45%;border:1px white solid">
                    返回首页
                </navigator>
            </view>
        </view>

        <view class="hlbblock30" style="margin-top: -50rpx" v-if="plist.delivery_type.value==10">
            <view style="display: flex;justify-content: space-between">
                <view>
                    <image src="/static/images/order/pay/goods.png" style="width: 40rpx;display: inline-block" mode="widthFix"></image>
                    <text style="font-weight: bold;margin-left: 20rpx"> 您的包裹整装待发</text>
                </view>
                <view>
                    <image src="/static/images/order/arrow.png" style="width: 40rpx" mode="widthFix"></image>
                </view>
            </view>
            <view style="width: 50%;display: flex;justify-content: space-between;align-items: center">
                <image src="/static/images/order/pay/location.png" style="width: 45rpx" mode="widthFix"></image>
                <view style="font-weight: bold">
                    {{plist.address.name}}
                </view>
                <view style="font-weight: bold">
                    {{plist.address.phone}}
                </view>
            </view>
            <view style="font-size: 25rpx;color: #999999;margin-left: 55rpx">
                {{plist.address.region.province+plist.address.region.city+plist.address.region.region+' '+plist.address.detail}}
            </view>
        </view>


		<!--<view class="success-icon d-c-c d-c">
			<text class="iconfont icon-queren"></text>
			<text class="name">支付成功</text>
		</view>
		<view class="success-price d-c-c">
			￥<text class="num">{{detail.pay_price}}</text>
		</view>
		<view class="order-info mt30 f28" v-if="detail.points_bonus > 0">
			<view class="d-b-c p20 border-b">
				<text class="gray9">积分赠送</text>
				<text class="gray3">{{detail.points_bonus}}</text>
			</view>
		</view>
		<view class="success-btns d-b-c">
			<button type="default" class="flex-1 mr10" @click="goHome()">返回首页</button>
			<button type="primary" class="flex-1 ml10" @click="goMyorder">我的订单</button>
		</view>-->
		<!--推荐-->
		<view><recommendProduct :location="30"></recommendProduct></view>
	</view>
</template>

<script>
	import Popup from '@/components/uni-popup.vue';
	import recommendProduct from '@/components/recommendProduct/recommendProduct.vue';
	export default {
		components: {
			recommendProduct
		},
		data() {
			return {
				/*是否加载完成*/
				loadding: true,
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				/*订单id*/
				order_id: 0,
				/*订单详情*/
				detail: {
					order_status: [],
					address: {
						region: []
					},
					product: [],
					pay_type: [],
					delivery_type: [],
					pay_status: []
				},
                plist:[],

			}
		},
		onLoad(e) {
			this.order_id = e.order_id;
		},
		mounted() {
			uni.showLoading({
				title: '加载中'
			});
			/*获取订单详情*/
			this.getData();
		},
		methods: {
			/*获取订单详情*/
			getData() {
				let _this = this;
				let order_id = _this.order_id;
				_this._get(
					'user.order/paySuccess', {
						order_id: order_id
					},
					function(res) {
					    console.log(res)
						_this.detail = res.data.order;
					    _this.plist = res.data.order.list[0]

						_this.loadding = false;
						uni.hideLoading();
					}
				);
			},
			/*返回首页*/
			goHome(){
				this.gotoPage('/pages/index/index')
			},
			/*返回我的订单*/
			goMyorder(){
                let _this = this;

                _this.gotoPage('/pages/order/order-detail?order_id='+_this.order_id);
				// this.gotoPage('/pages/index/order/myorder');
			}
		}
	}
</script>

<style>
	.pay-success .success-icon {
		display: flex;
		padding: 60rpx;
	}

	.pay-success .success-icon .iconfont {
		padding: 30rpx;
		background: #04BE01;
		border-radius: 50%;
		font-size: 80rpx;
		color: #FFFFFF;
	}

	.pay-success .success-icon .name {
		margin-top: 20rpx;
		font-size: 30rpx;
	}

	.pay-success .success-price {
		font-size: 36rpx;
	}

	.pay-success .success-price .num {
		font-size: 60rpx;
		font-weight: bold;
	}

	.pay-success .order-info {
		background: #FFFFFF;
	}

	.pay-success .success-btns {
		margin-top: 50rpx;
		padding: 30rpx;
	}

	.pay-success .success-btns button {
		font-size: 30rpx;
	}

	.pay-success .success-btns button[type="default"] {
		border: 1px solid #04BE01;
		color: #04BE01;
	}
</style>
