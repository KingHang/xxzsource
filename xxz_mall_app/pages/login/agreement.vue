<template>
	<view class="agreement">
		<view class="title">{{info.agreement_title}}</view>
		<view class="content">
			<rich-text :nodes="info.agreement_content"></rich-text>
		</view>
	</view>
</template>

<script>
	var _this;
	export default {
		data() {
			return {
				info: ''
			}
		},
		components:{
		},
		onLoad() {
			_this= this;
			_this.getInfo()
		},
		onReady() {
		},
		mounted() {
			
		},
		methods: {
			getInfo() {
				uni.showLoading({
				    title: '加载中',
				    mask:true
				});
				
				_this._post(
				    'setting/agreementList', {
						keyword: 'register'
				    },
				    function(res) {
				        uni.hideLoading()
				        _this.info = res.data.list.data[0]
				    }
				);
			}
		}
	}
</script>

<style>
	.agreement {
		color: #333333;
		margin: 30rpx;
	}
	.title {
		font-size: 34rpx;
		font-weight: bold;
		text-align: center;
	}
	.content {
		font-size: 30rpx;
	}
</style>