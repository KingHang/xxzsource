<template>
	<view class="my-live-order">
		
		<!-- <view class="top-tabbar">
			<view :class="state_active==0?'tab-item active':'tab-item'" @tap="stateFunc(0)">
				全部订单
			</view>
			<view :class="state_active==1?'tab-item active':'tab-item'" @tap="stateFunc(1)">
				待付款
			</view>
			<view :class="state_active==2?'tab-item active':'tab-item'" @tap="stateFunc(2)">
				待发货
			</view>
			<view :class="state_active==3?'tab-item active':'tab-item'" @tap="stateFunc(3)">
				待收货
			</view>
			<view :class="state_active==4?'tab-item active':'tab-item'" @tap="stateFunc(4)">
				待评价
			</view>
		</view> -->
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50" @scrolltolower="scrolltolowerFunc">
			<view class="item d-s-c" v-for="(item, index) in listData" :key="index">
				<view class="picture">
					<image :src="item.product[0].image.file_path" mode="aspectFill"></image>
				</view>
				<view class="flex-1 ml20 mr20">
					<view class="gray9">
						订单号：{{item.order_no}}
					</view>
					<view class="text-ellipsis-2 f30">{{item.product[0].product_name}}</view>
					<view class="price d-b-c">
						<view>
							￥<text class="fb f30">{{item.product[0].product_price}}</text>
						</view>
					</view>
				</view>
				<view class="red">
					<text v-if="item.is_settled==1">已结算</text>
					<text v-if="item.is_settled==0">未结算</text>
				</view>
			</view>

			<!-- 没有记录 -->
			<view class="d-c-c p30" v-if="listData.length == 0 && !loading">
				<text class="iconfont icon-wushuju"></text>
				<text class="cont">亲，暂无相关记录哦</text>
			</view>
			<uni-load-more v-else :loadingType="loadingType"></uni-load-more>
		</scroll-view>
		
		
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
			/*手机高度*/
			phoneHeight: 0,
			/*可滚动视图区域高度*/
			scrollviewHigh: 0,
			/*底部加载*/
			loading: true,
			/*没有更多*/
			no_more: false,
			/*商品列表*/
			listData: [],
			/*当前页面*/
			page: 1,
			/*一页多少条*/
			list_rows: 10,
			/*当前类别*/
			state_active:0,
			shop_supplier_id:''
		};
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
	onLoad(e) {
		this.room_id = e.room_id;
		this.shop_supplier_id=e.shop_supplier_id;
		this.getData();
	},
	mounted() {
		this.init();
	},
	methods: {
		/*初始化*/
		init() {
			let _this = this;
			uni.getSystemInfo({
				success(res) {
					_this.scrollviewHigh =  res.windowHeight;
					//_this.phoneHeight = res.windowHeight;
					// 计算组件的高度
					/* let view = uni.createSelectorQuery().select('.top-tabbar');
					view.boundingClientRect(data => {
						let h = _this.phoneHeight - data.height;
						_this.scrollviewHigh = h;
					}).exec(); */
				}
			});
		},
		
		/*状态切换*/
		stateFunc(e) {
			let self = this;
			if(self.state_active!=e){
				self.page = 1;
				self.loading = true;
				self.state_active = e;
				switch (e) {
					case 0:
						self.listData = [];
						self.dataType = 'all';
						break;
					case 1:
						self.listData = [];
						self.dataType = 'payment';
						break;
					case 2:
						self.listData = [];
						self.dataType = 'delivery';
						break;
					case 3:
						self.listData = [];
						self.dataType = 'received';
						break;
					case 4:
						self.listData = [];
						self.dataType = 'comment';
						break;
				};
				self.getData();
			}
		},

		/*获取数据*/
		getData() {
			let self = this;
			let page = self.page;
			let list_rows = self.list_rows;
			self.loading = true;
			self._get(
				'plus.live.RoomApply/orderList',
				{
					page: page || 1,
					list_rows: list_rows,
					shop_supplier_id:self.shop_supplier_id
				},
				function(res) {
					self.loading = false;
					self.listData = self.listData.concat(res.data.list.data);
					self.last_page = res.data.list.last_page;
					if (res.data.list.last_page <= 1) {
						self.no_more = true;
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
		}

	}
};
</script>

<style lang="scss" scoped>
.my-live-order {
}

.my-live-order .item {
	margin:20rpx 20rpx 0;
	padding: 20rpx;
	border-radius: 16rpx;
	background: #ffffff;
}

.my-live-order .picture {
	width: 100rpx;
	height: 100rpx;
}

.my-live-order .picture image{
	width: 100%;
	height: 100%;
}

.my-live-order .item .price{
	color: $dominant-color;
}

</style>
