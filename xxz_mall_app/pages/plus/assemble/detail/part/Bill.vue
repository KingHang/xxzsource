<template>
	<view class="bg-white m20" v-if="bill.length > 0">
		<view class="group-hd p-0-20 d-a-c border-b">
			<view class="left"><text class="f30">这些人刚刚购买成功，可参与拼单</text></view>
			<view class="right" @click="openMore">
				<text>查看更多</text>
				<text class="iconfont icon-jiantou white"></text>
			</view>
		</view>
		<view class="group-bd bill-user-list">
			<view class="item d-b-c p20" v-for="(item, index) in bill" :key="index">
				<view class="userinfo d-s-c">
					<view class="photo">
						<image :src="item.user.avatarUrl" mode="widthFix"></image>
					</view>
					<text class="ml10">{{ item.user.nickName }}</text>
				</view>
				<view class="btns d-s-c">
					<view class="d-s-c d-c">
						<text class="ml10">还差{{ item.dif_people }}人成团</text>
						<view class="gray9">
							<Countdown :config="rturnObjec(item)"></Countdown>
						</view>
					</view>
					<button type="primary" class="btn-red ml20" @click="goBill(item)">去拼单</button>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import Countdown from '@/components/countdown/countdown.vue';
	export default {
		components: {
			Countdown
		},
		data() {
			return {

			};
		},
		props: ['bill'],
		created() {

		},
		methods: {

			/*转换参数*/
			rturnObjec(item) {
				return {
					type: 'text',
					startstamp: 0,
					endstamp: item.end_time
				};
			},

			/*查看更多*/
			openMore() {
				this.$emit('openMore', true);
			},

			/*去拼单*/
			goBill(e) {
				this.$emit('gobill', e);
			}
		}
	};
</script>

<style>
	.bill-user-list {
		max-height: 240rpx;
		overflow: hidden;
	}

	.bill-user-list .photo {
		width: 80rpx;
		height: 80rpx;
		border-radius: 50%;
		overflow: hidden;
	}

	.bill-user-list .photo image {
		width: 100%;
		height: 100%;
	}
</style>
