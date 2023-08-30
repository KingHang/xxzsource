<template>
	<view class="express-info" v-if="!loadding">
        <view style="padding: 30rpx;background-color: white">
            <view></view>
            <view>
                <view style="color: #F63E36;font-size: 32rpx">
                    运输中
                </view>
                <view style="color: #999999;font-size: 24rpx">
                    物流公司：{{express.express_name}}
                </view>
                <view style="color: #999999;font-size: 24rpx">
                    运单编号：{{express.express_no}}
                </view>
            </view>
        </view>
		
	<!--	<view class="base-info p30">
			<view class="name">
				<text class="gray9">物流公司：</text>
				<text class="fb">{{express.express_name}}</text>
			</view>
			<view class="order-code pt20">
				<text class="gray9">物流单号：</text>
				<text class="fb red"> {{express.express_no}}</text>
			</view>
		</view>-->
		
        <view class="fui-list-group express-list">
            <block v-for="(item,index) in express.list">
                <view v-if="index==0" class="fui-list current no-border">
                    <view class="fui-list-inner">
                        <view class="text step" style="padding-left: 20rpx">{{item.context}}-{{item.status}}</view>
                        <view class="text time" style="padding-left: 20rpx">{{item.time}}</view>
                    </view>
                </view>
                <view v-else class="fui-list no-border">
                    <view class="fui-list-inner">
                        <view class="text step" style="padding-left: 20rpx;color: #999999">{{item.context}}-{{item.status}}</view>
                        <view class="text time" style="padding-left: 20rpx;color: #999999">{{item.time}}</view>
                    </view>
                </view>
            </block>
        </view>

		<!--<view class="list">
			<view :class="index==0?'active item':'item'" v-for="(item, index) in express.list" :key="index">
				<view class="content">{{item.context}}-{{item.status}}</view>
				<view class="datetime">{{item.time}}</view>
			</view>
		</view>-->
	</view>
</template>

<script>
	export default {
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
				/*商品id*/
				product_id: 0,
				/*快递信息*/
				express: {
					list: {}
				}
			};
		},
		onLoad(e) {
			uni.showLoading({
			    title: '加载中'
			});
			this.order_id = e.order_id;
			this.product_id = e.product_id;
		},
		mounted() {
			/*获取数据*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				if (this.product_id) {
					this.partDelivery();
				} else {
					this.unifiedDelivery();
				}
			},
			/*统一发货*/
			unifiedDelivery() {
				let self = this;
				let order_id = self.order_id;
				self._get('user.order/express', {
					order_id: order_id
				}, function(res) {
					self.express = res.data.express;
					self.loadding = false;
					uni.hideLoading();
				});
			},
			/*部分发货*/
			partDelivery() {
				let self = this;
				let order_id = self.order_id;
				let product_id = self.product_id;
				self._get('user.order/productExpress', {
					order_id: order_id,
					product_id: product_id
				}, function(res) {
					self.express = res.data.express;
					self.loadding = false;
					uni.hideLoading();
				});
			}
		}
	};
</script>

<style>
    .fui-list-group.express-list .fui-list .fui-list-inner:before {
        content: " ";
        width: 0;
        height: 100%;
        position: absolute;
        border-left: 4rpx solid #e5e4e5;
        top: 0;
        left: 40rpx;
        z-index: 9999;
    }

    .express-list .fui-list:first-child .fui-list-inner:before {
        top: 40rpx;
    }

    .express-list .fui-list:last-child .fui-list-inner:before {
        height: 40rpx;
    }

    .express-list .fui-list .fui-list-inner {
        padding: 30rpx 70rpx;
    }

    .express-list .fui-list.current .fui-list-inner .text {
        font-size: 26rpx;
    }

    .express-list .fui-list .fui-list-inner .text.step:before {
        content: " ";
        height: 20rpx;
        width: 20rpx;
        background: white;
        border: 10rpx solid rgba(0, 0, 0, 0.08);
        border-radius: 050%;
        position: absolute;
        z-index: 9999;
        top: 10rpx;
        left: -45rpx;
    }

    .express-list .fui-list.current .fui-list-inner .text.step:before {
        background: white;
        height: 20rpx;
        width: 20rpx;
        left: -44rpx;
        border: 10rpx solid #F63E36;
    }

    .express-list .fui-list {
        padding: 0;
    }

    .fui-list-inner .text {
        position: relative;
        font-size: 26rpx;
        line-height: 34rpx;
        color: #666;
    }

    .express-info .base-info {
		background: #ffffff;
		border-bottom: 1px solid #eeeeee;
	}

	.express-info .list {
		padding: 30rpx 30rpx 30rpx 50rpx;
		margin-top: 20rpx;
		border-top: 1px solid #eeeeee;
		background: #ffffff;
	}

	.express-info .list .item {
		position: relative;
		padding: 30rpx;
		padding-left: 40rpx;
		padding-right: 0;
		border-left: 1px solid #cccccc;
	}

	.express-info .list .item::before {
		display: block;
		content: '';
		position: absolute;
		top: 30rpx;
		left: -18rpx;
		width: 20rpx;
		height: 20rpx;
		border: 8rpx solid #ffffff;
		border-radius: 50%;
		background: #CCCCCC;
	}

	.express-info .list .item::after {
		display: block;
		content: '';
		position: absolute;
		top: 30rpx;
		left: -18rpx;
		width: 30rpx;
		height: 30rpx;
		border-radius: 50%;
		border: 4rpx solid #CCCCCC;
	}

	.express-info .list .item.active::before {
		background: #f00808;
	}

	.express-info .list .item.active::after {
		border: 4rpx solid #f00808;
	}

	.express-info .list .item {
		color: #999999;
	}

	.express-info .list .item.active {
		color: #f00808;
	}
</style>
