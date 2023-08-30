<template>
	<view class="look-evaluate">
		<view class="top-tabbar2" >
			<view :class="state_active == -1 ? 'tab-item active' : 'tab-item'" @click="stateFunc(0)">全部({{ Total.all }})</view>
			<view :class="state_active == 10 ? 'tab-item active' : 'tab-item'" @click="stateFunc(10)">
				<view class="d-c-c">
					<text class="ml10 gray9">好评({{ Total.praise }})</text>
				</view>
			</view>
			<view :class="state_active == 20 ? 'tab-item active' : 'tab-item'" @click="stateFunc(20)">
				<view class="d-c-c">
					<text class="ml10 gray9">中评({{ Total.review }})</text>
				</view>
			</view>
			<view :class="state_active == 30 ? 'tab-item active' : 'tab-item'" @click="stateFunc(30)">
				<view class="d-c-c">
					<text class="ml10 gray9">差评({{ Total.negative }})</text>
				</view>
			</view>
		</view>

		<!--评论列表-->
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50"
		 @scrolltolower="scrolltolowerFunc">
			<view class="">
                <view class="hlbblock30" v-for="(item, index) in tableData">

                    <view class="fsc">
                        <view style="display: flex;align-items: center">
                            <view>
                                <image :src="item.users.avatarUrl" style="width: 90rpx;height: 90rpx;border-radius: 45rpx"></image>
                            </view>
                            <view style="margin-left: 10rpx">
                                <view>
                                    {{ item.users.nickName }}
                                </view>
                                <view style="color: #999999;font-size: 24rpx">
                                    {{ item.create_time }}
                                </view>

                            </view>
                        </view>
                        <view style="display: flex;align-items: center">
                            <block v-if="item.score == 10">
                                <view >
                                    <image src="/static/images/goods/detail/good.png" style="width: 80rpx;height: 80rpx;margin-right: -20rpx;"></image>
                                </view>
                                <view style="padding:0 20rpx;color: #F63E36;background-color: #FFE5E4;height: 40rpx;line-height: 40rpx;border-radius: 20rpx;font-size: 22rpx;">
                                    好评
                                </view>
                            </block>
                            <block v-if="item.score == 20">
                                <view >
                                    <image src="/static/images/goods/detail/mid.png" style="width: 80rpx;height: 80rpx;margin-right: -20rpx;"></image>
                                </view>
                                <view style="padding:0 20rpx;color: #F68336;background-color: #FFF4E4;height: 40rpx;line-height: 40rpx;border-radius: 20rpx;font-size: 22rpx;">
                                    中评
                                </view>
                            </block>
                            <block v-if="item.score == 30">
                                <view >
                                    <image src="/static/images/goods/detail/bad.png" style="width: 80rpx;height: 80rpx;margin-right: -20rpx;"></image>
                                </view>
                                <view style="padding:0 20rpx;color: #999999;background-color: #F5F5F5;height: 40rpx;line-height: 40rpx;border-radius: 20rpx;font-size: 22rpx;">
                                    差评
                                </view>
                            </block>

                        </view>

                    </view>
                    <view style="margin-top: 10rpx">
                        {{ item.content }}
                    </view>
                    <view style="margin-top: 10rpx" class="box" v-for="(imgs, img_num) in item.image" :key="img_num">
                        <image :src="imgs.file_path" mode="aspectFill" width=""></image>
                    </view>

                </view>
				<!-- 没有记录 -->
				<view class="d-c-c p30" v-if="tableData.length==0 && !loading">
					<view class="none-data-box">
						<image src="/static/none.png" mode="widthFix"></image>
						<text>亲，暂无相关记录哦</text>
					</view>
				</view>
				<uni-load-more v-else :loadingType="loadingType"></uni-load-more>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	import uniLoadMore from "@/components/uni-load-more.vue";
	export default {
		components: {
			uniLoadMore
		},
		data() {
			return {
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				/*选中状态*/
				state_active: -1,
				/*商品id*/
				product_id: 0,
				/*评论列表*/
				tableData: [],
				/*统计*/
				Total: {
					/*总数*/
					all: 0,
					/*score = 30*/
					negative: 0,
					/*score = 10*/
					praise: 0,
					/*score = 20*/
					negative: 0,
					review: 0
				},
				/*页码*/
				page: 1,
				list_rows: 15,
				no_more: false,
				loading: true,
				/*最后一页码数*/
				last_page: 0
			};
		},
		computed: {
			/*加载中状态*/
			loadingType() {
				if (this.loading) {
					return 1;
				} else {
					if (this.tableData.length != 0 && this.no_more) {
						return 2;
					} else {
						return 0;
					}
				}
			}
		},
		onLoad(e) {
			this.product_id = e.product_id;
		},
		mounted() {
			this.init();
			/*获取数据*/
			this.getData();
		},
		methods: {
			/*初始化*/
			init() {
				let self = this;
				uni.getSystemInfo({
					success(res) {
						self.phoneHeight = res.windowHeight;
						// 计算组件的高度
						let view = uni.createSelectorQuery().select('.top-tabbar2');
						view.boundingClientRect(data => {
							let h = self.phoneHeight - data.height;
							self.scrollviewHigh = h;
						}).exec();
					}
				});
			},

			/*获取数据*/
			getData() {
				let _this = this;
				let product_id = _this.product_id;
				_this._get(
					'goods.comment/lists', {
						product_id: product_id,
						scoreType: _this.state_active,
						page: _this.page,
						list_rows: _this.list_rows
					},
					function(res) {
						_this.loading = false;
						_this.Total = res.data.total;
						_this.tableData = _this.tableData.concat(res.data.list.data);
						_this.last_page = res.data.list.last_page;
						if (res.data.list.last_page <= 1) {
							_this.no_more = true;
						}
					}
				);
			},

			/*可滚动视图区域到底触发*/
			scrolltolowerFunc() {
				let self = this;
				self.bottomRefresh = true;
				self.page++;
				self.loading = true;
				if (self.page > self.last_page) {
					self.loading = false;
					self.no_more = true;
					return;
				}
				self.getData();
			},

			/*类别切换*/
			stateFunc(e) {
				let self = this;
				if (self.state_active != e) {
					self.tableData = [];
					self.no_more = false;
					self.loading = true;
					self.state_active = e;
					self.page = 1;
					self.getData();
				}
			}
		}
	};
</script>

<style>
    .top-tabbar2 {
        height: 96rpx;
        line-height: 96rpx;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20rpx;
        box-sizing: border-box;

    }

    .look-evaluate .comment-list {
		background: #ffffff;
	}

	.look-evaluate .comment-list .item {
		padding-top: 30rpx;
		padding-bottom: 30rpx;
		border-top: none;
		border-bottom: 1px solid #dddddd;
	}

	.look-evaluate .iconfont {
		border-radius: 50%;
		font-size: 40rpx;
		text-align: center;
	}

	.look-evaluate .icon-pingjiahaoping {
		color: #f42222;
	}

	.look-evaluate .icon-pingjiazhongping {
		color: #f2b509;
	}

	.look-evaluate .icon-pingjiachaping {
		color: #999999;
	}

	.look-evaluate .imgs {
		flex-wrap: wrap;
	}

	.look-evaluate .imgs .box {
		margin-top: 10rpx;
		margin-right: 10rpx;
	}

	.look-evaluate .imgs .box:nth-child(3n) {
		margin-right: 0;
	}

	.look-evaluate .imgs .box,
	.look-evaluate .imgs .box image {
		width: 210rpx;
		height: 210rpx;
	}
</style>
