<template>

	<view class="bottom-panel" :class="Visible?'bottom-panel open':'bottom-panel close'" @click="closePopup">
		<view class="popup-bg">
		</view>
		<view class="content" v-on:click.stop>
			<view class="module-box module-share">
				<view class="hd d-c-c">
					赠品
				</view>
				<view class="title">
					购买即送以下商品
				</view>
				<scroll-view scroll-y="true" style="height: 600rpx;min-height: 300rpx;">
					<view class="box-s-b" v-for="(item,index) in product" :key="index">
						<view class="item">
							<image class="img" :src="item.product.image[0].file_path" mode="aspectFill"></image>
							<view class="good-right">
								<view class="name">{{item.product.product_name}}</view>
								<view class="num">{{item.giftSkuMsg.product_attr}}</view>
								<view class="num">赠品，件数x{{item.gift_num}}</view>
								<view class="price">￥0</view>
							</view>
						</view>
					</view>
				</scroll-view>
				<view class="btns">
					<button type="default" @click="closePopup">完成</button>
				</view>
			</view>

		</view>
	</view>

</template>

<script>
	export default {
		data() {
			return {
				/*是否可见*/
				Visible: false,
				poster_img: '',
			}
		},
		props: ['isGift', 'product'],
		watch: {
			'isGift': function(n, o) {
				if (n != o) {
					this.Visible = n;
				}
			}
		},
		methods: {
			/*关闭弹窗*/
			closePopup(type) {
				this.$emit('close', {
					type: type,
					poster_img:this.poster_img
				})
			},
		}
	}
</script>

<style lang="scss">
	.bottom-panel .popup-bg {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background: rgba(0, 0, 0, .6);
		z-index: 98;
	}
	.bottom-panel .popup-bg .wechat-box{ padding-top: var(--window-top);}
	.bottom-panel .popup-bg .wechat-box image{ width: 100%;}

	.bottom-panel .content {
		position: fixed;
		width: 100%;
		bottom: 0;
		min-height: 200rpx;
		max-height: 900rpx;
		background-color: #fff;
		transform: translate3d(0, 980rpx, 0);
		transition: transform .2s cubic-bezier(0, 0, .25, 1);
		bottom: env(safe-area-inset-bottom);
		z-index: 99;
	}

	.bottom-panel.open .content {
		transform: translate3d(0, 0, 0);
	}
	
	.content {
		padding: 0 30rpx;
		
		.title {
			font-size: 32rpx;
			color: #333333;
		}
		.item {
			display: flex;
			margin-top: 24rpx;
			.img {
				width: 200rpx;
				height: 200rpx;
				border-radius: 8rpx;
				margin-right: 16rpx;
			}
			.good-right {
				width: 65%;
			}
			.name {
				font-size: 28rpx;
				color: #1B1B1B;
				text-overflow:ellipsis;
				white-space: nowrap;
				overflow:hidden;
				word-break:break-all;
			}
			.num {
				color: #999999;
				font-size: 28rpx;
			}
			.price {
				font-size: 28rpx;
				color: #F63E36;
			}
		}
	}

	.bottom-panel.close .popup-bg {
		display: none;
	}

	.module-share .hd {
		height: 90rpx;
		line-height: 90rpx;
		font-size: 36rpx;
	}
	
	.module-share .item button,.module-share .item button::after{ background: none; border: none;}
	

	.module-share .icon-box {
		width: 100rpx;
		height: 100rpx;
		border-radius: 50%;
		background: #f6bd1d;
	}

	.module-share .icon-box .iconfont {
		font-size: 60rpx;
		color: #FFFFFF;
	}

	.module-share .btns {
		margin-top: 30rpx;
	}

	.module-share .btns button {
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 0;
		background: #F63E36;
		border-radius: 56rpx;
		color: #ffffff;
		margin-bottom: 150rpx;
	}

	.module-share .btns button::after {
		border-radius: 0;
	}

	.module-share .share-friend {
		background: #04BE01;
	}
	.icon-tijiaochenggong{
		width: 28rpx;
		height: 28rpx;
		line-height: 28rpx;
		text-align: center;
		font-size: 20rpx;
		color: #FF6633;
		border-radius: 50%;
		border: 1rpx solid #ff6633;
		margin-top: 7rpx;
		flex-shrink:initial;
	}
	.mb10{
		margin-bottom: 10rpx;
	}
</style>
