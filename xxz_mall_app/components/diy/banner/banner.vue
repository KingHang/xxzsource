<template>
	<view class="diy-banner-box">
		<swiper class="swiper" :indicator-dots="indicatorDots" :indicator-active-color="indicatorActiveColor" :autoplay="autoplay"
		 :interval="interval" :duration="duration">
			<swiper-item v-for="(item,index) in itemData.data" :key="index" @click="gotoPages(item)">
				<image :src="item.imgUrl"></image>
			</swiper-item>
		</swiper>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				indicatorActiveColor: '#ffffff'
			}
		},
		props: ['itemData'],
		created() {
			this.interval = this.itemData.params.interval;
			this.indicatorActiveColor = this.itemData.style.btnColor;
		},
		methods: {

			/*跳转页面*/
			gotoPages(e) {
				this.gotoPage(e.linkUrl);
			}
		}
	}
</script>

<style>
	.diy-banner-box {
		margin: 20rpx;
	}
	.diy-banner-box,
	.diy-banner-box .swiper {
		
		height: 188rpx;
	}

	.diy-banner-box image {
		/* margin: 20rpx 20rpx 0; */
		width: 100%;
		height: 188rpx;
		border-radius: 16rpx;
	}
</style>
