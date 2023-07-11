<template>
	<view class="diy-article">
		<!--文章列表-->
		<view class="article-item" v-for="(item, index) in listData" :class="'show-type__'+ itemData.style.display" :key="index"
		 @click="gotoPageFunc(item)">
			<!-- 有图模式 -->
			<template v-if="itemData.style.display == 10">
				<view class="d-b-c d-stretch d-c flex-1">
					<view class="">
						<view class="text-ellipsis f32">
							{{ item.article_title }}
						</view>
						<view class="des text-ellipsis mt10 f24 gray6">
							{{ item.dec }}
						</view>
					</view>
					<view class="d-s-c gray9 f26">
						<text>{{ item.create_time }}</text>
						<text class="ml30">阅读量:</text>
						<text class="ml10">{{ item.actual_views }}</text>
					</view>
				</view>
				<view class="picture ml20" v-if="item.image!=null&&item.image.file_path!=''">
					<image :src="item.image.file_path" mode="aspectFill"></image>
				</view>
			</template>
			<!-- 无图模式-->
			<template v-if="itemData.style.display == 20">
				<view class="f32 text-ellipsis lh200">
					{{ item.article_title }}
				</view>
			</template>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				/*数据列表*/
				listData: []
			};
		},
		props: ['itemData'],
		created() {
			this.listData = this.itemData.data;
		},
		methods: {
			/*跳转页面*/
			gotoPageFunc(e) {
				let url = 'pages/article/detail/detail?article_id=' + e.article_id;
				this.gotoPage(url);
			}
		}
	};
</script>

<style>
	.diy-article {
		background: #ffffff;
		margin: 20rpx;
		border-radius: 12rpx;
	}

	.diy-article .show-type__10,
	.diy-article .show-type__20 {
		display: flex;
		padding: 30rpx;
		border-bottom: 1rpx solid #D9D9D9;
	}

	.diy-article .show-type__10 .picture {
		width: 210rpx;
		height: 140rpx;
		border-radius: 12rpx;

	}

	.diy-article .show-type__10 .title {
		height: 80rpx;
	}

	.diy-article .show-type__10 .des {
		height: 36rpx;
	}

	.diy-article image {
		width: 100%;
		height: 100%;
		border-radius: 12rpx;
	}
</style>
