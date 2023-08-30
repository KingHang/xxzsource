<template>
	<view class="refund-apply pb100">
		<form @submit="formSubmit" @reset="formReset">
			<!--<view class="one-product d-s-c p30 bg-white ">
				<view class="cover">
					<image :src="product.image.file_path" mode="aspectFit"></image>
				</view>
				<view class="flex-1">
					<view class="pro-info">{{product.product_name}}</view>
					<view class="pt10 p-0-30 f24 gray9">
						<text class="">
							单价：¥{{product.line_price}}
						</text>
						<text class="ml20">
							数量：{{product.total_num}}
						</text>
					</view>
				</view>
			</view>-->

            <view class="fui-cell-group hlbblock" >
                <view  style="display: flex;justify-content: space-between;align-items: center;padding: 30rpx">
                    <view style="display: flex;align-items: center">
                        <view>
                            <image v-if="type==10" src="/static/images/order/refund/refund.png" style="width: 50rpx;" mode="widthFix"></image>
                            <image v-if="type==20" src="/static/images/order/refund/change.png" style="width: 50rpx;" mode="widthFix"></image>
                            <image v-if="type==30" src="/static/images/order/refund/money.png" style="width: 50rpx;" mode="widthFix"></image>
                        </view>
                        <view   @click="refundpop=true" style="margin-left: 20rpx">
                            <view style="font-size: 28rpx">
                              <view v-if="type==10">退货/退款 </view>
                              <view v-if="type==20">换货 </view>
                              <view v-if="type==30">仅退款 </view>
                            </view>
                            <view>
                                <text style="color: #999999;font-size: 24rpx">
                                    <block v-if="type==30">没收到货，或与卖家协商同意不用退货只退款</block>
                                    <block v-if="type==20">商品存在质量问题，联系卖家协商换货</block>
                                    <block v-if="type==10">已收到货，需要退还收到的货物</block>
                                </text>
                            </view>
                        </view>
                    </view>
                    <view>
                        <u-icon  name="arrow-right" color="#8e8e8e"></u-icon>
                    </view>
                </view>
            </view>

            <view class="fui-cell-group hlbblock" >
                <view style="padding: 30rpx;">
                    退款信息
                </view>
                <view class="fui-cell" v-if="type==10 || type==30">
                    <view class="fui-cell-label">退款金额</view>
                    <view class="fui-cell-info" style="display: -webkit-box;  display: -webkit-flex;  display: -ms-flexbox;  display: flex;  -webkit-box-align: center;-webkit-align-items: center;  -ms-flex-align: center;  align-items: center; ">
                        <text  style="color: #F63E36">￥</text>
                        <input bindchange="change" class="'fui-input '" name="price" style="color: #F63E36" type="text" :value="product.total_price" ></input>
                    </view>
                </view>
                <view style="font-size: 24rpx;color: #999999;margin-left: 30rpx" >
                    <text class="">可修改，最多￥{{product.total_price}}
                    </text>
                </view>
            </view>

            <view class="fui-cell-group hlbblock30" >
                <view style="">
                    退款说明和凭证
                </view>
                <view style="background-color: #F8F8F8;padding: 20rpx;margin-top: 20rpx">
                    <input  style="font-size: 24rpx" name="content" placeholder="选填(退款说明)" type="text" ></input>

                    <view  style="margin-top: 20rpx;display: flex" >
                        <view v-if="images.length<6" @click="openUpload()" data-type="image" style="width: 110rpx;height: 110rpx;border: 1px dashed #C9C9C9;text-align: center">
                            <image src="/static/images/order/refund/camara.png" style="width: 50rpx;display: inline-block;margin-top: 10rpx" mode="widthFix"></image>
                            <view style="color: #999999;font-size: 20rpx">
                                上传凭证
                            </view>
                        </view>
                        <view class="fui-images fui-images-sm">
                            <block >
                                <view style="position: relative;display: inline-block;width: 110rpx;height: 110rpx;margin-left: 20rpx" v-for="(imgs,img_num) in images" :key="img_num" @click="deleteFunc(imgs)">
                                    <image style="width: 110rpx;height: 110rpx;"   class="image image-sm" :data-index="index" data-type="image-preview" :src="imgs.file_path" ></image>
                                    <text style="position: absolute;top: -10rpx;right: -5rpx"  class="image-remove" :data-index="index" data-type="image-remove">
                                        <text class="icon icox-close"></text>
                                    </text>
                                </view>
                            </block>
                        </view>
                    </view>
                </view>
            </view>



            <!--申请原因-->
		<!--	<view class="group bg-white">
				<view class="group-hd">
					<view class="left">
						<text class="min-name">申请原因</text>
					</view>
				</view>
				<view class="d-s-c">
					<textarea class="p10 box-s-b border flex-1 f28 lh150" value="" name="content" placeholder="请详细填写申请原因，注意保持商品的完好，建议您先与卖家沟通" />
					</view>
            </view>
-->
            <!--退款金额-->
         <!--   <view class="group bg-white" v-if="type==10 || type==30">
                <view class="group-hd">
                    <view class="left">
                        <text class="min-name">退款金额：</text>
                        <text class="red f30">¥{{product.total_price}}</text>
                    </view>
                </view>
            </view>-->

            <!--上传图片-->
          <!--  <view class="group bg-white">
                <view class="group-hd">
                    <view class="left">
                        <text class="min-name">上传凭证</text>
                        <text class="gray9">(最多6张)</text>
                    </view>
                </view>
                <view class="upload-list d-s-c">
                    <view class="item" v-for="(imgs,img_num) in images" :key="img_num" @click="deleteFunc(imgs)">
                        <image :src="imgs.file_path" mode="aspectFit"></image>
                    </view>
                    <view class="item d-c-c d-stretch" v-if="images.length<6">
                        <view class="upload-btn d-c-c d-c flex-1" @click="openUpload()">
                            <text class="icon iconfont icon-xiangji f34"></text>
                            <text class="gray9">上传图片</text>
                        </view>
                    </view>
                </view>
            </view>-->

            <view class="foot-btns">
                <button class="hlbbutton" form-type="submit" >确认提交</button>
            </view>
        </form>

        <u-popup v-model="refundpop" mode="bottom" border-radius="14">
            <view style="padding: 30rpx">
                <view style="text-align: center;margin-bottom: 30rpx">
                    选择处理方式
                </view>
                    <view @click="tabType(10)"  style="display: flex;justify-content: space-between;align-items: center;margin-top: 20rpx">
                        <view style="display: flex;align-items: center">
                            <view>
                                <image src="/static/images/order/refund/refund.png" style="width: 50rpx;" mode="widthFix"></image>
                            </view>
                            <view style="margin-left: 20rpx">
                                <view style="font-size: 28rpx">
                                    退货/退款
                                </view>
                                <view>
                                    <text style="color: #999999;font-size: 24rpx">
                                        <block >已收到货，需要退还收到的货物</block>
                                    </text>
                                </view>
                            </view>
                        </view>
                        <view>
                            <u-icon name="arrow-right" color="#8e8e8e"></u-icon>
                        </view>
                    </view>
                <view @click="tabType(20)"  style="display: flex;justify-content: space-between;align-items: center;margin-top: 20rpx">
                    <view style="display: flex;align-items: center">
                        <view>
                            <image src="/static/images/order/refund/change.png" style="width: 50rpx;" mode="widthFix"></image>
                        </view>
                        <view style="margin-left: 20rpx">
                            <view style="font-size: 28rpx">
                                换货
                            </view>
                            <view>
                                <text style="color: #999999;font-size: 24rpx">
                                    <block >商品存在质量问题，联系卖家协商换货</block>
                                </text>
                            </view>
                        </view>
                    </view>
                    <view>
                        <u-icon name="arrow-right" color="#8e8e8e"></u-icon>
                    </view>
                </view>
                <view @click="tabType(30)"  style="display: flex;justify-content: space-between;align-items: center;margin-top: 20rpx">
                    <view style="display: flex;align-items: center">
                        <view>
                            <image src="/static/images/order/refund/money.png" style="width: 50rpx;" mode="widthFix"></image>
                        </view>
                        <view style="margin-left: 20rpx">
                            <view style="font-size: 28rpx">
                                仅退款
                            </view>
                            <view>
                                <text style="color: #999999;font-size: 24rpx">
                                    <block >没收到货，或与卖家协商同意不用退货只退款</block>
                                </text>
                            </view>
                        </view>
                    </view>
                    <view>
                        <u-icon name="arrow-right" color="#8e8e8e"></u-icon>
                    </view>
                </view>



            </view>
        </u-popup>

        <!--上传图片-->
		<Upload v-if="isUpload" @getImgs="getImgsFunc"></Upload>
    </view>


</template>

<script>
	import Upload from '@/components/upload/upload.vue';
    export default {
		components: {
			Upload
		},
        data() {
            return {
				/*是否加载完成*/
				loadding: true,
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
                type: 10,
				/*是否打开上传图片*/
				isUpload:false,
                /*订单商品id*/
                order_product_id: 0,
                /*订单商品*/
                product: {},
				images:[],
				/*小程序订阅消息*/
				temlIds: [],
                refundpop:false,
            }
        },
        onLoad(e){
            this.order_product_id = e.order_product_id;
        },
        mounted() {
            /*获取订单详情*/
            this.getData();
        },
        methods: {
            /*获取数据*/
            getData(){
                let self = this;
				uni.showLoading({
					title: '加载中'
				});
                let order_product_id = self.order_product_id;
                self._get( 'user.refund/apply',  {
                        order_product_id: order_product_id,
						platform: self.getPlatform()
                    },  function (res)  {
                        self.product = res.data.detail;
						self.temlIds = res.data.template_arr;
						if(self.product.orderM.delivery_type.value==30){
							self.type = 30;
						}
						uni.hideLoading();
                    }
                );
            },
            /*切换服务类型*/
            tabType(e){
                this.type = e;
                this.refundpop=false
            },
			
            /*提交表单*/
            formSubmit: function (e)
            {
                console.log(e)
                let self = this;
                var formdata = e.detail.value;
                formdata.type = self.type;
                formdata.order_product_id = self.order_product_id;
				formdata.images= JSON.stringify(self.images);
                let callback = function(){
					uni.showLoading({
						title: '正在提交',
						mask: true
					});
					self._post('user.refund/apply', formdata, function (res)
					{
						uni.hideLoading();
						uni.showToast({
							title: res.msg,
							duration: 3000,
							complete:function(){
								self.gotoPage('/pages/index/order/myorder?dataType=refund');
							}
						});
					});
				} 
				self.subMessage(self.temlIds, callback);
            },
			
			/*打开上传图片*/
			openUpload(){
				this.isUpload=true;
			},
			
			/*获取上传的图片*/
			getImgsFunc(e){
				let self=this;
				self.isUpload=false;
				if(e&&typeof(e)!='undefined'){
					let this_length=self.images.length,
					upload_length=e.length;
					if(this_length+upload_length<7){
						self.images=self.images.concat(e);
					}else{
						let leng=6-this_length;
						for(let i=0;i<leng;i++){
							self.images.push(e[i]);
						}
					}
				}
			},
			
			/*删除图片*/
			deleteFunc(e){
				this.images.splice(e,1);
			}
        }
    }
</script>

<style>

</style>
