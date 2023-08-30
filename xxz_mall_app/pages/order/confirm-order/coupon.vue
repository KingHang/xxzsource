<template>
	<view :class="Visible ? 'usable-coupon open' : 'usable-coupon close'">
		<view class="popup-bg" @click="closePopup"></view>
		<view style="border-top-right-radius: 30rpx;border-top-left-radius: 30rpx;" class="main pt30" v-on:click.stop>

            <view class="distr-tit">选择优惠券</view>


				<view class="p-0-30" style="padding-bottom: 50rpx">
                    <u-radio-group v-model="value" active-color="#f53630">
                    <view  v-for="(item, index) in datalist">
                        <view class="fsc" style="padding: 10rpx">
                            <view  >
                                {{item.name}}

                            </view>
                            <view style="display: flex;color: #F63E36">
                                <view style="margin-right: 30rpx">
                                    <view v-if="item.coupon_type.value==10">
                                        - ¥{{item.reduce_price}}
                                    </view>
                                    <view v-if="item.coupon_type.value==20">
                                        {{item.discount}}折
                                    </view>
                                </view>

                                <u-radio @change="radioChange" :name="item.user_coupon_id" >
                                </u-radio>
                            </view>
                        </view>
                    </view>
                        <view class="fsc" style="padding: 10rpx">
                            <view>
                               不使用优惠券
                            </view>
                            <view>
                                <u-radio @change="radioChange" :name="0" >
                                </u-radio>
                            </view>
                        </view>
                    </u-radio-group>
					<!--<view @click="selectCoupon(item.user_coupon_id)" style="margin-bottom: 15rpx;" :class="'coupon-item coupon-item-'+item.color.text"
					 v-for="(item, index) in datalist" :key="index">
						&lt;!&ndash;装饰用的小圆&ndash;&gt;
						<view class="circles"><text v-for="(circle, num) in 6" :key="num"></text></view>
						<view class="info w100">
							<view class="d-c-c d-c">
								<text class="f40 fb w-s-n">{{ item.coupon_type.text }}</text>
							</view>
						</view>
						<view class="operation d-b-c w-b">
							<view class="flex-1 o-h">
								<view class="f34">{{ item.name }}</view>
								<view class=" f24">最低消费{{ item.min_price }}元</view>

								<block v-if="item.expire_type == 10">
									<view class="mt30 f24 ">领取{{ item.expire_day }}天内有效</view>
								</block>
								<block v-if="item.expire_type == 20">
									<view class="mt30 f24 red">{{ item.start_time.text }}~{{ item.end_time.text }}</view>
								</block>
							</view>
							<view class="f30 mr20 b-radio">
								立即使用
							</view>
						</view>
					</view>-->
				</view>


            <view class="distr_btn">
                <button class="btn-gcred" @click="submit">完成</button>
            </view>

			<!--<view class="coupon-btns"><button type="default" @click="closePopup(0)" class="btn-cancel">不使用优惠券</button></view>-->
		</view>
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
				/*是否可见*/
				Visible: false,
				/*优惠券列表*/
				datalist: {},
				/*尺寸比例*/
				ratio: 1,
                value: 0,
			};
		},
		props: ['isCoupon', 'couponList','coupon_id'],

		onLoad() {},
		mounted() {
			this.init();
		},
		watch: {
			isCoupon: function(n, o) {
				if (n != o) {
					this.Visible = n;
					this.datalist = this.couponList;
					console.log(this.datalist)

					this.getHeight();
				}
			}
		},
		methods: {
			/*初始化*/
			init() {
				let self = this;
				self.value = self.coupon_id
				uni.getSystemInfo({
					success(res) {
						self.phoneHeight = res.windowHeight;
						self.ratio = res.windowWidth / 750;
						self.getHeight();
					}
				});
			},
            radioChange(e){
            },

			/*获取高度*/
			getHeight() {
				let count = Object.keys(this.couponList).length;
				if (count > 2) {
					this.scrollviewHigh = this.phoneHeight * 0.6;
				} else {
					if (count == 1) {
						this.scrollviewHigh = 230 * this.ratio;
					} else if (count == 2) {
						this.scrollviewHigh = 460 * this.ratio;
					}
				}
			},
            submit(){
                let self = this;
                if (self.value==0){
                    console.log(123)
                    self.closePopup(0)
                } else{
                    self.selectCoupon(self.value)
                }

            },

			/*选择优惠券*/
			selectCoupon(e) {
				this.closePopup(e);
			},
			/*关闭弹窗*/
			closePopup(e) {
				this.$emit('close', e);
			}
		}
	};
</script>

<style>
    .distr_btn button {
        width: 90%;
        margin: 0 auto;
        margin-bottom: 30rpx;
        height: 60rpx;
        line-height: 60rpx;
        border-radius: 30rpx;
    }
    .distr-tit {
        text-align: center;
        font-size: 39rpx;
        margin-top: 23rpx;
        margin-bottom: 70rpx;
    }
	.usable-coupon .popup-bg {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background: rgba(0, 0, 0, 0.6);
		z-index: 99;
	}

	.usable-coupon .main {
		position: fixed;
		width: 100%;
		bottom: 0;
		min-height: 200rpx;
		max-height: 900rpx;
		background-color: #fff;
		transform: translate3d(0, 980rpx, 0);
		transition: transform 0.2s cubic-bezier(0, 0, 0.25, 1);
		bottom: env(safe-area-inset-bottom);
		z-index: 99;
	}

	.usable-coupon .main {
		position: fixed;
		width: 100%;
		bottom: 0;
		min-height: 200rpx;
		max-height: 900rpx;
		background-color: #fff;
		transform: translate3d(0, 980rpx, 0);
		transition: transform 0.2s cubic-bezier(0, 0, 0.25, 1);
		bottom: env(safe-area-inset-bottom);
		z-index: 99;
	}

	.usable-coupon.open .main {
		transform: translate3d(0, 0, 0);
	}

	.usable-coupon.close .popup-bg {
		display: none;
	}

	.coupon-item-red .operation {
		/* background: #fdf1df; */
	}

	.coupon-btns .btn-cancel {
		height: 88rpx;
		line-height: 88rpx;
		font-size: 30rpx;
		background: #999999;
		color: #ffffff;
		border-radius: 0;
	}

	.coupon-item .w100 {
		padding: 0 75rpx;
	}

	.b-radio {
		border: 1rpx solid #FFFFFF;
		border-radius: 30rpx;
		padding: 10rpx 30rpx;
	}
</style>
