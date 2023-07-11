<template>
	<view class="bargain-container">
		<view class="top-box">
			<view class="inner-tab">
				<scroll-view scroll-X="true" show-scrollbar="false" class="scroll-X">
					<view class="tab-list">
						<view :class="bargain_activity_id == item.bargain_activity_id ? 'item active' : 'item'" v-for="(item, index) in categorys"
						 :key="index" @click="tabTypeFunc(index, item.bargain_activity_id)">
							{{ item.title }}
						</view>
					</view>
				</scroll-view>
			</view>
			<!--图片-->
			<view class="banner-image d-c-c" v-if="listData.detail">
				<image :src="listData.detail.file_path" mode="widthFix"></image>
			</view>
			<!--活动时间-->
			<view class="ad-datetime p-30-0 d-c-c pb0" v-if="listData.detail">
				<Countdown ref='countdown' :config="countdownConfig" @returnVal="returnValFunc"></Countdown>
			</view>
		</view>
		<!--内容-->
		<view class="bargain-list" v-if="!loading">
			<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50">

				<!--列表-->
				<view class="list d-s-c f-w">
					<view class="item d-s-s d-stretch" v-for="(item, index) in listData.list" :key="index">
						<view class="product-cover pr">
							<image :src="item.product.file_path" mode="aspectFit"></image>
						</view>
						<view class="product-info d-b-c d-c">
							<view class="product-title text-ellipsis-2 f26 gray3">{{ item.product.product_name }}</view>
							<view class="people-num price">
								<text class="f24">￥</text>
								<text class="f36 fb">{{ item.bargain_price }}</text>
								<text class="ml10 text-d-line gray9 f24">￥{{ item.product_price }}</text>
							</view>
							<view class="d-b-c ww100">
								<view class="f24">
								</view>
								<view class="right-btn"><button type="primary" @click="gotoDetail(item)">去砍价</button></view>
							</view>
						</view>
					</view>
				</view>
			</scroll-view>
		</view>
	</view>
</template>

<script>
	import Countdown from '@/components/countdown/countdown.vue'
	export default {
		components: {
			Countdown
		},
		data() {
			return {
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				/*活动类别*/
				categorys: [],
				/*当前活动Is*/
				bargain_activity_id: 0,
				/*商品列表*/
				listData: [],
				/*倒计时配置*/
				countdownConfig: {
					/*开始时间*/
					startstamp: 0,
					/*结束时间*/
					endstamp: 0
				},
				/*是否正在加载*/
				loading: true
			};
		},
		computed: {},
		onLoad(e) {},
		onShow() {
			/*获取产品列表*/
			this.getCategory();
		},
		mounted() {
			this.init();

		},
		onReachBottom() {},
		methods: {
			/*初始化*/
			init() {
				let _this = this;
				uni.getSystemInfo({
					success(res) {
						_this.phoneHeight = res.windowHeight;
						// 计算组件的高度
						let view = uni.createSelectorQuery().select('.top-box');
						view.boundingClientRect(data => {
							let h = _this.phoneHeight - data.height;
							_this.scrollviewHigh = h;
						}).exec();
					}
				});
			},

			/*类别切换*/
			tabTypeFunc(e, n) {
				this.type_active = e;
				this.bargain_activity_id = n;
				this.productList = [];
				this.getProdct();
			},

			/*获取数据*/
			getCategory() {
				let self = this;
				let param = {};
				let index = self.type_active;
				self._get('plus.bargain.product/active', {
					param
				}, function(res) {
					self.categorys = res.data.list;
					self.bargain_activity_id = self.categorys[0].bargain_activity_id;
					self.getProdct();
				});
			},

			/*获取商品*/
			getProdct() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				let bargain_activity_id = self.bargain_activity_id;
				self.loading = true;
				self._get(
					'plus.bargain.product/product', {
						bargain_activity_id: bargain_activity_id
					},
					function(res) {
						self.listData = res.data;
						self.countdownConfig.endstamp = res.data.detail.end_time;
						self.countdownConfig.startstamp = res.data.detail.start_time;
						uni.hideLoading();
						self.loading = false;
					}
				);
			},

			/*跳转产品详情*/
			gotoDetail(e) {
				this.$refs.countdown.clear();
				let url = 'pages/plus/bargain/detail/detail?bargain_product_id=' + e.bargain_product_id
				this.gotoPage(url);
			},

			/*跳转搜索页面*/
			gotoSearch() {
				this.page = 1;
				this.getData();
			},

			/*倒计时返回值*/
			returnValFunc(e) {
				console.log(e);
			},
		}
	};
</script>

<style lang="scss">
	page {
		background: #f2f2f2;
	}

	.bargain-container .inner-tab {
		background: #ffffff;
	}

	.bargain-container .inner-tab .tab-list {
		height: 100rpx;
		display: flex;
		justify-content: flex-start;
		align-items: center;
		background: #FFFFFF;
	}

	.bargain-container .inner-tab .item {
		height: 100rpx;
		line-height: 100rpx;
		white-space: nowrap;
		padding: 0 30rpx;
		font-size: 30rpx;
		color: #333333;
	}

	.bargain-container .inner-tab .item.active,
	.bargain-container .inner-tab .item .arrow.active .iconfont {
		background: #FFFFFF;
		font-size: 32rpx;
		color: #F6220C;
		position: relative;
	}

	.bargain-container .inner-tab .item.active::after {
		content: '';
		width: 60%;
		height: 4rpx;
		background: #F6220C;
		border-radius: 2rpx;
		position: absolute;
		bottom: 17rpx;
		left: 0;
		right: 0;
		margin: auto;
	}

	.bargain-container .inner-tab .box {
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: row;
	}

	.bargain-container .inner-tab .arrows {
		margin-left: 10rpx;
		line-height: 0;
	}

	.bargain-container .inner-tab .iconfont {
		line-height: 24rpx;
		font-size: 24rpx;
	}

	.bargain-container .inner-tab .arrow,
	.bargain-container .inner-tab .svg-icon {
		width: 20rpx;
		height: 20rpx;
	}

	.bargain-container .banner-image {
		width: 100%;
		box-sizing: border-box;
	}

	.bargain-container .banner-image image {
		width: 750rpx;
		height: 365rpx;
	}

	.bargain-container .ad-datetime::v-deep text {
		color: #333333;
		font-size: 28rpx;
	}

	.bargain-container .ad-datetime::v-deep .box {
		padding: 4rpx;
		border-radius: 4rpx;
		background: #F6220C;
		color: #ffffff;
	}

	.bargain-list .list {
		padding: 20rpx;
	}

	.bargain-list .list .item {
		width: 100%;
		padding: 30rpx;
		margin-bottom: 20rpx;
		box-sizing: border-box;
		border-radius: 16rpx;
		background: #ffffff;
	}

	.bargain-list .product-cover {
		padding: 4rpx;
	}

	.bargain-list .product-cover image {
		width: 200rpx;
		height: 200rpx;
		border-radius: 12rpx;
	}

	.bargain-list .product-info {
		flex: 1;
		padding-left: 20rpx;
		overflow: hidden;
	}

	.bargain-list .product-cover .people-num {
		position: absolute;
		right: 0;
		bottom: 0;
		left: 0;
		height: 50rpx;
		padding: 0 20rpx;
		line-height: 50rpx;
		font-size: 24rpx;
		box-sizing: border-box;
		background: rgba(0, 0, 0, 0.6);
	}

	.bargain-list .ad-datetime .box {
		padding: 4rpx;
		border-radius: 4rpx;
		background: #000000;
		color: #ffffff;
	}

	.bargain-list .product-title {
		width: 100%;
		height: 40rpx;
		line-height: 40rpx;
		font-size: 32rpx;
		color: #333333;
	}

	.bargain-list .people-num {
		width: 100%;
	}

	.bargain-list .already-sale {
		padding: 4rpx 0;
		color: #999;
		font-size: 24rpx;
	}

	.bargain-list .price {
		width: 100%;
		color: $dominant-color;
		font-size: 24rpx;
	}

	.bargain-list .price .num {
		padding: 0 4rpx;
	}

	.bargain-list .slider-box .slider {
		margin-top: 10rpx;
		height: 10rpx;
		background: #cccccc;
		border-radius: 5rpx;
	}

	.bargain-list .slider-box .slider-inner {
		height: 10rpx;
		background: #e2231a;
		border-radius: 5rpx;
	}

	.bargain-list .right-btn button {
		height: 60rpx;
		line-height: 60rpx;
		border-radius: 30rpx;
		background: #8D60FF;
		color: #ffffff;
		font-size: 32rpx;
	}
</style>
