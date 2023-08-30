<template>
	<view class="index">
		<!--支付方式-->
		<view class="deposit_bg">
			<view class="deposit_tit">类目：<text class="gray9">{{category.name}}</text></view>
			<view class="deposit_tit borderb">保证金：<text class="gray9">￥{{category.deposit_money}}</text></view>
		</view>
		<view class="buy-checkout">
			<view :class="pay_type == 20 ? 'item active border-b-e' : 'item border-b-e'" @click="payTypeFunc(20)">
				<view class="d-s-c">
					<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-weixin"></span></view>
					<text class="key">微信支付</text>
				</view>
				<view class="icon-box iconfont icon-tijiaochenggong d-c-c" v-if="pay_type==20"></view>
			</view>
			<view :class="pay_type == 10 ? 'item active' : 'item'" @click="payTypeFunc(10)">
				<view class="d-s-c">
					<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-yue"></span></view>
					<text class="key">余额支付</text>
				</view>
				<view class="icon-box iconfont icon-tijiaochenggong d-c-c" v-if="pay_type==10"></view>
			</view>
		</view>
		<view class="pay-bt">
			<button class="pay-bts" @tap="SubmitOrder">提交订单</button>
		</view>
	</view>
</template>

<script>
	import {pay} from '@/common/pay.js';
	export default {
		data() {
			return {
				category: {},
				pay_type: 20, //支付方式,
			}
		},
		onShow() {
			/*获取个人中心数据*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				self._get('purveyor.index/deposit', {}, function(res) {
					self.category = res.data.category;
				});
			},
			/*显示支付方式*/
			payTypeFunc(e) {
				this.pay_type = e;
			},
			/*提交订单*/
			SubmitOrder() {
				let self = this;
				self._post(
					'purveyor.index/deposit', {
						pay_type: self.pay_type,
						pay_source: self.getPlatform()
					},
					function(result) {
						if (self.pay_type == 20) {
							pay(result, self, function() {
								self.pay_window = false;
								self.showSuccess("支付成功", function() {
									self.gotoPage('/pages/user/my_shop/my_shop');
								}, function() {
									self.showSuccess("支付失败，请重试");
								})
							});
						} else {
							self.showSuccess("支付成功", function() {
								self.gotoPage('/pages/user/my_shop/my_shop');
							})
						};
					}
				);
			},
		}
	}
</script>

<style lang="scss">
	page{
		background-color: #f6f6f6;
	}
	.index {
		width: 750rpx;
	}
	.deposit_bg{
		background-color: #FFFFFF;
	}
	.deposit_tit{
		padding:0 30rpx;
		margin: 20rpx 0;
		height: 94rpx;
		line-height:94rpx;
		font-size:32rpx;
		background-color: #FFFFFF;
		border-top: 1rpx solid #efefef;
	}
	.borderb{
		border-bottom: 1rpx solid #efefef;
	}
	.h1 {
		font-size: 32rpx;
		color: #585858;
		font-weight: bold;
	}

	.h2 {
		font-size: 28rpx;
		color: #585858;
	}

	.h3 {
		font-size: 24rpx;
		color: #585858;
	}

	.h4 {
		font-size: 20rpx;
		color: #585858;
	}

	.iconfont {
		color: white;
	}

	.red {
		color: #E22319;
	}

	.gray {
		color: #585858;
	}

	.green {
		color: #24DB5A;
	}

	.orange {
		color: #FF7E00;
	}

	.bg_gray {
		background-color: #A2A2A2;
	}

	.bg_red {
		background-color: #E22319;
	}

	.bg_green {
		background-color: #24DB5A;
	}

	.bg_orange {
		background-color: #FF7E00;
	}

	.head {
		width: 750rpx;
		height: 100rpx;
		background-color: #ECECEC;
		display: flex;
		justify-content: space-between;
		padding: 20rpx 40rpx;
		box-sizing: border-box;
		align-items: center;
		box-shadow: 0 0 10rpx #585858;
	}

	.body_hint {
		width: 500rpx;
		height: 50rpx;
		line-height: 50rpx;
		margin: 0 auto;
		background-color: #FFF7E2;
		text-align: center;
		margin-top: 50rpx;
	}

	.progress_bar {
		width: 450rpx;
		margin: 0 auto;
		margin-top: 50rpx;
	}

	.progress_bar_image {
		width: 500rpx;
		margin: 0 auto;
		display: flex;
		justify-content: space-between;
		position: relative;
		top: -40rpx;
	}

	.progress_bar_image_items {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

	.progress_bar_image_item {
		width: 72rpx;
		height: 72rpx;
		border-radius: 50%;
		display: flex;
		justify-content: center;
		align-items: center;
		margin-bottom: 15rpx;
	}


	.return {
		width: 750rpx;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		margin-top: 100rpx;
	}

	.return view {
		margin-bottom: 20rpx;
	}

	.return_title {}

	.return_title2 {}

	.return .btn1 {
		width: 300rpx;
		height: 65rpx;
		line-height: 65rpx;
		border-radius: 50rpx;
		color: white;
		background-color: #FF7E00;
		margin-top: 50rpx;
		font-size: 30rpx;
	}

	.return .btn2 {
		width: 300rpx;
		height: 65rpx;
		line-height: 65rpx;
		border-radius: 50rpx;
		color: white;
		background-color: #24DB5A;
		margin-top: 50rpx;
		font-size: 30rpx;
	}

	.pay_window {
		width: 100%;
		height: 100%;
		position: absolute;
		top: 0;
		left: 0;
		background-color: rgba(0, 0, 0, 0.5);
		z-index: 999;
	}

	.pay_window_box {
		width: 70%;
		height: 500rpx;
		margin: 0 auto;
		background-color: white;
		position: relative;
		top: 200rpx;
		padding: 50rpx;
		border-radius: 30rpx;
	}

	.buy-checkout {
		width: 100%;
		background-color: #FFFFFF;
	}

	.buy-checkout .item {
		min-height: 50rpx;
		line-height: 50rpx;
		padding: 20rpx;
		display: flex;
		justify-content: space-between;
		font-size: 28rpx;
	}

	.buy-checkout .iconfont.icon-weixin {
		color: #04be01;
		font-size: 50rpx;
	}

	.buy-checkout .iconfont.icon-yue {
		color: #f0de7c;
		font-size: 50rpx;
	}

	.buy-checkout .item.active .iconfont.icon-xuanze {
		color: #04be01;
	}

	.pay-bt {
		padding-top: 130rpx;
		padding-bottom: 60rpx;
		display: flex;
		justify-content: center;
		width: 100%;
	}

	.pay-bts {
		width: 225rpx;
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		padding: 0;
		font-size: 32rpx;
		border-radius: 40rpx;
		color: #FFFFFF;
		background-color: #E2231A;
	}

	.icon-tijiaochenggong {
		color: #E2231A;
	}
</style>
