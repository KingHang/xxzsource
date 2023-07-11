<template>
	<view class="product-detail" v-if="!loadding">
		<!--图片-->
		<view class="product-pic">
			<swiper class="swiper" :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration">
				<swiper-item v-for="(item,index) in detail.product.image" :key="index">
					<image :src="item.file_path" mode="aspectFit"></image>
				</swiper-item>
			</swiper>
		</view>

		<!--基本信息-->
		<view class="bg-white">
			<view class="price-wrap">
				<view class="flex-1 d-s-s d-c">
					<view class="new-price">
						<text class="red f24">所需积分</text>
						<text class="red fb f50">{{detail.sku[0].point_num}}</text>
						<template v-if="detail.sku[0].point_money > 0">
							<text class="red f40 gray9">+</text>
							<text class="red">¥</text>
							<text class="num red fb f40">{{detail.sku[0].point_money}}</text>
						</template>
					</view>
					<text class="old-price">¥{{detail.product.line_price}}</text>
				</view>
				<text class="already-sale">还剩余{{detail.stock}}件</text>
			</view>
			<view class="product-name">
				{{detail.product.product_name}}
			</view>
			<view class="product-describe">
				{{detail.product.selling_point}}
			</view>
		</view>
		<view class="mt20">
			<!--已选择-->
			<view class="already-choice" v-if="detail.spec_type == 20" @click="openPopup('order')">
				<view class="group-hd">
					<view class="left">
						<text class="min-name gray9">选择</text>
					</view>
					<view class="flex-1 p-0-20 center-content f28 text-ellipsis o-h">
						{{alreadyChioce}}
					</view>
					<view class="right">
						<text class="icon iconfont icon-jiantou"></text>
					</view>
				</view>
			</view>
			<!-- 保障 -->
			<view class="already-choice" @click="showGuarantee" v-if="detail.server!=''">
				<view class="group-hd">
					<view class="left">
						<text class="min-name gray9">保障</text>
					</view>
					<view class="flex-1 p-0-20 center-content f28 text-ellipsis o-h">
						{{serverList}}
					</view>
					<view class="right">
						<text class="icon iconfont icon-jiantou"></text>
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
				<view class="shop_list_body_item_shop_info">
					<view class="h1 title">{{shop_info.name}}</view>
					<view class="f26 brand">主营品牌： {{shop_info.category_name}}</view>
					<view class="f26 sales">销量：{{shop_info.product_sales}}件</view>
				</view>
				<view class="shop_list_body_item_shop_others">
					<button type="default" @click="goto_shop()">进店看看</button>
					<view class="f26 collect">店铺评分：<text>{{shop_info.server_score}}</text></view>
				</view>
			</view>
		</view>
		<!--详情内容-->
		<view class="product-content">
			<view class="group-hd border-b-e">
				<view class="left"><text class="min-name">商品介绍</text></view>
			</view>
			<view class="content-box p30" v-html="detail.product.content"></view>
		</view>

		<!--底部按钮-->
		<view class="btns-wrap">
			<view class="customer-service d-c-c">
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
		<spec :isPopup="isPopup" :productModel="productModel" @close="closePopup"></spec>

		<!--客服-->
		<Mpservice v-if="isMpservice" :isMpservice="isMpservice" @close="closeMpservice"></Mpservice>

		<!-- 保障弹窗 -->
		<guarantee :isguarantee="isguarantee" :server="detail.server" @close="closeGuarantee"></guarantee>
	</view>
</template>

<script>
	import Spec from './popup/Spec.vue';
	import Mpservice from '@/components/mpservice/Mpservice.vue';
	import utils from '@/common/utils.js';
	import guarantee from '@/components/guarantee.vue';
	export default {
		components: {
			/*选择规格*/
			Spec,
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
				/*积分商品id*/
				point_product_id: null,
				/*产品图片展示*/
				imagList: [],
				alreadyChioce: '',
				/*详情*/
				detail: {
					/*商品规格*/
					product_sku: {},
					/*当前规格对象*/
					show_sku: {
						/*商品价格*/
						product_price: '',
						/*商品规格ID*/
						product_sku_id: 0,
						/*划线价*/
						line_price: '',
						/*库存*/
						stock_num: 0,
						/*图片*/
						sku_image: ''
					},
					show_point_sku: {}
				},
				/*是否确定购买弹窗*/
				isPopup: false,
				/*商品属性*/
				specData: null,
				/*子级页面传参*/
				productModel: {},
				/*商品规格*/
				productSku: [],
				/*是否打开客服*/
				isMpservice: false,
				serverList: '', //保障
				isguarantee: false,
				shop_info: {},
				store_open: 1,
			};
		},
		onLoad(e) {

			/*分类id*/
			this.point_product_id = e.point_product_id;
		},
		mounted() {
			/*获取产品详情*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				let point_product_id = self.point_product_id;
				uni.showLoading({
					title: '加载中'
				});
				self._get(
					'plus.points.product/detail', {
						point_product_id: point_product_id
					},
					function(res) {
						/*详情内容格式化*/
						res.data.detail.product.content = utils.format_content(res.data.detail.product.content);

						/*初始化商品多规格*/
						if (res.data.detail.product.spec_type == 20) {
							self.initSpecData(res.data.detail.sku, res.data.specData);
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
				}
				this.productModel = obj;
				this.isPopup = true;
			},

			/*关闭弹窗*/
			closePopup(e, cart_total_num) {
				this.isPopup = false;
				console.log(e)
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
			//跳转店铺首页
			goto_shop() {
				let self = this;
				self.gotoPage('/pages/shop/shop?shop_supplier_id=' + self.detail.supplier.shop_supplier_id);
			},
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
		padding: 20rpx 20rpx 0;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.product-detail .price-wrap .old-price {
		padding: 10rpx 0;
		font-size: 30rpx;
		color: #888888;
		text-decoration: line-through;
	}

	.product-detail .already-sale {
		font-size: 24rpx;
		color: #999999;
	}

	.product-detail .product-name {
		padding: 10rpx 20rpx 0;
		font-size: 36rpx;
		font-weight: bold;
		color: #333333;
	}

	.product-detail .product-describe {
		padding: 20rpx;
		line-height: 40rpx;
		font-size: 26rpx;
		color: red;
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
		height: 90rpx;
		right: 0;
		bottom: 0;
		left: 0;
		display: flex;
		background: #ffffff;
	}

	.btns-wrap .icon-box {
		width: 90rpx;
		height: 90rpx;
		border-right: 1px solid #dddddd;
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
		height: 91rpx;
		line-height: 90rpx;
		margin: 0;
		padding: 0;
		flex: 1;
		border-radius: 0;
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

	.already-choice {
		margin-top: 20rpx;
		padding: 0 30rpx;
		background: #ffffff;
	}

	.already-choice .center-content {
		line-height: 90rpx;
	}

	.shop_head_info {
		width: 100%;
		margin: 0 auto;
		padding: 5%;
		box-sizing: border-box;
		background-color: white;
	}

	.shop_list_body_item_shop {
		width: 100%;
		height: 120rpx;
		display: flex;
		justify-content: space-between;
	}

	.shop_list_body_item_shop_logo {
		width: 120rpx;
		height: 100%;
	}

	.shop_list_body_item_shop_logo image {
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.1);
		border-radius: 15rpx;
	}

	.shop_list_body_item_shop_info {
		padding: 10rpx;
		box-sizing: border-box;
		margin-left: -10%;
		padding-top: 0;
		display: flex;
		justify-content: space-between;
		flex-direction: column;
	}

	.shop_list_body_item_shop_others {
		padding: 10rpx;
		box-sizing: border-box;
		display: flex;
		justify-content: space-between;
		flex-direction: column;
		text-align: right;
		padding-top: 0;
	}

	.shop_list_body_item_shop_others button {
		width: 140rpx;
		height: 60rpx;
		line-height: 60rpx;
		padding: 0;
		background-image: linear-gradient(90deg, #F11E0B, #F77737);
		color: white;
	}
</style>
