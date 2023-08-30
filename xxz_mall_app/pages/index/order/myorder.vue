<template>
	<view>
        <view class="top-tabbar2">
            <u-tabs :list="navList" @change="tabClick" name="text" :current="current" height="70" bg-color="#f5f5f5" active-color="#F63E36" color="#555555"></u-tabs>
        </view>
		<!--列表-->
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50"
		 @scrolltolower="scrolltolowerFunc">
			<view class="order-list">
                <view v-for="(item,index) in listData" :key="index" class="order-item hlbblock"  style="position: relative">
                    <view class="i-top">
                        <view class="time">
                            <text class="order-type">
								{{item.order_source_text}}
							</text>
                            <text style="color: #999999;font-size: 24rpx;margin-left: 10rpx">订单号：{{item.order_no}}</text>
                        </view>
                        <text class="state" >{{item.state_text}}</text>
                    </view>
                    <block v-for="(goodsItem, goodsIndex) in item.product" :key="goodsIndex">
						<block>
							<view class="goods-box-single"  @click="gotoOrder(item.order_id)">
							    <view style="position: relative;">
									<image class="goods-img" :src="goodsItem.image.file_path" mode="aspectFill"></image>
									<image v-if="goodsItem.is_gift_product == 1"style="width: 68rpx;height: 32rpx;position: absolute;top: 0;left: 0;" src="/static/images/order/gift.png" mode="aspectFit"></image>
								</view>
							    <view class="right">
							        <view class="title ">
							            <view style="width: 80%;font-weight: bold;font-size: 30rpx;text-align: left">
							                <view class="linedot">
							                    {{goodsItem.product_name}}
							                </view>
							            </view>
							            <view class="attr-box">x{{goodsItem.total_num}}</view>
							        </view>
							        <text class="price">{{goodsItem.total_price}}</text>
							    </view>
							</view>
						</block>
                    </block>
                    <block>
						<view class="d-e-c f28 mr-30 pt20" style="border-top: 1px #F2F2F2 solid">
							共<text class="redF6">{{item.product.length}}</text>件商品 
							实付款：
							<text class="redF6 fb">¥ {{ item.pay_price }}</text>
						</view>
                        <view style="display: flex;padding: 20rpx 20rpx 30rpx;justify-content: flex-end;">
							<block v-if="item.order_status.value != 20">
								<!-- 未支付取消订单 -->
								<button v-if="item.pay_status.value == 10" class="action-btn" style="border:1px solid #F63E36;" @click="cancelOrder(item.order_id)">取消订单</button>
								<!-- 已支付取消订单 -->
								<block v-if="item.order_status.value != 21">
									<block v-if="item.pay_status.value == 20 && item.delivery_status.value == 10">
										<button class="action-btn recom" @click="cancelOrder(item.order_id)">申请取消</button>
									</block>
									<!-- 订单核销码 -->
									<block v-if="item.pay_status.value == 20 && item.delivery_type.value == 20 && item.delivery_status.value == 10">
										<button class="btn-red action-btn recom" @click="onQRCode(item.order_id)">自提</button>
									</block>
								</block>
								<text v-else class="count">取消申请中</text>
								<!-- 订单付款 -->
								<block v-if="item.pay_status.value == 10">
									<button class="btn-red action-btn recom" @click="onPayOrder(item.order_id)">付款</button>
								</block>
								<!-- 确认收货 -->
								<block v-if="item.delivery_status.value == 20 && item.receipt_status.value == 10">
									<button class="action-btn recom" @click="orderReceipt(item.order_id)">确认收货</button>
								</block>
								<!-- 订单评价 -->
								<button class="btn-red action-btn recom" v-if="item.order_status.value == 30 && item.is_comment == 0" @click="gotoEvaluate(item.order_id)">评价</button>

							</block>
						</view>
                    </block>
                </view>

				<view class="d-c-c p30" v-if="listData.length == 0 && !loading">
					<text class="iconfont icon-wushuju"></text>
					<text class="cont">亲，暂无相关记录哦</text>
				</view>
				<uni-load-more v-else :loadingType="loadingType"></uni-load-more>
			</view>
		</scroll-view>

		<!--支付选择-->
		<Popup :show="isPayPopup" msg="支付方式" @hidePopup="hidePopupFunc">
			<!--支付方式-->
			<view class="buy-checkout">
				<view :class="pay_type == 20 ? 'item active border-b-e' : 'item border-b-e'" @click="payTypeFunc(20)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-weixin"></span></view>
						<text class="key">微信支付</text>
					</view>
					<view class="icon-box d-c-c"></view>
				</view>
				<view v-if="showAlipay" :class="pay_type == 30 ? 'item active border-b-e' : 'item border-b-e'" @click="payTypeFunc(30)">
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
	</view>
</template>

<script>
	import Popup from '@/components/uni-popup.vue';
	import uniLoadMore from '@/components/uni-load-more.vue';
	import {
		pay
	} from '@/common/pay.js';
	var _self;
	export default {
		components: {
			Popup,
			uniLoadMore
		},
		data() {
			return {
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				/*状态选中*/
				state_active: 0,
				/*顶部刷新*/
				topRefresh: false,
				/*数据*/
				listData: [],
				/*数据类别*/
				dataType: 'all',
				/*是否显示支付类别弹窗*/
				isPayPopup: false,
				/*支付方式*/
				pay_type: 20,
				/*订单id*/
				order_id: 0,
				/*最后一页码数*/
				last_page: 0,
				/*当前页面*/
				page: 1,
				/*每页条数*/
				list_rows: 10,
				/*有没有等多*/
				no_more: false,
				/*是否正在加载*/
				loading: true,
				/*是否显示核销二维码*/
				isCodeImg: false,
				codeImg: '',
				/*是否显示支付宝支付，只有在h5，非微信内打开才显示*/
				showAlipay: false,
				isfirst:false,
                navList: [{
                    state: 'all',
                    text: '全部',
                },
                    {
                        state: 0,
                        text: '待付款',
                    },
                    {
                        state: 'group',
                        text: '拼团中',
                    },
                    {
                        state: 1,
                        text: '待发货',
                    },
                    {
                        state: 2,
                        text: '待收货',
                    },
                    {
                        state: 3,
                        text: '待评价',
                    }
                    ,
                    {
                        state: -1,
                        text: '退换货',
                    }
                ],
                current:0
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
		onReady() {
			uni.setNavigationBarTitle({
				title: '订单'
			});
		},
		onLoad(e) {
            _self = this;
			if (typeof e.dataType != 'undefined') {
				this.dataType = e.dataType;
			}

			if (this.dataType == 'payment') {
				this.state_active = 1;
				_self.current =1;
			} else if (this.dataType == 'received') {
				this.state_active = 3;
                _self.current =4;
			} else if (this.dataType == 'comment') {
				this.state_active = 4;
                _self.current =5;
			} else if (this.dataType == 'delivery') {
				this.state_active = 2;
                _self.current =3;
			}
            else if (this.dataType == 'refund') {
                this.state_active = 5;
                _self.current =6;
            }
			uni.setNavigationBarTitle({
				title: '订单'
			})
		},
		mounted() {
			this.init();
			this.initData();
			/*获取订单列表*/
			this.getData();
		},
		onShow() {
			if(this.isfirst){
				this.initData();
				this.getData();
			}
		},
		methods: {
            tabClick(index){
                // console.log(index)
                this.current = index
                this.page=1;
                this.total=0;
                // this.getlist();

                console.log(index)
                switch (index) {
                    case 0:
                        this.listData = [];
                        this.dataType = 'all';
                        break;
                    case 1:
                        this.listData = [];
                        this.dataType = 'payment';
                        break;
                    case 2:
                        this.listData = [];
                        this.dataType = 'group';
                        break;
                    case 3:
                        this.listData = [];
                        this.dataType = 'delivery';
                        break;
                    case 4:
                        this.listData = [];
                        this.dataType = 'received';
                        break;
                    case 5:
                        this.listData = [];
                        this.dataType = 'comment';
                        break;
                    case 6:
                        this.listData = [];
                        this.dataType = 'refund';
                        break;
                }
                this.getData();
            },

            initData(){
				let self=this;
				self.page = 1;
				self.listData = [];
				self.no_more=false;
			},
			/*初始化*/
			init() {
				let self = this;
				uni.getSystemInfo({
					success(res) {
						self.phoneHeight = res.windowHeight;
						// 计算组件的高度
						let view = uni.createSelectorQuery().in(self).select('.top-tabbar2');
						view.boundingClientRect(data => {
							let h = self.phoneHeight - data.height;
							self.scrollviewHigh = h;
						}).exec();
					}
				});
			},
			/*状态切换*/
			stateFunc(e) {
				console.log(11111, e);
				let self = this;
				if (self.state_active != e) {
					self.page = 1;
					self.loading = true;
					self.state_active = e;
					switch (e) {
						case 0:
							self.listData = [];
							self.dataType = 'all';
							break;
						case 1:
							self.listData = [];
							self.dataType = 'payment';
							break;
						case 2:
							self.listData = [];
							self.dataType = 'delivery';
							break;
						case 3:
							self.listData = [];
							self.dataType = 'received';
							break;
						case 4:
							self.listData = [];
							self.dataType = 'comment';
							break;
					}
					self.getData();
				}
			},

			/*可滚动视图区域到底触发*/
			scrolltolowerFunc() {
				let self = this;
				if (self.no_more) {
					return;
				}
				self.page++;
				if (self.page <= self.last_page) {
					self.getData();
				} else {
					self.no_more = true;
				}
			},

			/*获取数据*/
			getData() {
				let self = this;
				self.loading = true;
				let dataType = self.dataType;
				
				console.log(dataType)
				self._get(
					'user.order/lists', {
						dataType: dataType,
						page: self.page,
						pay_source: self.getPlatform(),
						list_rows: self.list_rows
					},
					function(res) {
						self.loading = false;
						self.listData = self.listData.concat(res.data.list.data);
						
						console.log(self.listData)
						
						self.last_page = res.data.list.last_page;
						if (res.data.list.last_page <= 1) {
							self.no_more = true;
						} else {
							self.no_more = false;
						}
						if (res.data.show_alipay) {
							self.showAlipay = true;
						}
						self.isfirst=true;
					}
				);
			},

			/*跳转页面*/
			gotoOrder(e) {
				this.gotoPage('/pages/order/order-detail?order_id=' + e);
			},
			/*隐藏支付方式*/
			hidePopupFunc() {
				this.isPayPopup = false;
			},
			toShop(id) {
				this.gotoPage('/pagesShop/shop/shop?shop_supplier_id=' + id);
			},
			/*去支付*/
			payTypeFunc(payType) {
				let self = this;
				self.isPayPopup = false;
				uni.showLoading({
					title: '加载中'
				});
				
				uni.login({
					provider: 'weixin',
					success: function(loginRes) {
						self.code = loginRes.code;
					}
				})
				self._post(
					'user.order/pay', {
						code: self.code,
						payType: payType,
						order_id: self.order_id,
						pay_source: self.getPlatform()
					},
					function(res) {
						res.data.order_type = 10;
						pay(res, self);
					}
				);
			},

			/*支付方式选择*/
			onPayOrder(orderId) {
				let self = this;
				self.isPayPopup = true;
				self.order_id = orderId;
			},

			/*确认收货*/
			orderReceipt(order_id) {
				let self = this;
				uni.showModal({
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
									self.listData = [];
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

			/*取消订单*/
			cancelOrder(e) {
				let self = this;
				let order_id = e;
				uni.showModal({
					title: '提示',
					content: '您确定要取消吗?',
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
									self.listData = [];
									self.getData();
								}
							);
						}
					}
				});
			},

			/*去评论*/
			gotoEvaluate(e) {
				this.gotoPage('/pages/order/evaluate/evaluate?order_id=' + e);
			}
		}
	};
</script>

<style lang="scss">
	page {
		background-color: #f2f2f2;
	}

    .order-item{
        display: flex;
        flex-direction: column;
        padding-left: 30upx;
        background: #fff;
        margin-top: 30upx;
        .i-top{
            display: flex;
            align-items: center;
            height: 80upx;
            padding-right:30upx;

            position: relative;
            .time {
                flex: 1;
				.order-type {
					border-radius: 10rpx;
					background-color: #999999;
					padding: 4rpx 15rpx;
					font-size: 22rpx;
					color: #FFFFFF;
					margin-bottom: 5rpx;
				}
            }
            .state{

                font-size: 24rpx;
            }
            .del-btn{
                padding: 10upx 0 10upx 36upx;

                position: relative;
                &:after{
                    content: '';
                    width: 0;
                    height: 30upx;
                    border-left: 1px solid $border-color-dark;
                    position: absolute;
                    left: 20upx;
                    top: 50%;
                    transform: translateY(-50%);
                }
            }
        }
        /* 多条商品 */
        .goods-box{
            height: 160upx;
            padding: 20upx 0;
            white-space: nowrap;
            .goods-item{
                width: 120rpx;
                height: 120rpx;
                display: inline-block;
                margin-right: 24upx;
            }
            .goods-img{
                display: block;
                width: 100%;
                height: 100%;
            }
        }
        /* 单条商品 */
        .goods-box-single{
            display: flex;
            padding: 20upx 0;
            .goods-img{
                display: block;
                width: 200rpx;
                height: 200rpx;
                border-radius: 15rpx;
            }
            .right{
                flex: 1;
                display: flex;
                flex-direction: column;
                padding: 0 30upx 0 24upx;
                overflow: hidden;
                color: black;
                .title{
                    font-size: $font-base + 2upx;
                    height: 150rpx;
                    display: flex;
                    justify-content: space-between;
                    .attr-box{
                        font-size: 30rpx;
                        font-weight: bold;
                    }
                }
				
				.title-card {
					font-size: $font-base + 2upx;
					height: 120rpx;
					display: flex;
					justify-content: space-between;
					.attr-box{
					    font-size: 30rpx;
					    font-weight: bold;
					}
				}

                .price{
                    text-align: left;
                    font-size: $font-base + 2upx;
                    color: #F63E36;
                    &:before{
                        content: '￥';
                        margin: 0 2upx 0 8upx;
                    }
                }
				
				.price-card {
					float: left;
					font-size: $font-base + 2upx;
					color: #F63E36;
					&:before{
					    content: '￥';
					    margin: 0 2upx 0 8upx;
					}
				}
				
				.period-time {
					display: flex;
					text {
						padding: 0 10rpx;
						height: 28rpx;
						background: #F5F5F5;
						border-radius: 200rpx;
						color: #6E6E6E;
						font-size: 20rpx;
						line-height: 28rpx;
						text-align: center;
					}
				}
            }
        }

        .price-box{
            display: flex;
            justify-content: flex-end;
            align-items: baseline;
            padding: 20upx 30upx;
            font-size: $font-sm + 2upx;
            color: $font-color-light;
            .num{
                margin: 0 8upx;
                color: $font-color-dark;
            }
            .price{
                font-size: $font-lg;
                color: $font-color-dark;
                &:before{
                    content: '￥';
                    font-size: $font-sm;
                    margin: 0 2upx 0 8upx;
                }
            }
        }
        .action-box{
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 100upx;
            position: relative;
            padding-right: 30upx;
        }
        .action-btn{
            width: 160upx;
            height: 60upx;
            margin: 0;
            margin-left: 24upx;
            padding: 0;
            text-align: center;
            line-height: 60upx;
            font-size: $font-sm + 2upx;
            color: #F63E36;
            background: #fff;
            border-radius: 100px;
            /*border:1px solid #F63E36;*/
            &:after{
                border-radius: 100px;
            }
            &.recom{
                background: #F63E36;
                color: #ffffff;
                &:after{
                    border-color: #f7bcc8;
                }
            }
        }
    }

	.top-tabbar {
		height: 96rpx;
	}

	.order-list .order-head .state-text {
		padding: 10rpx 12rpx;
		margin-right: 21rpx;
		border-radius: 4rpx;
		background: #FFE7E4;
		font-size: 22rpx;
		color: #F6220C;
	}

	.shop-name {
		font-size: 26rpx;
		font-family: PingFang SC;
		font-weight: 500;
		color: #333333;
	}

	.order-list .item {
		margin-bottom: 30rpx;
		padding: 30rpx;
		background: #ffffff;
	}

	.order-list .product-list,
	.order-list .one-product {
		padding: 30rpx 0 27rpx 0;
		height: 150rpx;
	}

	.one-product .pro-info {
		padding: 0 21rpx 0 37rpx;
		display: -webkit-box;
		width: 361rpx;
		overflow: hidden;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		font-size: 26rpx;
		color: #333333;
	}

	.order-list .cover,
	.order-list .cover image {
		width: 150rpx;
		height: 150rpx;
		border-radius: 8rpx;
	}

	.order-list .total-count {
		padding-left: 20rpx;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: flex-end;
	}

	.total-count .count {
		padding-top: 16rpx;
		color: #999999;
		font-size: 22rpx;
	}

	.product-list .total-count {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		background: rgba(255, 255, 255, 0.9);
	}

	.product-list .total-count .left-shadow {
		position: absolute;
		top: 0;
		bottom: 0;
		left: -24rpx;
		width: 24rpx;
		overflow: hidden;
	}

	.product-list .total-count .left-shadow::after {
		position: absolute;
		top: 0;
		bottom: 0;
		width: 24rpx;
		right: -12rpx;
		display: block;
		content: '';
		background-image: radial-gradient(rgba(0, 0, 0, 0.2) 10%, rgba(0, 0, 0, 0.1) 40%, rgba(0, 0, 0, 0) 80%);
	}

	.order-list .order-bts {
		display: flex;
		justify-content: flex-end;
		align-items: center;
	}

	.order-list .order-bts button {
		margin: 0;
		padding: 0 30rpx;
		height: 60rpx;
		line-height: 60rpx;
		margin-left: 20rpx;
		font-size: 32rpx;
		border: 1px solid #F6220C;
		border-radius: 30px;
		background: #ffffff;
		white-space: nowrap;
		color: #F6220C;
		font-family: PingFang SC;
	}

	.order-list .order-bts button::after {
		display: none;
	}

	.order-list .order-bts button.btn-border-red {
		border: 1px solid $dominant-color;
		font-size: 24rpx;
		color: $dominant-color;
	}

	.order-list .order-bts button.btn-red {
		background: linear-gradient(90deg, #FF6B6B 4%, #F6220C 100%);
		border-radius: 30rpx;
		font-size: 32rpx;
		font-family: PingFang SC;
		color: #ffffff;
		border: none;
	}

	.buy-checkout {
		width: 100%;
	}

	.buy-checkout .item {
		min-height: 50rpx;
		line-height: 50rpx;
		padding: 20rpx;
		display: flex;
		justify-content: space-between;
		font-size: 28rpx;
	}

	.buy-checkout .iconfont.icon-weixin {
		color: #04be01;
		font-size: 50rpx;
	}

	.buy-checkout .iconfont.icon-yue {
		color: #f0de7c;
		font-size: 50rpx;
	}

	.buy-checkout .item.active .iconfont.icon-xuanze {
		color: #04be01;
	}

	.item-dianpu {
		display: flex;
		justify-content: space-between;
		align-items: center;
		font-size: 24rpx;
		line-height: 30rpx;
	}

	.item-dianpu .icon-jiantou {
		font-size: 24rpx;
		color: #333333;
	}

	.item-d-l {
		display: flex;
	}

	.icon-dianpu1 {
		margin-right: 20rpx;
		color: #333333;
		font-size: 32rpx;
	}
	
	.verify-block {
		display: flex;
		justify-content: flex-end;
		align-items: center;
		float: right;
		.verify-btn {
			display: flex;
			align-items: center;
			background: #FFFFFF;
			border: 1px solid #F63E36;
			opacity: 1;
			border-radius: 56rpx;
			color: #F63E36;
			padding: 10rpx 20rpx;
			font-size: 28rpx;
		}
	}
</style>
