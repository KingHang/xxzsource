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

			<!--限时拼团-->
			<view class="limited-spike d-b-c m-0-20 mt20">
				<text class="left-name">限时拼团</text>
				<view class="right">
					<Countdown ref='countdown' :config="countdownConfig" @returnVal="returnValFunc"></Countdown>
				</view>
			</view>

			<!--基本信息-->
			<view class="bg-white m-0-20 mb20 p30 bottom-radius">
				<view class="price-wrap">
					<view class="left">
						<template>
							<view class="new-price">
								<text class="f24 redF6">￥</text>
								<text class="num">{{ detail.assemble_price }}</text>
							</view>
							<text class="old-price">{{ '￥'+detail.line_price }}</text>
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
			<!--拼团用户-->
			<Bill :bill="bill" @openMore="openMoreFunc" @gobill="gobillFunc"></Bill>
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
		<view class="btns-wrap d-s-c d-stretch">
			<template v-if="!loadding">
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
				<view class="buy-alone flex-1 d-c-c d-c" @click="gotoProducntDetail()">
					<text>￥{{ detail.product.product_price }}</text>
					<button type="primary">单独购买</button>
				</view>
				<view class="make-group flex-1 d-c-c d-c" @click="openPopup('order')">
					<text>￥{{ detail.assemble_price }}</text>
					<button type="primary">立即拼团</button>
				</view>
			</template>
		</view>

		<!--购物确定-->
		<spec :isPopup="isPopup" @confirm="confirm" :productModel="productModel" @close="closePopup"></spec>

		<!--客服-->
		<Mpservice v-if="isMpservice" :isMpservice="isMpservice" @close="closeMpservice"></Mpservice>

		<!-- 保障弹窗 -->
		<guarantee :isguarantee="isguarantee" :server="detail.server" @close="closeGuarantee"></guarantee>

		<!--更多用户-->
		<MoreBill v-if="ismore" :bill="bill" :ismore="true" @closeMore="openMoreFunc" @gobill="gobillFunc"></MoreBill>
	</view>
</template>

<script>
	import Countdown from '@/components/countdown/countdown.vue';
	import Mpservice from '@/components/mpservice/Mpservice.vue';
	import MoreBill from './popup/MoreBill.vue';
	import Spec from './popup/Spec.vue';
	import Bill from './part/Bill.vue';
	import utils from '@/common/utils.js';
	import guarantee from '@/components/guarantee.vue';
	export default {
		components: {
			Spec,
			Countdown,
			Mpservice,
			Bill,
			MoreBill,
			guarantee
		},
		data() {
			return {
				from_type: 10,
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				/*是否加载完成*/
				loadding: true,
				/*是否显示面板指示点*/
				indicatorDots: true,
				/*是否知道切换*/
				autoplay: true,
				/*自动切换时间间隔*/
				interval: 2000,
				/*滑动动画时长*/
				duration: 500,
				/*商品id*/
				product_id: null,
				/*产品图片展示*/
				imagList: [],
				/*活动详情*/
				activeDetail: {},
				alreadyChioce: '',
				/*详情*/
				detail: {
					product_sku: {},
					show_sku: {
						assemble_price: '',
						product_sku_id: 0,
						line_price: '',
						stock_num: 0,
						sku_image: '',
						assemble_product_sku_id: 0
					},
					show_point_sku: {}
				},
				/*已购买用户*/
				bill: [],
				/*是否确定购买弹窗*/
				isPopup: false,
				/*商品属性*/
				specData: null,
				/*子级页面传参*/
				productModel: {},
				/*拼团商品ID*/
				assemble_product_id: 0,
				/*倒计时配置*/
				countdownConfig: {
					/*开始时间*/
					startstamp: 0,
					/*结束时间*/
					endstamp: 0
				},
				/*是否打开客服*/
				isMpservice: false,
				/*是否打开更多*/
				ismore: false,
				/*拼团ID*/
				assemble_bill_id: null,
				/*当前用户id*/
				user_id: null,
				/*商品规格*/
				productSku: [],
				serverList: '', //保障
				isguarantee: false,
				shop_info: {},
				store_open: 1
			};
		},
		onLoad(e) {
			uni.showLoading({
				title: '加载中'
			});

			this.user_id = this.getUserId();

			/*拼团商品ID*/
			this.assemble_product_id = e.assemble_product_id;

			/*拼团组ID*/
			this.assemble_bill_id = e.assemble_bill_id;
		},
		mounted() {
			this.init();

			/*获取产品详情*/
			this.getData();
		},
		onHide() {},
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
							//#ifdef MP-WEIXIN
							h = _this.phoneHeight
							//#endif
							_this.scrollviewHigh = h;
						}).exec();
					}
				});
			},

			/*获取数据*/
			getData() {
				let self = this;
				self.loadding = true;
				self._get(
					'plus.assemble.product/detail', {
						assemble_product_id: self.assemble_product_id
					},
					function(res) {
						self.countdownConfig.startstamp = res.data.active.start_time;
						self.countdownConfig.endstamp = res.data.active.end_time;
						self.activeDetail = res.data.active;
						/*详情内容格式化*/
						res.data.detail.product.content = utils.format_content(res.data.detail.product.content);

						/*初始化商品多规格*/
						if (res.data.detail.product.spec_type == 20) {
							self.initSpecData(res.data.detail.assembleSku, res.data.specData);
						}
						//#ifdef MP-WEIXIN
						self.store_open = res.data.store_open;
						//#endif
						self.detail = res.data.detail;
						self.shop_info = res.data.detail.supplier;
						self.bill = res.data.bill;
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
				/*是否单团，0不，1是*/
				if ((this.activeDetail.is_single == 1 && e == 'order' && this.bill.length == 0) || (this.activeDetail.is_single ==
						0 && e == 'order') || e != 'order') {
					if (e == 'order' && this.assemble_bill_id == null) {
						this.assemble_bill_id = 0;
					}
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
							assemble_bill_id: this.assemble_bill_id,
							sum: 1
						},
						productSku: this.productSku,
						type: e
					};
					this.productModel = obj;
					this.isPopup = true;
				} else {
					this.ismore = true;
				}
			},
			confirm() {
				this.$refs.countdown.clear()
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

			/*打开客服*/
			openMaservice() {
				this.isMpservice = true;
			},

			/*关闭客服*/
			closeMpservice() {
				this.isMpservice = false;
			},

			/*跳转商品详情页面*/
			gotoProducntDetail() {
				this.$refs.countdown.clear();
				this.gotoPage('/pages/product/detail/detail?product_id=' + this.detail.product_id);
			},

			/*打开关闭更多*/
			openMoreFunc(e) {
				this.ismore = e;
			},
			closeGuarantee() {
				this.isguarantee = false;
			},
			showGuarantee() {
				this.isguarantee = true;
			},
			/*去拼团*/
			gobillFunc(e) {
				this.$refs.countdown.clear();
				this.ismore = false;
				for (let i = 0; i < e.billUser.length; i++) {
					let user = e.billUser[i];
					if (this.user_id === user.user_id) {
						this.gotoPage('/pages/plus/assemble/fight-group-detail/fight-group-detail?assemble_bill_id=' + e.assemble_bill_id);
						return;
					}
				}
				this.assemble_bill_id = e.assemble_bill_id;
				this.openPopup();
			},

			/*倒计时返回值*/
			returnValFunc(e) {},
			//跳转店铺首页
			goto_shop() {
				let self = this;
				self.gotoPage('/pages/shop/shop?shop_supplier_id=' + self.detail.supplier.shop_supplier_id);
			},
		},
		destroyed() {}
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
		font-size: 26rpx;
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
		width: 90rpx;
		height: 100rpx;
	}

	.btns-wrap .icon-box .iconfont {
		font-size: 40rpx;
		color: #888888;
	}

	.btns-wrap .customer-service button {
		height: 80rpx;
		line-height: 80rpx;
	}

	.btns-wrap button,
	.btns-wrap button:after {
		padding: 0;
		margin: 0;
		height: 30rpx;
		line-height: 30rpx;
		margin: 0;
		padding: 0;
		flex: 1;
		border-radius: 0;
		border: 0;
	}

	.btns-wrap .buy-alone button,
	.btns-wrap .make-group button {
		font-size: 26rpx;
		background: none;
	}

	.btns-wrap .buy-alone,
	.btns-wrap .buy-alone button {

		width: 220rpx;
		height: 80rpx;
		border-top-left-radius: 40rpx;
		border-bottom-left-radius: 40rpx;
		margin-left: 10rpx;
	}

	.btns-wrap .buy-alone {
		background: linear-gradient(0deg, #ffa945 0%, #ff8439 100%);
	}

	.btns-wrap .buy-alone text,
	.btns-wrap .make-group text {
		color: #ffffff;
		font-size: 32rpx;
	}

	.btns-wrap .make-group,
	.btns-wrap .make-group button {
		width: 220rpx;
		height: 80rpx;
		border-top-right-radius: 40rpx;
		border-bottom-right-radius: 40rpx;
		margin-right: 10rpx;
	}

	.btns-wrap .make-group {
		background: linear-gradient(0deg, #ff4444 0%, #f6220d 100%);
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
		border-radius: 12rpx 0 0 12rpx;
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
		background: linear-gradient(-90deg, #FF3C3C 0%, #FF6E35 98%);
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
		border-radius: 12rpx;
		line-height: 40rpx;
		text-align: center;
		background: #ffffff;
		color: #FF0001;
		font-size: 28rpx;
		padding: 6rpx 8rpx;
	}

	.product-detail .limited-spike .right::v-deep>text {
		margin-left: 10rpx;

	}

	.already-choice {
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

	.btnname {
		position: absolute;
		bottom: -14px;
		left: 0;
		right: 0;
		font-size: 22rpx;
		color: #333333;
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
