<template>
	<view class="order-datail pb100" v-if="!loadding">
		<!--详情状态-->
		<view class="order-state d-s-c">
			<view class="icon-box">
				<span v-if="detail.state_text == '已付款，待发货'" class="icon iconfont icon-icon"></span>
				<span v-if="detail.state_text == '待付款'" class="icon iconfont icon-icon"></span>
				<span v-if="detail.state_text == '已发货，待收货'" class="icon iconfont icon-daishouhuo"></span>
				<span v-if="detail.state_text == '已完成'" class="icon iconfont icon-xuanze"></span>
				<span v-if="detail.state_text == '已取消'" class="icon iconfont icon-gantanhao"></span>
			</view>
			<view class="state-cont flex-1">
				<view class="state-txt d-b-c">
					<text class="desc f34">{{ detail.state_text }}</text>
				</view>
				<view class="status-price pt10" v-if="detail.pay_status.value == 10">应付金额：¥ {{ detail.pay_price }}
				</view>
				<view class="countdown-datetime" v-if="detail.pay_end_time">
					<text>剩{{detail.pay_end_time}}自动关闭</text>
				</view>
			</view>
			<view class="dot-bg"></view>
		</view>

		<!--物流地址-->
		<view class="order-express p30 d-s-c" v-if="detail.delivery_type.value == 10">
			<view class="icon-box">
				<image style="width: 42rpx;height: 42rpx;" src="../../static/icon/address_icon.png" mode=""></image>
			</view>
			<view class="cont-text flex-1 o-h ml20 f30">
				<view class="express-text f32">{{ detail.address.name }}<text class="f26 gray9">
						{{ detail.address.phone }}</text></view>
				<view class="gray3 f26 pt10">
					{{ detail.address.region.province }}{{ detail.address.region.city }}{{ detail.address.region.region }}{{ detail.address.detail }}
				</view>
			</view>
			<view class="icon iconfont icon-jiantou"></view>
		</view>
		<!-- 上门自提：自提门店 -->
		<view class="order-express p30 d-s-s" v-if="detail.delivery_type.value == 20">
			<view class="flow-delivery__title m-top20"><span class="icon iconfont icon-dizhi1">自提门店</span></view>
			<view class="cont-text flex-1 o-h ml20 f30">
				<view class="express-text">
					{{ extractStore.store_name }} {{ extractStore.phone }}
					<view class="f24 gray9 pt10">
						{{ extractStore.region.province }} {{ extractStore.region.city }}
						{{ extractStore.region.region }} {{ extractStore.address }}
					</view>
				</view>
			</view>
		</view>

		<!-- 物流信息 -->
		<view class="group bg-white" v-if="detail.delivery_type.value == 10 && detail.delivery_status.value == 20"
			@click="gotoExpress(detail.order_id)">
			<view class="d-b-c">
				<view class="f28">
					<view class="p-20-0">
						<text class="gray9">物流公司：</text>
						<text>{{ detail.express.express_name }}</text>
					</view>
					<view class="p-20-0">
						<text class="gray9">物流单号：</text>
						<text>{{ detail.express_no }}</text>
					</view>
				</view>
				<view><text class="icon iconfont icon-jiantou"></text></view>
			</view>
		</view>

		<!--购物列表-->
		<view class="shops group bg-white">
			<view class="group-hd border-b-e"
				@tap="gotoPage('/pages/shop/shop?shop_supplier_id='+detail.supplier.shop_supplier_id)">
				<view class="left ">
					<i class="icon iconfont icon-dianpu1"></i>
					<text class="min-name">{{detail.supplier.name}}</text>
					<text class="icon iconfont icon-jiantou"></text>
				</view>
			</view>
			<view class="list">
				<view class="one-product p-20-0" v-for="(item, index) in detail.product" :key="index">
					<view class="d-f">
						<view class="cover">
							<image :src="item.image.file_path" mode="aspectFit"></image>
						</view>
						<view class="info flex-1 p-0-20">
							<view class="d-b-s">
								<view class="flex-1">
									<view class="title f28 gray3">{{ item.product_name }}</view>
									<view class="describe mt10 f24" v-if="item.spec_type==20">{{ item.product_attr }}
									</view>
								</view>
								<view>
									<view class=" count_choose">
										<view class="price">
											¥
											<text class="num">{{ item.product_price }}</text>
										</view>
										<view class="num-wrap">
											<view class="f22 tr">×{{ item.total_num }}</view>
										</view>
									</view>
								</view>
							</view>
						</view>
					</view>
					<view class="mt10 tr f28" v-if="item.is_user_grade==true">
						<text class="redF6 f26">会员折扣价：</text>
						<text class="redF6 f32">{{item.grade_product_price}}</text>
					</view>
					<view class="pt10 d-e-c">
						<!-- 申请售后 -->
						<view class="m-top20 dis-flex flex-x-end">
							<text v-if="item.refund">已申请售后</text>
							<view v-else-if="detail.isAllowRefund" @click="onApplyRefund(item.order_product_id)"><button
									type="default">申请售后</button></view>
						</view>
					</view>
				</view>
			</view>
		</view>

		<!--订单信息-->
		<view class="group bg-white f26">
			<view class="p-20-0">
				<text class="">订单编号：</text>
				<text>{{ detail.order_no }}</text>
			</view>
			<view class="p-20-0">
				<text class="">下单时间：</text>
				<text>{{ detail.create_time }}</text>
			</view>
			<view class="p-20-0">
				<text class="">支付方式：</text>
				<text>{{ detail.pay_type.text }}</text>
			</view>
			<view class="p-20-0">
				<text class="">配送方式：</text>
				<text>{{ detail.delivery_type.text }}</text>
			</view>
			<view class="p-20-0"
				v-if="detail.delivery_type.value==30 && detail.order_status.value==30 && detail.virtual_content !=''">
				<text class="">发货信息：</text>
				<text>{{ detail.virtual_content }}</text>
			</view>
			<view class="p-20-0">
				<text class="">备注：</text>
				<text>{{ detail.buyer_remark }}</text>
			</view>
			<view class="p-20-0" v-if="detail.order_status.value==20 && detail.cancel_remark !=''">
				<text class="">商家备注：</text>
				<text>{{ detail.cancel_remark }}</text>
			</view>
			<view v-if="service_open&&detail.supplier.user_id!=user_id" class="p-20-0 kefu" @click="tochat">
				<text class="icon iconfont icon-kefu2"></text>
				<text class="">联系卖家</text>
			</view>
		</view>

		<view class="group bg-white f26">
			<view class="p-20-0 d-b-c">
				<text class="">商品总价</text>
				<text>¥ {{ detail.total_price }}</text>
			</view>
			<view class="p-20-0 d-b-c">
				<text class="">运费</text>
				<text>¥ {{ detail.express_price }}</text>
			</view>
			<view class="p-20-0 d-b-c" v-if="detail.points_money>0">
				<text class="">积分抵扣金额：</text>
				<text class="redF6">- ¥{{ detail.points_money }}</text>
			</view>
			<view class="p-20-0 d-b-c" v-if=" detail.coupon_money>0">
				<text class="">店铺优惠券</text>
				<text class="redF6">- ¥ {{ detail.coupon_money }}</text>
			</view>
			<view class="p-20-0 d-b-c" v-if=" detail.coupon_money_sys>0">
				<text class="">平台优惠券</text>
				<text class="redF6">- ¥ {{ detail.coupon_money_sys }}</text>
			</view>
			<view class="p-20-0 d-b-c" v-if="detail.fullreduce_money>0">
				<text class="">满减金额</text>
				<text class="redF6">- ¥ {{ detail.fullreduce_money }}</text>
			</view>
			<view class="p-20-0 d-e-c fb f34">
				实付款：
				<text class="redF6">¥ {{ detail.pay_price }}</text>
			</view>
		</view>

		<!-- 操作栏 -->
		<view v-if="detail.order_status.value != 20&&detail.order_status.value != 30 && source=='user'"
			class="foot-btns">
			<!-- 取消订单 -->
			<button type="default" v-if="detail.pay_status.value == 10"
				@click="cancelOrder(detail.order_id)">取消订单</button>

			<block v-if="detail.order_status.value != 21">
				<block v-if="detail.pay_status.value == 20 && detail.delivery_status.value == 10">
					<button @click="cancelOrder(detail.order_id)" type="default">申请取消</button>
				</block>
			</block>
			<text v-else class="count f28 gray9">取消申请中</text>
			<block v-if="detail.pay_status.value == 10">
				<!-- 订单付款 -->
				<button @click="onPayOrder(detail.order_id)" type="primary" v-if="detail.pay_status.value == 10"
					class="ml10 btn-red">去支付</button>
			</block>
			<!-- 确认收货 -->
			<block v-if="detail.delivery_status.value == 20 && detail.receipt_status.value == 10">
				<button type="default" @click="orderReceipt(detail.order_id)">确认收货</button>
			</block>
		</view>

		<!--支付选择-->
		<Popup :show="isPayPopup" msg="支付方式" @hidePopup="hidePopupFunc">
			<!--支付方式-->
			<view class="buy-checkout ww100">
				<view :class="pay_type == 20 ? 'item active border-b-e' : 'item border-b-e'" @click="payTypeFunc(20)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-weixin"></span></view>
						<text class="key">微信支付</text>
					</view>
					<view class="icon-box d-c-c"></view>
				</view>
				<view v-if="showAlipay" :class="pay_type == 30 ? 'item active border-b-e' : 'item border-b-e'"
					@click="payTypeFunc(30)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-zhifubao"></span></view>
						<text class="key">支付宝支付</text>
					</view>
					<view class="icon-box d-c-c"></view>
				</view>
				<view :class="pay_type == 10 ? 'item active' : 'item'" @click="payTypeFunc(10)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-yue"></span></view>
						<text class="key">余额支付</text>
					</view>
					<view class="icon-box d-c-c"></view>
				</view>
			</view>
			<view class="bts"></view>
		</Popup>
		<Mpservice v-if="isMpservice" :isMpservice="isMpservice" :shopSupplierId="detail.supplier.shop_supplier_id"
			@close="closeMpservice"></Mpservice>
	</view>
</template>

<script>
	import Popup from '@/components/uni-popup.vue';
	import {
		pay
	} from '@/common/pay.js';
	import Mpservice from '@/components/mpservice/Mpservice.vue';
	export default {
		data() {
			return {
				/*是否加载完成*/
				loadding: true,
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				/*是否显示支付类别弹窗*/
				isPayPopup: false,
				/*订单id*/
				order_id: 0,
				/*订单详情*/
				detail: {
					order_status: [],
					address: {
						region: []
					},
					product: [],
					pay_type: [],
					delivery_type: [],
					pay_status: []
				},
				extractStore: {},
				source: 'user',
				service_open: 0,
				service_type: 10,
				isMpservice: false,
				/*是否显示支付宝支付，只有在h5，非微信内打开才显示*/
				showAlipay: false,
				user_id:0
			};
		},
		components: {
			Popup,
			Mpservice,
		},
		onLoad(e) {
			this.order_id = e.order_id;
			this.user_id=uni.getStorageSync('user_id');
			if (e.source) {
				this.source = e.source;
			}
		},
		mounted() {
			uni.showLoading({
				title: '加载中'
			});
			/*获取订单详情*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				let order_id = self.order_id;
				let url = 'user.order/detail';
				if(this.source == 'supplier'){
					url = 'supplier.order/detail';
				}
				self._get(url, {
						order_id: order_id,
						pay_source: self.getPlatform()
					},
					function(res) {
						self.detail = res.data.order;
						self.extractStore = res.data.order.extractStore;
						self.service_open = res.data.setting.service_open;
						if (res.data.setting.mp_service == null) {
							self.service_type = 10;
						} else {
							self.service_type = res.data.setting.mp_service.service_type;
						}
						if (res.data.show_alipay) {
							self.showAlipay = true;
						}
						self.loadding = false;
						uni.hideLoading();
					}
				);
			},
			/*显示支付方式*/
			hidePopupFunc() {
				this.isPayPopup = false;
			},

			/*取消订单*/
			cancelOrder(e) {
				let self = this;
				let order_id = e;
				wx.showModal({
					title: '提示',
					content: '您确定要取消当前订单吗?',
					success: function(o) {
						if (o.confirm) {
							uni.showLoading({
								title: '正在处理'
							});
							self._get(
								'user.order/cancel', {
									order_id: order_id
								},
								function(res) {
									uni.hideLoading();
									uni.showToast({
										title: '操作成功',
										duration: 2000,
										icon: 'success'
									});
									self.getData();
								}
							);
						}
					}
				});
			},

			/*确认收货*/
			orderReceipt(order_id) {
				let self = this;
				wx.showModal({
					title: '提示',
					content: '您确定要收货吗?',
					success: function(o) {
						if (o.confirm) {
							uni.showLoading({
								title: '正在处理'
							});
							self._post(
								'user.order/receipt', {
									order_id: order_id
								},
								function(res) {
									uni.hideLoading();
									uni.showToast({
										title: res.msg,
										duration: 2000,
										icon: 'success'
									});
									self.getData();
								}
							);
						}
					}
				});
			},
			/*查看物流*/
			gotoExpress(order_id) {
				this.gotoPage('/pages/order/express/express?order_id=' + order_id);
			},
			/*申请售后*/
			onApplyRefund(e) {
				this.gotoPage('/pages/order/refund/apply/apply?order_product_id=' + e);
			},

			/*去支付*/
			payTypeFunc(payType) {
				let self = this;
				let order_id = self.order_id;
				self.isPayPopup = false;
				uni.showLoading({
					title: '加载中'
				});
				self._post(
					'user.order/pay', {
						payType: payType,
						order_id: order_id,
						pay_source: self.getPlatform()
					},
					function(res) {
						uni.hideLoading();
						pay(res, self);
					}
				);
			},

			/*支付方式选择*/
			onPayOrder(orderId) {
				this.isPayPopup = true;
				this.order_id = orderId;
			},
			gotoProduct(item) {
				this.gotoPage('/pages/product/detail/detail?product_id=' + item.product_id);
			},
			tochat() {
				if (this.service_type == 10) {
					this.isMpservice = true;
				}
				if (this.service_type == 20) {
					this.gotoPage('/pages/plus/chat/chat?user_id=' + this.detail.supplier.supplier_user_id +
							'&shop_supplier_id=' + this.detail.supplier
							.shop_supplier_id + "&nickName=" + this.detail.supplier.name + '&order_id=' + this
							.order_id);
				}
			}
		}
	};
</script>

<style scoped>
	page {}

	.order-express {
		background: #ffffff;
		margin: 0 20rpx;
		border-radius: 12rpx;
		margin-top: 20rpx;
	}

	.order-express .icon-box .iconfont {
		font-size: 50rpx;
	}

	.order-datail {
		padding-bottom: 90 rpx;
		background-color: #F2F2F2;
	}

	.order-datail .fight-users {
		margin: 0 auto;
	}

	.order-datail .fight-users .user-box {
		width: 80rpx;
		height: 80rpx;
		margin: 10rpx;
		border-radius: 50%;
		border: 1px dashed #cccccc;
	}

	.order-datail .fight-users {
		padding: 30rpx;
	}

	.order-datail .fight-users .user-box image {
		width: 80rpx;
		height: 80rpx;
		border-radius: 50%;
	}

	.order-datail .fight-users .user-box .leader {
		position: absolute;
		top: -20rpx;
		left: 50%;
		margin-left: -30rpx;
		width: 60rpx;
		height: 30rpx;
		line-height: 30rpx;
		text-align: center;
		color: #ffffff;
		border-radius: 30rpx;
		border: 1px solid #ffffff;
		background: red;
	}

	.order-datail .fight-users .user-box.user-who {
		font-size: 50rpx;
		color: #999999;
	}

	.state-cont .countdown-datetime {
		margin-top: 16rpx;
	}

	.state-cont .countdown-datetime text {
		padding: 4rpx 8rpx;
		border-radius: 4rpx;
		background: rgba(0, 0, 0, .4);
	}

	.icon-dianpu1 {
		margin-right: 15rpx;
	}

	.kefu {
		border-top: 1rpx solid #cacaca;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.kefu .icon-kefu2 {
		color: #1296db;
		margin-right: 8rpx;
	}

	.group {
		margin: 0 20rpx;
		margin-top: 20rpx;
		border-radius: 12rpx;
	}

	.foot-btns button {
		background: linear-gradient(90deg, #FF6B6B 4%, #F6220C 100%);
		color: #FFFFFF;
		height: 60rpx;
		line-height: 60rpx;
		border-radius: 30rpx;
	}
</style>
