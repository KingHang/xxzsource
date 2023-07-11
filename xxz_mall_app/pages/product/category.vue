<template>
	<view class="category-wrap">
		<!-- #ifdef APP-PLUS -->
		<header-bar></header-bar>
		<!-- #endif -->
		<!-- 搜索框 -->
		<view class="index-search-box-cate" id="searchBox">
			<view class="index-search-cate t-c" @click="gotoSearch">
				<span class="icon iconfont icon-sousuo"></span>
				<text class="ml10">{{ searchName }}</text>
			</view>
		</view>

		<!--类别列表-->
		<view class="category-content">

			<!--一级分类 大图-->
			<view class="cotegory-type cotegory-type-1" v-if="show_type==10">
				<scroll-view scroll-y="true" class="scroll-Y" :style="'height:'+scrollviewHigh+'px;'">
					<view class="list">
						<view class="item" v-for="(item,index) in listData" :key="index" @click="gotoList(item.category_id)">
							<view class="pic">
								<image :src="hasImages(item)" mode="widthFix"></image>
							</view>
							<view class="p-20-0 fb tc f34">
								{{item.name}}
							</view>
						</view>
					</view>
				</scroll-view>
			</view>

			<!--一级分类 小图-->
			<view class="cotegory-type cotegory-type-2" v-if="show_type==20">
				<scroll-view scroll-y="true" class="scroll-Y" :style="'height:'+scrollviewHigh+'px;'">
					<view class="list">
						<view class="item" v-for="(item,index) in listData" :key="index" @click="gotoList(item.category_id)">
							<image :src="hasImages(item)" mode="aspectFit"></image>
							<text>{{item.name}}</text>
						</view>
					</view>
				</scroll-view>
			</view>


			<!--二级分类-->
			<view class="cotegory-type cotegory-type-3" v-if="show_type==30">
				<view class="category-tab">
					<scroll-view scroll-y="true" class="scroll-Y" :style="'height:'+scrollviewHigh+'px;'">
						<view :class="index==select_index?'item active':'item'" v-for="(item,index) in listData" :key="index" @click="selectCategory(index)">
							<text>{{item.name}}</text>
						</view>
					</scroll-view>
				</view>
				<view class="category-content pr">
					<scroll-view scroll-y="true" class="scroll-Y scroll-3" :style="'height:'+scrollviewHigh+'px;'">
						<view class="list">
							<view class="item" v-for="(item,index) in childlist" :key="index" @click="gotoList(item.category_id)">
								<image :src="hasImages(item)" mode="aspectFit"></image>
								<text class="type-name">{{item.name}}</text>
							</view>
						</view>
					</scroll-view>
				</view>
			</view>

		</view>
		<request-loading :loadding='isloadding'></request-loading>
	</view>
</template>

<script>
	export default {
		components: {},
		data() {
			return {
				isloadding: true,
				searchName: '搜索商品',
				/*展示方式*/
				show_type: 3,
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				/*数据*/
				listData: [],
				/*子类数据*/
				childlist: [],
				/*当前选中的分类*/
				select_index: 0,
			};
		},
		mounted() {
			this.init();
			this.getData();
		},
		methods: {
			/*初始化*/
			init() {
				let _this = this;
				uni.getSystemInfo({
					success(res) {
						_this.phoneHeight = res.windowHeight;
						// 计算组件的高度
						let view = uni.createSelectorQuery().select('#searchBox');
						view.boundingClientRect(data => {
							let h = _this.phoneHeight - data.height;
							_this.scrollviewHigh = h;
						}).exec();
					}
				});
			},

			/*判断是否有图片*/
			hasImages(e) {
				if (e.images != null && e.images.file_path != null) {
					return e.images.file_path;
				} else {
					return '';
				}
			},

			/*获取数据*/
			getData() {
				let _this = this;
				_this.isloadding = true;
				_this._get('product.category/index', {}, function(res) {
					_this.listData = res.data.list;
					_this.show_type = res.data.template.category_style;
					console.log(_this.show_type);
					if (_this.listData[0].child) {
						_this.childlist = _this.listData[0].child;
					}
					_this.background = res.data.background;
					_this.isloadding = false;
				});
			},

			/*选择分类*/
			selectCategory(e) {
				if (this.listData[e].child) {
					this.childlist = this.listData[e].child;
					this.select_index = e;
				}
			},

			/*跳转产品列表*/
			gotoList(e) {
				let category_id = e;
				let sortType = 'all';
				let search = '';
				let sortPrice = 0;
				this.gotoPage('/pages/product/list/list?category_id=' + category_id + '&sortType=' + sortType + '&search=' + search +
						'&sortPrice=' + sortPrice);
			},

			wxGetUserInfo: function(res) {
				if (!res.detail.iv) {
					uni.showToast({
						title: "您取消了授权,登录失败",
						icon: "none"
					});
					return false;
				}
			},

			/*跳转搜索页面*/
			gotoSearch() {
				this.gotoPage('/pages/product/search/search');
			},
			/**
			 * 设置分享内容
			 */
			onShareAppMessage() {
				let self = this;
				return {
					title: self.templet.share_title,
					path: '/pages/product/category?' + self.getShareUrlParams()
				};
			},
		}
	};
</script>

<style lang="scss">
	@import '@/common/mixin.scss';

	.foot_ {
		height: 98rpx;
		width: 100%;
	}

	.cotegory-type {
		line-height: 40rpx;
		background: #ffffff;
	}

	.cotegory-type image {
		width: 100%;
	}

	.cotegory-type-1 .list {
		padding: 20rpx;
	}

	.cotegory-type-1 .list .item {
		margin-top: 30rpx;
	}

	.cotegory-type-1 .list .item .pic {
		border: 1px solid #e3e3e3;
		width: 710rpx;
		height: auto;
		overflow: hidden;
		border-radius: 8px;
	}

	.cotegory-type-1 .list .item image {
		width: 100%;
		height: 100%;
	}

	.cotegory-type-2 .list,
	.cotegory-type-3 .list {
		padding: 0 20rpx;
		display: flex;
		flex-wrap: wrap;
		justify-content: flex-start;
	}

	.cotegory-type-2 .list .item,
	.cotegory-type-3 .list .item {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

	.cotegory-type-2 .list .item {
		padding: 0 16rpx;
		width: 200rpx;
		height: 300rpx;
		font-size: 28rpx;
	}

	.cotegory-type-2 .list .item image {
		width: 180rpx;
		height: 180rpx;
		margin-bottom: 20rpx;
	}

	.cotegory-type-3 {
		display: flex;
	}

	.cotegory-type-3 .category-tab {
		width: 150rpx;
		background: #FFFFFF;
		border-right: 1px solid #e3e3e3;
	}

	.cotegory-type-3 .category-tab .item {
		padding: 40rpx 0;
		font-size: 32rpx;
		text-align: center;
	}

	.cotegory-type-3 .category-tab .item.active {
		position: relative;
		background: #ffffff;
		// font-weight: bold;
		color: $dominant-color;
	}

	.cotegory-type-3 .category-tab .item.active::after {
		position: absolute;
		content: '';
		top: 40rpx;
		bottom: 40rpx;
		right: 0;
		width: 2rpx;
		height: 24rpx;
		margin: auto;
		background: $dominant-color;
	}

	.cotegory-type-3 .category-content {
		flex: 1;
		margin: 0 20rpx;
	}

	.cotegory-type-3 .list .item {
		width: 140rpx;
		height: 200rpx;
		margin-top: 40rpx;
		margin-right: 40rpx;
		font-size: 24rpx;
	}

	.cotegory-type-3 .list .item:nth-child(3n) {
		margin-right: 0;
	}

	.cotegory-type-3 .list .item image {
		width: 140rpx;
		height: 140rpx;
	}

	.cotegory-type-3 .list .item .type-name {
		display: block;
		margin-top: 20rpx;
		height: 80rpx;
		line-height: 60rpx;
		text-overflow: ellipsis;
		width: 100%;
		color: #818181;
		font-size: 26rpx;
		white-space: nowrap;
		overflow: hidden;
		text-align: center;
	}

	.scroll-3 {
		background: #ffffff;
		border-radius: 12px;
	}
</style>
