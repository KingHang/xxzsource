<template>
	<view class="" :style="{ background: itemData.style.background, padding: itemData.style.paddingTop + 'px 0' }">

        <scroll-view scroll-x="true" style="white-space: nowrap">
            <block v-for="(item, index) in listData" :key="index">
                <view style="width: 40%;display: inline-block;margin: 15rpx;position: relative"   >
                    <image :src="'/static/images/coupon/bg-'+item.color.text+'.png'" style="width: 100%" mode="widthFix"></image>
                    <view style="position: absolute;width: 100%;top: 0;left: 0">
                        <view :class="'font-'+item.color.text"  style="padding: 20rpx;display: flex">
                            <view style="width: 50%;text-align: center;">
                                <view style="display: flex;align-items: center;height: 50rpx">
                                    <block v-if="item.coupon_type.value == 10">
                                        <view style="font-size: 24rpx;font-weight: bold">￥</view>
                                        <view style="font-size: 48rpx;font-weight: bolder;line-height: 30rpx">{{ item.reduce_price }}</view>
                                    </block>
                                    <block v-if="item.coupon_type.value == 20">
                                       <view style="font-size: 48rpx;font-weight: bolder;line-height: 30rpx">{{ item.discount }}</view>
                                        <view style="font-size: 24rpx;font-weight: bold">折</view>

                                    </block>
                                </view>
                                <view>
                                    {{ item.reduce_text }}
                                </view>
                            </view>
                            <view style="width: 50%;text-align: center">

                                <view style="font-size: 22rpx;color: rgba(255,255,255,0.6)">
                                    ·{{ item.coupon_type.text }}·
                                </view>
                                <view style="position: relative">
                                    <image src="/static/images/coupon/btn.png" style="width: 100%" mode="widthFix"></image>
                                    <view style="position: absolute;top: 13rpx;left: 0;width: 100%">
                                        <view @click="receiveCoupon(index)" v-if="item.state.value == 1" style="text-align: center;font-size: 24rpx">
                                            领取
                                        </view>
                                        <view v-else style="text-align: center;font-size: 24rpx">
                                            {{ item.state.text }}
                                        </view>

                                    </view>
                                </view>
                            </view>


                        </view>

                    </view>
                </view>
            </block>
        </scroll-view>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				/*是否显示点*/
				indicatorDots: false,
				/*是否自动*/
				autoplay: true,
				/*切换时间*/
				interval: 5000,
				/*动画过渡时间*/
				duration: 1000,
				/*数据列表*/
				listData: []
			};
		},
		props: ['itemData'],
		created() {
			this.listData = this.itemData.data;
		},
		methods: {
			scroll(e) {},
			/**
			 * 领取优惠券
			 */
			receiveCoupon: function(index) {
				let self = this;
				let item = self.listData[index];
				if (item.state.value == 0) {
					return false;
				}
				self._post(
					'user.voucher/receive', {
						coupon_id: item.coupon_id
					},
					function(result) {
						uni.showToast({
							title: '领取成功',
							icon: 'success',
							mask: true,
							duration: 2000
						});
						item.state.value = 0;
						item.state.text = '已领取';
					}
				);
			}
		}
	};
</script>

<style>
	.diy-coupon {
		margin: 20rpx;
	}

	.diy-coupon .swiper {
		width: 750rpx;
		height: 168rpx;
	}

	.diy-coupon .coupon-item {
		width: 710rpx;
		height: 168rpx;
		align-items: stretch;
		align-content: stretch;
		color: #ffffff;
	}
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

	.diy-coupon .coupon-item.bg-red {
		background: #e62423;
	}

	.diy-coupon .coupon-item.bg-blue {
		background: #178ed9;
	}

	.diy-coupon .coupon-item.bg-yellow {
		background: #f4a50b;
	}

	.diy-coupon .coupon-item.bg-violet {
		background: #ab0bf6;
	}

	.diy-coupon .coupon-item .left-type {
		padding: 0 30rpx 0 40rpx;
		width: 40rpx;
		font-size: 40rpx;
		line-height: 40rpx;
		text-align: center;
		font-weight: bold;
		border-right: 4rpx dashed rgba(255, 255, 255, .4);
	}

	.diy-coupon .left-side-line {
		position: absolute;
		width: 20rpx;
		top: 0;
		left: -15rpx;
		overflow: hidden;
	}

	.diy-coupon .right-side-line {
		position: absolute;
		width: 20rpx;
		top: 0;
		right: -15rpx;
		overflow: hidden;
	}

	.diy-coupon .side-line .round {
		display: block;
		width: 20rpx;
		height: 20rpx;
		border-radius: 50%;
		margin: 4rpx 0;
		background: #ffffff;
	}

	.diy-coupon .center-content::before,
	.diy-coupon .center-content::after {
		position: absolute;
		display: block;
		content: '';
		width: 30rpx;
		height: 15rpx;
		background: #FFFFFF;
	}

	.diy-coupon .center-content::before {
		top: 0;
		right: -16rpx;
		border-radius: 0 0 15rpx 15rpx;
	}

	.diy-coupon .center-content::after {
		bottom: 0;
		right: -16rpx;
		border-radius: 15rpx 15rpx 0 0;
	}

	.diy-coupon .coupon-item .center-content {
		padding: 20rpx 40rpx;
		display: flex;
		justify-content: space-between;
		flex-direction: column;
		align-items: flex-start;
		flex: 1;
	}

	.diy-coupon .coupon-item .center-content .content-top {
		height: 50rpx;
		line-height: 50rpx;
	}

	.diy-coupon .coupon-item .center-content .content-datatime {
		padding: 4rpx 10rpx;
		border-radius: 30rpx;
		font-size: 20rpx;
		background: rgba(0, 0, 0, .2);
	}

	.diy-coupon .coupon-item .right-receive {
		padding: 0 40rpx 0 30rpx;
		width: 30rpx;
		text-align: center;
		font-size: 30rpx;
		line-height: 30rpx;
		text-align: center;
		border-left: 4rpx dashed rgba(255, 255, 255, .4);
		background: rgba(0, 0, 0, .6);
	}

	.diy-coupon .coupon-item .no-receive {
		background: #acacac;
		color: #787878;
	}
</style>
