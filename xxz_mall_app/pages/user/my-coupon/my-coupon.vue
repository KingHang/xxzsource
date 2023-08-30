<template>
	<view>
		<!--<view class="top-tabbar">
			<view :class="state_active == 0 ? 'tab-item active' : 'tab-item'" @click="stateFunc(0)">未使用</view>
			<view :class="state_active == 1 ? 'tab-item active' : 'tab-item'" @click="stateFunc(1)">已使用</view>
			<view :class="state_active == 2 ? 'tab-item active' : 'tab-item'" @click="stateFunc(2)">已过期</view>
		</view>-->

        <view class="top-tabbar" style="background-color: white">
            <view style="display: flex;justify-content: space-between;width: 90%;margin: auto;padding: 30rpx" >
                <view @click="stateFunc(0)"  :class="state_active == 0?'actred':'nred'" style="position: relative">
                    待使用
                </view>
                <view @click="stateFunc(1)" :class="state_active == 1?'actred':'nred'" style="position: relative">
                    已使用
                </view>
                <view @click="stateFunc(2)" :class="state_active == 2?'actred':'nred'" style="position: relative">
                    已过期
                </view>
            </view>
        </view>

		<!--列表-->
		<scroll-view style="margin-bottom: 90rpx" scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50"
		 @scrolltoupper="scrolltoupperFunc" @scrolltolower="scrolltolowerFunc">


            <block v-for="sup in supList">
                <block v-for="(item,sup_index) in sup.list">
                    <view  class="hlbblock" @click.stop="gotoPage('/pagesCoupon/coupon/detail?coupon_id='+ item.coupon_id)" style="overflow: hidden;position: relative;width: 95%">
                        <view   style="width: 100%;">
                            <view  style="display: flex;height: 200rpx">
                                <view style="position: relative">
                                    <block v-if="state_active==2">
                                        <image src="/static/images/coupon/item.png" style="width:200rpx" mode="widthFix"></image>
                                    </block>
                                    <block>
                                        <image :src="'/static/images/coupon/item-'+item.color.text+'.png'" style="width:200rpx" mode="widthFix"></image>
                                    </block>

                                </view>
                                <view :class="state_active!=2?'font-'+item.color.text:'font-deafult'" style="position: absolute;padding: 10rpx">
                                    <view style="font-size: 22rpx;margin: 20rpx">
                                        {{item.coupon_type.text}}
                                    </view>

                                    <view style="margin: 20rpx">
                                        <text v-if="item.coupon_type.value == 10" style="font-size: 50rpx;font-weight: bold"><text style="font-size: 36rpx;margin-right: 10rpx">¥</text> {{item.reduce_price}}</text>
                                        <block v-else-if="item.coupon_type.value == 20">
                                            <text style="font-size: 50rpx;font-weight: bold;margin-right: 10rpx">{{item.discount}}</text>折</block>

                                    </view>
                                </view>
                                <view style="padding: 20rpx;width: 80%">
                                    <view>
                                        {{item.name}}
                                    </view>
                                    <view class="font2599" style="height: 40rpx">
                                        {{ item.reduce_text }}
                                    </view>
                                    <view style="display: flex;justify-content: space-between;align-items: baseline">
                                        <view class="font2599">
                                            <view class="text" style="font-size: 23rpx" v-if="item.expire_type ==10"> 有效期：剩余{{item.expire_day}}天</view>
                                            <view class="text" style="font-size: 23rpx" v-if="item.expire_type ==20">{{item.start_time.text}}-{{item.end_time.text}}</view>
                                        </view>
                                        <block v-if="state_active==2">
                                            <view  class="hlbbutton" style="font-size: 25rpx;width: 150rpx;height: 50rpx;line-height: 50rpx;background-color: white;color: #C1C1C1;border: 1px #F0F0F0 solid">
                                                <text  >已过期</text>
                                            </view>
                                        </block>
                                        <block v-else>
                                            <view v-if="item.state.value>0" @click.stop="gotoPage('/pages/product/category')" class="hlbbutton" style="font-size: 25rpx;width: 150rpx;height: 50rpx;line-height: 50rpx">
                                                <text  >{{ item.state.text }}</text>
                                            </view>
                                            <view v-else class="hlbbutton" style="font-size: 25rpx;width: 150rpx;height: 50rpx;line-height: 50rpx;background-color: #F0F0F0;color: #C1C1C1">
                                                <text  >{{ item.state.text }}</text>
                                            </view>
                                        </block>

                                    </view>
                                </view>
                            </view>
                        </view>
                    </view>
                </block>
            </block>
			<!--<view class="p30 ">
				<view class="item-wrap mb20" v-for="(item, index) in supList" :key="index" v-if="item.list.length > 0">
					<view class="d-f">
						<span class="icon iconfont icon-dianpu1"></span>
						<view class="con_tit">{{item.name}}</view>
					</view>
					<view v-for="(sup_item,sup_index) in item.list" :key="sup_index" class="coupon_item" v-if="item.list.length > 0">
						<view :class="sup_item.is_expire==0&&sup_item.is_use==0 ? 'coupon-item coupon-item-'+sup_item.color.text : 'coupon-item coupon-item-gray'"
						 @click="lookRule(sup_item)">
							<view class="coupon_type">
								{{item.name=='平台优惠券'?'平台通用':item.name}}
							</view>
							&lt;!&ndash;装饰用的小圆&ndash;&gt;
							<view class="circles">
								<text v-for="(circle, num) in 8" :key="num"></text>
							</view>
							<view class="info">
								<view>{{sup_item.coupon_type.text}}</view>
							</view>
							<view class="operation d-b-c">
								<view class="flex-1 coupon-content">
									<view>
										<template v-if=" sup_item.coupon_type.value == 10 ">
											<view class="price">
												<text>￥</text>
												<text class="f40 fb">{{ sup_item.reduce_price }}</text>
											</view>
										</template>
										<template v-if="sup_item.coupon_type.value == 20 ">
											<text class="f40 fb">{{ sup_item.discount }}</text><text>折</text>
										</template>
									</view>
									<view class="f30">{{sup_item.name}}</view>
									<view class="f24">
										<template v-if="sup_item.expire_type ==10">
											有效期：剩余{{sup_item.expire_day}}天
										</template>
										<template v-if="sup_item.expire_type ==20">
											有效期：{{sup_item.start_time.text}}至{{sup_item.end_time.text}}
										</template>
									</view>
								</view>

								<view class="right-box d-c-c">
									<view type="default" v-if="sup_item.state.value>0" class="f30">
										未使用
									</view>
									<view v-else class="f30">
										<text>{{sup_item.state.text}}</text>
									</view>
								</view>
							</view>
						</view>
						<view class="range_item d-b-c" v-if="sup_item.apply_range == 20" @click.stop="gotoPage('/pagesCoupon/coupon/detail?coupon_id='+ sup_item.coupon_id)">
							<view>限购店铺部分商品</view>
							<view><text class="icon iconfont icon-jiantou" style="color: #999999; font-size: 24rpx;"></text></view>
						</view>
					</view>
					<view class="">
						<view class="bottom-refresh">
							<view class="d-c-c p30" v-if="loading">
								<text class="gray3">加载中...</text>
							</view>
							<view class="d-c-c p30" v-if="no_more ">
								<text class="gray3">~~加载完成~~</text>
							</view>
						</view>
					</view>
					<view class="d-c-c p30" v-if="DataList.length==0 && !loading">
						<text class="iconfont icon-wushuju"></text>
						<text class="cont">亲，暂无相关记录哦</text>
					</view>
				</view>
			</view>-->
		</scroll-view>

        <navigator url="/pagesCoupon/coupon/coupon" style="background-color: white;height: 100rpx;position: fixed;bottom: 0;width: 100%">
            <view style="display: flex;margin: auto;align-items: center;justify-content: space-around;width: 30%;padding-top: 20rpx">
                <image src="/static/images/coupon/coupon.png" style="width: 40rpx;" mode="widthFix"></image>
                领券中心
                <u-icon name="arrow-right" size="25" color="#8e8e8e"></u-icon>
            </view>
        </navigator>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				/*状态选中*/
				state_active: 0,
				/*列表*/
				DataList: {},
				no_more: false,
				loading: false,
				data_type: 'not_use',
				supList: []
			};
		},
		mounted() {
			/*初始化*/
			this.init();
			/*获取数据*/
			this.getData();
		},
		methods: {
			/*初始化*/
			init() {
				let self = this;
				uni.getSystemInfo({
					success(res) {
						self.phoneHeight = res.windowHeight;
						// 计算组件的高度
						let view = uni.createSelectorQuery().select('.top-tabbar');
						view.boundingClientRect(data => {
							let h = self.phoneHeight - data.height;
							self.scrollviewHigh = h;
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
				let data_type = self.data_type;
				self._get('user.voucher/lists', {
					data_type: data_type,
				}, function(res) {
				    console.log(res)
					uni.hideLoading();
					self.DataList = res.data.list;
					self.getSup();
				});
			},
			/* 优惠券分类 */
			getSup() {
				let self = this;
				let supList = [];
				let platformCoupon = {
					name: "平台优惠券",
					list: []
				};
				self.DataList.forEach((item, index) => {
					if (item.supplier == null) {
						platformCoupon.list.push(item)
					} else {
						if (supList == '') {
							supList.push({
								name: item.supplier.name,
								list: [item]
							})
						} else {
							supList.forEach((sup_item, sup_index) => {
								if (sup_item.name !== item.supplier.name) {
									supList.push({
										name: item.supplier.name,
										list: [item]
									})
								} else {
									sup_item.list.push(item)
								}
							})
						}
					}
				})
				supList.push(platformCoupon);
				self.supList = supList;
			},
			/*状态切换*/
			stateFunc(e) {
				let self = this;
				if (self.state_active != e) {
					if (e == 0) {
						self.data_type = 'not_use';
					}
					if (e == 1) {
						self.data_type = 'is_use';
					}
					if (e == 2) {
						self.data_type = 'is_expire';
					}
					self.state_active = e;
					self.getData();
				}
			},

			/*可滚动视图区域到顶触发*/
			scrolltoupperFunc() {
				console.log('滚动视图区域到顶');
			},

			/*可滚动视图区域到底触发*/
			scrolltolowerFunc() {
				console.log('滚动视图区域到底');
			},

			/*查看规则*/
			lookRule(item) {
				item.rule = true;
			},

			/*关闭规则*/
			closeRule(item) {
				item.rule = false;
			}
		}
	};
</script>

<style>

    .font-red{
        color: #B20303;
    }
    .font-blue{
        color: #035DB2;
    }
    .font-yellow{
        color: #E57621;
    }
    .font-violet{
        color: #4903B2;
    }
    .font-deafult{
        color: #999999;
    }
    .nred{
        color: #555555;
    }
    .actred{
        color: #EB2728;
    }
    .actred:after{
        content: " ";
        display: inline-block;
        height: 16rpx;
        width: 60rpx;
        border-width: 4rpx 0 0 0;
        border-color: #EB2728;
        border-style: solid;
        border-radius: 4rpx;
        position: absolute;
        top: 50rpx;
        left: 6rpx;
        margin-left: .3em;
    }

	.d-f {
		display: flex;
		align-items: center;
	}

	.icon-dianpu1 {
		margin-right: 15rpx;
	}

	.coupon_type {
		padding: 10rpx 20rpx;
		position: absolute;
		z-index: 100;
		color: #ffffff;
		right: 0;
		top: 0;
		background: #cacaca80;
		height: 36rpx;
		border-bottom-left-radius: 18rpx;
	}

	.con_tit {
		font-weight: 800;
		font-size: 31rpx
	}

	.coupon_item {
		margin: 20rpx 0;
	}
	.range_item{
		border: 1rpx solid #D9D9D9;
		border-top: none;
		padding: 8rpx;
		border-bottom-left-radius:10rpx ;
		border-bottom-right-radius:10rpx ;
		color: #666666;
		box-shadow: 0 0 8rpx rgba(0, 0, 0, 0.1);
	}
</style>
