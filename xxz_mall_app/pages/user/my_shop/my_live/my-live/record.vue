<template>
	<!--我的直播-->
	<view class="my-live">
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50" @scrolltolower="scrolltolowerFunc">
			<view class="item-box pr" v-for="(item, index) in listData" :key="index">
				<view class="noreturn-video" v-if="!item.record_url&&item.live_status!=101&&item.live_status==103">
					无回放
				</view>
				<view class="return-video" v-if="item.live_status==101">
					直播中
				</view>
				<view class="return-video" v-if="item.record_url&&item.live_status==103">
					回放
				</view>
				<view class="d-b-c d-stretch">
					<view class="cover-picture" >
						<image :src="item.share.file_path" mode="aspectFill"></image>
					</view>
					<view class="flex-1 content ml20">
						<text class="f28">{{ item.name }}</text>
						<view class="gray9 f24">开始时间：{{item.real_start_time_text}}</view>
					</view>
					<view class="close_btn">
						<text class="iconfont icon-guanbi" @tap="deleteFunc(item)"></text>
					</view>
					<view class="btns-box">
						<button v-if="item.live_status==101" class="edit-btn jion-btn mt20" @tap="gotoLive(item)">继续直播</button>
						<button class="edit-btn jion-btn mt20" v-if="item.record_url&&item.live_status!=101" @tap="gotoLive(item)">观看回放</button>
					</view>
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
import config from '@/config.js';
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
			/*顶部刷新*/
			topRefresh: false,
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
			/*是否只是音波*/
			pureAudio: false,
			/*防止两次点击操作间隔太快*/ 
			tapTime: '',
			/*当前用户名*/
			userName:'',
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
	mounted() {
		this.init();
	},
	onShow() {
		this.listData = [];
		this.page = 1;
		this.no_more = false;
		/*获取列表*/
		this.getData();
	},
	methods: {
		/*初始化*/
		init() {
			let _this = this;
			uni.getSystemInfo({
				success(res) {
					_this.scrollviewHigh=res.windowHeight;
				}
			});
		},

		/*获取数据*/
		getData() {
			let self = this;
			let page = self.page;
			let list_rows = self.list_rows;
			self.loading = true;
			self._get(
				'plus.live.RoomApply/lists',
				{
					page: page || 1,
					list_rows: list_rows,
					is_end:1
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
		},

		/*删除*/
		deleteFunc(item) {
			let self = this;
			wx.showModal({
				title: '提示',
				content: '您确定要删除吗?',
				success: function(o) {
					uni.showLoading({
						title: '正在处理'
					});
					self._post(
						'plus.agent.room/delete',
						{
							room_id: item.room_id
						},
						function(res) {
							uni.hideLoading();
							uni.showToast({
								title: res.msg,
								duration: 2000,
								icon: 'success'
							});
							self.listData = [];
							self.page = 1;
							self.getData();
						}
					);
				}
			});
		},

		/*跳转页面*/
		addRoom() {
			this.gotoPage('/pages/user/my-live/live/live');
		},

		/*跳转页面*/
		editLive(e) {
			var url='/pagesLive/live/live?sence=create&room_id=' + e.room_id;
			this.gotoPage(url);
		},

		/*添加直播产品*/
		gotoListFunc(e) {
			this.gotoPage('/pages/user/my-live/productsList/productsList?room_id=' + e.room_id);
		},
		
		/*跳转直播*/
		gotoLive(item){
			var url='/pagesLive/live/live?sence=create&room_id=' + item.room_id;
			this.gotoPage(url);
		},

	}
};
</script>

<style lang="scss" scoped>
.my-live {
	
}
.my-live .item-box {
	margin: 20rpx 20rpx 0;
	padding: 10rpx 20rpx;
	border-radius: 16rpx;
	background: #ffffff;
}

.my-live .video-url{
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	border-radius: 16rpx;
	background: rgba(0,0,0,.6);
	z-index: 80;
}

.my-live .return-video{
	position: absolute;
	top: 0;
	left: 0;
	padding: 10rpx;
	background: $dominant-color;
	color: #FFFFFF;
	z-index: 80;
}
.my-live .noreturn-video{
	position: absolute;
	top: 0;
	left: 0;
	padding: 10rpx;
	background: #FF6633;
	color: #FFFFFF;
	z-index: 80;
}
.my-live .item-box .content{
	display: flex;
	justify-content: flex-start;
	flex-direction: column;
	align-items: flex-start;
}
.my-live .item {
	
	margin-top: 20rpx;
	padding: 20rpx;
	border-radius: 16rpx;
	background: #ffffff;
}
.my-live .picture {
	margin-right: 20rpx;
	width: 100rpx;
}
.my-live .cover-picture {
	width: 160rpx;
	height: 160rpx;
}
.my-live .cover-picture image {
	width: 100%;
	height: 100%;
}

.my-live .picture .title {
	height: 40rpx;
	line-height: 40rpx;
	padding: 0 10rpx;
	text-align: center;
	background: #eeeeee;
	color: #333333;
}
.my-live .operation button {
	height: 60rpx;
	line-height: 60rpx;
	border-radius: 0;
}
.my-live .operation .edit-btn {
	background: #ffffff;
	border: 1rpx solid #f72b6c;
	color: #f72b6c;
}
.my-live .operation .delete-btn {
	background: #f72b6c;
	color: #ffffff;
}
.my-live .add-product {
	margin-left: 20rpx;
	width: 130rpx;
	height: 130rpx;
	border: 1px solid #eeeeee;
	background: #eeeeee;
}

.my-live .add-product image {
	width: 100%;
	height: 100%;
}

.add-video-btns {
	padding: 0;
}
.add-video-btns button {
	width: 100%;
	height: 90rpx;
	line-height: 90rpx;
	background: #f72b6c;
	color: #ffffff;
	border-radius: 0;
}

.my-live .item-box .btns-box{
	position: absolute;
	right: 16rpx;
	bottom: 16rpx;
	display: flex;
	justify-content: space-between;
	align-items: flex-end;
	flex-direction: column;
}
.jion-btn{
	background-color: #E2231A;
	color: #FFFFFF;
}
.close_btn{
	position: absolute;
	right: 20rpx;
	top: 20rpx;
}
</style>
