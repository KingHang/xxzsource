<template>
	<view class="diy-assembleproduct" v-if="itemData.data.product_list.length>0">
		<view class="diy-head d-b-c">
			<view class="left d-s-c">
				<view class="name d-c-c">
					<image src="../../../static/icon/activity.png" mode="" style="height: 32rpx;width: 32rpx;margin-right: 18rpx;"></image>每日必拼
				</view>
			</view>
			<view class="right" @click="gotoList">
				<text class="f26 gray9">更多</text>
				<text class="iconfont icon-jiantou"></text>
			</view>
		</view>
		<view class="diy-body">
			<scroll-view scroll-x="true">
				<view class="product-list column__3">
					<view class="product-item" v-for="(item, index) in itemData.data.product_list" :key="index" @click="gotoDetail(item)">
						<!-- 两列三列 -->
						<template>
							<view class="assemble-num"><text>{{ item.assemble_num }}人团</text></view>
							<view class="product-cover">
								<image :src="item.product.file_path" mode="aspectFit"></image>
							</view>
							<view class="product-info p-0-10">
								<view class="product-title" v-if="itemData.style.show.productName === true">{{ item.product.product_name }}</view>
								<view class="product-price" v-if="itemData.style.show.assemblePrice == true">
									<text class="red f20">¥</text>
									<text class="red fb f32">{{ item.assemble_price }}</text>
									<text class="ml10 gray9 text-d-line f20" v-if="itemData.style.show.linePrice == true">¥</text>
									<text class="gray9 f24 text-d-line" v-if="itemData.style.show.linePrice == true">{{ item.product_price }}</text>
								</view>
							</view>
							<view class="assemble_btn">去开团</view>
						</template>
					</view>
				</view>
			</scroll-view>
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
		created() {
			console.log(this.$props.itemData)
		},
		methods: {
			scroll(e) {},

			/*跳转列表*/
			gotoList() {
				let url = '/pages/plus/assemble/list/list';
				this.gotoPage(url);
			},

			/*跳转产品详情*/
			gotoDetail(e) {
				let url = '/pages/plus/assemble/detail/detail?assemble_product_id=' + e.assemble_product_id;
				this.gotoPage(url);
			}
		}
	};
</script>

<style lang="scss">
	.diy-assembleproduct {
		margin: 20rpx;
		border-radius: 20rpx;
		padding: 0 20rpx 20rpx;
		background: #FFFFFF;
	}

	.diy-assembleproduct .diy-head {
		height: 100rpx;
	}

	.diy-assembleproduct .diy-head .name {
		font-size: 32rpx;
		font-weight: bold;
	}

	.diy-assembleproduct .diy-head .datetime {
		margin-left: 40rpx;
	}

	.diy-assembleproduct .diy-head .datetime::v-deep>span {
		display: inline-block;
	}

	.diy-assembleproduct .diy-head .datetime::v-deep text {
		font-size: 24rpx;
		color: #F6220C;
	}

	.diy-assembleproduct .diy-head .datetime::v-deep .box {
		padding: 4rpx;
		font-size: 22rpx;
		background: #F6220C;
		color: #F6220C;
	}

	.diy-assembleproduct .diy-head .icon-jiantou {
		margin-left: 8rpx;
		color: #999999;
		font-size: 22rpx;
	}

	.diy-assembleproduct .product-list {
		flex-wrap: nowrap;
	}

	.diy-assembleproduct .product-list .product-item {
		flex-shrink: 0;
		border-radius: 12rpx;
		width: 300rpx;
		background: linear-gradient(180deg, #FFECEC 0%, rgba(255, 255, 255, 0.19) 93%);
		overflow: hidden;
	}

	.diy-assembleproduct .product-list image {
		width: 100%;
		height: 100%;
	}

	.diy-assembleproduct .product-list .product-title {
		margin: 0 auto;
		margin-top: 32rpx;
		width: 80%;
		height: 30rpx;
		display: -webkit-box;
		overflow: hidden;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
		font-size: 30rpx;
	}

	.diy-assembleproduct .product-list .product-price {
		margin: 0 auto;
		margin-top: 20rpx;
		margin-bottom: 30rpx;
		width: 100%;
		line-height: 24rpx;
		display: -webkit-box;
		overflow: hidden;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
		text-align: center;
	}

	.diy-assembleproduct .product-list.column__3 .product-title {
		height: 30rpx;
		line-height: 30rpx;
		font-size: 30rpx;
	}

	.diy-assembleproduct .product-list.column__2,
	.diy-assembleproduct .product-list.column__3 {
		display: flex;
		justify-content: flex-start;
		align-items: center;
	}

	.diy-assembleproduct .product-list .assemble-num {
		padding: 18rpx 22rpx;
		font-size: 24rpx;
		color: #F6220C;
	}

	.diy-assembleproduct .column__3 .product-item {
		width: 300rpx;
		margin-right: 20rpx;
	}

	.diy-assembleproduct .column__3 .product-cover {
		width: 230rpx;
		height: 230rpx;
		margin: 0 auto;
	}

	.diy-assembleproduct .column__3 .assemble_btn {
		background: linear-gradient(90deg, #FF4545 0%, #F6220C 100%);
		height: 50rpx;
		line-height: 50rpx;
		font-size: 26rpx;
		text-align: center;
		color: #FFFFFF;
	}
</style>
