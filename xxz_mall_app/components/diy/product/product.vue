<template>
	<view class="diy-product" :style="{ background: itemData.style.background }">
		<view :class="['display__' + itemData.style.display]">
			<!-- 列表平铺 -->
			<template v-if="itemData.style.display == 'list'">
				<!-- 单列商品 -->
				<template v-if="itemData.style.column == 1">
					<view :class="['column__' + itemData.style.column]">
						<view class="hlbblock" style="padding: 20rpx;width: 100%" v-for="(product, index) in itemData.data">
							<view @click="gotoDetail(product.product_id)" style="display: flex;width: 100%;justify-content: space-between;">
								<view style="width: 40%">
									<image :src="product.image" style="width: 200rpx;height: 190rpx;border-radius: 15rpx" mode="aspectFill"></image>
								</view>
								<view style="width: 65%;">
									<view style="height: 130rpx">
										<view class="linedot">
											{{product.product_name}}
										</view>
										<view style="font-size: 24rpx;color: #999999;margin-top: 40rpx">
											已售{{product.product_sales}}件
										</view>
									</view>
									<view style="display: flex;justify-content: space-between;height: 55rpx;align-items: baseline">
										<view style="color: #F63E36">
											<text style="font-size: 28rpx;color: #F63E36;font-weight: bold">¥{{product.product_price}}</text><text style="font-size: 35rpx"></text>
											<text class="huaxianjia" style="font-size: 24rpx;color: #999999;margin-left: 20rpx">¥{{product.line_price}}</text>
										</view>
									</view>
								</view>
							</view>
						</view>
					</view>
				</template>
				
				<!-- 两列三列 -->
				<template v-else>
					<view class="product-list" :class="['column__' + itemData.style.column]">
						<view class="product-item" v-for="(product, index) in itemData.data" :key="index" @click="gotoDetail(product.product_id)">
							<view class="product-cover">
								<image :src="product.image" mode="widthFix"></image>
							</view>
							<view class="product-info">
								<view class="product-title">
									{{ product.product_name }}
								</view>
								<view class="price" style="font-size: 24rpx;color: #999999">
									已售{{product.product_sales}}件
								</view>
								<view class="price" style="display: flex;justify-content: flex-start;height: 55rpx;align-items: baseline">
									<text style="font-size: 28rpx;color: #F63E36;font-weight: bold">¥{{product.product_price}}</text><text style="font-size: 35rpx"></text>
									<text class="huaxianjia" style="font-size: 24rpx;color: #999999;margin-left: 20rpx">¥{{product.line_price}}</text>
								</view>
							</view>
						</view>
					</view>
				</template>
			</template>
			
			<!-- 横向滚动 -->
			<template v-else>
				<scroll-view :scroll-top="scrollTop" scroll-x="true">
					<view class="product-list" :class="['column__' + itemData.style.column]">
						<view v-for="(product, index) in itemData.data" :key="index" @click="gotoDetail(product.product_id)">
							<view class="product-item">
								<view class="product-cover">
									<image :src="product.image" mode="aspectFill"></image>
								</view>
								<view class="product-info">
									<view v-if="itemData.style.show.productName == 1" class="product-title">
										{{ product.product_name }}
									</view>
									<view class="price d-s-c f12">
										<view v-if="itemData.style.show.productPrice == 1" class="orange">
											<text>¥</text>
											<text class="">{{ product.product_price }}</text>
										</view>
										<view class="ml10 gray9 text-d-line" v-if="itemData.style.show.linePrice == 1 && product.line_price > 0">
											¥{{ product.line_price }}
										</view>
									</view>
								</view>
							</view>
						</view>
					</view>
				</scroll-view>
			</template>
		</view>
	</view>
</template>

<script>
	export default {
		components: {},
		data() {
			return {};
		},
		props: ['itemData'],
		created() {},
		methods: {
			scroll(e) {},

			/*跳转产品详情*/
			gotoDetail(e) {
				let url = '/pages/product/detail/detail?product_id=' + e;
				this.gotoPage(url);
			}
		}
	};
</script>

<style>
	.diy-product {
		padding: 0 20rpx;
	}
	
    .huaxianjia {
        text-decoration: line-through;
        color: #999;
        margin-left: 5rpx;
    }

	.diy-product .product-list.column__1 .product-item {
		margin-bottom: 20rpx;
		background: #ffffff;
		width: 100%;
	}

	.diy-product .product-list.column__1 .product-item-box {
		display: flex;
		justify-content: flex-start;
		align-items: stretch;
	}

	.diy-product .product-list .product-cover image {
		width: 100%;
		height: 100%;
	}

	.diy-product .product-list.column__1 .product-cover {
		width: 240rpx;
		height: 240rpx;
		border-radius: 20rpx;
		overflow: hidden;
	}

	.diy-product .product-list.column__1 .product-info {
		margin: 20rpx 20rpx 20rpx 30rpx;
		flex: 1;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
	}

	.diy-product .product-list .product-info .already-sale text {
		padding: 4rpx 8rpx;
		border-radius: 8rpx;
		background: #f2f2f7;
		color: #999;
	}

	.diy-product .product-list .product-title {
		display: -webkit-box;
		font-size: 32rpx;
		overflow: hidden;
		word-wrap: break-word;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
	}

	.diy-product .product-list.column__1 .product-title {
		max-height: 80rpx;
		line-height: 40rpx;
	}

	.diy-product .product-list.column__1 .selling-point {
		display: -webkit-box;
		overflow: hidden;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		line-height: 30rpx;
		max-height: 60rpx;
	}

	.diy-product .product-list.column__1 .already-sale {
		margin-top: 10rpx;
	}

	.diy-product .product-list.column__2 .product-title {
		height: 80rpx;
		line-height: 40rpx;
		margin-top: 20rpx;
		font-size: 26rpx;
		margin-bottom: 20rpx;
	}

	.diy-product .product-list.column__3 .product-title {
		margin-top: 20rpx;
		margin-left: 10rpx;
		margin-right: 10rpx;
		height: 72rpx;
		line-height: 36rpx;
		font-size: 30rpx;
	}

	.diy-product .product-list.column__2 .product-info {
		padding: 0 24rpx;
	}

	.diy-product .product-list .price {
		font-size: 34rpx;
	}

	.diy-product .product-list.column__2 .price {
		margin-top: 10rpx;
		font-size: 30rpx;
	}

	.diy-product .product-list.column__3 .price {
		margin-top: 10rpx;
		padding: 0 10rpx;
		font-size: 28rpx;
	}

	.diy-product .product-list.column__3 .price .text-d-line {
		font-size: 22rpx;
	}

	.diy-product .product-list.column__2,
	.diy-product .product-list.column__3 {
		display: flex;
		flex-wrap: wrap;
		justify-content: flex-start;
		align-items: flex-start;
	}

	.diy-product .column__2 .product-item {
		width: 345rpx;
		margin-right: 20rpx;
		margin-bottom: 20rpx;
		padding-bottom: 20rpx;
		overflow: hidden;
		background: #ffffff;
		box-shadow: 0px 8rpx 3rpx 0px rgba(6, 0, 1, 0.03);
		border-radius: 12rpx;
	}

	.diy-product .display__list .column__2 .product-item:nth-child(2n + 0) {
		margin-right: 0;
	}

	.diy-product .column__2 .product-cover {
		width: 100%;
		height: 260rpx;
		border-radius: 12rpx 12rpx 0 0;
		/* margin: 20rpx auto; */
		overflow: hidden;
	}

	.diy-product .column__3 .product-item {
		width: 230rpx;
		margin-bottom: 10rpx;
		margin-right: 10rpx;
		padding-bottom: 10rpx;
		border-radius: 8rpx;
		overflow: hidden;
		background: #ffffff;
		box-shadow: 0 0 8rpx rgba(0, 0, 0, 0.1);
	}

	.diy-product .display__list .column__3 .product-item:nth-child(3n + 0) {
		margin-right: 0;
	}

	.diy-product .column__3 .product-cover {
		width: 100%;
		height: 230rpx;
		overflow: hidden;
	}

	.diy-product .display__slide .product-list {
		flex-wrap: nowrap;
	}

	.diy-product .display__slide .column__2 .product-item {
		width: 300rpx;
	}

	.diy-product .display__slide .column__3 .product-item {
		width: 200rpx;
	}
</style>
