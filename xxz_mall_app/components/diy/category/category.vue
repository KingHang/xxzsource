<template>
	<view class="diy-category">
		<view class="category-header">
			<view class="category-list">
				<scroll-view :scroll-x="true" :scroll-with-animation="true" :scroll-into-view="scroll_category_id">
					<view class="cate-flex">
						<view
							class="item-box"
							v-for="(item, index) in itemData.data"
							:key="index"
							:class="category_id == item.category_id ? 'active-cate' : ''"
							:id="'category_id-' + index"
							@click="changeCategory(item.category_id, index)"
						>
							<view class="cate-name">{{ item.name }}</view>
							<view class="cate-line">
								<image src="/static/line.png" style="width: 78rpx" mode="widthFix"></image>
							</view>
						</view>
					</view>
				</scroll-view>
			</view>
			<view class="all-show" @click="showPop">
				<image src="/static/category_all.png" style="width: 48rpx" mode="widthFix"></image>
				<text class="show-text">分类</text>
			</view>
		</view>
		
		<!-- 商品列表 -->
		<list :productData="productData" />
		
		<!-- 分类弹窗 -->
		<u-popup :closeable="true" v-model="isShow" mode="bottom" height="72%" border-radius="20">
			<view class="pop-box">
				<view class="pop-title">商品分类</view>
				<view class="pop-sec-title">一级</view>
				<view class="pop-category">
					<view
					    class="item-button"
						v-for="(item, index) in itemData.data"
						:key="index"
						:class="category_id == item.category_id ? 'active-button' : ''"
						@click="selectFirstCategory(item, index)"
					>
						{{ item.name }}
					</view>
				</view>
				<view v-if="childList.length > 0" class="pop-sec-title">二级</view>
				<view v-if="childList.length > 0" class="pop-category">
					<view
					    class="item-button"
						v-for="(item, index) in childList"
						:key="index"
						:class="child_category_id == item.category_id ? 'active-button' : ''"
						@click="selectSecondCategory(item.category_id)"
					>
						{{ item.name }}
					</view>
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
import list from './product/list';

export default {
	components: { list },
	data() {
		return {
			scroll_category_id: 'scroll_category_id_0',
			category_id: 0,
			child_category_id: 0,
			isShow: false,
			childList: [],
			productData: {
				list: [],
				style: {}
			}
		};
	},
	props: ['itemData'],
	created() {
		this.productData.list = this.itemData.list;
		this.productData.style = this.itemData.style;
	},
	methods: {
		// 切换分类
		changeCategory(category_id, index) {
			this.category_id = category_id;
			let nextIndex = index - 2;
			nextIndex = nextIndex <= 0 ? 0 : nextIndex;
			this.scroll_category_id = `category_id-${nextIndex}`;
			this.getProductData(category_id);
		},
		// 显示弹窗
		showPop() {
			this.isShow = true;
		},
		// 选择一级分类
		selectFirstCategory(item, index) {
			this.category_id = item.category_id;
			let nextIndex = index - 2;
			nextIndex = nextIndex <= 0 ? 0 : nextIndex;
			this.scroll_category_id = `category_id-${nextIndex}`;
			this.childList = item.child;
			// 没有子分类直接关闭弹窗
			if (!item.child.length) {
				// 直接关闭弹窗
				this.getProductData(item.category_id);
				this.isShow = false;
			}
		},
		// 选择二级分类
		selectSecondCategory(category_id) {
			this.child_category_id = category_id;
			this.getProductData(category_id);
			this.isShow = false;
		},
		// 获取商品数据
		getProductData(category_id) {
			let self = this;
			self._get('goods.goods/lists', {
				page: 1,
				category_id: category_id,
				list_rows: self.itemData.params.showType === 'limit' ? self.itemData.params.showNum : 20
			}, function(res) {
				self.productData.list = res.data.list.data;
			});
		}
	}
};
</script>

<style lang="scss" scoped>
.diy-category {
	padding: 0 20rpx;
	
	.category-header {
		height: 102rpx;
		display: flex;
		justify-content: space-between;
		margin-top: 20rpx;
		
		.category-list {
			width: 90%;
			
			scroll-view {
				width: auto;
				
				.cate-flex {
					display: flex;
					
					.item-box {
						min-width: 78rpx;
						text-align: center;
						cursor: pointer;
						margin-right: 20rpx;
						flex-shrink: 0;
						
						&:active {
							background-color: none;
						}
						
						.cate-name {
							font-size: 26rpx;
							font-family: PingFang SC;
							color: #1B1B1B;
						}
						
						.cate-line {
							width: 78rpx;
							margin: 0 auto;
							display: none;
						}
					}
					
					.active-cate {
						.cate-name {
							font-weight: bold;
							font-size: 28rpx;
						}
						
						.cate-line {
							display: block;
						}
					}
				}
			}
		}
		
		.all-show {
			cursor: pointer;
			text-align: center;
			
			.show-text {
				font-size: 20rpx;
				font-family: PingFang SC;
				color: #1B1B1B;
			}
		}
	}
}

.pop-box {
	padding: 52rpx 30rpx;
	
	.pop-title {
		font-size: 28rpx;
		font-family: PingFang SC;
		font-weight: bold;
		line-height: 36rpx;
		color: #3C3C3C;
	}
	
	.pop-sec-title {
		font-size: 24rpx;
		font-family: PingFang SC;
		line-height: 36rpx;
		color: #999999;
		margin: 48rpx 0 20rpx;
	}
	
	.pop-category {
		display: flex;
		flex-wrap: wrap;
		
		.item-button {
			font-size: 24rpx;
			font-family: PingFang SC;
			color: #333333;
			background: #F0F0F0;
			border: 2rpx solid #F0F0F0;
			border-radius: 40rpx;
			padding: 12rpx 32rpx;
			margin: 0 16rpx 16rpx 0;
		}
		
		.active-button {
			background: #FFEBE9;
			border: 2rpx solid #F63E36;
		}
	}
}
</style>
