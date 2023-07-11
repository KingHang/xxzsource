<template>
	<view class="im_index">
		<view class="im_item" v-for="(item,index) in imList" :key="index" @click="jumpPage(item.user_id,item.user.nickName)">
			<view class="im_item_left">
				<image :src="item.user.avatarUrl" mode=""></image>
			</view>
			<view class="im_item_right">
				<view class="im_item_right_item">
					<view class="title">{{item.user.nickName}}</view>
					<view class="time">{{item.newMessage.create_time}}</view>
				</view>
				<view class="im_item_right_item">
					<view class="content" v-if="item.newMessage.type==0">{{item.newMessage.content}}</view>
					<view class="content" v-if="item.newMessage.type==1">[图片]</view>
					<view class="content" v-if="item.newMessage.type==2">[商品]</view>
					<view v-if="item.num>0" class="message">{{item.num}}</view>
				</view>
			</view>
		</view>
		<view class="hint" v-if="imList.length==0">
			您当前并没有与任何人聊天哦！
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				imList: [
				],
				user_id: '',
				official: '', //站内信数据
				logistic: '', //物流数据
			}
		},
		onShow() {
			this.get_im_list();
		},
		methods: {
			//获取聊天列表
			get_im_list() {
				let self = this;
				self._post('plus.chat.supplierChat/index', {
					
				}, (res) => {
					console.log(res)
					self.imList = res.data.list
					self.official = res.data.official
					self.logistic = res.data.logistic

				})
			},

			jumpPage(user_id,nickName) {
				this.gotoPage('/pages/plus/chat/supplier_chat?user_id=' + user_id+'&nickName='+nickName);
			},
		}
	}
</script>

<style>
	.im_index {
		width: 100%;
	}

	.im_item {
		width: 90%;
		height: 120rpx;
		margin: 0 auto;
		display: flex;
		align-items: center;
	}

	.im_item_left {
		box-sizing: border-box;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.im_item_left image {
		width: 80rpx;
		height: 80rpx;
		margin: 0 auto;
		margin-top: 5%;
		border-radius: 50%;
		background-color: rgba(0, 0, 0, 0.1);
	}

	.im_item_right {
		width: 555rpx;
		padding: 10rpx;
		border-bottom: 1px #dcdcdc solid;
		box-sizing: border-box;
		display: flex;
		flex-direction: column;
	}

	.message {
		width: 30rpx;
		height: 30rpx;
		border-radius: 50%;
		color: white;
		background-color: red;
		display: flex;
		justify-content: center;
		align-items: center;
		font-size: 15rpx;
	}

	.im_item_right_item {
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin-bottom: 10rpx;
	}

	.title {
		font-size: 32rpx;
	}


	.guangfang {
		font-size: 16rpx;
		color: #F36A24;
		border: 1rpx #F36A24 solid;
		border-radius: 10rpx;
		padding: 5rpx 10rpx;
		box-sizing: border-box;
		position: relative;
		left: -65rpx;
	}

	.time {
		font-size: 26rpx;
		color: #999999;
	}

	.content {
		font-size: 26rpx;
		color: #999999;
		overflow: hidden;
		text-overflow:ellipsis;
		white-space: nowrap;
		overflow:hidden;
		word-break:break-all;
	}

	.hint {
		width: 750rpx;
		text-align: center;
		font-size: 32rpx;
		color: #585858;
		margin-top: 20rpx;
	}
</style>