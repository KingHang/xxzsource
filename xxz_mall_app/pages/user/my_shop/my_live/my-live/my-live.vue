<template>
	<view class="weidian">
		<view class="weidian_head">
			<view>
				<view class="weidian_head_body_bottom">
					<view class="weidian_head_body_bottom_item">
						<view>总收入(元)</view>
						<text>{{data.totalPrice==null?0:data.totalPrice}}</text>
					</view>
					<view class="weidian_head_body_bottom_item">
						<view>总订单(笔)</view>
						<text>{{data.orderCount==null?0:data.orderCount}}</text>
					</view>
					<view class="weidian_head_body_bottom_item">
						<view>礼物收入</view>
						<text>{{data.gifPrice}}</text>
					</view>
				</view>
<!-- 				<view class="weidian_head_body_bottom">
					<view class="weidian_head_body_bottom_item">
						<view>总收入(元)</view>
						<text>0.00x</text>
					</view>
					<view class="weidian_head_body_bottom_item">
						<view>总订单(笔)</view>
						<text>0.00x</text>
					</view>
					<view class="weidian_head_body_bottom_item">
						<view>礼物数</view>
						<text>0.00x</text>
					</view>
				</view> -->
			</view>
		</view>
		<view class="weidian_body">
			<view class="weidian_body_menu_list">
				<view class="weidian_body_menu_list_item" @click="gotoPage('/pages/user/my_shop/my_live/my-live/record')">
					<view class="weidian_body_menu_list_left_icon icon iconfont icon-caidan"></view>
					<view class="weidian_body_menu_list_title_left">直播记录</view>
					<view class="weidian_body_menu_list_right_icon icon iconfont icon-jiantou"></view>
				</view>
				<view class="weidian_body_menu_list_item" @click="gosupPage('/pages/user/my_shop/my_live/my-live/edit')">
					<view class="weidian_body_menu_list_left_icon icon iconfont icon-gouwu"></view>
					<view class="weidian_body_menu_list_title_left">商品管理</view>
					<view class="weidian_body_menu_list_right_icon icon iconfont icon-jiantou"></view>
				</view>
				<view class="weidian_body_menu_list_item" @click="gotoPage('/pages/user/my_shop/my_live/notice/notice')">
					<view class="weidian_body_menu_list_left_icon icon iconfont icon-icon_xianshi-xian"></view>
					<view class="weidian_body_menu_list_title_left">直播预告</view>
					<view class="weidian_body_menu_list_right_icon icon iconfont icon-jiantou"></view>
				</view>
				<view class="weidian_body_menu_list_item" @click="gosupPage('/pages/user/my_shop/my_live/live-order/live-order')">
					<view class="weidian_body_menu_list_left_icon icon iconfont icon-quanbudingdan"></view>
					<view class="weidian_body_menu_list_title_left">直播订单</view>
					<view class="weidian_body_menu_list_right_icon icon iconfont icon-jiantou"></view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				data:{
					gifPrice:0,
					orderCount:0,
					totalPrice:0,
					shop_supplier_id:''
				}
			}
		},
		onLoad(e) {
			this.shop_supplier_id=e.shop_supplier_id;
		},
		onShow() {
			this.getData();
		},
		methods: {
			gosupPage(path){
				this.gotoPage(path+'?shop_supplier_id='+this.shop_supplier_id);
			},
			// 获取用户数据
			getData() {
				let self = this;
				self._get('plus.live.Room/livedata', {
					
				}, function(res) {
					self.data=res.data.data;
				})
			},
		}
	}
</script>

<style>
	page {
		background: #f2f2f2;
	}

	.weidian {}

	.iconfont {
		font-size: 14px;
	}

	.weidian_head {
		width: 90%;
		/* height: 360rpx; */
		border-radius: 30rpx;
		margin: 0 auto;
		margin-top: 20rpx;
		margin-bottom: 40rpx;
		background-image: linear-gradient(90deg, #FB8E55, #F97D3D);
	}

	.weidian_body {}

	.weidian_head_body_top {
		width: 100%;
		height: 200rpx;
		background-image: linear-gradient(90deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
	}

	.weidian_head_body_top_left {
		width: 50%;
		height: 200rpx;
		display: inline-block;
		font-size: 30rpx;
		color: white;
		padding-top: 40rpx;
		padding-left: 45rpx;
		box-sizing: border-box;
	}

	.weidian_head_body_top_left view {
		margin-top: 20rpx;
	}

	.weidian_head_body_top_left image {
		width: 36rpx;
		height: 36rpx;
		display: inline-block;
	}

	.weidian_head_body_top_right {
		width: 30%;
		display: inline-block;
		position: relative;
		top: -25rpx;
		left: 120rpx;
	}

	.weidian_head_body_top_right button {
		width: 82%;
		height: 60rpx;
		line-height: 60rpx;
		border-radius: 30rpx;
		color: #F36A23;
	}

	.weidian_head_body_bottom {
		width: 100%;
		height: 160rpx;
		padding-top: 30rpx;
		box-sizing: border-box;
		font-size: 26rpx;
	}

	.weidian_head_body_bottom_item {
		width: 33%;
		display: inline-block;
		float: left;
		/* margin: 0 41rpx; */
		color: white;
		text-align: center;
	}

	.weidian_head_body_bottom_item view {
		margin-bottom: 20rpx;
	}

	.weidian_head_body_bottom_item text {
		font-size: 30rpx;
	}

	/* 我的订单css */
	.seller_center_body_order {
		width: 90%;
		background-color: white;
		margin: 0 auto;
		border-radius: 20rpx;
		padding: 20rpx;
		box-sizing: border-box;
	}

	.seller_center_body_order_head {
		font-size: 30rpx;
		margin-bottom: 50rpx;
		display: flex;
		justify-content: space-between;
		align-items: flex-end;
	}

	.seller_center_body_order_head_more {
		font-size: 24rpx;
		color: #7f7f7f;
		display: flex;
		align-items: center;
	}

	.seller_center_body_order_body {
		width: 100%;
	}

	.seller_center_body_order_body_item {
		display: inline-block;
		width: 25%;
		text-align: center;
	}

	.seller_center_body_order_body image {
		width: 48rpx;
		height: 48rpx;
		margin: 0 auto;
		margin-bottom: 20rpx;
	}

	/* 订单css  END */
	.weidian_body_menu_list {
		width: 90%;
		margin-left: 50rpx;
		font-size: 30rpx;
		color: #484848;
	}

	.weidian_body_menu_list_item {
		width: 97%;
		height: 70rpx;
		line-height: 70rpx;
		border-bottom: 1rpx #dcdcdc solid;
		margin-top: 40rpx;
		box-sizing: border-box;
	}

	.weidian_body_menu_list_title_left {
		display: inline-block;
		float: left;
	}

	.weidian_body_menu_list_right_icon {
		display: inline-block;
		float: right;
		font-size: 36rpx;
	}
	.weidian_body_menu_list_left_icon {
		display: inline-block;
		float: left;
		font-size: 36rpx;
		margin-right: 10rpx;
	}

	.weidian_body_menu_list_title_right {
		display: inline-block;
		float: right;
		color: #d5d5d5;
		font-size: 24rpx;
		position: relative;
		top: 5rpx;
		margin-right: 5rpx;
	}
</style>
