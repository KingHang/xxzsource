<template>
	<view class="product-detail">
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" v-if="!loadding">
			<!--图片-->
			<view class="product-pic">
				<swiper class="swiper" :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration">
					<swiper-item v-for="(item, index) in detail.product.image" :key="index">
						<image :src="item.file_path" mode="aspectFit"></image>
					</swiper-item>
				</swiper>
			</view>

			<!--限时秒杀-->
			<view class="limited-spike d-b-c m-0-20 mt20">
				<text class="left-name">限时秒杀</text>
				<view class="right d-s-c">
					<Countdown ref="countdown" :config="countdownConfig" @returnVal="returnValFunc"></Countdown>
				</view>
			</view>

			<!--基本信息-->
			<view class="bg-white  m-0-20 mb20 p30 bottom-radius">
				<view class="price-wrap">
					<view class="left">
						<template>
							<view class="new-price">
								<text class="f24 redF6">￥</text>
								<text class="num">{{ detail.seckill_price }}</text>
							</view>
							<text class="old-price">{{ "￥"+detail.line_price }}</text>
						</template>
					</view>
					<template>
						<text class="already-sale">已出售{{ detail.product_sales }}件</text>
					</template>
				</view>
				<view class="product-name">{{ detail.product.product_name }}</view>
				<view class="product-describe">{{ detail.product.selling_point }}</view>
			</view>
			<view class="m20 br12 o-h p-0-30 bg-white">
				<!--已选择-->
				<view class="already-choice" :class="detail.server!=''?'border-b-d9':''" v-if="detail.spec_type == 20" @click="openPopup('order')">
					<view class="group-hd">
						<view class="left">
							<text class="min-name gray9">选择：</text>
						</view>
						<view class="flex-1 p-0-20 center-content f28 text-ellipsis o-h">
							{{alreadyChioce}}
						</view>
						<view class="right">
							<text class="icon iconfont icon-jiantou" style="font-size: 22rpx;color: #9A9A9A;"></text>
						</view>
					</view>
				</view>
				<!-- 保障 -->
				<view class="already-choice" @click="showGuarantee" v-if="detail.server!=''">
					<view class="group-hd">
						<view class="left">
							<text class="min-name gray9">服务：</text>
						</view>
						<view class="flex-1 p-0-20 center-content f28 text-ellipsis o-h">
							{{serverList}}
						</view>
						<view class="right">
							<text class="icon iconfont icon-jiantou" style="font-size: 22rpx;color: #9A9A9A;"></text>
						</view>
					</view>
				</view>
			</view>
			<!-- 店铺信息 -->
			<view class="shop_head_info" v-if="store_open">
				<view class="shop_list_body_item_shop">
					<view class="shop_list_body_item_shop_logo">
						<image :src="shop_info.logos" mode=""></image>
					</view>
					<view class="shop_list_body_item_shop_info flex-1">
						<view class="f32 title fb">{{shop_info.name}}</view>
						<view class="f26 brand gray9">主营品牌： {{shop_info.category_name}}</view>
						<view class="f26 sales gray9">销量：{{shop_info.product_sales}}件</view>
					</view>
					<view class="shop_list_body_item_shop_others">
						<view class="f26 collect">店铺评分：<text class="fb redF6">{{shop_info.server_score}}</text></view>
						<button @click="goto_shop()">进店看看</button>
					</view>
				</view>
			</view>
			<!--详情内容-->
			<view class="product-content">
				<view class="group-hd border-b-e">
					<view class="d-s-c">
						<view class="pro_nameline"></view><text class="min-name  f32 fb">商品介绍</text>
					</view>
				</view>
				<view class="content-box" v-html="detail.product.content"></view>
			</view>
		</scroll-view>

		<!--底部按钮-->
		<view class="btns-wrap">
			<view class="customer-service d-c-c">
				<view class="icon-box d-c-c" @click="gotoPage('/pages/index/index')">
					<button class="d-c-c d-c bg-white">
						<text class="btn_btom pr icon iconfont icon-Homehomepagemenu gray3" style="height: 50rpx;line-height: 60rpx;"></text>
						<text class="f22 gray3" style="height: 50rpx;line-height: 40rpx;">首页</text>
					</button>
				</view>
				<!-- #ifdef MP-WEIXIN -->
				<view class="icon-box">
					<button class="d-c-c d-c" open-type="contact" session-from="wxapp">
						<text class="icon iconfont icon-kefu2 gray3" style="height: 50rpx;line-height: 60rpx;"></text>
						<text class="f22 gray3" style="height: 50rpx;line-height: 40rpx;">客服</text>
					</button>
				</view>
				<!-- #endif -->
				<!-- #ifdef H5 -->
				<view class="icon-box" @click="openMaservice">
					<button class="d-c-c d-c">
						<text class="icon iconfont icon-kefu2 gray3" style="height: 50rpx;line-height: 60rpx;"></text>
						<text class="f22 gray3" style="height: 50rpx;line-height: 40rpx;">客服</text>
					</button>
				</view>
				<!-- #endif -->
			</view>
			<button type="primary" class="buy" @click="openPopup('order')">立即抢购</button>
		</view>

		<!--购物确定-->
		<spec :isPopup="isPopup" @confirm="confirm" :productModel="productModel" @close="closePopup"></spec>

		<!--客服-->
		<Mpservice v-if="isMpservice" :isMpservice="isMpservice" @close="closeMpservice"></Mpservice>

		<!-- 保障弹窗 -->
		<guarantee :isguarantee="isguarantee" :server="detail.server" @close="closeGuarantee"></guarantee>
	</view>
</template>

<script>
	import Spec from './popup/Spec.vue';
	import Countdown from '@/components/countdown/countdown.vue';
	import Mpservice from '@/components/mpservice/Mpservice.vue';
	import utils from '@/common/utils.js';
	import guarantee from '@/components/guarantee.vue';
	export default {
		components: {
			/*选择规格*/
			Spec,
			/*倒计时组件*/
			Countdown,
			/*客服*/
			Mpservice,
			guarantee
		},
		data() {
			return {
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				/*是否加载完成*/
				loadding: true,
				/*是否显示面板指示点*/
				indicatorDots: true,
				/*是否自动切换*/
				autoplay: true,
				/*自动切换时间间隔*/
				interval: 2000,
				/*滑动动画时长*/
				duration: 500,
				/*秒杀商品id*/
				seckill_product_id: null,
				/*详情*/
				detail: {
					/*商品规格*/
					product_sku: {},
					/*当前规格对象*/
					show_sku: {
						/*秒杀价格*/
						seckill_price: '',
						/*商品规格ID*/
						product_sku_id: 0,
						/*划线价格*/
						line_price: '',
						/*库存数量*/
						stock_num: 0,
						/*商品规格图片*/
						sku_image: '',
						/*秒杀商品规格ID*/
						seckill_product_sku_id: 0
					},
					/*暂不知晓*/
					show_point_sku: {}
				},
				/*已经选择的规格*/
				alreadyChioce: '',
				/*是否确定购买弹窗*/
				isPopup: false,
				/*倒计时配置*/
				countdownConfig: {
					/*开始时间*/
					startstamp: 0,
					/*结束时间*/
					endstamp: 0
				},
				/*商品属性*/
				specData: null,
				/*子级页面传参*/
				productModel: {},
				/*商品规格*/
				productSku: [],
				/*是否打开客服*/
				isMpservice: false,
				/*购物车商品数量*/
				cart_total_num: 0,
				serverList: '', //保障
				isguarantee: false,
				shop_info: {},
				store_open: 1,
			};
		},
		onLoad(e) {
			/*分类id*/

			this.seckill_product_id = e.seckill_product_id;

		},
		mounted() {
			this.init();
			/*获取产品详情*/
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
						let view = uni.createSelectorQuery().select('.btns-wrap');
						view.boundingClientRect(data => {
							let h = _this.phoneHeight - data.height;
							_this.scrollviewHigh = h;
						}).exec();
					}
				});
			},

			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				self._get(
					'plus.seckill.product/detail', {
						seckill_product_id: self.seckill_product_id
					},
					function(res) {
						/*倒计时*/
						self.countdownConfig.endstamp = res.data.active.end_time;
						self.countdownConfig.startstamp = res.data.active.start_time;

						/*详情内容格式化*/
						res.data.detail.product.content = utils.format_content(res.data.detail.product.content);

						/*初始化商品多规格*/
						if (res.data.detail.product.spec_type == 20) {
							self.initSpecData(res.data.detail.seckillSku, res.data.specData);
						}
						//#ifdef MP-WEIXIN
						self.store_open = res.data.store_open;
						//#endif
						self.detail = res.data.detail;
						self.shop_info = res.data.detail.supplier;
						self.getServer();
						self.loadding = false;
						uni.hideLoading();
					}
				);
			},
			getServer() {
				let self = this;
				let serverList = [];
				if (self.detail.server) {
					self.detail.server.forEach((item, index) => {
						serverList.push(item.name)
					})
					self.serverList = serverList.join('·')
				}
			},
			closeGuarantee() {
				this.isguarantee = false;
			},
			showGuarantee() {
				this.isguarantee = true;
			},
			/*多规格商品*/
			initSpecData(list, data) {
				for (let i = 0; i < list.length; i++) {
					let item = list[i];
					if (item.productSku) {
						let arr = item.productSku.spec_sku_id.split('_').map(Number);
						this.productSku.push(arr);
					}
				}
				for (let i in data.spec_attr) {
					for (let j = 0; j < data.spec_attr[i].spec_items.length; j++) {
						let item = data.spec_attr[i].spec_items[j];
						if (this.hasSpec(item.item_id, i)) {
							item.checked = false;
							item.disabled = false;
						} else {
							data.spec_attr[i].spec_items.splice(j, 1);
							j--;
						}
					}
				}

				this.specData = data;
				if (this.specData.spec_attr) {
					this.specData.spec_attr.forEach(item => {
						this.alreadyChioce += item.group_name;
						this.alreadyChioce += ' / ';
					});
					this.alreadyChioce = this.alreadyChioce.replace(/(\s\/\s)$/gi, '');
				}
			},

			/*判断有没有规格*/
			hasSpec(id, _index) {
				let flag = false;
				for (let i = 0; i < this.productSku.length; i++) {
					let arr = this.productSku[i];
					if (arr[_index] == id) {
						flag = true;
						break;
					}
				}
				return flag;
			},

			/*打开窗口*/
			openPopup(e) {
				let obj = {
					specData: this.specData,
					detail: this.detail,
					productSpecArr: this.specData != null ? new Array(this.specData.spec_attr.length) : [],
					show_sku: {
						sku_image: '',
						seckill_price: 0,
						product_sku_id: 0,
						line_price: 0,
						seckill_stock: 0,
						seckill_product_sku_id: 0,
						sum: 1
					},
					productSku: this.productSku,
					type: e
				};
				this.productModel = obj;
				this.isPopup = true;
			},

			/*关闭弹窗*/
			closePopup(e, cart_total_num) {
				this.isPopup = false;
				if (e && e.spec_attr) {
					this.alreadyChioce = '';
					let has = '已选：';
					let noone = '';
					e.spec_attr.forEach(item => {
						if (item.spec_items) {
							let h = '';
							for (let i = 0; i < item.spec_items.length; i++) {
								let child = item.spec_items[i];
								if (child.checked) {
									h = child.spec_value + ' / ';
									break;
								}
							}
							if (h != '') {
								has += h;
							} else {
								noone += item.group_name;
							}
						}
					});
					if (noone != '') {
						this.alreadyChioce = noone;
					} else {
						has = has.replace(/(\s\/\s)$/gi, '');
						this.alreadyChioce = has;
					}
				}
				if (cart_total_num) {
					this.cart_total_num = cart_total_num;
				}
			},
			confirm() {
				this.$refs.countdown.clear();
			},
			/*打开客服*/
			openMaservice() {
				this.isMpservice = true;
			},

			/*关闭客服*/
			closeMpservice() {
				this.isMpservice = false;
			},
			//跳转店铺首页
			goto_shop() {
				let self = this;
				self.gotoPage('/pages/shop/shop?shop_supplier_id=' + self.detail.supplier.shop_supplier_id);
			},

			/*倒计时返回状态*/
			returnValFunc(e) {}
		}
	};
</script>

<style lang="scss">
	.product-detail {
		padding-bottom: 90rpx;
	}

	.product-detail .product-pic,
	.product-detail .product-pic .swiper,
	.product-detail .product-pic image {
		width: 750rpx;
		height: 750rpx;
	}

	.product-detail .price-wrap {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.product-detail .price-wrap .left {
		display: flex;
		justify-content: flex-start;
		align-items: flex-end;
	}

	.product-detail .price-wrap .new-price {
		color: $dominant-color;
		font-size: 30rpx;
		font-weight: bold;
	}

	.product-detail .price-wrap .new-price .num {
		padding: 0 4rpx;
		font-size: 40rpx;
	}

	.product-detail .price-wrap .old-price {
		margin-left: 10rpx;
		font-size: 24rpx;
		color: #999999;
		text-decoration: line-through;
	}

	.product-detail .already-sale {
		font-size: 24rpx;
		color: #999999;
	}

	.product-detail .product-name {
		padding-top: 26rpx;
		font-size: 32rpx;
		font-weight: bold;
		color: #333333;
	}

	.product-detail .product-describe {
		padding: 18rpx;
		line-height: 40rpx;
		font-size: 26rpx;
		color: #666666;
		background-color: #f2f2f2;
		border-radius: 12rpx;
		word-break: break-all;
		margin-top: 28rpx;
	}

	.product-comment,
	.product-content {
		margin-top: 20rpx;
		background: #ffffff;
	}

	.product-content .content-box p image {
		width: 100%;
	}

	.product-content .content-box {
		font-size: 36rpx;
	}

	.btns-wrap {
		position: fixed;
		height: 100rpx;
		right: 0;
		bottom: 0;
		left: 0;
		display: flex;
		align-items: center;
		background: #ffffff;
	}

	.btns-wrap .icon-box {
		width: 100rpx;
		height: 100rpx;
	}

	.btns-wrap .icon-box .iconfont {
		font-size: 40rpx;
		color: #888888;
	}

	.btns-wrap .icon-box .iconfont .num {
		position: absolute;
		padding: 0 5rpx;
		top: 10rpx;
		left: 50%;
		height: 30rpx;
		line-height: 30rpx;
		border-radius: 15rpx;
		font-size: 20rpx;
		color: #ffffff;
		background: red;
	}

	.btns-wrap button,
	.btns-wrap button:after {
		height: 80rpx;
		line-height: 80rpx;
		margin: 0;
		padding: 0;
		flex: 1;
		border-radius: 40rpx;
		border: 0;
	}

	.btns-wrap button.add-cart {
		background: $orange-color;
	}

	.btns-wrap button.buy {
		background: $dominant-color;
	}

	.share-box {
		position: fixed;
		padding-right: 10rpx;
		width: 80rpx;
		height: 80rpx;
		right: 0;
		bottom: 180rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 16rpx 0 0 16rpx;
		background: rgba(0, 0, 0, 0.8);
	}

	.share-box button {
		padding: 0;
		background: 0;
		line-height: 60rpx;
	}

	.share-box .iconfont {
		margin-bottom: 10rpx;
		font-size: 50rpx;
		color: #ffffff;
	}

	.create-img {
		width: 100%;
		padding: 20rpx;
		box-sizing: border-box;
	}

	.create-img image {
		width: 100%;
	}

	.create-img button {
		width: 100%;
	}

	.product-detail .limited-spike {
		padding: 0 35rpx;
		height: 90rpx;
		color: #ffffff;
		border-radius: 12rpx 12rpx 0 0;
		background: linear-gradient(-90deg, #CB2BFF 0%, #7727E7 98%);
		// background: linear-gradient(to bottom, #ff6c65, #e2231a);
	}

	.product-detail .limited-spike .left-name {
		font-size: 32rpx;
		color: #ffffff;
	}

	.product-detail .limited-spike .right::v-deep text {
		color: #FFFFFF;
		font-size: 28rpx;
	}

	.product-detail .limited-spike .right::v-deep .box {
		height: 40rpx;
		padding: 4rpx;
		border-radius: 8rpx;
		line-height: 40rpx;
		text-align: center;
		background: #ffffff;
		color: #FF0001;
	}

	.product-detail .limited-spike .right::v-deep>text {
		margin-left: 10rpx;

	}

	.already-choice {
		margin-top: 20rpx;
		padding: 0 30rpx;
		background: #ffffff;
	}

	.already-choice .center-content {
		line-height: 90rpx;
	}

	.shop_head_info {
		margin: 20rpx;
		padding: 30rpx;
		box-sizing: border-box;
		background-color: white;
		border-radius: 12rpx;
	}

	.shop_list_body_item_shop {
		width: 100%;
		height: 120rpx;
		display: flex;
		justify-content: space-between;
	}

	.shop_list_body_item_shop_logo {
		width: 120rpx;
		height: 120rpx;
	}

	.shop_list_body_item_shop_logo image {
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.1);
		border-radius: 12rpx;
	}

	.shop_list_body_item_shop_info {
		box-sizing: border-box;
		margin-left: 20rpx;
		padding-top: 0;
		display: flex;
		justify-content: space-between;
		flex-direction: column;
	}

	.shop_list_body_item_shop_others {
		box-sizing: border-box;
		display: flex;
		justify-content: space-between;
		flex-direction: column;
		text-align: right;
		padding-top: 0;
	}

	.shop_list_body_item_shop_others button {
		width: 160rpx;
		height: 60rpx;
		border: 1rpx solid #F6220C;
		border-radius: 30rpx;
		line-height: 60rpx;
		font-size: 26rpx;
		font-family: PingFang SC;
		font-weight: 500;
		color: #F6220C;
		text-align: center;
		padding: 0;
		background-color: #ffffff;
	}

	.bottom-radius {
		border-bottom-left-radius: 12rpx;
		border-bottom-right-radius: 12rpx;
	}

	.pro_nameline {
		width: 4rpx;
		height: 24rpx;
		background-color: #f6220c;
		margin-right: 12rpx;
	}
</style>
