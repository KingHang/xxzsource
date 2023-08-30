<template>
	<view class="">
		<!--文章列表-->

        <view class="hlbblock30" v-for="(item, index) in listData" @click="gotoPageFunc(item)">
            <view class="fsc">
                <block v-if="itemData.style.display==10">
                    <view style="width: 70%">
                        <view style="color: #6E6564;font-size: 32rpx">
                            {{ item.article_title }}
                        </view>
                        <view style="color: #999999;font-size: 24rpx">
                            {{ item.dec }}
                        </view>
                    </view>
                    <view style="width: 30%">
                        <image :src="item.image.file_path" style="width: 200rpx;height: 150rpx;border-radius: 12rpx" mode="aspectFill"></image>
                    </view>
                </block>
                <block v-else>
                    <view style="width: 100%">
                        <view style="color: #6E6564;font-size: 32rpx">
                            {{ item.article_title }}
                        </view>
                        <view style="color: #999999;font-size: 24rpx">
                            {{ item.dec }}
                        </view>
                    </view>
                </block>
            </view>
            <view class="fsc" style="margin-top: 20rpx">
                <view style="color: #999999;font-size: 24rpx">
                    阅读量:{{ item.actual_views }}
                </view>
                <view style="color: #999999;font-size: 24rpx">
                    {{ item.create_time }}
                </view>
            </view>
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
				let url = 'pagesArticle/article/detail/detail?article_id=' + e.news_id;
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
