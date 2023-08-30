<template>
	<view class="user-index">
		<!-- #ifdef APP-PLUS -->
		<header-bar></header-bar>
		<!-- #endif -->
		
		<!--个人信息-->
		<view v-if="!loadding">
            <view :class="detail.status!=1&&detail.user_id>0?'bandline':'disheight'" style="height: 460rpx;position: relative;overflow: hidden;">
                <view>
                    <image  src="http://img.pighack.com/202203182253329cf795124.png" style="width: 750rpx;" mode="widthFix"></image>
                </view>
                <view style="position: absolute;top: 0;right: 0;width: 100%;">
                    <view v-if="detail.isagent>0" @click="actionshare=true" style="position: absolute;top: 50rpx;right: 0;width: 145rpx;display: flex;background-color: #F63E36;padding: 5rpx 20rpx;border-bottom-left-radius: 30rpx;border-top-left-radius: 30rpx">
                        <image src="/static/images/member/share.png" mode="widthFix" style="width: 40rpx"></image>
                        <view style="color: white;margin-left: 10rpx;font-size: 26rpx">
                            店铺
                        </view>
                    </view>
                    <view style="padding: 50rpx 30rpx;display: flex;align-items: center;z-index: 900;">
                        <view style="width: 20%">
                            <image  :src="detail.avatarUrl || '/static/missing-face.png'" style="width: 100rpx;height: 100rpx;border-radius: 50rpx"></image>
                        </view>
                        <view>
                            <view style="display: flex;justify-content: space-between;align-items: center;width: 100%">
                                <block v-if="detail.user_id>0">
                                    <view @click='navTo("/pages/member/info")'>
                                        <text style="font-size: 35rpx;font-weight: bold">{{detail.nickName || '未命名'}}</text>
<!--
                                        <u-icon name="edit-pen-fill" color="#8b837d" size="33" style="margin-left: 20rpx"></u-icon>
                                        <text class="font2599" >
                                            编辑
                                        </text>-->
                                    </view>
                                </block>
                                <block v-else>
                                    <view @click='navTo("/pages/public/login")'>
                                        <text style="font-size: 35rpx;font-weight: bold">立即登入</text>
                                    </view>
                                </block>
                            </view>
                            <view style="display: flex;align-items: center">
                                <view style="display: flex;align-items: center">
                                    <image src="/static/images/member/lv.png" mode="widthFix" style="width: 40rpx"></image>
                                    <view>
                                        {{detail.grade.name || '无等级'}}
                                    </view>
                                </view>
                                <view >
                                    <block v-if="member.user_id>0">
                                        <u-tag v-if="setting.is_grow === '1'" :text="setting.growth_name+' '+detail.growth_value" style="margin:0 10rpx" color="#333333" type="success" mode="dark" shape="circle" bg-color="#f9dfd8" />
                                        <u-tag v-if="member.isagent==1" :text="'推广码  '+detail.user_id"  color="#333333" type="success" mode="dark" shape="circle" bg-color="#FFFFFF" />
                                    </block>
                                    <block v-else>
                                        <u-tag v-if="setting.is_grow === '1'" :text="setting.growth_name+' '+detail.growth_value" style="margin:0 10rpx" color="#333333" type="success" mode="dark" shape="circle" bg-color="#f9dfd8" />
                                    </block>
                                </view>
                            </view>
                        </view>
                    </view>

                    <view style="display: flex;justify-content: space-around;align-items: center;width: 90%;margin: auto">
                        <view @click="jumpPage('/pages/order/recharge')" style="width: 25%;text-align: center;position: relative">
                            <image src="/static/images/member/recharge.png" style="width: 55rpx;position: absolute;right: 30rpx;top: -20rpx" mode="widthFix"></image>
                            <view style="font-size: 35rpx;font-weight: bold">
                                {{ detail.balance }}
                            </view>
                            <view>
                                余额 <image src="/static/images/order/arrow.png" style="width: 25rpx;margin-left: 5rpx;margin-bottom: 6rpx;display: inline-block" mode="widthFix"></image>
                            </view>
                        </view>
                        <view v-if="setting.is_points === '1'" @click="jumpPage('/pages/user/points/points')" style="width: 25%;text-align: center;position: relative">
                            <image src="/static/images/member/exchange.png" style="width: 55rpx;position: absolute;right: 30rpx;top: -20rpx" mode="widthFix"></image>
                            <view style="font-size: 35rpx;font-weight: bold">
                                {{ detail.points }}
                            </view>
                            <view>
                                {{setting.points_name}} <image src="/static/images/order/arrow.png" style="width: 25rpx;margin-left: 5rpx;margin-bottom: 6rpx;display: inline-block" mode="widthFix"></image>
                            </view>
                        </view>
                        <view @click="jumpPage('/pages/user/my-coupon/my-coupon')" style="width: 25%;text-align: center;position: relative">
                            <view style="font-size: 35rpx;font-weight: bold">
                                {{ coupon }}
                            </view>
                            <view>
                                优惠券 <image src="/static/images/order/arrow.png" style="width: 25rpx;margin-left: 5rpx;margin-bottom: 6rpx;display: inline-block" mode="widthFix"></image>
                            </view>
                        </view>
                    </view>
                    <view style=" width: 92%;margin: 30rpx auto;position: relative" v-if="detail.user_id>0" @click="navTo('/pages/public/comsuccess?self=1')">
                        <image src="https://img.pighack.com/2022050310120005eed8013.png" style="width: 100%;" mode="widthFix"></image>
                        <view style="position: absolute;top: 45rpx;right: 40rpx;color: white;font-size: 22rpx" v-if="member.isagent==1">
                            审核中 <u-icon name="arrow-right" size="22"></u-icon>
                        </view>
                    </view>
                </view>
            </view>

        <!--    <u-popup  v-model="actionshare" mode="bottom" height="" border-radius="50">
                <view style="padding: 30rpx;margin-top: 50rpx">
                    <u-divider bg-color="white">分享至</u-divider>
                </view>

                <view style="display: flex;justify-content: space-around;margin-bottom: 30rpx">


                    <view v-if="showh5" @click="sharefriend" style="text-align: center">
                        <view>
                            <image src="/static/images/common/wechat.png" style="width: 80rpx" mode="widthFix"></image>
                        </view>
                        <view style="font-size: 25rpx;margin-top: 10rpx">
                            群或好友
                        </view>
                    </view>
                    <view v-if="showmp"  style="text-align: center;position: relative">
                        <button open-type="share"  style="width: 150rpx;height: 150rpx;position: absolute">

                        </button>
                        <view>
                            <image src="/static/images/common/wechat.png" style="width: 80rpx" mode="widthFix"></image>
                        </view>
                        <view style="font-size: 25rpx;margin-top: 10rpx">
                            群或好友
                        </view>
                    </view>
                    <view @click="shareline" style="text-align: center">
                        <view>
                            <image src="/static/images/common/line.png" style="width: 80rpx" mode="widthFix"></image>
                        </view>
                        <view style="font-size: 25rpx;margin-top: 10rpx">
                            朋友圈
                        </view>
                    </view>
                </view>
                <view style="width: 100%;height: 20rpx;background-color:#eeeeee ">
                </view>
                <view style="width: 100%;text-align: center;line-height: 100rpx" @click="actionshare=false">
                    取消
                </view>
            </u-popup>

            <u-mask :show="friendshow" @click="friendshow = false">
                <view class="">
                    <image src="/static/images/common/arrow.png" style="width: 120rpx;position: absolute;top: 10rpx;right: 10rpx" mode="widthFix"></image>
                    <view class='content' style="color: white;width: 100%;text-align: center;margin-top: 150rpx">请点击右上角<br/>通过【发送给朋友】<br/>邀请好友</view>
                </view>
            </u-mask>

            <u-mask :show="lineshow" @click="lineshow = false">
                <view class="" style="text-align: center">
                    <image :src="img" style="width: 65%;margin:100rpx auto 30rpx" mode="widthFix"></image>
                    <view @click="saveimg" class="hlbbutton" style="margin: auto;width: 280rpx">
                        去发朋友圈
                    </view>
                </view>
            </u-mask>-->



          <!--  <view class="user-header">
				<view class="news" @click="gotoPage('/pagesChat/chat/chat_list')">
					<image class="chat" src="../../../static/icon/chat.png" mode=""></image>
				</view>
				<view v-if="msgcount!=0" class="news_num">
					{{msgcount}}
				</view>
				<view class="user-header-inner">
					<view class="user-info">
						<view class="photo">
							<image :src="detail.avatarUrl" mode="aspectFill"></image>
						</view>
						<view class="info">
							<view class="d-c-c mb23">
								<view class="name">{{ detail.nickName }}</view>
								<text class="ml20 grade" v-if="detail.grade_id > 0">
									{{ detail.grade.name }}
								</text>
							</view>
							<view class="tel d-s-c">
								<text class="f26">ID:{{ detail.user_id }}</text>
							</view>
						</view>
					</view>
				</view>
				&lt;!&ndash;我的订单&ndash;&gt;
				<view class="my-order">
					<view class="list d-a-c flex-1">
						<view class="item d-c-c d-c" @click="jumpPage('/pages/user/my-wallet/my-wallet')">
							<view class=" red_mini">{{ detail.balance }}</view>
							<text class="pt16 f24 gray3">账号余额</text>
						</view>
						<view class="item order_center d-c-c d-c" @click="jumpPage('/pages/user/points/points')">
							<view class=" red_mini">{{ detail.points }}</view>
							<text class="pt16 f24 gray3">{{setting.points_name}}</text>
						</view>
						<view class="item d-c-c d-c" @click="jumpPage('/pages/user/my-coupon/my-coupon')">
							<view class="red_mini">{{ coupon }}</view>
							<text class="pt16 f24 gray3">优惠券</text>
						</view>
						<view v-if="setting.balance_open==1">
							<view class="item d-c-c d-c" @click="jumpPage('/pages/user/my-wallet/my-wallet')">
								<view class="icon-box"><span class="icon iconfont icon-qianbao"></span></view>
								<text>我的钱包</text>
							</view>
						</view>
					</view>
				</view>
			</view>-->
			<view class="bind_phone" v-if="!detail.mobile">
				<view class="bind_content">
					<view class="bind_txt">点击绑定手机号，确保账户安全</view>
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="getPhoneNumber" class="bind_btn" @getphonenumber="getPhoneNumber">去绑定</button>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<button class="bind_btn" @click="bindMobile">去绑定</button>
					<!-- #endif -->
				</view>
			</view>
			<!--我的资产-->

            <view class="hlbblock30" style="">
                <u-section title="我的订单" @click="jumpPage('/pages/index/order/myorder?dataType=all')" :show-line="false" color="#333333" sub-color="#999999" sub-title="查看全部"></u-section>
                <view v-if="detail.user_id>0" style="display: flex;justify-content: space-around;align-items: center;padding-top: 20rpx;border-top: 1px solid #f3f3f3;margin-top: 20rpx">
                    <view style="text-align: center;position: relative" @click="jumpPage('/pages/index/order/myorder?dataType=payment')">
                        <image src="/static/ndm/order/st0.png" mode="aspectFit" style="width: 50rpx"></image>
                        <view v-if="orderCount.payment != null && orderCount.payment>0"  style="background-color:#f42f2a;color: white;font-size: 25rpx;width: 35rpx;height:35rpx;border-radius: 50%;text-align: center;line-height: 35rpx;position: absolute;top: 0;right: -10rpx;">{{orderCount.payment}}</view>
                        <view class="lhfont">待付款</view>
                    </view>
                    <view style="text-align: center;position: relative" @click="jumpPage('/pages/index/order/myorder?dataType=delivery')">
                        <image src="/static/ndm/order/st1.png" mode="aspectFit" style="width: 50rpx"></image>
                        <view v-if="orderCount.delivery != null && orderCount.delivery>0"  style="background-color:#f42f2a;color: white;font-size: 25rpx;width: 35rpx;height:35rpx;border-radius: 50%;text-align: center;line-height: 35rpx;position: absolute;top: 0;right: -10rpx;">{{orderCount.delivery}}</view>
                        <view class="lhfont">待发货</view>
                    </view>
                    <view style="text-align: center;position: relative" @click="jumpPage('/pages/index/order/myorder?dataType=received')">
                        <image src="/static/ndm/order/st2.png" mode="aspectFit" style="width: 50rpx"></image>
                        <view v-if="orderCount.received != null && orderCount.received>0"  style="background-color:#f42f2a;color: white;font-size: 25rpx;width: 35rpx;height:35rpx;border-radius: 50%;text-align: center;line-height: 35rpx;position: absolute;top: 0;right: -10rpx;">{{orderCount.received}}</view>
                        <view class="lhfont">待收货</view>
                    </view>
                    <view style="text-align: center;position: relative" @click="jumpPage('/pages/index/order/myorder?dataType=comment')">
                        <image src="/static/ndm/order/st3.png" mode="aspectFit" style="width: 50rpx"></image>
                        <view v-if="orderCount.comment != null && orderCount.comment>0"  style="background-color:#f42f2a;color: white;font-size: 25rpx;width: 35rpx;height:35rpx;border-radius: 50%;text-align: center;line-height: 35rpx;position: absolute;top: 0;right: -10rpx;">{{orderCount.comment}}</view>
                        <view class="lhfont">待评价</view>
                    </view>
                    <view style="text-align: center;position: relative" @click="jumpPage('/pages/order/refund/index/index')">
                        <image src="/static/ndm/order/st-1.png" mode="aspectFit" style="width: 50rpx"></image>
                        <view v-if="orderCount.refund != null && orderCount.refund>0"  style="background-color:#f42f2a;color: white;font-size: 25rpx;width: 35rpx;height:35rpx;border-radius: 50%;text-align: center;line-height: 35rpx;position: absolute;top: 0;right: -10rpx;">{{orderCount.refund}}</view>
                        <view class="lhfont">退换货</view>
                    </view>

                </view>


            </view>
		<!--	<view class="my-assets">
				<view class="my-assets-all">
					<view class="f30 fb">我的订单</view>
					<view class="gray9 f26" @click="jumpPage('/pages/index/order/myorder?dataType=all')">全部订单<text class="icon iconfont icon-jiantou"></text></view>
				</view>
				<view class="d-b-c w100">
					<view class="item" @click="jumpPage('/pages/index/order/myorder?dataType=payment')">
						<view class="icon-box pr">
							<image src="../../../static/icon/icon-icon.png" mode=""></image>
							<text class="dot d-c-c" v-if="orderCount.payment != null && orderCount.payment > 0">{{ orderCount.payment }}</text>
						</view>
						<text>待付款</text>
					</view>
					<view class="item" @click="jumpPage('/pages/index/order/myorder?dataType=delivery')">
						<view class="icon-box pr">
							<image src="../../../static/icon/icon-daifahuo.png" mode=""></image>
							<text class="dot d-c-c" v-if="orderCount.delivery != null && orderCount.delivery > 0">{{ orderCount.delivery }}</text>
						</view>
						<text class="">待发货</text>
					</view>
					<view class="item" @click="jumpPage('/pages/index/order/myorder?dataType=received')">
						<view class="icon-box pr">
							<image src="../../../static/icon/icon-daishouhuo.png" mode=""></image>
							<text class="dot d-c-c" v-if="orderCount.received != null && orderCount.received > 0">{{ orderCount.received }}</text>
						</view>
						<text>待收货</text>
					</view>
					<view class="item" @click="jumpPage('/pages/index/order/myorder?dataType=comment')">
						<view class="icon-box pr">
							<image src="../../../static/icon/icon-quanbudingdan.png" mode=""></image>
							<text class="dot d-c-c" v-if="orderCount.comment != null && orderCount.comment > 0">{{ orderCount.comment }}</text>
						</view>
						<text>待评价</text>
					</view>
					<view class="item" @click="jumpPage('/pages/order/refund/index/index')">
						<view class="icon-box pr">
							<image src="../../../static/icon/icon-tuikuan.png" mode=""></image>
							<text class="dot d-c-c" v-if="orderCount.refund != null && orderCount.refund > 0">{{ orderCount.refund }}</text>
						</view>
						<text>退款/售后</text>
					</view>
				</view>
			</view>-->



            <view class="hlbblock30" >
                <u-grid :col="4" :border="false">
                    <block v-for="item in menus">
                        <u-grid-item @click="jumpPage(item.link_url)">
                            <image :src="item.image_url" mode="aspectFit" style="width: 50rpx"></image>
                            <view class="grid-text grids" style="font-size: 24rpx">{{ item.title }}</view>
                        </u-grid-item>
                    </block>

                </u-grid>
            </view>



			<!--菜单-->
		<!--	<view class="menu-wrap ">
				<view class="group-bd f-w">
					<view :class="'item ' + item.icon + '-box'" v-for="(item, index) in menus" :key="index" @click="jumpPage(item.link_url)">
						<view class="icon-round d-c-c">
							<image class="icon-round" :src="item.image_url" mode=""></image>
						</view>
						<text class="name">{{ item.title }}</text>
					</view>
				</view>
			</view>-->
			<!--推荐-->
			<view>
				<recommendProduct :location="20"></recommendProduct>
			</view>
		</view>
		<request-loading :loadding='isloadding'></request-loading>
	</view>
</template>

<script>
	import recommendProduct from '@/components/recommendProduct/recommendProduct.vue';
	export default {
		components: {
			recommendProduct
		},
		data() {
			return {
				isloadding: true,
				/*签到数据*/
				sign: {},
				/*是否加载完成*/
				loadding: true,
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				/*菜单*/
				menus: [],
				detail: {
					balance: 0,
					points: 0,
					growth_value: 0,
					grade: {
						name: ''
					},
				},
				is_recycle: 0,
				orderCount: {},
				coupon: 0,
				setting: {},
				user_type: '', //用户状态
				msgcount: 0, //用户未读消息
			};
		},
		onPullDownRefresh() {
			let self = this;
			self.getData();
		},
		onShow() {
			/*获取个人中心数据*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				self.isloadding = true;
				self._get('user.index/detail', {
					url: self.url
				}, function(res) {
					//#ifdef MP-WEIXIN
					// if (res.data.getPhone) {
						// self.gotoPage('/pages/login/bindmobile');
						// return;
					// }
					//#endif
					
					self.detail = res.data.userInfo;
					self.sign = res.data.sign;
					self.coupon = res.data.coupon;
					self.orderCount = res.data.orderCount;

					self.menus = res.data.menus;
					self.setting = res.data.setting;
					self.is_recycle = res.data.userInfo.is_recycle;
					self.msgcount = res.data.msgcount;
					
					// 当会员成为分销商时，显示分销商等级
					if (res.data.is_agent && res.data.agentInfo) {
						self.detail.grade.name = res.data.agentInfo.grade.name;
					}
					
					self.loadding = false;
					uni.stopPullDownRefresh();
					self.isloadding = false;
				});
			},
			bindMobile() {
				this.gotoPage('/pages/user/modify-phone/modify-phone');
			},
			/*跳转页面*/
			jumpPage(path) {
				let self = this;
				if (path.startsWith('/')) {
					self.gotoPage(path);
				} else {
					self[path]();
				}
			},
			/*扫一扫核销*/
			scanQrcode: function() {
				this.gotoPage('/pages/user/scan/scan');
			},
			getPhoneNumber(e) {
				var self = this;
				if (e.detail.errMsg !== 'getPhoneNumber:ok') {
					return false;
				}
				uni.showLoading({
					title: '加载中'
				})
				uni.login({
					success(res) {
						// 发送用户信息
						self._post('user.user/bindMobile', {
							code: res.code,
							encrypted_data: encodeURIComponent(e.detail.encryptedData),
							iv: encodeURIComponent(e.detail.iv),
						}, result => {
							uni.showToast({
								title: '绑定成功'
							});
							// 执行回调函数
							self.detail.mobile = result.data.mobile;
						}, false, () => {
							uni.hideLoading()
						});
					}
				});
			},
		}
	};
</script>

<style>
    page {
        background: #F2F2F2;
    }
    image{
        display: inline-block;
    }
    .lhfont{
        font-size: 24rpx;
        margin-top: 10rpx;

    }
    .disheight{
        height: 380rpx !important;
    }

	.w100 {
		width: 100%;
	}

	.foot_ {
		height: 98rpx;
		width: 100%;
	}

	.user-header {
		position: relative;
		background: #e2231a;
	}

	.user-header .user-header-inner {
		position: relative;
		padding: 30rpx 30rpx 120rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		overflow: hidden;
		margin-bottom: 100rpx;
	}

	.user-header .user-header-inner::after,
	.user-header .user-header-inner::before {
		display: block;
		content: '';
		position: absolute;
		border-radius: 50%;
		z-index: 0;
	}

	.user-header .user-header-inner::after {
		width: 400rpx;
		height: 400rpx;
		right: -100rpx;
		bottom: -200rpx;
		background-image: radial-gradient(90deg, rgba(255, 255, 255, 0.2) 10%, rgba(255, 255, 255, 0));
	}

	.user-header .user-header-inner::before {
		width: 200rpx;
		height: 200rpx;
		left: -60rpx;
		top: -20rpx;
		background-image: radial-gradient(-90deg, rgba(255, 255, 255, 0.2) 10%, rgba(255, 255, 255, 0));
	}

	.user-header .user-info {
		display: flex;
		justify-content: flex-start;
		align-items: center;
	}

	.user-header .photo,
	.user-header .photo image {
		width: 100rpx;
		height: 100rpx;
		border-radius: 50%;
	}

	.user-header .photo {
		border: 4rpx solid #ffffff;
	}

	.user-header .info {
		padding-left: 20rpx;
		box-sizing: border-box;
		overflow: hidden;
		color: #ffffff;
	}

	.user-header .info .name {
		font-weight: bold;
		font-size: 32rpx;
	}

	.user-header .info .tel {
		font-size: 26rpx;
	}

	.user-header .info .grade {
		display: block;
		padding: 4px 16rpx;
		font-size: 22rpx;
		/* height: 36rpx; */
		line-height: 36rpx;
		border-radius: 40rpx;
		background: #B90F00;
		color: #ffffff;
		font-family: PingFang SC;
	}

	.user-header .sign-box {
		position: absolute;
		right: 20rpx;
		padding: 0 10rpx;
		height: 50rpx;
		border: 1px solid #ffe300;
		border-radius: 25rpx;
		font-size: 24rpx;
		color: #ffe300;
		z-index: 10;
	}

	.user-header .sign-box .iconfont {
		color: #ffe300;
	}

	.user-header .my-order {
		position: absolute;
		padding: 0 30rpx;
		/* height: 240rpx; */
		right: 20rpx;
		bottom: -75rpx;
		left: 20rpx;
		box-sizing: border-box;
		border-radius: 16rpx;
		/* box-shadow: 0 0 6rpx 0 rgba(0, 0, 0, 0.1); */
		background: #ffffff;
		z-index: 10;
	}

	.order_center {
		border-left: 1rpx solid #D9D9D9;
		border-right: 1rpx solid #D9D9D9;
	}

	.my-order .item {
		display: flex;
		margin: 20rpx 0;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		font-size: 26rpx;
		flex: 1;
	}

	.my-assets .icon-box image {
		width: 48rpx;
		height: 48rpx;
		margin-bottom: 16rpx;
	}

	.my-order .icon-box,
	.my-assets .icon-box {
		width: 60rpx;
		height: 60rpx;
	}

	.my-order .icon-box .iconfont,
	.my-assets .icon-box .iconfont {
		font-size: 50rpx;
		color: #333333;
	}

	.my-assets .icon-box .dot {
		position: absolute;
		top: -13rpx;
		right: -8rpx;
		height: 25rpx;
		min-width: 25rpx;
		padding: 4rpx;
		border-radius: 20rpx;
		font-size: 20rpx;
		background: #f00808;
		color: #ffffff;
	}

	.my-assets {
		margin: 0 20rpx;
		padding: 30rpx;
		padding-top: 0;
		background: #ffffff;
		border-radius: 12rpx;
	}

	.my-assets .item {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

	.my-wallet {
		position: relative;
		width: 200rpx;
		border-left: 1px solid #dddddd;
	}

	.my-wallet::after {
		position: absolute;
		display: block;
		content: '';
		left: 0;
		border: 8rpx solid transparent;
		border-left-color: #dddddd;
	}

	.menu-wrap {
		margin: 0 20rpx;
		margin-top: 30rpx;
		background: #ffffff;
		/* box-shadow: 0 0 6rpx 0 rgba(0, 0, 0, 0.1); */
		border-radius: 12rpx;
	}

	.menu-wrap .group-bd {
		display: flex;
		justify-content: flex-start;
		align-items: flex-start;
	}

	.menu-wrap .item {
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: column;
		width: 142rpx;
		height: 150rpx;
		font-size: 24rpx;
	}

	.menu-wrap .item.icon-dizhi1-box .icon-round {
		background-image: linear-gradient(135deg, #67b4e2 10%, #356dce 70%, #5c8fe8 90%);
	}

	.menu-wrap .item.icon-youhuiquan1-box .icon-round {
		background-image: linear-gradient(135deg, #e87ea4 10%, #ff268a 70%, #fe0d76 90%);
	}

	.menu-wrap .item.icon-youhuiquan--box .icon-round {
		background-image: linear-gradient(135deg, #ff5a30 10%, #ff2b3c 70%, #ff1740 90%);
	}

	.menu-wrap .item.icon-fenxiao1-box .icon-round {
		background-image: linear-gradient(135deg, #7ceeba 10%, #1ed2b7 70%, #17c0ad 90%);
	}

	.menu-wrap .item.icon-kanjia-box .icon-round {
		background-image: linear-gradient(135deg, #f2a904 10%, #f27d04 70%, #eaa031 90%);
	}

	.menu-wrap .item.icon-shezhi1-box .icon-round {
		background-image: linear-gradient(135deg, #615f6c 10%, #4c4a58 70%, #615f6c 90%);
	}

	.menu-wrap .icon-round {
		width: 48rpx;
		height: 48rpx;
		color: #ffffff;
	}

	.menu-wrap .item .iconfont {
		font-size: 40rpx;
		color: #ffffff;
	}

	.menu-wrap .item .name {
		margin-top: 19rpx;
	}

	.bind_phone {
		width: 100%;
		height: 80rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		margin-bottom: 30rpx;
	}

	.bind_content {
		display: flex;
		justify-content: space-between;
		align-items: center;
		background: #ffffff;
		/* box-shadow: 0 0 6rpx 0 rgba(0, 0, 0, 0.1); */
		border-radius: 16rpx;
		height: 100%;
		padding: 0 20rpx;
	}

	.bind_txt {}

	.bind_btn {
		width: 134rpx;
		height: 50rpx;
		line-height: 50rpx;
		font-size: 22rpx;
		border-radius: 25rpx;
		text-align: center;
		color: #FFFFFF;
		background-color: #e2231a;
	}

	.vertical {
		position: absolute;
		top: 10px;
		right: 53px;
		z-index: 100000;
	}

	.vertical_img {
		width: 100rpx;
		height: 100rpx;
	}

	.f20 {
		margin-left: 5rpx;
		font-size: 19rpx;
	}

	.red_mini {
		color: #333333;
		font-size: 36rpx;
		font-weight: bold;
	}

	.icon-zhuanshutequan {
		color: #f5dca6;
		margin-right: 3px;
	}

	.news {
		position: absolute;
		top: 40rpx;
		right: 20rpx;
		z-index: 100;
	}

	.news .chat {
		width: 40rpx;
		height: 40rpx;
	}

	.news .icon-xiaoxi {
		font-size: 50rpx;
		color: #FFFFFF;
	}

	.news_num {
		position: absolute;
		top: 24rpx;
		right: 44rpx;
		z-index: 100;
		border-radius: 50%;
		width: 25rpx;
		height: 25rpx;
		text-align: center;
		line-height: 25rpx;
		color: #FFFFFF;
		background-color: #ff6633;
		padding: 5rpx;
		font-size: 20rpx;
	}

	.my-assets-all {
		display: flex;
		justify-content: space-between;
		align-items: center;
		height: 90rpx;
		line-height: 90rpx;
	}

	.my-assets-all .icon.icon-jiantou {
		font-size: 12px;
		color: #999999;
	}
</style>
