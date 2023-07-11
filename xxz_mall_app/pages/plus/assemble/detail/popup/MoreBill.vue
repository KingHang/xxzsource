<template>
	<Popup :show="isPopup" :width="width" :height="height" type="middle" :backgroundColor="backgroundColor" :boxShadow="boxShadow" :padding="0" @hidePopup="hidePopupFunc">
		<view class="more-bill-box">
			<view class="d-c-c p30 f30 border-b">
				正在拼团
			</view>
			<view class="list">
				<view class="item d-b-c p20 border-b" v-for="(item,index) in bill" :key="index" v-if="index<10">
					<view class="d-s-c">
						<view class="photo">
							<image :src="item.user.avatarUrl" mode="widthFix"></image>
						</view>
						<view class="d-s-c ml20 d-c lh150">
							<view class="d-s-c ww100">
								<text>{{ item.user.nickName }}</text>
								<text class="ml10">还差{{ item.dif_people }}人</text>
							</view>
							<view class="ww100 gray9">
								<Countdown :config="rturnObjec(item)"></Countdown>
							</view>
						</view>
					</view>
					<view class="">
						<button class="btn-red" type="default"  @click="goBill(item)">去拼单</button>
					</view>
				</view>
				<view class="d-c-c p30 gray9 f28" v-if="bill.length>10">
					仅显示10个拼单
				</view>
			</view>
		</view>
	</Popup>
</template>

<script>
	import Popup from '@/components/uni-popup.vue';
	import Countdown from '@/components/countdown/countdown.vue';
	export default{
		components:{
			Popup,
			Countdown
		},
		data(){
			return{
				/*是否显示*/
				isPopup: false,
				/*展示类别*/
				type: 0,
				/*宽度*/
				width: 600,
				/*高度*/
				height: 1200,
				/*背景颜色*/
				backgroundColor: 'none',
				/*阴影*/
				boxShadow: 'none',
			}
		},
		props:['ismore','bill'],
		watch:{
		},
		created() {
			this.isPopup=this.ismore;
		},
		methods:{
			
			/*转换参数*/
			rturnObjec(item){
				return {type:'text',startstamp:0,endstamp:item.end_time};
			},
			
			/*关闭弹窗*/
			hidePopupFunc(){
				this.$emit('closeMore',false)
			},
			
			/*去拼单*/
			goBill(e) {
				this.$emit('gobill', e);
			}
			
		}
	}
</script>

<style>
	.more-bill-box{ width: 100%; background: #FFFFFF;}
	.more-bill-box .list{ max-height: 660rpx; overflow-y: auto; box-shadow: 0 -10rpx 30rpx 0 inset rgba(255,255,255,.5);}
	.more-bill-box .photo{ width: 60rpx; height: 60rpx; border-radius: 50%; overflow: hidden;}
	.more-bill-box .photo image{ width: 100%; height: 100%;}
</style>
