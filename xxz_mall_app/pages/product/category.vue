<template>
	<view class="category-wrap">
		<!-- #ifdef APP-PLUS -->
		<header-bar></header-bar>
		<!-- #endif -->
		
		<view class="top-tabbar2" v-if="showBrand">
			<u-tabs :list="navListBrand" @change="tabClick" name="text" :current="current" :isScroll="false" height="70" bg-color="#F63E36" active-color="#FFFFFF" inactive-color="#F5F5F5"></u-tabs>
		</view>
		<view class="top-tabbar2" v-else>
		    <u-tabs :list="navList" @change="tabClick" name="text" :current="current" :isScroll="false" height="70" bg-color="#F63E36" active-color="#FFFFFF" inactive-color="#F5F5F5"></u-tabs>
		</view>
		
        <block v-if="current == 0">
			<block v-if="show_type == 30 || show_type == 40">
			    <view style="background-color: #F63E36;height: 220rpx" id="searchBox">
			        <view style="width: 95%;margin: 0 auto;padding-top: 50rpx;">
			            <u-search  :disabled="true" :action-style="actstyle"  @click="gotoSearch" action-text="搜索" shape="round" search-icon="search" ></u-search>
			        </view>
			    </view>
			</block>
			<block v-else>
			    <view style="background-color: #F63E36;height: 130rpx" id="searchBox">
			        <view style="width: 95%;margin: 0 auto;padding-top: 50rpx;">
			            <u-search  :disabled="true" :action-style="actstyle"  @click="gotoSearch" action-text="搜索" shape="round" search-icon="search" ></u-search>
			        </view>
			    </view>
			</block>
		</block>
		<block v-else>
			<view style="background-color: #F63E36;height: 220rpx" id="searchBox">
			    <view style="width: 95%;margin: 0 auto;padding-top: 50rpx;">
			        <u-search v-model="search" :action-style="actstyle" action-text="搜索" shape="round" search-icon="search" @custom="searchData" @search="searchData"></u-search>
			    </view>
			</view>
		</block>
		
		<!--类别列表-->
		<view class="category-content" v-if="current == 0">
			<!--一级分类 大图-->
			<view class=" " v-if="show_type == 10">
				<scroll-view scroll-y="true" class="scroll-Y" :style="'height:'+scrollviewHigh+'px;'">
                    <view v-for="(item,index) in listData" class="hlbblock" style="overflow: hidden" @click="gotoList(item.category_id)">
                        <view style="width: 100%;height: 250rpx;background-color: #2C405A">
                            <image :src="item.images.file_path" style="width: 100%;height: 250rpx;" mode="aspectFill"></image>
                        </view>
                        <view style="text-align: center;padding: 20rpx">
                            {{item.name}}
                        </view>
                    </view>
				</scroll-view>
			</view>
			
			<!--一级分类 小图-->
			<view class="" v-if="show_type == 20">
				<scroll-view scroll-y="true" class="scroll-Y" :style="'height:'+scrollviewHigh+'px;'">
                    <view style="margin: 30rpx">
                        <u-grid :col="3" :border="false" >
                            <u-grid-item bg-color="#f5f5f5" v-for="(item,index) in listData" @click="gotoList(item.category_id)">
                                <image :src="hasImages(item)"  style="width: 90%;height: 150rpx;" mode="aspectFill"></image>
                                <view style="font-size: 25rpx;margin-top: 10rpx">
                                    {{item.name}}
                                </view>
                            </u-grid-item>
                        </u-grid>
                    </view>
				</scroll-view>
			</view>
			
			<!--二级分类-->
            <block v-if="show_type == 30">
                <view style="width: 100%;height: 980rpx;display: flex">
                    <view style="width: 25%;height: 120%">
                        <scroll-view  scroll-y="true" style="height: 100%;">
                            <view  :class="index==select_index?'chosept':'pt'" v-for="(item,index) in listData" :key="index" @click="selectCategory(index)">
                                <view class="ptitem linedot">
                                    {{item.name}}
                                </view>
                            </view>
                            <view style="height: 150rpx"></view>
                        </scroll-view>
                    </view>
                    <view style="width: 70%;height: 120%;background-color: white;border-radius: 10rpx;margin-top: -50rpx;padding: 10rpx;">
                        <scroll-view  scroll-y="true" style="height: 100%;">
                            <u-grid :col="3" :border="false">
                                <u-grid-item v-for="(item,index) in childlist" @click="gotoList(item.category_id)">
                                    <image :src="hasImages(item)" style="width: 100%;height: 100rpx;" mode="aspectFill"></image>
                                    <view style="font-size: 25rpx;margin-top: 10rpx">
                                        {{item.name}}
                                    </view>
                                </u-grid-item>
                            </u-grid>
                        </scroll-view>
                    </view>
                </view>
            </block>
			
			<!--一级分类带商品-->
			<block v-if="show_type == 40">
			    <view style="width: 100%;height: 980rpx;display: flex">
			        <view style="width: 25%;height: 120%">
			            <scroll-view scroll-y="true" style="height: 100%;">
			                <view :class="index==select_index?'chosept':'pt'" v-for="(item,index) in listData" :key="index" @click="selectCategory(index)">
			                    <view class="ptitem linedot">
			                        {{item.name}}
			                    </view>
			                </view>
			                <view style="height: 150rpx"></view>
			            </scroll-view>
			        </view>
			        <view style="width: 70%;height: 120%;background-color: white;border-radius: 10rpx;margin-top: -50rpx;padding: 20rpx;">
						<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50" @scrolltolower="scrolltolowerFunc">
							<view v-for="item in productList" @click="gotoProductList(item.product_id)" style="display: flex;width: 100%;justify-content: space-between;margin-bottom: 40rpx;">
								<view style="width: 40%">
									<image :src="item.product_image" style="width: 200rpx;height: 190rpx;border-radius: 15rpx"  mode="aspectFill"></image>
								</view>
								<view style="width: 60%;margin-left: 20rpx;">
									<view style="min-height: 130rpx;line-height: 40rpx;">
										{{item.product_name}}
									</view>
									<view style="height: 55rpx;">
										<text style="font-size: 28rpx;color: #F63E36;font-weight: bold">¥{{item.product_price}}</text>
									</view>
								</view>
							</view>
			            </scroll-view>
			        </view>
			    </view>
			</block>
		</view>
		<view v-else class="category-content">
			<view style="width: 100%;height: 980rpx;display: flex">
			    <view style="width: 25%;height: 120%">
			        <scroll-view scroll-y="true" style="height: 100%;">
			            <view :class="index==select_index?'chosept':'pt'" v-for="(item,index) in listData" :key="index" @click="selectCategory(index)">
			                <view class="ptitem linedot">
			                    {{item.name}}
			                </view>
			            </view>
			            <view style="height: 150rpx"></view>
			        </scroll-view>
			    </view>
				<view style="width: 70%;height: 120%;background-color: white;border-radius: 10rpx;margin-top: -50rpx;padding: 20rpx;">
					<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50" @scrolltolower="scrolltolowerFunc">
						<view v-for="item in productList" @click="gotoBrand(item.branddaysign.sign_id)" style="display: flex;width: 100%;margin-bottom: 40rpx;">
							<view>
								<image :src="item.image.file_path" style="width: 112rpx;height: 112rpx;border-radius: 8rpx"  mode="aspectFill"></image>
							</view>
							<view style="margin-left: 20rpx;">
								<view style="color: #1B1B1B;font-weight: bold;" class="text-ellipsis">
									{{item.branddaysign.selling_points ? item.branddaysign.selling_points : item.brand_name}}
								</view>
								<view style="display: flex;align-items: center;">
									<text style="font-size: 28rpx;color: #999999;margin-right: 10rpx;">{{item.product_down}}件单品</text>
									<view style="font-size: 20rpx;background: #FFE5E4;color: #F63E36;margin-right: 10rpx;padding: 0 5rpx;border-radius: 8rpx;display: flex;" v-for="(tag, tagIndex) in item.supplier.supplierApply" v-if="tagIndex < 2">
										{{tag.servers.name}}
									</view>
								</view>
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
                actstyle:{
                    color:'white'
                },
				/*分类商品数据*/
				category_id: 0,
				productList: [],
				loading: true,
				no_more: false,
				page: 1,
				list_rows: 10,
				last_page: 0,
				navList: [{
				    state: 'good',
				    text: '商品分类',
				}],
				navListBrand: [{
				    state: 'good',
				    text: '商品分类',
				}],
				current: 0,
				search: '',
				showBrand: false
			};
		},
		onLoad(options) {
			let _this = this;
			if (options && options.current == 1) {
				_this.current = 1
			}
			
		},
		computed: {
			/*加载中状态*/
			loadingType() {
				if (this.loading) {
					return 1;
				} else {
					if (this.productList.length != 0 && this.no_more) {
						return 2;
					} else {
						return 0;
					}
				}
			}
		},
		mounted() {
			this.init();
			this.getBrandSetting();
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
			tabClick(index) {
			    this.current = index
			    this.productList = [];
			    this.loading = true;
			    this.no_more = false;
			    this.page = 1;
				if (index == 0) {
					this.getProductData();
				} else {
					this.getBrandData();
				}
			},
			/*获取数据*/
			getData() {
				let _this = this;
				_this.isloadding = true;
				_this._get('goods.category/index', {}, function(res) {
					_this.listData = res.data.list;
					_this.show_type = res.data.template.category_style;
					if (_this.listData.length > 0) {
						if (_this.listData[0].child) {
							_this.childlist = _this.listData[0].child;
						}
						_this.category_id = _this.listData[0].category_id;
						
						if (_this.current == 0) {
							_this.getProductData();
						} else {
							_this.getBrandData();
						}
					}
					_this.background = res.data.background;
					_this.isloadding = false;
				});
			},
			
			/*选择分类*/
			selectCategory(e) {
			    this.childlist = this.listData[e].child;
			    this.select_index = e;
				this.category_id = this.listData[e].category_id;
				this.productList = [];
				this.loading = true;
				this.no_more = false;
				this.page = 1;
				if (this.current == 0) {
					this.getProductData();
				} else {
					this.getBrandData();
				}
			},
			
			/*获取商品数据*/
			getProductData() {
				let self = this;
				let page = self.page;
				let list_rows = self.list_rows;
				let category_id = self.category_id;
				self.loading = true;
				self._get('goods.goods/lists', {
					page: page || 1,
					category_id: category_id,
					list_rows: list_rows
				}, function(res) {
					self.loading = false;
					self.productList = self.productList.concat(res.data.list.data);
					self.last_page = res.data.list.last_page;
					if (res.data.list.last_page <= 1) {
						self.no_more = true;
					}
				});
			},
			searchData() {
			    let self = this;
			
			    self.page = 1;
				self.productList = [];
			    self.getBrandData();
			},
			getBrandData() {
				let self = this;
				let page = self.page;
				let list_rows = self.list_rows;
				let category_id = self.category_id;
				self.loading = true;
				
				self._post(
					'plugin.brand.brand/brand_category', {
						page: page || 1,
						category_id: self.category_id,
						search: self.search
					},
					function(res) {
						self.loading = false;
						console.log(res.data.data)
						self.productList = self.productList.concat(res.data.data);
						
						self.last_page = res.data.data.last_page;
						if (res.data.data.last_page <= 1) {
							self.no_more = true;
						}
						console.log(self.productList)
					}
				);
			},
			getBrandSetting() {
				let self = this;
				self._get('plugin.brand.brand/getBaseData', {
				}, function(result) {
					if (result && result.data && result.data.is_open == 1) {
						self.showBrand = true
					} else {
						self.showBrand = false
						self.current = 0
					}
					self.getData();
				});
			},
			getBrandList() {
				let self = this;
				let page = self.page;
				let list_rows = self.list_rows;
				let category_id = self.category_id;
				self.loading = true;
				
				self._post(
					'plugin.brand.brand/brand_category', {
						page: page || 1,
						category_id: self.category_id,
						search: self.search
					},
					function(res) {
						self.loading = false;
						console.log(res.data.data)
						self.productList = self.productList.concat(res.data.data);
						
						self.last_page = res.data.data.last_page;
						if (res.data.data.last_page <= 1) {
							self.no_more = true;
						}
						console.log(self.productList)
					}
				);
			},
			
			/*可滚动视图区域到底触发*/
			scrolltolowerFunc() {
				let self = this;
				self.page++;
				self.loading = true;
				if (self.page > self.last_page) {
					self.loading = false;
					self.no_more = true;
					return;
				}
				self.getProductData();
			},
			
			/*跳转产品列表*/
			gotoList(e) {
				let category_id = e;
				let sortType = 'all';
				let search = '';
				let sortPrice = 0;
				this.gotoPage('/pages/product/list/list?category_id=' + category_id + '&sortType=' + sortType + '&search=' + search + '&sortPrice=' + sortPrice);
			},
			
			/*跳转产品列表*/
			gotoProductList(e) {
				let url = 'pages/product/detail/detail?product_id=' + e
				this.gotoPage(url);
			},
			
			gotoBrand(e) {
				let url = '/pagesBrand/brand/index?sign_id=' + e
				this.gotoPage(url);
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

    page {
        background: #f5f5f5;
    }

    .pt{
        height: 120rpx;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .chosept{
        height: 120rpx;
        background-color: white;
        color: #F63E36;
        display: flex;
        align-items: center;
        justify-content: center;
        .ptitem{
            width: 190rpx;
            text-align: center;
            border-left: 2px #F63E36 solid;
            line-height: 28rpx;
        }
    }

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
