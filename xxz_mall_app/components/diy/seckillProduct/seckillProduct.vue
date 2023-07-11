<template>
	<view class="diy-seckillProduct" v-if="itemData.data.product_list.length>0">
		<view class="sharpproduct-head d-b-c">
			<view class="left d-s-c">
				<view class="name d-c-c">
					<image src="../../../static/icon/activity.png" mode="" style="height: 32rpx;width: 32rpx;margin-right: 18rpx;"></image>限时秒杀
				</view>
				<view class="datetime d-s-c ml20">
					<Countdown :config="countdownConfig"></Countdown>
				</view>
			</view>
			<view class="right d-e-c" @click="gotoList">
				<text class="gray9 f26">更多</text>
				<text class="iconfont icon-jiantou"></text>
			</view>
		</view>
		<view class="sharpproduct-body column__3">
			<scroll-view scroll-x="true">
				<view class="product-list">
					<view class="product-item" v-for="(item, index) in itemData.data.product_list" :key="index" @click="gotoDetail(item.seckill_product_id)">
						<!-- 两列三列 -->
						<template>
							<view class="product-cover">
								<image :src="item.product.file_path" mode="aspectFit"></image>
							</view>
							<view class="product-info p-0-10">
								<view v-if="itemData.style.show.productName == true" class="product-title">{{ item.product.product_name }}</view>
								<view class="price d-c-c">
									<view v-if="itemData.style.show.seckillPrice == true" class="red">
										<text class="f20">¥</text>
										<text class="f32 fb">{{ item.seckill_price }}</text>
									</view>
									<text class="ml10 gray9 f20 text-d-line" v-if="itemData.style.show.linePrice == true">¥</text>
									<text class="gray9 f24 text-d-line" v-if="itemData.style.show.linePrice == true">{{ item.product_price }}</text>
								</view>
							</view>
						</template>
					</view>
				</view>
			</scroll-view>
		</view>
	</view>
</template>

<script>
	import Countdown from '@/components/countdown/countdown.vue';
	export default {
		components: {
			Countdown
		},
		data() {
			return {
				/*倒计时配置*/
				countdownConfig: {
					/*开始时间*/
					startstamp: 0,
					/*结束时间*/
					endstamp: 0,
					/*标题*/
					title: ' '
				}
			};
		},
		props: ['itemData'],
		created() {
			this.countdownConfig.endstamp = this.itemData.data.end_time;
			this.countdownConfig.startstamp = this.itemData.data.start_time;
		},
		methods: {
			scroll(e) {},

			/*跳转列表*/
			gotoList() {
				let url = '/pages/plus/seckill/list/list';
				this.gotoPage(url);
			},

			/*跳转产品详情*/
			gotoDetail(e) {
				let url = '/pages/plus/seckill/detail/detail?seckill_product_id=' + e;
				this.gotoPage(url);
			}
		}
	};
</script>

<style lang="scss">
	.diy-seckillProduct {
		margin: 20rpx 20rpx;
		border-radius: 20rpx;
		padding: 0 20rpx 20rpx;
		background: #ffffff;
	}

	.sharpproduct-head {
		height: 100rpx;
		color: #000000;
	}

	.sharpproduct-head .name {
		font-size: 32rpx;
		font-weight: bold;
	}

	.sharpproduct-head .datetime::v-deep text {
		font-size: 24rpx;
		color: #F6220C;
	}

	.sharpproduct-head .datetime::v-deep .box {
		padding: 4rpx 10rpx;
		font-size: 22rpx;
		background: #FFEBEB;
		color: #F6220C;
	}

	.sharpproduct-head .icon-jiantou {
		margin-left: 8rpx;
		color: #999999;
		font-size: 22rpx;
	}

	.diy-seckillProduct .product-list .product-item {
		overflow: hidden;
		flex-shrink: 0;
	}

	.diy-seckillProduct .product-list .product-cover image {
		width: 100%;
		height: 100%;
		background-color: #F4F4F4;
		border-radius: 12rpx;
	}

	.diy-seckillProduct .product-list .product-cover .price {
		font-size: 20rpx;
		color: #F01A1A;
		line-height: 26rpx;
		padding-top: 16rpx;
		padding-bottom: 8rpx;
	}

	.diy-seckillProduct .product-list .product-title {
		margin-top: 26rpx;
		height: 30rpx;
		line-height: 30rpx;
		display: -webkit-box;
		overflow: hidden;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
		font-size: 30rpx;
	}

	.diy-seckillProduct .column__3 .product-list .product-title {
		font-size: 28rpx;
	}

	.diy-seckillProduct .column__3 .product-list {
		display: flex;
		flex-wrap: nowrap;
		justify-content: flex-start;
	}

	.diy-seckillProduct .column__3 .product-item {
		width: 240rpx;
		margin-right: 20rpx;
		border-radius: 12rpx;
		overflow: hidden;
		background: #ffffff;
	}

	.diy-seckillProduct .column__3 .product-cover {
		width: 240rpx;
		height: 240rpx;
		margin: 0 auto;

	}

	.diy-assembleproduct .product-list {
		flex-wrap: nowrap;
	}
</style>
