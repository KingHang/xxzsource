<template>
	<view class="order-refund-detail pb100">
        <view class="hlbblock" style="padding: 20rpx">
            <view>
                <u-steps :list="line" :current="current" mode="dot" active-color="#f53630" un-active-color="#999999"></u-steps>
            </view>
			
            <view v-if="detail.is_agree.value == 0 && detail.status.value != 30" @click="cancelorder" style="text-align: center;color: #F63E36;border-top: 1px #F2F2F2 solid;margin-top: 15rpx;padding-top: 15rpx">
                取消申请
            </view>
			
            <view v-if="detail.status.value == 0 && detail.is_agree.value == 10 && detail.is_user_send == 0" @click="formshow = true" style="text-align: center;color: #F63E36;border-top: 1px #F2F2F2 solid;margin-top: 15rpx;padding-top: 15rpx">
                填写物流
            </view>

            <u-popup v-model="formshow" mode="center" border-radius="20" width="90%">
                <form @submit="formSubmit"  style="padding: 20rpx">
                    <view style="text-align: center;font-size: 34rpx;font-weight: bold">
                        填写物流
                    </view>
                    <view style="padding: 20rpx">
                        <view style="display: flex;margin-top: 30rpx">
                            <view style="width: 30%">
                                快递公司
                            </view>
                            <view class="fsc" style="width: 70%">
                                <view>
                                    <picker mode="selector" @change="onExpressChange" :range="expressList"
                                            range-key="express_name" :value="index">
                                        <text v-if="index > -1 ">{{expressList[index].express_name}}</text>
                                        <text v-else class="col-80" style="color: #C9C9C9">请选择</text>
                                    </picker>
                                </view>
                                <view>
                                    <u-icon name="arrow-down"></u-icon>
                                </view>
                            </view>
                        </view>
                        <view style="display: flex;margin-top: 30rpx">
                            <view style="width: 30%">
                                快递单号
                            </view>
                            <view style="width: 70%">
                                <input  placeholder="请填写物流单号" name="express_no"></input>
                            </view>
                        </view>
                    </view>
                    <view class="fsc" style="margin-top: 20rpx">
                        <view @click="formshow = false" class="hlbbuttonempty" style="width: 45%">
                            取消
                        </view>
                        <button formType="submit" class="hlbbutton" style="width: 45%">
                            确定
                        </button>
                    </view>
                </form>

              <!--  <form @submit="formSubmit"  report-submit>
                    <view class="group bg-white">
                        <view class="p-20-0 border-b f34">
                            填写物流信息
                        </view>
                        <view class="p-20-0 d-s-c">
                            <view class="gray9">物流公司：</view>
                            <view class="flex-1 p20 border">
                                <picker mode="selector" @change="onExpressChange" :range="expressList"
                                        range-key="express_name" :value="index">
                                    <text v-if="index > -1 ">{{expressList[index].express_name}}</text>
                                    <text v-else class="col-80">请选择物流公司</text>
                                </picker>
                            </view>
                        </view>
                        <view class="p-20-0 d-s-c">
                            <view class="gray9">物流单号：</view>
                            <view class="flex-1 border">
                                <input class="p10" placeholder="请填写物流单号" name="express_no"></input>
                            </view>
                        </view>
                        <view class="mt20">
                            <button class="btn-red" formType="submit">确认发货</button>
                        </view>
                    </view>
                </form>-->

            </u-popup>
        </view>

        <view class="hlbblock" style="padding: 20rpx">
            <view style="margin-bottom: 20rpx">
                退款信息
            </view>
                <view style="color: #999999;font-size: 24rpx;line-height: 60rpx;border-top: 1px #F2F2F2 solid; ">
                    退款编号：{{detail.order_master.order_no}}
                </view>
            <view style="color: #999999;font-size: 24rpx;line-height: 60rpx">
                申请时间：{{detail.create_time}}
            </view>
            <view style="color: #999999;font-size: 24rpx;line-height: 60rpx">
                退款原因：{{detail.apply_desc}}
            </view>
            <view style="color: #999999;font-size: 24rpx;line-height: 60rpx">
                退款金额：￥{{ detail.refund_money }}
            </view>
        </view>
		
        <view v-if="list.length > 0" class="hlbblock" style="padding: 20rpx">
            <view style="margin-bottom: 20rpx">
            处理进度
            </view>
            <view v-for="(item, index) in list" :key="index" style="display: flex">
                <view>
                    <u-icon name="checkmark-circle" size="33" :color="index === 0 ? '#F63E36' : '#999999'"></u-icon>
                </view>
                <view style="margin-left: 10rpx">
                    <view>
                        {{ item.desc }}
                    </view>
                    <view style="color: #999999;font-size: 22rpx">
                        {{ item.create_time }}
                    </view>
                </view>
            </view>
        </view>

        <!--售后状态-->
		<!--<view class="order-state d-s-c">
			<view class="icon-box"><span class="icon iconfont icon-gantanhao"></span></view>
			<view class="state-cont flex-1">
				<view class="state-txt d-b-c">
					<text class="desc f34">{{detail.state_text}}</text>
				</view>
			</view>
			<view class="dot-bg"></view>
		</view>-->
<!--
		&lt;!&ndash;商品信息&ndash;&gt;
		<view class="p30 mt20 bg-white" style="display: none">
			<view class="one-product border-b-e d-s-c pb20">
				<view class="cover">
					<image :src="detail.orderproduct.image.file_path" mode="aspectFit"></image>
				</view>
				<view class="flex-1">
					<view class="pro-info">{{detail.orderproduct.product_name}}</view>
					<view class="pt10 p-0-30">
						<text class="f24 gray9">
							{{detail.orderproduct.product_attr}}
						</text>
					</view>
				</view>
			</view>
			<view class="d-e-c pt20 lh150">
				<view class="f24">
					商品金额：
					<text class="red">¥{{detail.orderproduct.total_price}}</text>
				</view>
			</view>
			<view class="d-e-c pt10 lh150">
				<view class="f24">
					订单实付金额：
					<text class="red">¥{{detail.orderproduct.total_pay_price}}</text>
				</view>
			</view>
		</view>

		&lt;!&ndash; 已退款金额 &ndash;&gt;
		<view class="group bg-white" v-if=" detail.status.value == 20 && detail.type.value == 10 ">
			<text class="gray9">已退款金额：</text>
			<text class="gray9">￥{{ detail.refund_money }}</text>
		</view>

		&lt;!&ndash;申请售后信息&ndash;&gt;
		<view class="group bg-white f24">
			<view class="p-20-0 border-b f34">
				申请退货信息
			</view>
			<view class="p-20-0 f28">
				<text class="gray9">售后类型：</text>
				<text>{{detail.type.text}}</text>
			</view>
			<view class="p-20-0 f28">
				<text class="gray9">申请原因：</text>
				<text>
					{{detail.apply_desc}}
				</text>
			</view>
			<view class="p-20-0 upload-list d-s-s f28">
				<text class="gray9">申请凭证：</text>
				<view class="d-s-s f-w">
					<block v-if="detail.image.length>0">
						<view class="item" v-for="(imgs,img_num) in detail.image" :key="img_num">
							<image :src="imgs.file_path" mode="aspectFit"></image>
						</view>
					</block>
					 <block v-else>
						 无
					 </block>
				</view>
			</view>
		</view>
		
		&lt;!&ndash; 售后信息&ndash;&gt;
		<view v-if="detail.plate_status.value != 0" class="group bg-white">
			<view class="p-20-0 border-b f34">
				平台处理结果:
			</view>
			<view class="p-20-0">
				<text class="red f28" v-if="detail.plate_status.value == 10">客服处理中</text>
				<text class="red f28" v-if="detail.plate_status.value == 20">平台已同意退款</text>
				<text class="red f28" v-if="detail.plate_status.value == 30">平台已拒绝退款</text>
			</view>
		</view>
		<view v-if="detail.status.value == 10" class="group bg-white">
			<view class="p-20-0 border-b f34">
				商家拒绝原因
			</view>
			<view class="p-20-0">
				<text class="red f28">{{ detail.refuse_desc }}</text>
			</view>
		</view>
		&lt;!&ndash; 平台拒绝原因 &ndash;&gt;
		<view v-if="detail.plate_status.value == 30" class="group bg-white">
			<view class="p-20-0 border-b f34">
				平台拒绝原因
			</view>
			<view class="p-20-0">
				<text class="red f28">{{ detail.plate_desc }}</text>
			</view>
		</view>
		&lt;!&ndash;退货地址&ndash;&gt;
		<view class="group bg-white" v-if="detail.is_agree.value == 10">
			<view class="p-20-0 border-b f34">
				退货地址
			</view>
			<view class="pt30 f28">
				<text class="gray9">收货人：</text>
				<text>{{detail.address.name}}</text>
			</view>
			<view class="pt30 f28">
				<text class="gray9">联系电话：</text>
				<text>{{detail.address.phone}}</text>
			</view>
			<view class="pt30 f28">
				<text class="gray9">详情地址：</text>
				<text>{{detail.address.detail}}</text>
			</view>
			<view class="pt30 d-b-c f28" v-if="detail.express_no">
				<view class="">
					<text class="gray9">物流公司：</text>
					<text>{{detail.express.express_name}}</text>
				</view>
				<view class="">
					<text class="gray9">物流单号：</text>
					<text>{{detail.express_no}}</text>
				</view>
			</view>
			<view class="mt20 pb20 border-t gray9">
				<view class="pt20">
					· 未与卖家协商一致情况下，请勿寄到付或平邮
				</view>
				<view class="pt10">
					· 请填写真实有效物流信息
				</view>
			</view>
		</view>
		&lt;!&ndash; 填写物流信息 &ndash;&gt;
		<form @submit="formSubmit" v-if="detail.status.value == 0 && detail.is_agree.value == 10 && detail.is_user_send == 0 " report-submit>
			<view class="group bg-white">
				<view class="p-20-0 border-b f34">
					填写物流信息
				</view>
                <view class="p-20-0 d-s-c">
                    <view class="gray9">物流公司：</view>
					<view class="flex-1 p20 border">
					    <picker mode="selector" @change="onExpressChange" :range="expressList"
							range-key="express_name" :value="index">
					        <text v-if="index > -1 ">{{expressList[index].express_name}}</text>
					        <text v-else class="col-80">请选择物流公司</text>
					    </picker>
					</view>
                </view>
                <view class="p-20-0 d-s-c">
                    <view class="gray9">物流单号：</view>
                    <view class="flex-1 border">
                        <input class="p10" placeholder="请填写物流单号" name="express_no"></input>
                    </view>
                </view>
				<view class="mt20">
				    <button class="btn-red" formType="submit">确认发货</button>
				</view>
            </view>
        </form>
	-->

    </view>
</template>

<script>
	export default {
		data() {
			return {
				/*是否加载完成*/
				loadding: true,
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				expressList: {},
				index: -1,
				order_refund_id: 0,
				/*退货详情*/
				detail: {
					address: {},
				},
				express_id: 0,
				/*消息模板*/
				temlIds: [],
                line: [],
				list: [],
                current: 0,
                formshow: false
			}
		},
		onLoad(e) {
			this.order_refund_id = e.order_refund_id;
		},
		mounted() {
			/*获取详情*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				let order_refund_id = self.order_refund_id;
				self._get(
					'user.refund/detail', {
						order_refund_id: order_refund_id,
						platform: self.getPlatform()
					},
					function(res) {
                        uni.hideLoading();
						self.detail = res.data.detail;
						self.line = res.data.line;
						self.list = res.data.list;
						self.current = res.data.current;
						self.expressList = res.data.expressList;
						self.temlIds = res.data.template_arr;
                        if (self.detail.status.value == 30) {
                            let line = res.data.line[0];
                            console.log(line)
                            line.name = '已取消';
                            self.current = 0;
                        }
					}
				);
			},
			
			/*选择物流*/
            onExpressChange: function(e) {
				this.index=e.target.value;
                this.express_id=this.expressList[this.index].express_id;
			},

			/*发货*/
            formSubmit: function(e) {
				let self = this;
				var formdata = e.detail.value;
				formdata.order_refund_id = self.order_refund_id;
				formdata.express_id = self.express_id;
				self.formshow =false;
				let callback = function(){
					uni.showLoading({
						title: '正在提交',
						mask: true
					});
					self._post('user.refund/delivery', formdata, function(res) {
						uni.hideLoading();
						uni.showToast({
							title: res.msg,
							duration: 3000,
							complete: function() {
								self.gotoPage('/pages/order/refund/detail/detail?order_refund_id=' + self.order_refund_id);
							}
						});
					});
				}; 
				self.subMessage(self.temlIds, callback);
			},
            cancelorder: function() {
                let self = this;
                var formdata = [];
                formdata.order_refund_id = self.order_refund_id;
                let callback = function(){
                    uni.showLoading({
                        title: '正在提交',
                        mask: true
                    });
                    self._post('user.refund/cancel', formdata, function(res) {
                        uni.hideLoading();
                        uni.showToast({
                            title: res.msg,
                            duration: 3000,
                            complete: function() {
                                self.gotoPage('/pages/order/refund/detail/detail?order_refund_id=' + self.order_refund_id);
                            }
                        });
                    });
                };
                self.subMessage(self.temlIds, callback);
            }
		}
	}
</script>

<style>
.order-refund-detail .btn-red{
	height: 88rpx;
	line-height: 88rpx;
	border-radius: 44rpx;
	box-shadow: 0 8rpx 16rpx 0 rgba(226,35,26,.6);}
</style>
