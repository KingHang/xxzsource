<template>
	<!--我的预约-->
	<view class="my-live">
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50" @scrolltolower="scrolltolowerFunc">
			<view class="item-box" v-for="(item, index) in listData" :key="index">
				<view class="item d-s-s d-c">
					<view class="d-s-s">
						<view class="picture">
							<view class="cover-picture"><image :src="item.share.file_path" mode="aspectFill"></image></view>
						</view>
						<view class="flex-1 d-s-s d-c">
							<text class="f28">{{ item.name }}</text>
							<view class="gray9 f24">开始时间：{{item.start_time_text}}</view>
							<view class="d-c-c mt10" v-if="item.live_status==102"><Countdown :config="getCountdownConfig(item)"></Countdown></view>
							<view class="d-c-c mt10" v-if="item.live_status==101">正在直播中</view>
							<view class="d-c-c mt10" v-if="item.live_status!=101&&item.live_status!=102">直播结束</view>
						</view>
					</view>
					<view class="operation d-e-c ww100 mt20">
						<button type="primary" v-if="item.live_status==101||item.live_status==102||item.live_status==104" class="delete-btn" @tap="nowLive(item)">立即直播</button>
						<button type="primary" v-if="item.live_status==0" class="delete-btn">等待审核</button>
						<button type="primary" class="edit-btn" @tap="deleteFunc(item)">删除</button>
						<button type="primary" class="edit-btn" @tap="livePoster(item)">分享</button>
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
		<view class="foot-btns add-video-btns"><button type="default" @tap="addRoom">创建预告</button></view>
		
		<share v-if="qrcode!=null" :qrcode="qrcode" @close="closeShare"></share>
	
	</view>
</template>

<script>
import uniLoadMore from '@/components/uni-load-more.vue';
import Countdown from '@/components/countdown/countdown.vue';
import share from '../dialog/share.vue'
export default {
	components: {
		uniLoadMore,
		share,
		Countdown
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
			/*生成海报*/
			qrcode:null
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
					_this.phoneHeight = res.windowHeight;
					// 计算组件的高度
					let view = uni.createSelectorQuery().select('.add-video-btns');
					view.boundingClientRect(data => {
						let h = _this.phoneHeight - data.height;
						_this.scrollviewHigh = h;
					}).exec();
				}
			});
		},
		getCountdownConfig(item){
			return  {
				startstamp: 0,
				endstamp: item.start_time,
				title: '距开播还剩',
				type: 'text'
			}
		},
		/*创建并进入直播间*/
		nowLive: function(item) {
			// 防止两次点击操作间隔太快
			var nowTime = new Date();
			if (nowTime - this.tapTime < 1000) {
				return;
			}
			var url = '/pagesLive/live/live?sence=create&room_id=' + item.room_id;
			this.gotoPage(url);

			this.tapTime = nowTime;
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
					is_notice:1
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
					if (o.confirm) {
						uni.showLoading({
							title: '正在处理'
						});
						self._post(
							'plus.live.RoomApply/delete',
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
					} else {
						uni.showToast({
							title: '取消收货',
							duration: 1000,
							icon: 'none'
						});
					}
				}
			});
		},

		/*跳转页面*/
		addRoom() {
			this.gotoPage('/pages/user/my_shop/my_live/add/add');
		},

		/*添加直播产品*/
		gotoListFunc(e) {
			this.gotoPage('/pages/user/my-live/productsList/productsList?room_id=' + e.room_id);
		},
		
		/*生成海报*/
		livePoster(e){
			let self = this;
			uni.showLoading({
				title: '加载中',
			});
			let source = 'wx';
			//#ifdef H5
			source = 'mp';
			//#endif
			self._get('plus.live.Room/poster', {
				room_id: e.room_id,
				source: source
			}, (result) => {
				self.qrcode = result.data.qrcode;
			}, null, () => {
				uni.hideLoading();
			});
		},
		
		/**/
		closeShare(){
			this.qrcode=null;
		}

	}
};
</script>

<style scoped>
.my-live {
}
.my-live .item-box {
	padding: 10rpx 20rpx;
}
.my-live .item {
	padding: 20rpx;
	border-radius: 16rpx;
	background: #ffffff;
}
.my-live .picture {
	margin-right: 20rpx;
	width: 160rpx;
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
	border: 1rpx solid #E2231A;
	color: #E2231A;
	margin-left: 20rpx;
}
.my-live .operation .delete-btn {
	background: #ffffff;
	color: #E2231A;
	border: 1rpx solid #E2231A;
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
	background: #E2231A;
	color: #ffffff;
	border-radius: 0;
}
</style>
