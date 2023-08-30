<template>
	<view class="" v-if="!loadding">
		<form @submit="formSubmit" @reset="formReset">
			<view class="" v-for="(item, index) in tableData" :key="index">


                <view style="display: flex;padding: 20rpx;background-color: white">
                    <view style="width: 20%">
                        <image :src="item.image.file_path" style="width: 90rpx;height: 90rpx" mode="aspectFit"></image>
                    </view>
                    <view class="fsc" style="width: 80%;justify-content: space-around">
                        <view @click="gradeFunc(item, 10, index)" style="display: flex;align-items: center">
                            <block v-if="item.score == 10">
                                <image  src="/static/images/goods/detail/good.png" style="width: 80rpx;height: 80rpx;"></image>
                                <view style="color: #F63E36;font-weight: bold">
                                    好评
                                </view>
                            </block>
                            <block v-else>
                                <image  src="/static/images/goods/detail/good-s.png" style="width: 80rpx;height: 80rpx;"></image>
                                <view style="color: #999999;">
                                    好评
                                </view>
                            </block>
                        </view>
                        <view @click="gradeFunc(item, 20, index)" style="display: flex;align-items: center">
                            <block v-if="item.score == 20">
                                <image  src="/static/images/goods/detail/mid.png" style="width: 80rpx;height: 80rpx;"></image>
                                <view style="color: #F63E36;font-weight: bold">
                                    中评
                                </view>
                            </block>
                            <block v-else>
                                <image  src="/static/images/goods/detail/mid-s.png" style="width: 80rpx;height: 80rpx;"></image>
                                <view style="color: #999999;">
                                    中评
                                </view>
                            </block>
                        </view>
                        <view @click="gradeFunc(item, 30, index)" style="display: flex;align-items: center">
                            <block v-if="item.score == 30">
                                <image  src="/static/images/goods/detail/bad.png" style="width: 80rpx;height: 80rpx;"></image>
                                <view style="color: #F63E36;font-weight: bold">
                                    差评
                                </view>
                            </block>
                            <block v-else>
                                <image  src="/static/images/goods/detail/bad-s.png" style="width: 80rpx;height: 80rpx;"></image>
                                <view style="color: #999999;">
                                    差评
                                </view>
                            </block>
                        </view>
                    </view>
                </view>

                <view class="hlbblock30">
                    <view>
                        <view style="background: #F3F3F2;height: 200rpx;width: 100%;border-radius: 10rpx">
                            <textarea style="padding: 10rpx"  v-model="item.content" placeholder="请输入评价内容" />
                        </view>
                    </view>
                    <view class="upload-list d-s-c" v-model="item.image_list">
                        <view class="item" v-for="(imgs, img_num) in item.image_list" :key="img_num"  @click="deleteImg(index, img_num)">
                            <image :src="imgs.file_path" mode="aspectFit"></image>
                        </view>
                        <view class="item upload-btn d-c-c d-c" @click="openUpload(index)" v-if="item.image_list.length < 9">
                            <text class="icon iconfont icon-xiangji"></text>
                            <text class="gray9">上传图片</text>
                        </view>
                    </view>
                </view>

                <view class="hlbblock30">
					<!-- <view class="evalu-value">
						描述相符：
						<view class="eval">
							<i
								v-for="(itemEv, indexEv) in describe[index]"
								:key="indexEv"
								style="margin-right: 10rpx;"
								:class="itemEv ? 'icon iconfont icon-start' : 'icon iconfont icon-start1'"
							 @click="choosees(indexEv, index)"
							></i>
						</view>
					</view>
					<view class="evalu-value">
						服务态度：
						<view class="eval">
							<i
								v-for="(itemEv, indexEv) in service[index]"
								:key="indexEv"
								:class="itemEv ? 'icon iconfont icon-start' : 'icon iconfont icon-start1'"
								@click="chooseServ(indexEv, index)"
							></i>
						</view>
					</view>
					<view class="evalu-value">
						配送服务：
						<view class="eval">
							<i
								v-for="(itemEv, indexEv) in logistics[index]"
								:key="indexEv"
								:class="itemEv ? 'icon iconfont icon-start' : 'icon iconfont icon-start1'"
								@click="chooseLog(indexEv, index)"
							></i>
						</view>
					</view> -->
                    <view class="evalu-value">
                        描述相符：
                        <u-rate gutter="30" :count="5" size="40" inactive-color="#dddddd" active-color="#ffc51f"  v-model="tableData[index].describe_score"></u-rate>
                    </view>
                    <view class="evalu-value">
                        服务态度：
                        <u-rate gutter="30" :count="5" size="40" inactive-color="#dddddd" active-color="#ffc51f"  v-model="tableData[index].server_score"></u-rate>
                    </view>
                    <view class="evalu-value">
                        配送服务：
                        <u-rate gutter="30" :count="5" size="40" inactive-color="#dddddd" active-color="#ffc51f"  v-model="tableData[index].express_score"></u-rate>
                    </view>

                </view>

			</view>
			<view class="foot-btns"><button form-type="submit" class="hlbbutton" style="width: 100%">确认提交</button></view>
		</form>

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
			order_id: '',
			/*页面数据*/
			tableData: [],
			score: 10,
			/*是否打开上传图片*/
			isUpload: false,
			image_id: [],
			img: '/static/temp/photo.jpg',
			index: '',
			service:[],
			logistics:[],
			describe:[],
		};
	},
	onLoad(e) {
		this.order_id = e.order_id;
	},
	mounted() {
		uni.showLoading({
			title: '加载中'
		});
		/*获取页面数据*/
		this.getData();
	},
	methods: {
		getData() {
			let self = this;
			let order_id = self.order_id;
			self._get(
				'user.comment/order',
				{
					order_id: order_id
				},
				function(res) {
					self.tableData = res.data.productList;
					let b = self.tableData.forEach((item, index) => {
						self.tableData[index].score = 10;
						self.tableData[index].express_score = 0;
						self.tableData[index].server_score = 0;
						self.tableData[index].describe_score = 0;
						
						self.tableData[index].image_list = [];
						self.service.push([false,false,false,false,false]);
						self.logistics.push([false,false,false,false,false]);
						self.describe.push([false,false,false,false,false]);
					});
					self.loadding = false;
					uni.hideLoading();
				}
			);
		},
		/*选择评价*/
		gradeFunc(item, e, index) {
			item.score = e;
			// this.tableData[index].score = e;
			this.$set(this.tableData, index, item);
			console.log(this.tableData)
		},
		/* 物流评分 */
		chooseLog(n,m){
			let self=this;
			self.tableData[m].express_score =0;
			this.logistics[m].forEach((item,index)=>{
				if(index<=n){
					this.logistics[m].splice(index,1,true);
					self.tableData[m].express_score++;
				}else{
					this.logistics[m].splice(index,1,false);
				}
			})

		},
		/* 服务评分 */
		chooseServ(n,m){
			let self=this;
			self.tableData[m].server_score =0;
			this.service[m].forEach((item,index)=>{
				if(index<=n){
					this.service[m].splice(index,1,true);
					self.tableData[m].server_score++;
				}else{
					this.service[m].splice(index,1,false);
				}
				
			})
			console.log(self.tableData)
		},
		/* 描述评分 */
		choosees(n,m){
			let self=this;
			self.tableData[m].describe_score =0;
			this.describe[m].forEach((item,index)=>{
				if(index<=n){
					this.describe[m].splice(index,1,true);
					self.tableData[m].describe_score++;
				}else{
					this.describe[m].splice(index,1,false);
				}
			})
			console.log(self.tableData)
		},
		
		/*提交*/
		formSubmit: function(e) {
			let self = this;
			let order_id = self.order_id;
			let formData=[];
			console.log(this.tableData)
			this.tableData.forEach((item,index)=>{
				console.log(item);
				formData[index]={
				order_product_id:item.order_product_id,
				product_id:item.product_id,
				score:item.score,
				image_list:item.image_list,
				express_score:item.express_score,
				server_score:item.server_score,
				describe_score:item.describe_score,
				content:''
				}
				if(item.content){
				formData[index].content=item.content;
				}
			})
			self._post(
				'user.comment/order',
				{
					order_id: order_id,
					formData: JSON.stringify(formData)
				},
				function(res) {
					self.showSuccess('评价成功！',function(){
						self.gotoPage('/pages/index/order/myorder', 'redirect');
					});
				}
			);
		},

		/*打开上传图片*/
		openUpload(index) {
			// self.isUpload = false;
			this.index = index;
			this.isUpload = true;
		},

		/*获取上传的图片*/
		getImgsFunc(e) {
			let self = this;
			if(e&&typeof(e)!='undefined'){
				let index = self.index;
				self.tableData[index].image_list = self.tableData[index].image_list.concat(e);
			}
			self.isUpload = false;
		},
		
		/*点击图片删除*/
		deleteImg(i,n){
			this.loadding=true;
			this.tableData[i].image_list.splice(n,1);
			console.log(this.tableData)
			this.loadding=false;
		}
	}
};
</script>

<style>
    page{
        background-color: #F0F0F0;
    }
.evaluate {
	/* background: #eeeeee; */
}
.evaluate-item {
	margin-bottom: 20rpx;
	background: #ffffff;
	border-bottom: 1px solid #dddddd;
}
.product .cover,
.product .cover image {
	width: 160rpx;
	height: 160rpx;
}
.evaluate .grade .item .iconfont {
	width: 60rpx;
	height: 60rpx;
	line-height: 60rpx;
	border-radius: 50%;
	font-size: 40rpx;
	color: #ffffff;
	text-align: center;
}
.evaluate .grade .item {
	height: 60rpx;
	padding-right: 20rpx;
	line-height: 60rpx;
	border-radius: 30rpx;
	transition: background-color 0.4s;
}
.grade .flex-1:nth-child(1) .iconfont {
	background: #f42222;
}
.grade .flex-1:nth-child(2) .iconfont {
	background: #f2b509;
}
.grade .flex-1:nth-child(3) .iconfont {
	background: #999999;
}
.grade .flex-1.active:nth-child(1) .item {
	background: #f42222;
	color: #ffffff;
}
.grade .flex-1.active:nth-child(2) .item {
	background: #f2b509;
	color: #ffffff;
}
.grade .flex-1.active:nth-child(3) .item {
	background: #999999;
	color: #ffffff;
}
.icon-start{
	color: #f5a623;
}
.evalu-value{
	display: flex;
	margin-bottom: 30rpx;
}
.eval{
	display: flex;
	justify-content: space-around;
	align-items: center;
}
.evalu{
	display: flex;
	align-items: baseline;
	flex-direction: column;
}
</style>
