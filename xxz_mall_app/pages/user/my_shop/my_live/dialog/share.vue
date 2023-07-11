<template>
	<view class="share-wrap">
		<Popup :show="isPopup" :width="width" :height="height" :backgroundColor="backgroundColor" :boxShadow="boxShadow" :padding="0" @hidePopup="hidePopupFunc">
			<view class="content ww100">
				<image :src="qrcode" mode="widthFix"></image>
				<button class="btn-rose-shadow mt20" type="default" @tap="savePosterImg">保存图片</button>
			</view>
			<view class="close-btns" @tap="hidePopupFunc(true)"><text class="icon iconfont icon-guanbi"></text></view>
		</Popup>
	</view>
</template>

<script>
import Popup from '@/components/uni-popup.vue';
export default {
	components: {
		Popup
	},
	data() {
		return {
			/*是否显示*/
			isPopup: false,
			/*展示类别*/
			type: 0,
			/*宽度*/
			width: 600,
			/*高度*/
			height: 800,
			/*背景颜色*/
			backgroundColor: 'none',
			/*阴影*/
			boxShadow: 'none',
			form: {},
			coupon: []
		};
	},
	props: ['qrcode'],
	created() {
		
	},
	mounted() {
		/*获取弹出层内容*/
		this.setData();
	},
	methods: {
		/*获取弹出层内容*/
		setData() {
			this.isPopup = true;
		},
		/*关闭弹窗*/
		hidePopupFunc(e) {
			this.isPopup = false;
			this.$emit('close');
		},
		
		//保存海报图片
		savePosterImg() {
			let self = this;
			uni.showLoading({
				title: '加载中'
			});
			// 下载海报图片
			uni.downloadFile({
				url: self.qrcode,
				success(res) {
					uni.hideLoading();
					// 图片保存到本地
					uni.saveImageToPhotosAlbum({
						filePath: res.tempFilePath,
						success(data) {
							uni.showToast({
								title: '保存成功',
								icon: 'success',
								duration: 2000
							});
							// 关闭商品海报
							self.isCreatedImg = false;
						},
						fail(err) {
							if (err.errMsg === 'saveImageToPhotosAlbum:fail auth deny') {
								uni.showToast({
									title: '请允许访问相册后重试',
									icon: 'none',
									duration: 1000
								});
								setTimeout(() => {
									uni.openSetting();
								}, 1000);
							}
						},
						complete(res) {
							console.log('complete');
						}
					});
				}
			});
		},
		
	}
};
</script>

<style scoped>
.share-wrap {
	width: 100%;
	border-radius: 20upx;
	overflow: hidden;
}

.share-wrap .content image{
	width: 100%;
}

.share-wrap .close-btns {
	margin: 50rpx auto 0;
	width: 60rpx;
	height: 60rpx;
	display: flex;
	justify-content: center;
	align-items: center;
	border-radius: 50%;
	background: none;
	border: 2px solid #ffffff;
}

.home-push-wrap .close-btns .iconfont {
	color: #ffffff;
}
</style>
