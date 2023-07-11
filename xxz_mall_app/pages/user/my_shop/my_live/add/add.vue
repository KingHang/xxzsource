<template>
	<view>
		<scroll-view scroll-y="true" class="pr scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50">
			<view class="add-live-room">
				<view class="form-level">
						<view class="level-name ">标题</view>
						<view class="level-content"><input type="text" v-model="form.name" placeholder="请输入标题" /></view>
					</view>
					<view class="form-level">
						<view class="level-name">直播间开始时间</view>
						<view class="level-content"><pickerViewDatetime @get="getStartTime"></pickerViewDatetime></view>
					</view>

					<!-- <view class="form-level">
				<view class="level-name">计划结束时间</view>
				<view class="level-content">
					<pickerViewDatetime @get="getStartEnd"></pickerViewDatetime>
				</view>
			</view> -->

					<!-- <view class="form-level">
				<view class="level-name">背景图</view>
				<view class="level-content">
					<view class="upload-btn d-c-c d-c" @tap="openUpload('cover_img_')" v-if="!form.cover_img_id">
						<text class="icon iconfont icon-xiangji"></text>
						<text class="gray9">上传图片</text>
					</view>
					<view class="video-cover" @tap="openUpload('cover_img_')" v-else><image :src="form.cover_img_path" mode="aspectFit"></image></view>
				</view>
			</view> -->
<!-- 					<view class="form-level">
						<view class="level-name">关联商品</view>
						<view class="level-content">
							<view class="d-b-c relation-product" @tap="showProducts">
								<template v-if="relationList.length > 0">
									<text>已关联{{ relationList.length }}个商品</text>
								</template>
								<template v-else>
									<text>选择商品</text>
								</template>
								<text class="iconfont icon-jiantou"></text>
							</view>
						</view>
					</view> -->
<!-- 					<view>
						<picker class="typepicker" mode="selector" :range="categorylist" @change="bindPickerChange">
							<label>类别：</label>
							<label>{{categorylist[index]}}</label>
						</picker>
					</view> -->
					<view class="form-level pt60">
						<view class="level-name">预告封面</view>
						<view class="level-content">
							<view class="upload-btn d-c-c d-c" @tap="openUpload('share_img_')" v-if="!form.share_img_id">
								<text class="icon iconfont icon-xiangji"></text>
								<text class="gray9">上传图片</text>
							</view>
							<view class="video-cover" @tap="openUpload('share_img_')" v-else><image class="video_img" :src="form.share_img_path" mode="aspectFit"></image></view>
						</view>
					</view>
			</view>
			
		</scroll-view>

		
		<view class="foot-btns add-video-btns"><button type="primary" @tap="formSubmit" class="btn-rose">立即创建</button></view>
		
		<!--商品列表-->
		<Products :open="open_products" :relationList="relationList" @close="closeProducts"></Products>

		<!--上传图片-->
		<Upload v-if="isUpload" @getImgs="getImgsFunc" :type="file_type"></Upload>
	</view>
</template>

<script>
import Upload from '@/components/upload/upload.vue';
import pickerViewDatetime from '@/components/picker-view-datetime/pickerViewDatetime.vue';
import Products from './dialog/Products.vue';
export default {
	components: {
		Upload,
		pickerViewDatetime,
		Products
	},
	data() {
		return {
			/*手机高度*/
			phoneHeight: 0,
			/*可滚动视图区域高度*/
			scrollviewHigh: 0,
			/*是否登录成功*/
			loading: true,
			/*form表单*/
			form: {
				/*直播间名称*/
				name: '',
				/*背景图*/
				//cover_img_id: null,
				//cover_img_path: '',
				/*直播间分享图*/
				share_img_id: null,
				share_img_path: '',
				/*直播开始时间*/
				start_time: '',
				/*预计结束时间*/
				end_time: '',
				/*商品id*/
				productIds:[],
				category_id:0
			},
			/*是否打开上传图片*/
			isUpload: false,
			/*文件类别*/
			file_type: null,
			/*当前图片ID*/
			cur_image: null,
			/*是否展示商品*/
			open_products: false,
			/*关联商品*/
			relationList: [],
			categorylist:[],
			category:'',
			categorytype:[],
			index:0
		};
	},
	computed: {},
	onLoad() {
	},
	mounted() {
		this.init();
		// this.getCategory();
	},
	methods: {
		/*初始化*/
		init() {
			let _this = this;
			uni.getSystemInfo({
				success(res) {
					_this.phoneHeight = res.windowHeight;
					// 计算组件的高度
					let view = uni.createSelectorQuery().select('.foot-btns');
					view.boundingClientRect(data => {
						let h = _this.phoneHeight - data.height;
						_this.scrollviewHigh = h;
					}).exec();
				}
			});
		},
		getCategory(){
			let self=this;
			self._post('plus.live.RoomApply/category', {
				
			}, function(res) {
				res.data.list.forEach((item,index)=>{
					self.categorylist.push(item.name)
				})
				self.categorytype=res.data.list;
			});
		},
		/*展开商品*/
		showProducts() {
			this.open_products = true;
		},

		/*关闭商品*/
		closeProducts(e) {
			this.open_products = false;
			this.relationList=e;
		},

		/*提交*/
		formSubmit: function(e) {
			let self = this;

			if (self.form.name == '') {
				wx.showModal({
					title: '提示',
					content: '请输入标题',
					showCancel: false
				});
				return;
			}
			if (/[<>*{}()^%$#@!~&= ]/.test(self.form.name)) {
				wx.showModal({
					title: '提示',
					content: '标题不能为空或包含特殊字符',
					showCancel: false
				});
				return;
			}

			if (new Date(self.form.start_time).getTime() >= new Date(self.form.end_time).getTime()) {
				wx.showModal({
					title: '提示',
					content: '开始时间不能大于结束时间',
					showCancel: false
				});
				return;
			}

			/* if(self.form.cover_img_id==null){
				wx.showModal({
				  title: '提示',
				  content: '请上传背景图片',
				  showCancel: false
				});
				return;
			} */

			if (self.form.share_img_id == null) {
				wx.showModal({
					title: '提示',
					content: '请上传封面图',
					showCancel: false
				});
				return;
			}
			console.log(this.relationList)
			self.form.productIds= this.relationList;
			self._post('plus.live.RoomApply/addnotice', self.form, function(res) {
				uni.showToast({
					title: '创建成功'
				});
				uni.navigateBack({
					delta:1
				})
			});
		},

		/*获取开始时间*/
		getStartTime(e) {
			this.form.start_time = e + ':00';
		},

		/*获取计划结束时间*/
		getStartEnd(e) {
			this.form.end_time = e + ':59';
		},

		/*打开上传图片*/
		openUpload(id_name) {
			this.cur_imagetype = id_name;
			this.file_type = 'image';
			this.isUpload = true;
		},

		/*获取上传的图片*/
		getImgsFunc(e) {
			if (e && typeof e != 'undefined' && this.cur_imagetype != null) {
				this.form[this.cur_imagetype + 'id'] = e[0]['file_id'];
				this.form[this.cur_imagetype + 'path'] = e[0]['file_path'];
				this.cur_imagetype = null;
			}
			this.isUpload = false;
		},
		bindPickerChange(e){
			this.index = e.target.value;
			this.category=this.categorylist[this.index];
			this.form.category_id=this.categorytype[this.index].category_id;
		}
	}
};
</script>

<style scoped>
.add-live-room {
	padding: 20rpx;
	background: #ffffff;
}
.add-live-room .form-level {
	font-size: 30rpx;
	justify-content: flex-start;
	align-items: flex-start;
	flex-direction: column;
}
.add-live-room .form-level .level-name {
	width: 240rpx;
	color: #000000;
}
.add-live-room .level-content {
	margin-top: 20rpx;
	width: 100%;
}
.add-live-room .level-content input{
	margin-bottom: 20rpx;
	border: 1rpx solid #cacaca;
	padding: 20rpx;
}
.relation-product {
	height: 90rpx;
	padding: 0 10rpx;
	line-height: 90rpx;
	border-radius: 16rpx;
	border: 1rpx solid #cccccc;
}

.add-video-btns {
	padding: 0;
	z-index: 80;
}
.add-video-btns button {
	width: 100%;
	height: 90rpx;
	line-height: 90rpx;
	background: #E2231A;
	color: #ffffff;
	border-radius: 0;
}
.typepicker{
	background-color: #E2231A;
	color: #FFFFFF;
	width: 200rpx;
	height: 75rpx;
	border-radius: 20rpx;
	text-align: center;
	line-height: 75rpx;
	padding-left: 15rpx;
	font-size: 25rpx;
	/* border: 1rpx solid #CACACA; */
	margin: 25 0rpx;
}
.video_img{
	width: 640rpx;
	height: 480rpx;
	display: block;
}
</style>
