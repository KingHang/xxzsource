<template>
	<view class="" v-if="!loadding">
        <view style="position: relative">
            <image src="/static/images/group/detail-bg.png" style="width: 100%" mode="widthFix"></image>
            <view style="padding: 20rpx;position: absolute;top: 30rpx;left: 0;width: 100%">
                <view class="fsc">
                    <view @click="goback">
                        <u-icon name="arrow-left" color="white"></u-icon>
                    </view>
                    <view style="color: white;font-size: 34rpx">
                        限时拼团
                    </view>
                    <view>
                    </view>
                </view>

                <view style="margin-left: 30rpx">
                    <view style="color: white;font-size: 40rpx;margin-top: 30rpx">
                        {{detail.state_text}}
                    </view>
                    <view v-if="detail.assemble_status==10" style="color: white;font-size: 24rpx;margin-top: 10rpx">
                        剩余<u-count-down separator="zh" color="#FFFFFF" bgColor="rgba(255,255,255,0.01)" separator-color="#FFFFFF" font-size="28" separator-size="28" height="28" :timestamp="detail.billuser.bill.countdown"></u-count-down>
                    </view>
                </view>
            </view>
            <view class="hlbblock30" style="position: absolute;top: 290rpx;left: 30rpx">
                <view style="display: flex;justify-content: space-between">
                    <view>
                        <image src="/static/images/order/pay/goods.png" style="width: 40rpx" mode="widthFix"></image>
                        <text style="font-weight: bold;margin-left: 20rpx">
                            {{detail.state_text}}
                        </text>
                    </view>
                    <view>
                        <image src="/static/images/order/arrow.png" style="width: 30rpx" mode="widthFix"></image>
                    </view>
                </view>


                <block v-if="detail.delivery_type.value == 10">
                    <view  style="display: flex;align-items: center">
                        <image src="/static/images/order/pay/location.png" style="width: 45rpx" mode="widthFix"></image>
                        <view style="font-weight: bold;margin-left: 20rpx">
                            {{detail.address.name}}
                        </view>
                        <view style="font-size: 27rpx;color: #999999;margin-left: 20rpx">
                            {{detail.address.phone}}
                        </view>
                    </view>
                    <view style="font-size: 25rpx;color: #999999;margin-left: 55rpx">
                        {{ detail.address.region.province }}{{ detail.address.region.city }}{{ detail.address.region.region }}{{ detail.address.detail }}
                    </view>
                    <view  @click="gotoExpress(detail.order_id)" class="fui-list"  v-if="detail.delivery_type.value == 10 && detail.delivery_status.value == 20">
                        <view class="fui-list-media">
                            <view class="fui-list-icon">
                                <text class="icox icox-icon049"></text>
                            </view>
                        </view>
                        <view class="fui-list-inner">
                            <view class="text">{{detail.express.express_name}}</view>
                            <view class="text">{{detail.express_no}}</view>
                        </view>

                    </view>
                </block>

                <block v-if="detail.delivery_type.value == 20">
                    <block >
                        <view  style="display: flex;align-items: center">
                            <image src="/static/images/order/store.png" style="width: 45rpx" mode="widthFix"></image>

                            <view style="font-weight: bold;margin-left: 20rpx">
                                {{extractStore.store_name}}
                            </view>
                            <view style="font-size: 27rpx;color: #999999;margin-left: 20rpx">
                                {{extractStore.phone}}
                            </view>
                        </view>
                        <view style="font-size: 25rpx;color: #999999;margin-left: 55rpx">
                            {{ extractStore.region.province }} {{ extractStore.region.city }}
                            {{ extractStore.region.region }} {{ extractStore.address }}
                        </view>
                    </block>
                </block>
            </view>

        </view>





        <view class="hlbblock"  style="padding: 30rpx;margin-top: 180rpx">
            <view style="display: flex;align-items: center" class="" hoverClass="none" >
                <image src="/static/images/cart/shop.png" style="width: 40rpx" mode="widthFix"></image>
                <view style="color: #999999;font-size: 26rpx;margin-left: 15rpx">{{detail.supplier.name}}</view>
            </view>
            <block v-for="(item, index) in detail.product">
                <view style="padding-top: 25rpx"  >
                    <view style="width: 100%;display: flex">
                        <view  style="width: 40%">
                            <image :src="item.image.file_path" style="width: 200rpx;height: 190rpx;border-radius: 15rpx"  mode="aspectFill"></image>
                        </view>
                        <view style="width: 65%;margin-left: 10rpx">
                            <view style="height: 130rpx">
                                <view class="linedot">
                                    {{item.product_name}}
                                </view>
                                <view v-if="item.spec_type==20"  style="font-size: 25rpx;color: #999999">
                                    {{item.product_attr}}
                                </view>
                            </view>
                            <view style="display: flex;justify-content: space-between;height: 55rpx;align-items: baseline">
                                <view style="color: #F63E36">

                                    <text style="font-size: 20rpx">¥</text>
                                    <text style="font-size: 35rpx">
                                        {{item.is_user_grade?item.grade_product_price:item.product_price}}
                                    </text>
                                </view>
                                <view style="font-size: 30rpx">
                                    x{{item.total_num}}
                                </view>
                            </view>

                        </view>
                    </view>
                    <view style="padding: 20rpx 0;margin: 20rpx 0;border-top: 1px #F2F2F2 solid;border-bottom: 1px #F2F2F2 solid">
                        <view class="fsc">
                            <view style="color: #999999">
                                商品总价
                            </view>
                            <view style="color: #999999;font-size: 24rpx">
                                ¥ {{detail.total_price}}
                            </view>
                        </view>
                        <view class="fsc">
                            <view style="color: #999999">
                                运费
                            </view>
                            <view style="color: #999999;font-size: 24rpx">
                                ¥ {{detail.express_price}}
                            </view>
                        </view>
                    </view>
                    <view style="display: flex;justify-content: flex-end">
                        <view>
                            <text style="margin-right: 20rpx">
                                实付费:
                            </text>

                            <text style="color: #F63E36;font-size: 24rpx">
                                ¥
                            </text>
                            <text style="color: #F63E36;font-size: 40rpx">
                                {{detail.pay_price}}
                            </text>
                        </view>

                    </view>

                </view>
                <view class="text-right" style="color:#666">
                    <text class="refund-btn block btn btn-sm btn-default-o" v-if="item.refund">已申请售后</text>
                    <text class="hlbbuttonempty" style="padding:8rpx 10rpx" v-else-if="detail.isAllowRefund" @click="onApplyRefund(item.order_product_id)">申请售后</text>
                </view>
            </block>

        </view>

        <navigator :url="'/pages/plugin/assemble/fight-group-detail/fight-group-detail?assemble_bill_id='+detail.product[0].bill_source_id" class="hlbblock" >
            <navigator :url="'groupdetail?id='+order.id" style="padding: 20rpx;display: flex;justify-content: space-between">
                <view>
                    拼团进度
                </view>
                <view style="display: flex;align-items: center">
                    <block v-if="detail.teams.length>0">
                        <view  style="color: #F63E36;font-size: 20rpx">
                            <block  v-for="(item, index) in detail.teams">
                                <image v-if="index<=2" :src="item.avatar" style="width: 40rpx;height: 40rpx;border-radius: 20rpx;margin: 5rpx"></image>
                            </block>
                            <text style="margin: 5rpx">
                                ...
                            </text>

                        </view>
                        <view style="width: 36rpx;height: 36rpx;border-radius: 18rpx; background-color: #fee8e7;color:#F63E36;text-align: center;font-size: 22rpx">
                            {{data.teams.length}}
                        </view>
                    </block>

                    <u-icon name="arrow-right" size="30" color="#959495" style="margin-left: 10rpx"></u-icon>
                </view>

            </navigator>

        </navigator>




        <view class="fui-cell-group price-cell-group hlbblock">

            <view class="fui-cell" v-if="detail.points_money>0">
                <view class="fui-cell-label">积分抵扣金额</view>
                <view class="fui-cell-info"></view>
                <view class="fui-cell-remark noremark">-¥ {{detail.points_money}}</view>
            </view>
            <view class="fui-cell" v-if="detail.coupon_money>0">
                <view class="fui-cell-label" style="width:auto;">优惠券优惠</view>
                <view class="fui-cell-info"></view>
                <view class="fui-cell-remark noremark">-¥ {{detail.coupon_money}}</view>
            </view>
            <view class="fui-cell" v-if="detail.coupon_money_sys>0">
                <view class="fui-cell-label" style="width:auto;">平台优惠券</view>
                <view class="fui-cell-info"></view>
                <view class="fui-cell-remark noremark">-¥ {{detail.coupon_money_sys}}</view>
            </view>

            <view class="fui-cell" v-if="detail.fullreduce_money>0">
                <view class="fui-cell-label">满减金额</view>
                <view class="fui-cell-info"></view>
                <view class="fui-cell-remark noremark">-¥ {{detail.fullreduce_money}}</view>
            </view>
        </view>

        <view class="fui-cell-group hlbblock30">
            <view class="order-info">
                <view class="fui-cell-label" style="font-size:24rpx;color:#999">
                    <text class="fui-cell-label" selectable="true">订单编号：{{detail.order_no}}</text>
                </view>
                <view class="fui-cell-label" style="font-size:24rpx;color:#999">
                    <text class="fui-cell-label" selectable="true">下单时间：{{detail.create_time}}</text>
                </view>
                <view class="fui-cell-label" style="font-size:24rpx;color:#999" >
                    <text class="fui-cell-label" selectable="true">支付方式：{{detail.pay_type.text}}</text>
                </view>
                <view class="fui-cell-label" style="font-size:24rpx;color:#999" >
                    <text class="fui-cell-label" selectable="true">配送方式：{{detail.delivery_type.text}}</text>
                </view>
                <view class="fui-cell-label" style="font-size:24rpx;color:#999" v-if="detail.delivery_type.value==30 && detail.order_status.value==30 && detail.virtual_content !=''">
                    <text class="fui-cell-label" selectable="true">发货信息：{{detail.virtual_content}}</text>
                </view>
                <view class="fui-cell-label" style="font-size:24rpx;color:#999" >
                    <text class="fui-cell-label" selectable="true">备注：{{detail.buyer_remark}}</text>
                </view>
                <view class="fui-cell-label" style="font-size:24rpx;color:#999" v-if="detail.order_status.value==20 && detail.cancel_remark !=''">
                    <text class="fui-cell-label" selectable="true">商家备注：{{detail.cancel_remark}}</text>
                </view>
            </view>
        <!--   <view class="fui-cell">
               <view class="fui-cell-label" style="width:auto;">实付费</view>
               <view class="fui-cell-info"></view>
               <view class="fui-cell-remark noremark">
                   <text class="text-danger">
                       <text style="font-size:30rpx">¥ {{detail.pay_price}}</text>
                   </text>
               </view>
           </view>-->

        </view>




        <view style="height: 200rpx">

        </view>

		<!-- 操作栏 -->
		<view v-if="detail.order_status.value != 20&&detail.order_status.value != 30 && source=='user'"
			class="foot-btns">
			<!-- 取消订单 -->
			<view class="hlbbuttonempty"  v-if="detail.pay_status.value == 10"
				@click="cancelOrder(detail.order_id)">取消订单</view>



			<block v-if="detail.order_status.value != 21">
				<block v-if="detail.pay_status.value == 20 && detail.delivery_status.value == 10">
					<view class="hlbbuttonempty" @click="cancelOrder(detail.order_id)" >申请取消</view>


                    <navigator class="hlbbutton"   :url="'/pages/plugin/assemble/fight-group-detail/fight-group-detail?assemble_bill_id='+detail.product[0].bill_source_id"
                        >邀请好友</navigator>


				</block>
			</block>
			<text v-else class="count f28 gray9">取消申请中</text>
			<block v-if="detail.pay_status.value == 10">
				<!-- 订单付款 -->
				<view class="hlbbutton" @click="onPayOrder(detail.order_id)"  v-if="detail.pay_status.value == 10"
					>去支付</view>
			</block>
			<!-- 确认收货 -->
			<block v-if="detail.delivery_status.value == 20 && detail.receipt_status.value == 10">
				<view class="hlbbutton" type="default" @click="orderReceipt(detail.order_id)">确认收货</view>
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
				user_id:0,
                sgp:[],

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

            goback() {
                uni.navigateBack()
            },

			/*获取数据*/
			getData() {
				let self = this;
				let order_id = self.order_id;
				let url = 'user.order/detail';
				if(this.source == 'supplier'){
					url = 'purveyor.order/detail';
				}
				self._get(url, {
						order_id: order_id,
						pay_source: self.getPlatform()
					},
					function(res) {
						self.detail = res.data.order;
						
						console.log(res)
                        self.sgp  = self.detail.product[0];
						
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
            gotorefund(id) {
                this.gotoPage('/pages/order/refund/detail/detail?order_refund_id=' + id);
            },
			tochat() {
				if (this.service_type == 10) {
					this.isMpservice = true;
				}
				if (this.service_type == 20) {
					this.gotoPage('/pagesChat/chat/chat?user_id=' + this.detail.supplier.supplier_user_id +
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
    image{
        display: inline-block;
    }

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
