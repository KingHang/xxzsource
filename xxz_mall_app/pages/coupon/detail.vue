<template>
	<view class="coupon_bg" v-if="!loading">
		<!-- #ifdef APP-PLUS -->
		<header-bar></header-bar>
		<!-- #endif -->
		<!-- #ifdef MP-WEIXIN -->
		<view class="titleBar">
			<view class="head_top" :style="'height:'+statusBarHeight+'px;'"></view>
			<view class="tc d-c-c f36 pr" :style="'height:'+titleBarHeight+'px;z-index:100'">
				<view class="reg180" @click="goback"><text class="icon iconfont icon-jiantou"></text></view>
				<view>优惠券</view>
			</view>
			<view :style="'height:'+titleHeight+'px;opacity:'+opacity+';'" class="top_bg"></view>
		</view>
		<view :style="'height:'+titleHeight+'px;'">
			<view class="head_top" :style="'height:'+statusBarHeight+'px;'"></view>
			<view :style="'height:'+titleBarHeight+' px;'"></view>
		</view>
		<!-- #endif -->
		<view class="coupon-detail-top">
			<view class="top_box">
				<view><text class="icon iconfont icon-dianpu1" style="color: #333333;font-size: 30rpx;"></text><text class="gray3 fb f30 ml10">{{detail.supplier.name}}</text></view>
				<!-- 优惠券 -->
				<view class="item-wrap">
					<view :class="'coupon-item coupon-item-'+detail.color.text">
						<!--装饰用的小圆-->
						<view class="circles">
							<text v-for="(circle,num) in 8" :key="num"></text>
						</view>
						<view class="info">
							<view>{{detail.coupon_type.text}}</view>
						</view>
						<view class="operation d-b-c w-b">
							<view class="flex-1 coupon-content">
								<view>
									<template v-if="detail.coupon_type.value == 10">
										<view class="price">
											<text>￥</text>
											<text class="f40 fb">{{detail.reduce_price}}</text>
										</view>
									</template>
									<template v-if="detail.coupon_type.value == 20">
										<text class="f40 fb">{{ detail.discount }}</text><text>折</text>
									</template>
								</view>
								<view class="f30">{{detail.name}}</view>
								<view class="f24">
									<template v-if="detail.expire_type == 20">
										有效期：{{ detail.start_time.text }} 至 {{ detail.end_time.text }}
									</template>
									<template v-if="detail.expire_type == 10">
										有效期：领取后{{ detail.expire_day }}天有效
									</template>
								</view>
							</view>
							<view class="btns d-c-c" @click="receiveCoupon()">
								<button  v-if=" detail.state.value == 1" type="default" :class="'btn-red'">
									立即领取
								</button>
									<button type="default" v-else class="btn-gray" v-on:click.stop>
								{{ detail.state.text }}
							</button>
							</view>
						</view>
					</view>
				</view>
			</view>
			<!--  -->
			<view class="redF6 f26 fb d-c-c">
				<image class="decorate" src="/static/icon/coupons_arrow.png" mode=""></image>
				<text class="ml10 mr10">指定以下商品使用</text>
				<image class="decorate" src="/static/icon/coupons_arrow.png" mode=""></image>
			</view>
			<!-- 商品列表 -->
			<view class="o-h pro_list">
				<view class="pro_item" v-for="(item,index) in product" :key='index' @click="gotoPage('/pages/product/detail/detail?product_id='+item.product_id)">
					<view class="pro_item_image">
						<image :src="item.product_image || '/static/default.png'" mode=""></image>
					</view>
					<view class="f26 gray3 text-ellipsis mt20 mb23 tc">{{item.product_name}}</view>
					<view class="fb redF6 tc"><text class="f22">￥</text><text class="f32">{{item.product_price}}</text></view>
					<view class="d-c-c">
						<button class="add_btn">加入购物车</button>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				loading:true,
				statusBarHeight: 0,
				titleBarHeight: 0,
				titleHeight: 0,
				opacity: 0,
				coupon_id: 0,
				product: [],
				detail: {}
			}
		},
		onPageScroll(e) {
			if (e.scrollTop < 100) {
				this.opacity = e.scrollTop / 100
			} else {
				this.opacity = 1
			}

		},
		onLoad(e) {
			this.coupon_id = e.coupon_id
			this.GetStatusBarHeight();
		},
		onShow() {
			/*获取数据*/
			this.getData();
		},
		methods: {
			GetStatusBarHeight() {
				const SystemInfo = uni.getSystemInfoSync();
				// #ifdef MP-WEIXIN
				let that = this;
				let statusBarHeight = SystemInfo.statusBarHeight;
				this.statusBarHeight = uni.getMenuButtonBoundingClientRect().top;
				this.titleBarHeight = uni.getMenuButtonBoundingClientRect().height;
				this.titleHeight = this.statusBarHeight + this.titleBarHeight + 8;
				// #endif
				// #ifndef MP-WEIXIN
				this.statusBarHeight = SystemInfo.statusBarHeight;
				this.titleHeight = this.statusBarHeight;
				// #endif
			},
			getData() {
				let self = this;
				self.loading=true;
				uni.showLoading({
					title: '加载中'
				});
				let data_type = self.data_type;
				self._get('coupon.coupon/detail', {
					coupon_id: self.coupon_id,
				}, function(res) {
					uni.hideLoading();
					self.loading=false;
					self.detail = res.data.model;
					self.product = res.data.model.product;
				});
			},
			receiveCoupon(){
				let self = this;
				if (self.detail.state.value == 0) {
					return false;
				}
				self._post(
					'user.coupon/receive', {
						coupon_id: self.detail.coupon_id
					},
					function(result) {
						uni.showToast({
							title: '领取成功',
							icon: 'success',
							mask: true,
							duration: 2000
						});
						self.detail.state.value = 0;
						self.detail.state.text = '已领取';
					}
				);
			},
			goback() {
				uni.navigateBack()
			}
		}
	}
</script>

<style>
	page {}

	.coupon_bg {
		background: linear-gradient(0deg, #FFFDD7 0%, #FEE6DF 100%);
	}

	.titleBar {
		position: fixed;
		width: 100%;
		top: 0;
		z-index: 100;
	}

	.top_bg {
		width: 750rpx;
		position: absolute;
		background: linear-gradient(41deg, #FFF4F1 13%, #FDFFF7 48%, #F6FFFB 75%, #FFF2EE 100%);
		z-index: 99;
		top: 0;

	}

	.top_box {
		background: rgba(0, 0, 0, 0.05);
		padding: 28rpx 30rpx 30rpx 30rpx;
		border-radius: 20rpx;
		margin-top: 10rpx;
		margin-bottom: 5rpx;
	}

	.coupon-wrap {
		padding: 30rpx;
	}

	.coupon-detail-top {}

	.item-wrap {
		padding: 20rpx 0;
	}

	.coupon-item .btns button {
		border: none;
	}

	.coupon-item .circles text {
		display: block;
		width: 10rpx;
		height: 20rpx;
		background: linear-gradient(0deg, #FFFDD7 0%, #FEE6DF 100%);
		border-radius: 0 10rpx 10rpx 0
	}

	.coupon-item .info::before,
	.coupon-item .info::after {
		background: linear-gradient(0deg, #FFFDD7 0%, #FEE6DF 100%);
	}

	.decorate {
		width: 16rpx;
		height: 16rpx;
	}

	.add_btn {
		width: 181rpx;
		height: 40rpx;
		background: #F6220C;
		border-radius: 20rpx;
		font-size: 24rpx;
		font-family: PingFang SC;
		font-weight: 500;
		color: #FFFFFF;
		padding: 0;
		border: none;
		display: flex;
		justify-content: center;
		align-items: center;
		margin-bottom: 22rpx;
	}

	.add_btn .icon-jia {
		color: #FFFFff;
		font-size: 18rpx;
	}

	.pro_list {
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-wrap: wrap;
		padding: 0 30rpx;
		padding-bottom: 80rpx;
	}

	.pro_item {
		width: 335rpx;
		border-radius: 12rpx;
		overflow: hidden;
		background-color: #FFFFFF;
		margin-top: 20rpx;
	}

	.pro_item_image>image {
		width: 335rpx;
		height: 270rpx;
	}

	.head_top {
		position: relative;
		line-height: 30px;
		color: #FFFFFF;
		font-size: 32rpx;
	}

	.reg180 {
		padding-right: 20rpx;
		text-align: right;
		transform: rotateY(180deg);
		position: absolute;
		bottom: 0;
		height: 100%;
		display: flex;
		align-items: center;
		left: 0;
	}

	.icon-jiantou {
		color: #333333;
		font-size: 32rpx;
	}
</style>
