<template>
	<view class="bg-lottery" :style="'background-image: url('+ bg_image +');'">
		<view class="rule-title">
			<view class="rule-title-t">积分:<text class="yellow">{{user_points}}</text></view>
			<view class="tc mt20">每次抽奖消耗 {{ points }} 积分,今日剩余{{times}}次</view>
		</view>
		
		<view class="lottery-title">
			<almost-lottery :prizeList="prizeList" :prizeIndex="prizeIndex"
				:lotteryBg="'/uni_modules/almost-lottery/static/almost-lottery/almost-lottery__bg.png'" :actionBg="'/uni_modules/almost-lottery/static/almost-lottery/almost-lottery__action.png'"
				@reset-index="prizeIndex = -1" @draw-start="handleDrawStart" @draw-end="handleDrawEnd"
				@finish="handleDrawFinish" v-if="prizeList.length" />
		</view>
		<view class="special-box">
			<view class="special-content" :class="'display_1'">
				<view class="special-content-list" :style="'transform: translate(0,'+styleValue+'px);'">
					<view class="content-item text-ellipsis" v-for="(item, index) in listData" :key="index">
						<text>{{item.showText}}</text>
					</view>
				</view>
			</view>
		</view>
		<view class="ww100 mb23">
			<button type="default" class="btn-red record" @click="getRecord()">中奖记录</button>
		</view>
		<view class="ruler-box">
			<view class="f30 fb mb23">活动规则</view>
			<view class="f30" v-html="content"></view>
		</view>
		<popup :show="isPopup" type='middle' backgroundColor='#ea564c' :padding="40" :width="638" @hidePopup='hidePopup'>
			<view class="pr ww100 tc mb38">
				<view class="pop-tit-line"></view>
				<view class="pop-tit-text">恭喜，中奖啦</view>
			</view>
			<view class="d-c-c d-c ww100 product-box pr">
				<view class="circular-left"></view>
				<view class="circular-right"></view>
				<view></view>
				<view class="pop_img">
					<image :src="result.image" mode=""></image>
				</view>
				<view class="">
					<view class="f26 text-ellipsis fb">{{result.name}}</view>
				</view>
			</view>
		</popup>
		<popup :show="isRecord" type='bottom' :padding="0" :width="750"  @hidePopup='hideRecordPopup'>
			<view class="p30 record-box">
				<view class="pr ww100 tc mb38">
					<view class="pop-record-line"></view>
					<view class="pop-record-text">我的记录</view>
				</view>
				<view>
					<scroll-view scroll-y="true" style="max-height: 600rpx;" @scrolltolower="scrolltolowerFunc"
						lower-threshold="50">
						<view class="d-b-c border-b-d9" style="height: 70rpx;" v-for="(item,index) in recordList"
							:key='index'>
							<view class="">{{item.record_name}}</view>
							<view class="d-e-c">
								<view class="gray9">{{item.create_time}}</view>
								<button type="default" class="btn-red record-list-btn" v-if="item.status==0"
									@click="gotoPage('/pages/plus/lottery/receive?record_id='+item.record_id)">领取</button>
								<button type="default" class="btn-gray record-list-btn" v-else>已领取</button>
							</view>
						</view>
						<!-- 没有记录 -->
						<view class="d-c-c p30" v-if="recordList.length==0 && !loading">
							<text class="iconfont icon-wushuju"></text>
							<text class="cont">亲，暂无相关记录哦</text>
						</view>
						<uni-load-more v-else :loadingType="loadingType"></uni-load-more>
					</scroll-view>
				</view>
			</view>
		</popup>
	</view>
</template>

<script>
	import popup from '@/components/uni-popup.vue'
	import AlmostLottery from '@/uni_modules/almost-lottery/components/almost-lottery/almost-lottery.vue';
	import uniLoadMore from "@/components/uni-load-more.vue";
	export default {
		components: {
			AlmostLottery,
			popup,
			uniLoadMore
		},
		data() {
			return {
				loading: true,
				finish: false,
				times: 0,
				points:'',
				user_points:'',
				bg_image: '',
				content: '',
				// 以下是奖品配置数据
				// 奖品数据
				prizeList: [],
				// 奖品是否设有库存
				onStock: true,
				// 中奖下标
				prizeIndex: -1,
				result: {},
				listData: [],
				/*样式*/
				styleValue: '',
				/*当前角标*/
				index_num: 0,
				/*高度*/
				lineHeight: 0,
				/*最大数*/
				maxSize: 0,
				/*时间*/
				timer: null,
				isPopup: false,
				isRecord: false,
				recordList: [],
				list_rows: 10,
				last_page: 0,
				/*当前页面*/
				page: 1,
				/*没有更多*/
				no_more: false,
			};
		},
		computed: {
			/*加载中状态*/
			loadingType() {
				if (this.loading) {
					return 1;
				} else {
					if (this.recordList.length != 0 && this.no_more) {
						return 2;
					} else {
						return 0;
					}
				}
			}
		},
		onShow() {
			this.isRecord=false;
			this.restoreData();
			this.handleInitCanvas()
		},
		methods: {
			/*初始化*/
			init() {
				let self = this;
				if (this.listData.length > 1) {
					uni.getSystemInfo({
						success(res) {
							self.lineHeight = res.windowWidth / 750 * 60;
							self.maxSize = self.listData.length;
							self.timer = setInterval(function() {
								self.animation();
							}, 3000);
						}
					});
				}
			},
			/*动画*/
			animation() {
				let self = this;
				self.index_num++;
				if (self.index_num >= self.maxSize) {
					self.index_num = 0;
				}
				this.styleValue =-self.lineHeight * self.index_num ;
			},
			async getPrizeList() {
				uni.showLoading({
					title: '奖品准备中...'
				})
				this.loading = true;
				// 等待接口返回的数据进一步处理
				let res = await this.requestApiGetPrizeList();
				if (res.ok) {
					let data = res.data;
					if (data.length) {
						this.prizeList = data;
					}
				} else {
					uni.hideLoading()
					uni.showToast({
						title: '获取奖品失败'
					})
				}
			},
			requestApiGetPrizeList() {
				let self = this;
				return new Promise((resolve, reject) => {
					self._post('plus.lottery.Lottery/getLottery', {},
						function(res) {
							self.loading = false;
							self.bg_image = res.data.data.file_path;
							self.content = res.data.data.content;
							self.prizeList = res.data.data.prize;
							self.listData = res.data.recordList;
							self.listData.forEach((item, index) => {
								let num = Math.floor(Math.random() * 30) + 1;
								item.showText = num + '分钟前' + item.user.nickName + '抽中了' + item
									.record_name
							})
							self.times = res.data.nums;
							self.points = res.data.data.points;
							self.user_points = res.data.data.user_points;
							self.init();
							resolve({
								ok: true,
								data: self.prizeList
							})
						})
				})
			},
			// 重新生成
			handleInitCanvas() {
				this.prizeList = [];
				this.getPrizeList();

			},
			// 本次抽奖开始
			handleDrawStart() {
				console.log('start')
				let self = this;
				if (self.loading || !self.finish) {
					return
				}
				if (self.times <= 0) {
					self.showError('次数不足')
					return
				}
				self.loading = true;
				self._post('plus.lottery.Lottery/draw', {},
					function(res) {
						self.times--;
						self.loading = false;
						self.result = res.data.result;
						let list = [...self.prizeList];
						let prizeId = res.data.result.prize_id;
						for (let i = 0; i < list.length; i++) {
							let item = list[i]
							if (item.prize_id === prizeId) {
								// 中奖下标
								self.prizeIndex = i
								break
							}
						}
						console.log('本次抽中奖品 =>', self.prizeList[self.prizeIndex].name)
					},
					function(err) {
						self.loading = false;
					})
			},
			hidePopup() {
				this.isPopup = false;
			},
			hideRecordPopup() {
				this.isRecord = false;
			},

			// 本次抽奖结束
			handleDrawEnd() {
				this.isPopup = true;
				this.handleInitCanvas();
				// 完成抽奖后，这里处理你拿到结果后的逻辑
			},
			// 抽奖转盘绘制完成
			handleDrawFinish(res) {
				this.finish = true;
				// 抽奖转盘准备就绪后，这里处理你的逻辑
				// console.log('抽奖转盘绘制完成', res)
			},
			/*还原初始化*/
			restoreData() {
				this.recordList = [];
				this.page = 1;
				this.no_more = false;
				this.last_page = 1;
			},
			getList() {
				let self = this;
				self.loading = true;
				self._post('plus.lottery.Lottery/record', {
						list_rows: self.list_rows,
						page: self.page || 1,
					},
					function(res) {
						self.loading = false;
						self.recordList = self.recordList.concat(res.data.list.data);
						self.last_page = res.data.list.last_page;
						if (res.data.list.last_page <= 1) {
							self.no_more = true;
						}
					})
			},
			getRecord() {
				this.restoreData();
				this.getList();
				this.isRecord = true;

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
				self.getList();
			},
		}
	}
</script>

<style lang="scss">
	.bg-lottery {
		width: 750rpx;
		min-height: 1843rpx;
		background-size: 750rpx 1843rpx;
		background-position: 0 0;
		overflow: hidden;
	}
	.rule-title{
		font-size: 26rpx;
		margin: 0 auto;
		width: 640rpx;
		margin-top: 220rpx;
		color: #FFFFFF;
	}
	.rule-title-t{
		background-color: #EE1413;
		border-radius: 12rpx;
		height: 80rpx;
		line-height: 80rpx;
		width: 400rpx;
		padding:25rpx 35rpx;
		box-sizing: border-box;
		display: flex;
		justify-content: center;
		align-items: center;
		margin: 0 auto;
	}
	.lottery-title {
		padding-top: 40rpx;
		position: relative;
		z-index: 1;
	}

	.lottery-base {
		position: relative;
		z-index: 0;
		margin-top: -6rpx;
		margin-bottom: 80rpx;
	}

	.base-image {
		width: 424rpx;
		height: 186rpx;
		margin: 0 auto;
	}

	.btn-image {
		position: absolute;
		left: 0;
		right: 0;
		margin: 0 auto;
		bottom: -48rpx;
		width: 262rpx;
		height: 130rpx;
	}

	.btn-red.record {
		width: 300rpx;
		height: 81rpx;
		background: #E03325;
		border: 0px solid #E03325;
		border-radius: 10rpx;
		font-size: 30rpx;
		font-family: SourceHanSansSC;
		font-weight: 500;
		color: #FFFFFF;
		margin: 0 auto;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.ruler-box {
		box-sizing: border-box;
		width: 678rpx;
		margin: 0 auto;
		background: #FFFFFF;
		border-radius: 12rpx;
		padding: 20rpx 45rpx;
	}

	.special-box {
		width: 678rpx;
		height: 60rpx;
		background: #F9C68B;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 22rpx;
		padding: 0 64rpx;
		box-sizing: border-box;
		margin: 60rpx auto;
	}

	.special-content {
		width: 551rpx;
		font-size: 30rpx;
		font-family: Source Han Sans CN;
		font-weight: 500;
		color: #E8573C;
		flex: 1;
		margin: 0 auto;
		overflow: hidden;
	}

	.special-content-list {
		transition: transform .4s;
	}

	.content-item {
		height: 60rpx;
		line-height: 60rpx;
	}

	.special-content.display_1 {
		height: 60rpx;
	}

	.pop_img {
		width: 130rpx;
		height: 130rpx;
		margin-bottom: 20rpx;
		image {
			width: 130rpx;
			height: 130rpx;
		}
	}

	.pop-record-line {
		background-color: #333333;
		width: 245rpx;
		height: 1rpx;
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		margin: auto;
		z-index: -1;
	}

	.pop-record-text {
		color: #333333;
		font-size: 28rpx;
		background-color: #ffffff;
		display: inline;
		padding: 0 12rpx;
	}

	.pop-tit-line {
		background-color: #f5ddc1;
		width: 472rpx;
		height: 2rpx;
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		margin: auto;
		z-index: -1;
	}

	.pop-tit-text {
		color: #f5ddc1;
		font-size: 52rpx;
		background-color: #ea564c;
		display: inline;
		padding: 0 16rpx;
	}

	.product-box {
		border-radius: 24rpx;
		background-color: #FFFFFF;
		padding: 45rpx 0;
	}

	.circular-right {
		width: 44rpx;
		height: 44rpx;
		border-radius: 50%;
		background-color: #ea564c;
		position: absolute;
		top: 0;
		bottom: 0;
		margin: auto 0;
		right: -22rpx;
	}

	.circular-left {
		width: 44rpx;
		height: 44rpx;
		border-radius: 50%;
		background-color: #ea564c;
		position: absolute;
		top: 0;
		bottom: 0;
		margin: auto 0;
		left: -22rpx;
	}

	.record-box {
		padding-bottom: 70rpx;
	}

	.record-list-btn {
		width: 96rpx;
		height: 40rpx;
		line-height: 40rpx;
		font-size: 22rpx;
		color: #FFFFFF;
		display: flex;
		justify-content: center;
		align-items: center;
		padding: 0;
		margin-left: 16rpx;
	}
</style>
