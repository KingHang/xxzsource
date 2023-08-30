<template>


    <view>

        <view style="background-color: white;padding: 30rpx">
            <u-input v-model="userInfo.weixin"></u-input>
        </view>

        <view class="setup-btn" @tap="submit()">保存</view>


    </view>



</template>

<script>
	export default {
		components: {},
		data() {
			return {
				userInfo: {}
			};
		},
		onShow() {
			/*获取个人中心数据*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				self._get('user.index/setting', {}, function(res) {
					self.userInfo = res.data.userInfo;
					
					console.log(self.userInfo)
					uni.hideLoading();
				});
			},
			gotoBind() {
				this.gotoPage("/pages/user/modify-phone/modify-phone");
			},
            submit() {
                let self = this;
                self._post(
                    'user.user/updateUser',
                    {
                        type:2,
                        weixin:self.userInfo.weixin
                    },
                    result => {
                        uni.showToast({
                            title: '修改成功',
                            duration: 2000
                        });
                        setTimeout(function(){
                            // 执行回调函数
                            uni.navigateBack();
                        }, 2000);
                    },
                    false,
                    () => {
                        uni.hideLoading();
                    }
                );

			},
		}
	};
</script>

<style>
    .tfont{
        font-size: 28rpx;
        color: #6E6564;
        
    }
	.address-form .key-name {
		width: 200rpx;
	}

	.address-form .btn-red {
		height: 88rpx;
		line-height: 88rpx;
		border-radius: 44rpx;
		box-shadow: 0 8rpx 16rpx 0 rgba(226, 35, 26, .6);
	}

	.setup-btn {
		position: fixed;
		bottom: 20rpx;
		left: 5%;
		width: 90%;
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 80rpx;
		background-color: #fd3826;
		color: #fff;
		font-size: 30rpx;
		display: flex;
		justify-content: center;
		margin: 0 auto;
	}
</style>
