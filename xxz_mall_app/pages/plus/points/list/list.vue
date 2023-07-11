<template>
	<view class="prodcut-list-wrap">
		<view class="point_top">
			<image src="../../../../static/point_bg.png" mode=""></image>
			<view class="tc pr">
				<view class="f60 fb white">{{my_points}}</view>
				<view class="f26 white">我的积分</view>
			</view>
		</view>
		<view class="p-0-20 bg-white f32 gray3 fb ponit_title">好物兑换</view>
		<view class="list">
			<view class="item" v-for="(item, index) in listData" :key="index">
				<view class="product-cover">
					<image :src="item.product_image" mode="aspectFit"></image>
				</view>
				<view class="product-info flex-1">
					<view class="product-title">{{ item.product.product_name }}</view>
					<view class="already-sale d-b-c">
						<text>还剩{{ item.stock }}件</text>
					</view>
					<view class="price">
						<template v-if="item.sku[0].point_money!=0">
							¥
							<text class="num">{{ item.sku[0].point_money }}</text>
							<text class="f30">+</text>
						</template>

						<text class="num">{{ item.sku[0].point_num }}</text>
						<text class="f26">积分</text>
					</view>
				</view>
				<view class="d-c-e">
					<view class="point_btn" @click="gotoList(item.point_product_id)">兑换</view>
				</view>
			</view>
		</view>
		<!-- 没有记录 -->
		<view class="d-c-c p30" v-if="listData.length == 0 && !loading">
			<text class="iconfont icon-wushuju"></text>
			<text class="cont">亲，暂无相关记录哦</text>
		</view>
		<uni-load-more v-else :loadingType="loadingType"></uni-load-more>
	</view>
</template>

<script>
	import uniLoadMore from '@/components/uni-load-more.vue';
	export default {
		components: {
			uniLoadMore
		},
		data() {
			return {
				/*是否加载完成*/
				loading: true,
				/*数据列表*/
				listData: [],
				/*最后一页码数*/
				last_page: 0,
				/*当前页面*/
				page: 1,
				/*每页条数*/
				list_rows: 10,
				no_more: false,
				my_points: 0
			};
		},
		onLoad() {
			this.getData();
		},
		computed: {
			/*加载中状态*/
			loadingType() {
				if (this.loading) {
					return 1;
				} else {
					if (this.listData.length != 0 && this.no_more) {
						return 2;
					} else {
						return 0;
					}
				}
			}
		},
		onReachBottom() {
			let self = this;
			if (self.page < self.last_page) {
				self.page++;
				self.getData();
			}
			self.no_more = true;
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				self.loading = true;
				self._get(
					'plus.points.product/index', {
						page: self.page || 1,
						list_rows: self.list_rows
					},
					function(res) {
						self.loading = false;
						self.listData = self.listData.concat(res.data.list.data);
						self.my_points = res.data.user.points;
						self.last_page = res.data.list.last_page;
						if (res.data.list.last_page <= 1) {
							self.no_more = true;
						}
					}
				);
			},

			/*跳转产品列表*/
			gotoList(e) {
				this.gotoPage('/pages/plus/points/detail/detail?point_product_id=' + e);
			}
		}
	};
</script>

<style lang="scss">
	page {
		background-color: #FFFFFF;
	}

	.point_top {
		position: relative;
		width: 750rpx;
		height: 240rpx;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.point_top image {
		position: absolute;
		width: 750rpx;
		height: 240rpx;
		z-index: 0;
	}

	.prodcut-list-wrap .list {
		background: #ffffff;
		padding-left: 20rpx;
	}

	.ponit_title {
		padding-top: 40rpx;
	}

	.prodcut-list-wrap .list .item {
		padding: 30rpx 0;
		display: flex;
		border-bottom: 1px solid #dddddd;
	}

	.prodcut-list-wrap .product-cover,
	.prodcut-list-wrap .product-cover image {
		width: 150rpx;
		height: 150rpx;
		border-radius: 12rpx;
	}

	.prodcut-list-wrap .product-info {
		flex: 1;
		margin-left: 26rpx;
		display: flex;
		flex-direction: column;
		justify-content: space-between;

	}

	.prodcut-list-wrap .product-title {
		display: -webkit-box;
		overflow: hidden;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
		font-size: 32rpx;
		width: 380rpx;
	}

	.prodcut-list-wrap .price {
		color: #F6220C;
		font-size: 20rpx;
		line-height: 28rpx;
	}

	.prodcut-list-wrap .price .num {
		padding: 0 4rpx;
		font-size: 36rpx;
	}

	.prodcut-list-wrap .already-sale {
		color: #999;
		font-size: 26rpx;
		margin-bottom: 18rpx;
	}

	.prodcut-list-wrap .already-sale .btn-red {
		line-height: 2;
		font-size: 28rpx;
	}

	.point_btn {
		width: 120rpx;
		height: 50rpx;
		background: linear-gradient(90deg, #7B45FF 0%, #961EFF 100%);
		border-radius: 25rpx;
		font-size: 26rpx;
		color: #FFFFFF;
		line-height: 50rpx;
		text-align: center;
		margin-right: 30rpx;
	}
</style>
