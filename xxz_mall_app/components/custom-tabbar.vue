<template>
	<view class="costom-tabbar">
		<view class="tabbar-item" @click="changeTabbar(0)">
			<image v-if="index===0" src="../static/images/activity/home-active.png" mode="aspectFill"></image>
			<image v-else src="../static/images/activity/home.png" mode="aspectFill"></image>
			<text :class="index===0?'active':''">首页</text>
		</view>
		<view class="tabbar-item" @click="changeTabbar(1)">
			<image v-if="index===1" src="../static/images/activity/activity-active.png" mode="aspectFill"></image>
			<image v-else src="../static/images/activity/activity.png" mode="aspectFill"></image>
			<text :class="index===1?'active':''">活动</text>
		</view>
		<view class="tabbar-item" @click="changeTabbar(2)">
			<image v-if="index===2" src="../static/images/activity/mine-active.png" mode="aspectFill"></image>
			<image v-else src="../static/images/activity/mine.png" mode="aspectFill"></image>
			<text :class="index===2?'active':''">我的</text>
		</view>
	</view>
</template>

<script>
	export default {
		name: "custom-tabbar",
		props: {
			tabIndex: {
				type: Number,
				default: 0
			},
		},
		data() {
			return {
				index: 0
			}
		},
		created() {
			this.index = this.tabIndex;
		},
		methods: {
			changeTabbar(index) {
				this.index = index;
				let path = ''
				if (index == 0) {
					path = '/pagesActivity/activity/home/index'
				} else if (index == 1) {
					path = '/pagesActivity/activity/activity/index'
				} else if (index == 2) {
					path = '/pagesActivity/activity/mine/index'
				}
				uni.redirectTo({
					url: path
				})
			},
		}
	}
</script>

<style lang="scss">
	.costom-tabbar {
		display: flex;
		justify-content: space-around;
		position: fixed;
		bottom: 0;
		z-index: 9999;
		width: 100%;
		height: 100rpx;
		background-color: #ffffff;
		border-top: 1rpx solid #F2F2F2;
		padding: 10rpx 0;
		.tabbar-item {
			text-align: center;
			image {
				width: 48rpx;
				height: 48rpx;
			}
			text {
				font-size: 20rpx;
			}
			.active {
				color: $main-color;
			}
		}
	}
</style>
