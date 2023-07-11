<template>
	<view>
		<scroll-view scroll-y="true" class="pr scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50">
			<view class="add-live-room">
				<form @submit="nowLive">
					<view class="d-c-c mt10"></view>
					<view class="group">
						<view class="group-hd">
							<view class="right"><button type="default" class="btn-red-border" @tap="showProducts">+添加商品</button></view>
						</view>
						<view class="group-bd">
							<view class="item d-s-c" v-for="(item, index) in form.data" :key="index" @tap="gotoProduct(item)">
								<view class="picture"><image :src="item.product.image[0].file_path" mode="aspectFill"></image></view>
								<view class="flex-1 ml20 mr20">
									<view class="text-ellipsis-2 f30">{{ item.product.product_name }}</view>
									<view class="price">
										￥
										<text class="fb f30">{{ item.product.product_price }}</text>
									</view>
								</view>
								<button type="default" @click.stop="deleteFunc(item)">移除</button>
							</view>
						</view>
					</view>
				</form>
			</view>
		</scroll-view>

		<!--商品列表-->
		<Products :open="open_products" :relationList="relationList" @close="closeProducts"></Products>

		<!--上传图片-->
		<Upload v-if="isUpload" @getImgs="getImgsFunc" :type="file_type"></Upload>
	</view>
</template>

<script>
import Countdown from '@/components/countdown/countdown.vue';
import Upload from '@/components/upload/upload.vue';
import pickerViewDatetime from '@/components/picker-view-datetime/pickerViewDatetime.vue';
import Products from './dialog/Products.vue';
export default {
	components: {
		Upload,
		pickerViewDatetime,
		Products,
		Countdown
	},
	data() {
		return {
			/*手机高度*/
			phoneHeight: 0,
			/*可滚动视图区域高度*/
			scrollviewHigh: 0,
			/*是否登录成功*/
			loading: true,
			/*form表单*/
			form: {
				/*直播间名称*/
				name: '',
				/*背景图*/
				//cover_img_id: null,
				//cover_img_path: '',
				/*直播间分享图*/
				share_img_id: null,
				share_img_path: '',
				/*直播开始时间*/
				start_time: '',
				/*预计结束时间*/
				end_time: '',
				/*商品id*/
				productIds: [],
				data: []
			},
			/*是否打开上传图片*/
			isUpload: false,
			/*文件类别*/
			file_type: null,
			/*当前图片ID*/
			cur_image: null,
			/*是否展示商品*/
			open_products: false,
			/*关联商品*/
			relationList: [],
			/*倒计时配置*/
			countdownConfig: {
				/*开始时间*/
				startstamp: 0,
				/*结束时间*/
				endstamp: 0,
				/*标题*/
				title: '距开播还剩',
				/*类别*/
				type: 'text'
			},
			tapTime: '',
			/*判断是否主播*/
			is_user: false,
			shop_supplier_id:''
		};
	},
	onLoad(e) {
		this.form.room_id = e.room_id;
		this.shop_supplier_id=e.shop_supplier_id;
		this.getDetail();
		// this.getProduct();
	},
	mounted() {
		this.init();
	},
	methods: {
		/*获取详情*/
		getDetail() {
			let self = this;
			uni.showLoading({
				title: '加载中'
			});
			let data_type = self.data_type;
			self._get(
				'plus.live.RoomApply/liveproduct',{
					shop_supplier_id:self.shop_supplier_id
				},
				function(res) {
					uni.hideLoading();
					self.form = res.data.list;
					self.form.data.forEach(item => {
						self.relationList.push(item.product_id);
					});
					
					// self.countdownConfig.endstamp = res.data.model.start_time;
				}
			);
		},
		getProduct() {
			let self = this;
			uni.showLoading({
				title: '加载中'
			});
			self._get(
				'plus.live.RoomApply/liveproduct',{},
				function(res) {
					uni.hideLoading();
				}
			);
		},

		/*初始化*/
		init() {
			let _this = this;
			uni.getSystemInfo({
				success(res) {
					_this.scrollviewHigh = res.windowHeight;
				}
			});
		},

		/*展开商品*/
		showProducts() {
			this.open_products = true;
		},

		/*关闭商品*/
		closeProducts(e) {
			let self = this;
			self.open_products = false;
			let arr = [];		
			if(e==null){
				return;
			}
			for (let i = 0; i < e.length; i++) {
				if (self.relationList.indexOf(e[i]) != -1) {
					e.splice(i, 1);
					i--;
				}
			}			
			if (e.length > 0) {
				self._post(
					'plus.live.RoomApply/addProduct',
					{
						productIds: e
					},
					function(res) {
						uni.showToast({
							title: '绑定商品成功了',
							duration: 2000,
							icon: 'success'
						});

						self.getDetail();
					}
				);
			}
		},

		/*跳转商品详情*/
		gotoProduct(e) {
			let url = 'pages/product/detail/detail?product_id=' + e.product_id;
			this.gotoPage(url);
		},

		/*移除*/
		deleteFunc(item) {
			let self = this;
			uni.showModal({
				title: '提示',
				content: '您确定要移除吗?',
				success: function(o) {
					if (o.confirm) {
						uni.showLoading({
							title: '正在处理'
						});
						self._post(
							'plus.live.RoomApply/delProduct',
							{
								room_id: item.room_id,
								product_id: item.product.product_id
							},
							function(res) {
								uni.hideLoading();
								uni.showToast({
									title: res.msg,
									duration: 2000,
									icon: 'success'
								});
								self.getDetail();
							}
						);
					} else {
						uni.showToast({
							title: '取消移除',
							duration: 1000,
							icon: 'none'
						});
					}
				}
			});
		},

		/*创建并进入直播间*/
		nowLive: function() {
			var self = this;
			// 防止两次点击操作间隔太快
			var nowTime = new Date();
			if (nowTime - this.tapTime < 1000) {
				return;
			}
			if (!self.form.name) {
				self.form.name = '新建直播间';
			}
			if (/[<>*{}()^%$#@!~&= ]/.test(self.form.name)) {
				uni.showModal({
					title: '提示',
					content: '名称不能为空或包含特殊字符',
					showCancel: false
				});
				return;
			}

			var url = '/pages/pagesLive/live/live?roomName=' + self.form.name + '&roomID=' + self.form.room_id;
			if (self.is_user) {
				url = '/pages/pagesLive/live/live?type=create&roomName=' + self.form.name + '&roomID=' + self.form.room_id;
			}

			self.gotoPage(url);
			self.tapTime = nowTime;
		}
	}
};
</script>

<style scoped>
.share-image image {
	border: 1rpx solid #dddddd;
	width: 240rpx;
	height: 240rpx;
	border-radius: 16rpx;
}

.add-live-room .item {
	margin-bottom: 20rpx;
	padding: 20rpx;
	background: #ffffff;
}

.add-live-room .picture {
	width: 200rpx;
	height: 200rpx;
}

.add-live-room .picture image {
	width: 100%;
	height: 100%;
}

.add-live-room .item button {
	padding: 0 30rpx;
	height: 60rpx;
	line-height: 60rpx;
	background: #E2231A;
	color: #ffffff;
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
