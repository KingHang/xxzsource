<template>
	<view class="live-container">
		
		<!--内容-->
		<view class="content d-s-c">
			<view class="cover d-c-c d-c" @tap="openUpload('share_img_')" >
				<template v-if="!form.share_img_id">
					<text class="icon iconfont icon-xiangji"></text>
					<text class="gray9">上传图片</text>
				</template>
				<template v-else>
					<image :src="form.share_img_path" mode="aspectFit"></image>
				</template>
				
			</view>
			<view class="flex-1 ml20 f30">
				 <!-- <textarea class="textarea" v-model="form.name" placeholder="请输入直播间名称" /> -->
				<input type="text" class="p-20-0" v-model="form.name" placeholder="请输入直播间名称" />
			</view>
		</view>
<!-- 		<view class="livepicker">
			<picker class="typepicker" mode="selector" :range="categorylist" @change="bindPickerChange">
				<label>类别：</label>
				<label>{{categorylist[index]}}</label>
			</picker>
		</view> -->
		
		<view class="foot-btn-box">
			<button type="default" @tap="formSubmit">发起直播</button>
		</view>
		
		<!--上传图片-->
		<Upload v-if="isUpload" @getImgs="getImgsFunc" :type="file_type"></Upload>
		
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
				/*form表单*/
				form: {
					/*直播间名称*/
					name: '',
					/*背景图*/
					//cover_img_id: null,
					//cover_img_path: '',
					/*直播间分享图*/
					share_img_id: null,
					share_img_path: ''
				},
				/*是否打开上传图片*/
				isUpload: false,
				/*文件类别*/
				file_type: null,
				/*当前图片ID*/
				cur_image: null,
				categorylist:[],
				category:'',
				categorytype:[],
				index:0
			}
		},
		mounted() {
			// this.getCategory();
		},
		methods: {
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
			/*输入框*/
			bindTextAreaBlur(e){
				this.form.name=e;
			},
			
			/*提交*/
			formSubmit: function(e) {
				let self = this;
				
				if(self.form.name==''){
					wx.showModal({
					  title: '提示',
					  content: '请输入直播间名称',
					  showCancel: false
					});
					return;
				}
				if (/[<>*{}()^%$#@!~&= ]/.test(self.form.name)) {
				  wx.showModal({
				    title: '提示',
				    content: '名称不能为空或包含特殊字符',
				    showCancel: false
				  });
				  return;
				};
				
				if(self.form.share_img_id==null){
					wx.showModal({
					  title: '提示',
					  content: '请上传直播间分享图',
					  showCancel: false
					});
					return;
				}
				
				self._post('plus.live.RoomApply/addlive', self.form, function(res) {
					uni.showToast({
						title: '创建成功'
					});
					var url = '/pagesLive/live/live?type=create&room_id=' + res.data.room_id;
					
					//#ifdef APP-PLUS
						url = '/pagesLive/live/live-nvue?type=create&room_id=' + res.data.room_id;
					//#endif
					self.gotoPage(url);
				});
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
	}
</script>

<style lang="scss" scoped>
.live-container{
	width: 100%;
	height: 100vh;
	position: relative;
	background-image: linear-gradient(-60deg, #685b59,#888383, #817574,#7b6b69);
}

.live-container .content{
	position: absolute;
	padding: 10rpx;
	top: 100rpx;
	right: 20rpx;
	left: 20rpx;
	border-radius: 20rpx;
	background: rgba(0,0,0,.4);
}

.live-container .content .cover{
	width: 200rpx;
	height: 200rpx;
	overflow: hidden;
	border-radius: 20rpx;
	border: 1rpx solid rgba(255,255,255,.2);
}

.live-container .content .cover image{
	width: 100%;
	height: 100%;
	border-radius: 20rpx;
}

.live-container .content input[type=text]{
	color: #FFFFFF;
}

.live-container .content .textarea{
	width: 100%;
	padding: 20rpx 0;
	height: 160rpx;
	line-height: 50rpx;
	color: #FFFFFF;
}

.live-container .foot-btn-box{
	position: fixed;
	right: 20rpx;
	bottom: 20rpx;
	left: 20rpx;
	
}

.live-container .foot-btn-box button{
	width: 100%;
	height: 88rpx;
	line-height: 88rpx;
	border-radius: 44rpx;
	background: $dominant-color;
	color: #FFFFFF;
	font-size: 30rpx;
}
.livepicker{
	position: absolute;
	top: 345rpx;
	left: 20rpx;
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
</style>
