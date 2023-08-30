<template>
	<view class=" " v-if="!loadding">
		<block v-if="DataList.length > 0">
            <block v-for="item in DataList">
                <view v-if="item.show_center == 1" class="hlbblock" style="overflow: hidden;position: relative;width: 95%">
                    <view style="width: 100%;">
                        <view style="display: flex;height: 200rpx" @click.stop="gotoPage('/pagesCoupon/coupon/detail?coupon_id='+ item.coupon_id)">
                            <view style="position: relative">
                                <image :src="'/static/images/coupon/item-'+item.color.text+'.png'" style="width:200rpx" mode="widthFix"></image>
                            </view>
                            <view :class="'font-'+item.color.text"  style="position: absolute;padding: 10rpx">
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
                                        <view class="text" style="font-size: 23rpx" v-if="item.expire_type ==10"> 有效期：领取{{ item.expire_day }}天内有效</view>
                                        <view class="text" style="font-size: 23rpx" v-if="item.expire_type ==20">{{item.start_time.text}}-{{item.end_time.text}}</view>

                                    </view>
                                    <view @click.stop="receive(item.coupon_id )" v-if="item.state.value>0" class="hlbbutton not-get">
                                        <text>{{ item.state.text }}</text>
                                    </view>
                                    <view v-else class="has-get">
                                        <text>{{ item.state.text }}</text>
                                    </view>
                                </view>
                            </view>
                        </view>
						<view class="go-use" @click="goUse">
							<view class="left">已领取至“我的优惠券”</view>
							<view class="right">
								立即使用
								<u-icon name="arrow-right" color="#F63E36"></u-icon>
							</view>
						</view>
                    </view>
                </view>
            </block>
<!--



			<view class="item-wrap" v-for="(item, index) in DataList" :key="index">
				<view :class="'coupon-item coupon-item-'+item.color.text" @click="lookRule(item)">
					&lt;!&ndash;装饰用的小圆&ndash;&gt;
					<view class="circles">
						<text v-for="(circle,num) in 8" :key="num"></text>
					</view>
					<view class="info">
						<view >{{item.coupon_type.text}}</view>
					</view>
					<view class="operation d-b-c w-b">
						<view class="flex-1 coupon-content">
							<view :class="item.is_expire==0&&item.is_use==0?item.color.text:''">
								<template v-if=" item.coupon_type.value == 10 ">
									<view class="price" >
										<text>￥</text>
										<text class="f40 fb">{{ item.reduce_price }}</text>
									</view>
								</template>
								<template v-if="item.coupon_type.value == 20 ">
									<text class="f40 fb">{{ item.discount }}</text><text>折</text>
								</template>
							</view>
							<view class="f30">{{item.name}}</view>
							<view class="f24">
								<template v-if="item.expire_type ==10">
									有效期：领取{{ item.expire_day }}天内有效
								</template>
								<template v-if="item.expire_type ==20">
									有效期：{{item.start_time.text}}至{{item.end_time.text}}
								</template>
							</view>
						</view>
						<view class="btns d-c-c">

							<button type="default" v-if="item.state.value>0" :class="'btn-'+item.color.text" v-on:click.stop="receive(item.coupon_id )">
								立即领取
							</button>
							<button type="default" v-else class="btn-gray" v-on:click.stop>
								{{ item.state.text }}
							</button>
						</view>
					</view>
				</view>
				<view class="range_item d-b-c" v-if="item.apply_range == 20" @click.stop="gotoPage('/pagesCoupon/coupon/detail?coupon_id='+ item.coupon_id)">
					<view>限购店铺部分商品</view>
					<view><text class="icon iconfont icon-jiantou" style="color: #999999; font-size: 24rpx;"></text></view>
				</view>
			</view>-->
		</block>
		<block v-else>
			<view class="none-data-box">
				<image src="/static/none.png" mode="widthFix"></image>
				<text>暂无数据</text>
			</view>
		</block>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				/*是否加载完成*/
				loadding: true,
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				DataList: [],
				/*当前页面*/
				page: 1,
				/*每页条数*/
				list_rows: 10,
			};
		},
		mounted() {
			uni.showLoading({
				title: '加载中'
			});
			/*获取优惠券列表*/
			this.getData();
		},
		methods: {
			
			/*获取数据*/
			getData() {
				let self = this;
				self._get('voucher.coupon/lists', {
					page: self.page,
					list_rows: self.list_rows,
				}, function(res) {
				    console.log(res)
					self.DataList = res.data.list;
					self.loadding = false;
					uni.hideLoading();
				});
			},
			/*查看规则*/
			lookRule(item) {
			    console.log('lookRule')
				item.rule = true;
			},

			/*关闭规则*/
			closeRule(item) {
				item.rule = false;
			},

			/*领取优惠券*/
			receive(e) {
				let self = this;
				uni.showLoading({
					title: '领取中'
				});
				self._post('user.voucher/receive', {
					coupon_id: e,
				}, function(res) {
					uni.hideLoading();
					uni.showToast({
						title: '领取成功',
						duration: 2000,
						icon: 'success'
					});
				});
				self.getData();
			},
			goUse() {
				uni.switchTab({
					url: '/pages/product/category'
				})
			}
		}
	};
</script>

<style lang="scss">
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
	.coupon-wrap {
		padding: 30rpx;
	}

	.item-wrap {
		margin-bottom: 20rpx;
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
	
	.not-get {
		font-size: 25rpx;
		width: 150rpx;
		height: 50rpx;
		line-height: 50rpx;
	}
	
	.has-get {
		font-size: 25rpx;
		width: 150rpx;
		height: 50rpx;
		line-height: 50rpx;
		text-align: center;
		color: #F63E36;
		border-radius: 35rpx;
		background-color: #FFE9E8;
		margin: 15rpx;
	}
	
	.go-use {
		width: 100%;
		background-color: #ffffff;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 20rpx;
		border-top: 1rpx solid #eeeeee;
		.left {
			font-size: 22rpx;
			color: #999999;
		}
		.right {
			border: 1rpx solid #F63E36;
			color: #F63E36;
			font-size: 24rpx;
			border-radius: 40rpx;
			padding: 5rpx 10rpx;
			margin-right: 15rpx;
		}
	}
</style>
