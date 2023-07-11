<template>
	<view>
		<view class="product-background" :class="{ close: !open }" @tap="closeFunc()"></view>

		<view class="product-container" :class="{ close: !open }">
			<!--头部-->
			<view class="head d-b-c p-0-20 f30">
				<text>关联商品</text>
				<button type="default" class="btn-red-border" @tap="closeFunc(true)">确认</button>
			</view>
			<scroll-view scroll-y="true" class="scroll-Y">
				<view class="product-list">
					<view class="item d-s-s ww100" v-for="(item, index) in listData" :key="index"  @tap="relationProduct(item)">
						<view class="product-picture">
							<image :src="item.product_image" mode="aspectFill"></image>
						</view>
						<view class="flex-1 ml20">
							<view class="text-ellipsis-2 f30">
								{{ item.product_name }}
							</view>
							<view class="d-b-c p-20-0">
								<view class="price">
									<text class="ml20 gray3">售价：<text class="red">{{item.product_price}}</text></text>
								</view>
								<!-- <button class="relation" :disabled="isrelation(item)" type="primary" @tap="relationProduct(item)">添加</button> -->
								<view>
									<view class="relation_close" v-if="!isrelation(item)"></view>
									<view class="relation_choose" v-else><text class="icon iconfont icon-tijiaochenggong" ></text></view>
								</view>
							</view>
						</view>
					</view>
				</view>
			</scroll-view>
		</view>
	</view>
</template>

<script>

export default {
	components: {
	},
	data() {
		return {
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
			/*选择商品列表*/
			productIds:[],
		};
	},
	computed: {
		
	},
	props: ['open','relationList'],
	watch: {
		open: function(n, o) {
			if (n != o && n) {
				this.start();
				this.productIds=this.relationList;
			}
		}
	},
	methods: {
		
		/*判断是否关联*/
		isrelation(e){
			if(this.productIds.indexOf(e.product_id)!=-1){
				return true;
			}else{
				return false;
			}
		},
		
		/*开始*/
		start(){
			this.loading=true;
			this.no_more=false;
			this.listData=[];
			this.page=1;
			this.list_rows=10;
			this.getData();
		},
		
		/*获取数据*/
		getData() {
			let self = this;
			let page = self.page;
			let list_rows = self.list_rows;
			self.loading = true;
			self._get(
				'plus.live.RoomApply/product_list',
				{
					page: page || 1,
					list_rows: list_rows
				},
				function(res) {
					self.loading = false;
					self.listData = self.listData.concat(res.data.list.data);
					console.log(self.listData)
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
		},
		
		/*设置商品*/
		relationProduct(e){
			let self = this;
			let i=this.productIds.indexOf(e.product_id);
			if(i!=-1){
				this.productIds.splice(i,1);
			}else{
				this.productIds.push(e.product_id);
			}
		},

		/*关闭*/
		closeFunc(e) {
			if(e!=null){
				this.$emit('close', this.productIds);
			}else{
				this.$emit('close', null);
			}
		},
		
		/*跳转商品详情*/
		gotoProduct(e){
			let url='pages/product/detail/detail?product_id=' + e.product_id+'&room_id='+this.liveRoomDetal.room_id
			this.gotoPage(url);
		},
		
		/*跳转到订单*/
		gotoOrder(){
			this.$parent.exit();
			let url='pages/order/myorder/myorder'
			this.gotoPage(url);
		}
	}
};
</script>

<style lang="scss" scoped>
.product-background {
	position: fixed;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	background-color: rgba(0, 0, 0, 0.3);
	transition: transform 0s ease, background-color 0.3s ease;
	z-index: 90;
}

.product-background.close {
	background-color: rgba(0, 0, 0, 0);
	transform: translate(0, 100%);
}

.product-container {
	position: fixed;
	right: 0;
	bottom: 0;
	left: 0;
	height: 900rpx;
	border-radius: 16rpx 16rpx 0 0;
	background: #ffffff;
	transition: transform 0.2s ease;
	z-index: 99;
}

.product-container.close {
	transform: translate(0, 900rpx);
}

.product-container .head {
	height: 90rpx;
	line-height: 90rpx;
}

.product-container scroll-view {
	height: 810rpx;
}
.product-picture {
	position: relative;
	width: 160rpx;
	height: 160rpx;
}

.product-picture .active{
	position: absolute;
	display: block;
	right: 0;
	bottom: 0;
	left: 0;
	height: 40rpx;
	line-height: 40rpx;
	background: $dominant-color;
	color: #FFFFFF;
	text-align: center;
}

.product-container image {
	width: 100%;
	height: 100%;
}
.product-container .product-list {
	padding: 20rpx;
}
.product-container .product-list .item {
	width: 100%;
	border-bottom: 1rpx solid #eeeeee;
}
.product-container .product-list button {
}
.relation_close{
	width: 50rpx;
	height: 50rpx;
	border-radius: 50%;
	border: 1rpx solid #CACACA;
}
.relation_choose{
	width: 50rpx;
	height: 50rpx;
	background-color: #09BB07;
	border-radius: 50%;
	text-align: center;
	line-height: 50rpx;
}
</style>
