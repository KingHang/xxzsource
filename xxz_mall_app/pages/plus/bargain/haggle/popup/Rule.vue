<template>
	<view class="rule-detail-wrap" v-show="openRule">
		<view class="rule-bg" @click="closeRule" :class="openRule ? 'active' : ''"></view>
		<view class="rule-content" v-on:click.stop>
			<view class="title pr">
				<text class="">活动规则</text>
				<text class="iconfont icon-guanbi" @click="closeRule"></text>
			</view>
			<view class="content">{{ setting.bargain_rules }}</view>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			/*是否显示*/
			openRule: false
		};
	},
	props: ['isRule', 'setting'],
	watch: {
		isRule: function(n, o) {
			if (n != o) {
				this.openRule = n;
			}
		}
	},
	methods: {
		/*关闭规则*/
		closeRule() {
			this.openRule = false;
			this.$emit('close');
		}
	}
};
</script>

<style>
.rule-detail-wrap {
	position: fixed;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	width: 100%;
	z-index: 999;
}
.rule-detail-wrap .rule-bg {
	position: absolute;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	background-color: rgb(0, 0, 0);
	opacity: 0;
	transition: all 0.3s ease-out;
}
.rule-detail-wrap .rule-bg.active {
	opacity: 0.8;
}
.rule-detail-wrap .rule-content {
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
	background-color: #fff;
	border-top-left-radius: 20rpx;
	border-top-right-radius: 20rpx;
	transition: all 0.3s ease-out;
	box-sizing: border-box;
}
.rule-detail-wrap .rule-content .title {
	height: 100rpx;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 32rpx;
	position: relative;
	color: #333;
	box-sizing: border-box;
}
.rule-detail-wrap .rule-content .iconfont {
	position: absolute;
	right: 20rpx;
	top: 20rpx;
}
.rule-detail-wrap .rule-content .content {
	padding: 30rpx;
	max-height: 600rpx;
	font-size: 28rpx;
	line-height: 150%;
	overflow-y: auto;
}
</style>
