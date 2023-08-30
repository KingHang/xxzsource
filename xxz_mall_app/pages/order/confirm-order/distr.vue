<template>
	<view  :class="Visible ? 'usable-distr open' : 'usable-distr close'">
		<view class="popup-bg" @click="closePopup"></view>
		<view style="border-top-right-radius: 30rpx;border-top-left-radius: 30rpx;" class="main pt30 " v-on:click.stop>
			<view class="distr-tit">选择配送方式</view>


            <view class="fsc" style="padding: 30rpx">
                <view @click="radioChange(10)" :class="checked_id==10?'choseitem':''" style="background-color: #F9F9F9;width: 47%;display: flex;align-items: center;height: 120rpx;border-radius: 10rpx;position: relative;overflow: hidden;  border: 1px #F9F9F9 solid;">
                    <image src="/static/images/order/d1.png" style="width: 60rpx;margin: 20rpx" mode="widthFix"></image>
                    <view :class="checked_id==10?'itemfont':''"  style="font-size: 32rpx;font-weight: bold">
                        快递配送
                    </view>
                    <image v-if="checked_id==10" src="/static/images/order/check.png" style="width: 55rpx;position: absolute;bottom: 0;right: 0" mode="widthFix"></image>

                </view>
                <view v-if="deliverySetting[1]==20" @click="radioChange(20)" :class="checked_id==20?'choseitem':''" style="background-color: #F9F9F9;width: 47%;display: flex;align-items: center;height: 120rpx;border-radius: 10rpx;position: relative;overflow: hidden;  border: 1px #F9F9F9 solid;">
                    <image src="/static/images/order/d2.png" style="width: 60rpx;margin: 20rpx" mode="widthFix"></image>
                    <view :class="checked_id==20?'itemfont':''"  style="font-size: 32rpx;font-weight: bold">
                        上门自提
                    </view>
                    <image v-if="checked_id==20" src="/static/images/order/check.png" style="width: 55rpx;position: absolute;bottom: 0;right: 0" mode="widthFix"></image>

                </view>

            </view>


            <view style="height: 300rpx">

                <view v-if="checked_id==20">

                    <navigator :url="'/pages/order/confirm-order/store?chooseSotr='+chooseSotr" class="d-b-c pr20" >
                        <block v-if="!extract_store.store_id">
                            <view class="add-address d-s-c">
                                <view class="icon-box mr10">
                                    <span class="icon iconfont icon-dizhi1"></span>
                                </view>
                                <text>请选择自提点</text>
                            </view>
                            <view>
                                <i class='iconfont icon-jiantou'></i>
                            </view>
                        </block>
                        <block v-else>
                            <view class="address-defalut-wrap">
                                <view class="info d-s-s">
                                    <text class="state">当前选择</text>
                                    <view class="province-c-a d-s-s flex-1" style="font-size: 32rpx;font-weight: bold">

                                        {{ extract_store.store_name }}
                                    </view>
                                </view>
                                <view class="address">

                                </view>
                                <view class="user">
                                    <text class="name" style="font-size: 28rpx;color: #999999">{{ extract_store.address }}</text>
<!--                                    <text class="tel">{{ extract_store.phone }}</text>-->
                                </view>
                            </view>
                            <view>
                                <i class='iconfont icon-jiantou'></i>
                            </view>
                        </block>

                    </navigator>
                </view>
            </view>
			<view class="distr_btn">
				<button class="hlbbutton" style="margin-bottom: 50rpx;height: 80rpx;line-height: 80rpx;border-radius: 40rpx" @click="closePopup">完成</button>
			</view>
		</view>
	</view>
</template>

<script>
	import Storeinfo from './store-info';

	export default {
		data() {
			return {
				/*是否可见*/
				Visible: false,
				checked_id: 10,
				choose_store_id: 0
			}
		},
		components: {
			Storeinfo,
		},
		props: ['isDist', 'extract_store', 'last_extract', 'deliverySetting','chooseSotr','orderShop'],
		watch: {
			isDist(val) {
				this.Visible = val;
			}
		},
		onLoad(options) {
			let self = this;
			self.options = options;
		},
		methods: {
			closePopup(e) {
				if (this.checked_id == 20 && this.$props.extract_store.store_id == null) {
					uni.showToast({
						icon: 'none',
						title: '请选择自提点'
					})
				} else {
					this.$emit('close', e);
				}
			},
			radioChange(n) {
				let self = this;
				self.checked_id = n;
				self.$fire.fire('checkedfir', n);
			},
		}

	}
</script>

<style >

    .choseitem{
        background-color: #FFF5F5 !important;
        border: 1px #F63E36 solid !important;


    }
    .itemfont{
        color: #F63E36;

    }
	.usable-distr .popup-bg {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background: rgba(0, 0, 0, 0.6);
		z-index: 99;
	}

	.usable-distr .main {
		position: fixed;
		width: 100%;
		bottom: 0;
		min-height: 400rpx;
		max-height: 1400rpx;
		background-color: #fff;
		transform: translate3d(0, 980rpx, 0);
		transition: transform 0.2s cubic-bezier(0, 0, 0.25, 1);
		/*bottom: env(safe-area-inset-bottom);*/
		z-index: 99;
	}

	.usable-distr.open .main {
		transform: translate3d(0, 0, 0);
	}

	.usable-distr.close .popup-bg {
		display: none;
	}

	.distr-tit {
		text-align: center;
		font-size: 39rpx;
		margin-top: 23rpx;
		margin-bottom: 70rpx;
	}

	.distr-list {
		padding: 25rpx;
		margin-bottom: 25rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;

	}

	.bor_botm {
		border-bottom: 1rpx solid #cacaca;
	}

	.distr_btn button {
		width: 90%;
		margin: 0 auto;
		margin-bottom: 30rpx;
		height: 60rpx;
		line-height: 60rpx;
		border-radius: 30rpx;
	}
</style>
