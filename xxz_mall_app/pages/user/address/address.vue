<template>
	<view v-if="!loadding">
		<view v-if="listData.length>0">
			<view class=" ">
				<!--<view class="tr add_add" @click="addAddress()">新增收货地址</view>-->
				<view class="  d-s-c  hlbblock30" v-for="(item,index) in listData" :key="index">
					<!--<view class="radio">
						<radio style="transform:scale(0.8)" color='#F6220C' :value="item.address_id+''" :checked="default_id==item.address_id+''"
						 @click="radioChange(item.address_id)" />
					</view>-->
					<view @click="chosefun(item)" class="info flex-1 ml20">
						<view class="user f32">

                            <u-tag v-if="default_id==item.address_id" text="默认" size="mini" type="success" mode="dark" shape="square" color="white" bg-color="#F63E36" style="margin-right: 10rpx"/>

                            <text>{{item.name}}</text>   <text style="margin-left: 20rpx">{{item.phone}}</text>
				<!--			<text>{{item.name}}</text>
							<text class="ml20 gray9 f26">{{item.phone}}</text>
							<text class="ml20 defaul_add" v-if="default_id==item.address_id">默认</text>-->
						</view>
						<view class="pt20 f26 gray3">
							{{item.region.province}}{{item.region.city}}{{item.region.region}}{{item.detail}}
						</view>
					</view>

                    <view style="display: flex;align-content: center;width: 17%;justify-content: space-between">
                        <view @click="delAddress(item.address_id)">
                            <image src="/static/images/cart/delete.png" style="width: 40rpx" mode="widthFix"></image>
                        </view>
                        <view @click="editAddress(item.address_id)">
                            <image src="/static/images/address/edit.png" style="width: 40rpx" mode="widthFix"></image>
                        </view>

                    </view>
					<!--<view class="icon-box plus d-c-c ml30" @click="delAddress(item.address_id)">
						<image class="add_icon_img" src="../../../static/icon/delete.png" mode=""></image>
					</view>
					<view class="none_line ml30"></view>
					<view class="icon-box plus d-c-c ml30 mr40" @click="editAddress(item.address_id)">
						<image class="add_icon_img" src="../../../static/icon/edit.png" mode=""></image>
					</view>-->

				</view>
			</view>
		</view>

        <view @click="addAddress()"  style="width: 85%;position: fixed;bottom: 20rpx;left: 50rpx; text-align: center;color: #FFFFFF;line-height: 70rpx;border-radius: 35rpx;background-color: #F63E36;" >
            新增收货地址
        </view>

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
				/*数据*/
				listData: [],
				/*默认地址id*/
				default_id: '0',
				options: {}
			}
		},
		onLoad: function(options) {
			this.options = options;
		},
		onShow: function() {
			uni.showLoading({
				title: '加载中'
			});
			/*获取地址列表*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				let dataType = self.dataType;
				self._get('user.address/lists', {}, function(res) {
					self.listData = res.data.list;
					self.default_id = res.data.default_id + '';
					self.loadding = false;
					uni.hideLoading();
				});
			},

			/*跳转页面*/
			addAddress() {
				let delta = 1;
				if (this.options.source === 'order') {
					delta = 2;
				}
				this.gotoPage('/pages/user/address/add/add?delta=' + delta);
			},

			/*点击单选*/
			radioChange(e) {
				let self = this;
				self.default_id = e;
				self._post('user.address/setDefault', {
					address_id: e,
				}, function(res) {
					if (self.options.source === 'order') {
						// #ifndef H5
						uni.navigateBack();
						// #endif
						// #ifdef H5
						history.go(-1);
						// #endif
					}
				});
				return false;

			},
            chosefun(item){
			    console.log(item)
                let self = this;
                if (self.options.source === 'order') {
                    uni.setStorageSync('orderAddress',item)
                    uni.navigateBack();
                }
            },

			/*编辑地址*/
			editAddress(e) {
				let delta = 1;
				if (this.options.source === 'order') {
					delta = 2;
				}
				this.gotoPage('/pages/user/address/edit/edit?address_id=' + e + '&delta=' + delta);
			},

			/*删除地址*/
			delAddress(e) {
				let self = this;
				wx.showModal({
					title: "提示",
					content: "您确定要移除当前收货地址吗?",
					success: function(o) {
						o.confirm && self._get('user.address/delete', {
							address_id: e
						}, function(result) {
							if (result.code == 1) {
								uni.showToast({
									title: '删除成功',
									duration: 2000
								});
								self.getData();
							}

						});
					}
				});

			}
		}
	}
</script>

<style>
    page{
        background: #F2F2F2;
        padding-bottom: 120upx;
    }
	.address-list {
		border-top: 16rpx solid #F2F2F2;
		padding: 0 20rpx;
		padding-bottom: 90rpx;
	}

	.foot-btns {
		padding: 0;
	}

	.foot-btns .btn-red {
		width: 100%;
		height: 90rpx;
		line-height: 90rpx;
		border-radius: 0;
	}

	.none_add {
		padding: 314rpx 214rpx 60rpx 214rpx;
	}

	.no_add {
		width: 322rpx;
		height: 180rpx;
	}

	.no_add_add {
		width: 320rpx;
		height: 80rpx;
		border: 2rpx solid #FFB7B7;
		border-radius: 40rpx;
		text-align: center;
		line-height: 80rpx;
		font-size: 32rpx;
		font-family: PingFang SC;
		font-weight: 500;
		color: #F6220C;
		margin: 0 auto;
	}

	.add_add {
		height: 64rpx;
		line-height: 64rpx;
		font-size: 26rpx;
		font-family: PingFang SC;
		font-weight: 500;
		color: #0777CD;
		padding: 0 35rpx;
		border-bottom: 1rpx solid #D9D9D9;
	}

	.defaul_add {
		padding: 9rpx 14rpx 10rpx 15rpx;
		background: #FFE7E4;
		font-size: 22rpx;
		font-family: PingFang SC;
		font-weight: 500;
		color: #F6220C;
	}

	.add_icon_img {
		width: 30rpx;
		height: 30rpx;
	}

	.none_line {
		width: 1rpx;
		height: 44rpx;
		background: #D9D9D9;
	}
</style>
