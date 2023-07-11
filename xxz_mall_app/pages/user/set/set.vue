<template>
	<view class="address-form">
		<view class="bg-white p-0-30 f30">
			<view class="d-b-c p-30-0 border-b">
				<text class="key-name">会员ID</text>
				<view class="d-e-c">
					<text class="mr20">{{userInfo.user_id}}</text>
				</view>
			</view>
			<view class="d-b-c p-30-0 border-b">
				<text class="key-name">昵称</text>
				<view class="d-e-c">
					<text class="mr20">{{userInfo.nickName}}</text>
				</view>
			</view>
			<view class="d-b-c p-30-0">
				<text class="key-name">手机号码</text>
				<view class="d-e-c" v-if="userInfo.mobile">
					<text class="mr20">{{userInfo.mobile}}</text>
				</view>
				<view class="d-e-c" v-if="!userInfo.mobile" @click="gotoBind">
					<text class="mr20">未绑定</text>
					<text class="iconfont icon-jiantou"></text>
				</view>
			</view>
			<view class="setup-btn" @tap="logout()">退出登录</view>
		</view>
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
					uni.hideLoading();
				});
			},
			gotoBind() {
				this.gotoPage("/pages/user/modify-phone/modify-phone");
			},
			logout() {
				uni.removeStorageSync('token');
				uni.removeStorageSync('user_id');
				this.gotoPage('/pages/index/index');
			},
		}
	};
</script>

<style>
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
