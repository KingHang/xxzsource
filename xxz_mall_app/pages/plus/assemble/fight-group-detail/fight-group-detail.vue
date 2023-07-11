<template>
	<view class="fight-group-detail" v-if="!loadding">
		<!--产品-->
		<view class="product-info p30 d-s-c bg-white">
			<view class="product-cover">
				<image :src="productData.product.image[0].file_path" mode="aspectFit"></image>
			</view>
			<view class="info flex-1 ml30 f30">
				<text>{{productData.product.product_name}}</text>
				<view class="mt20">
					<text>{{productData.assemble_num}}人团：</text>
					<text class="ml30 red">￥</text>
					<text class="fb red f34">{{productData.assembleSku[0].assemble_price}}</text>
				</view>
			</view>
		</view>
		
		<!--拼团人-->
		<view class="fight-users mt20 bg-white">
			<!--成员列表-->
			<view class="user-list d-c-c">
				<view class="user-box pr" v-for="(item,index) in billData.billUser" :key="index">
					<text class="leader" v-if="index==0">团长</text>
					<image :src="item.user.avatarUrl" mode="aspectFit"></image>
				</view>
				<view class="user-box user-who d-c-c">
					?
				</view>
			</view>
			<view class="d-c-c mt30 gray3">
				还差<text class="fb">{{dif_people}}</text>人成团
			</view>
			<view class="mt20 d-c-c gray6 f34">
				<Countdown :config="countdownConfig" @returnVal="returnValFunc"></Countdown>
			</view>
		</view>
		
		<view class="leader-info d-b-c p30">
			<view class="photo d-s-c">
				<image :src="billData.user.avatarUrl" mode="aspectFit"></image>
				<text class="ml20">{{billData.user.nickName}}</text>
			</view>
			<view class="gray9">
				<text>{{billData.create_time}}</text>
				<text class="ml10">开团</text>
			</view>
		</view>
		
		<!--底部按钮-->
		<view class="fight-group-btns">
			<button v-if="!reallyAssemble" type="default" class="btn-red" @click="gotoAssemble(billData)">去拼团</button>
			<button v-else type="default" class="btn-red" open-type="share" @click="shareFunc">分享拼团</button>
		</view>
		
		<!--分享-->
		<share :isshare="isshare" :product_id="product_id" @close="closeShare"></share>
		<!--app分享-->
		<AppShare :isAppShare="isAppShare" :appParams="appParams" @close="closeAppShare"></AppShare>
	</view>
</template>

<script>
	import Countdown from '@/components/countdown/countdown.vue';
	import share from './popup/share.vue';
	import AppShare from '@/components/app-share.vue';
	export default {
		components:{
			Countdown,
			share,
			AppShare
		},
		data() {
			return {
				/*是否正在加载*/
				loadding:true,
				/*拼团组ID*/
				assemble_bill_id:null,
				/*拼团组对象*/
				billData:{},
				/*商品对象*/
				productData:{},
				/*倒计时配置*/
				countdownConfig: {
					type:'text',
					/*开始时间*/
					startstamp: 0,
					/*结束时间*/
					endstamp: 0
				},
				/*是否分享*/
				isshare:false,
				/*还差几人*/
				dif_people: 0,
				/*是否已经参团*/
				reallyAssemble:false,
				/*app分享*/
				isAppShare: false,
				appParams: {
					title: '',
					summary: '',
					path: ''
				}
			}
		},
		onLoad(e) {
			this.assemble_bill_id = e.assemble_bill_id;
			//#ifdef H5
			if (this.isWeixin()) {
				this.url = window.location.href;
			}
			//#endif
		},
		mounted() {
			/*获取产品详情*/
			this.getData();
		},
		methods: {
			
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				self._get(
					'plus.assemble.bill/detail',
					{
						assemble_bill_id: self.assemble_bill_id,
						url: self.url
					},
					function(res) {
						self.billData=res.data.bill;
						self.productData=res.data.product
						self.countdownConfig.startstamp=0;
						self.countdownConfig.endstamp=self.billData.end_time;
						self.dif_people = self.productData.assemble_num-self.billData.billUser.length<0?0:self.productData.assemble_num-self.billData.billUser.length
						// 配置微信分享参数
						//#ifdef H5
						if (self.url != '') {
							let params = {
								assemble_bill_id: self.assemble_bill_id
							};
							self.configWx(res.data.share.signPackage, res.data.share.shareParams, params);
						}
						//#endif
						console.log(self.billData.billUser);
						self.reallyAssemble=self.hasAssemble(self.billData.billUser);
						
						self.loadding = false;
						uni.hideLoading();
					}
				);
			},
			
			/*查看是否已经拼团*/
			hasAssemble(list){
				let flag=false;
				let user_id = this.getUserId();
				for(let i=0;i<list.length;i++){
					if(user_id==list[i].user_id){
						flag=true;
						break;
					}
				}
				return flag;
			},
			
			/*分享*/
			shareFunc(){
				let self = this;
				//#ifndef APP-PLUS
				self.isshare = true;
				//#endif
				//#ifdef APP-PLUS
				self.appParams.title = '【仅限' + self.dif_people + '个名额】，快来参与拼团吧';
				self.appParams.summary = self.productData.product.product_name;
				// 构建页面参数
				let params = self.getShareUrlParams({
					assemble_bill_id: self.assemble_bill_id
				});
				self.appParams.path = '/pages/plus/assemble/fight-group-detail/fight-group-detail?' + params;
				self.appParams.image = self.productData.product.image[0].file_path;
				self.isAppShare = true;
				//#endif
			},
			/*关闭分享*/
			closeShare(){
				this.isshare=false;
			},
			//关闭分享
			closeAppShare(data) {
				this.isAppShare = false;
			},
			/*返回状态*/
			returnValFunc(){},
			/*小程序分享*/
			onShareAppMessage() {
				let self = this;
				// 构建页面参数
				let params = self.getShareUrlParams({
					assemble_bill_id: self.assemble_bill_id
				});
				return {
					title: '【仅限'+self.dif_people+'个名额】，快来参与拼团吧',
					path: '/pages/plus/assemble/fight-group-detail/fight-group-detail?' + params
				};
			},
			
			/*去拼团*/
			gotoAssemble(e){
				let url='pages/plus/assemble/detail/detail?assemble_product_id=' + e.assemble_product_id+'&assemble_bill_id='+e.assemble_bill_id
				this.gotoPage(url);
			}
		}
	}
</script>

<style>
.fight-group-detail{ padding-bottom: 100rpx;}
.fight-group-detail .product-cover image{ width: 200rpx; height: 200rpx;}
.fight-group-detail .fight-users .user-box{ width: 80rpx; height: 80rpx; margin: 10rpx; border-radius: 50%; border: 1px dashed #CCCCCC;}
.fight-group-detail .fight-users{ padding: 30rpx;}
.fight-group-detail .fight-users .user-box image{width: 80rpx; height: 80rpx;border-radius: 50%;}
.fight-group-detail .fight-users .user-box .leader{ position: absolute; top: -20rpx; left: 50%; margin-left: -30rpx; width: 60rpx; height: 30rpx; line-height: 30rpx; text-align: center; color: #FFFFFF; border-radius: 30rpx; border: 1px solid #FFFFFF; background: red;}
.fight-group-detail .fight-users .user-box.user-who{ font-size: 50rpx; color: #999999;}
.fight-group-detail .leader-info .photo image{ width: 60rpx; height: 60rpx; border-radius: 50%; }
.fight-group-btns{ position: fixed; right: 0; bottom: 0; left: 0; padding: 0 30rpx; box-sizing: border-box;}
.fight-group-btns button.btn-red{ margin-bottom: 20rpx; height: 80rpx; line-height: 80rpx; border-radius: 40rpx;}
</style>
