<template>


    <view>
        <view class="hlbblock30 fsc" style="" >
            <view class="tfont">
                头像
            </view>
            <view @click="changeAvatarUrl" style="display: none;">
                <u-avatar  :src="userInfo.avatarUrl"></u-avatar>
            </view>
			<view class="d-e-c">
				<view class="info-image">
					<button style="padding: 0;" open-type="chooseAvatar" @chooseavatar="onChooseAvatar">
						<image class="avatarUrl-image" :src="userInfo.avatarUrl || '/static/default.png'" mode=""></image>
					</button>
				</view>
				<text class="icon iconfont icon-jiantou"></text>
			</view>
        </view>

        <view class="hlbblock30">
            <view style="display: flex">
                <view class="tfont" style="width: 30%">
                    会员ID
                </view>
                <view>
                    {{userInfo.user_id}}
                </view>
            </view>
            <view style="display: flex;margin-top: 30rpx">
                <view class="tfont" style="width: 30%">
                    昵称
                </view>
                <view class="fsc" style="width: 70%"  @click="changeName('nickName')">
                    {{userInfo.nickName}}
                    <view class="wlkarrow">

                    </view>
                </view>
            </view>
            <view style="display: flex;margin-top: 30rpx">
                <view class="tfont" style="width: 30%">
                    手机号码
                </view>
                <view class="fsc" style="width: 70%" @click="gotoBind">
                    {{userInfo.mobile||'未绑定'}}

                    <view class="wlkarrow" style="color: #999999;font-size: 24rpx">
                        修改
                    </view>
                </view>
            </view>
            <view class="setup-btn" @tap="logout()">退出登录</view>
        </view>
		<Popup :show="isPopup" type="bottom" :width="750" :padding="0" @hidePopup="hidePopupFunc">
			<form @submit="subName" class="ww100">
				<view class="d-s-s d-c p20 mpservice-wrap">
					<view class="tc f32 fb ww100">修改</view>
					<template v-if="type == 'mobile' || type == 'nickName' || type == 'user_name' || type == 'email' || type == 'idcard'">
						<view class="pop-input d-b-c">
							<!-- #ifdef MP-WEIXIN -->
							<input name='newName' :type="type=='nickName'?'nickname':'text'" class="flex-1" placeholder="请输入" :value="newName" @input="changeinput" />
							<!-- #endif -->
							<!-- #ifndef MP-WEIXIN -->
							<input :type="type=='text'" name='newName' class="flex-1" placeholder="请输入" :value="newName" @input="changeinput" />
							<!-- #endif -->
							<view class="icon-guanbi icon iconfont" @click="clearName"></view>
						</view>
					</template>
					<view class="d-a-c ww100">
						<button class="cancel-button" @click="hidePopupFunc">取消</button>
						<button class="define-btn" form-type="submit">确认</button>
					</view>
				</view>
			</form>
		</Popup>
		<!-- 上传头像 -->
		<Upload v-if="isUpload" @getImgs="getImgsFunc"></Upload>
    </view>
</template>

<script>
	import Upload from '@/components/upload/uploadOne.vue';
	import Popup from '@/components/uni-popup.vue';
	export default {
		components: {
			Upload,
			Popup
		},
		data() {
			return {
				userInfo: {},
				isPopup: false,
				isUpload: false,
				imageList: [],
				newName: '',
				type:'',
			};
		},
		onShow() {
			/*获取个人中心数据*/
			this.getData();
		},
		methods: {
			onChooseAvatar(e) {
				let self = this;
				console.log(e);
				self.uploadFile([e.detail.avatarUrl]);
			},
			/* 修改头像 */
			changeAvatarUrl() {
				let self = this;
				self.isUpload = true;
			},
			changeName(type) {
				let self = this;
				if (type == 'mobile') {
					self.oldmobile = self.userInfo.mobile;
				}
				self.type = type;
				self.newName = self.userInfo[type];
				self.isPopup = true;
			},
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				self._get('user.index/setting', {}, function(res) {
					self.userInfo = res.data.userInfo;
					
					console.log(self.userInfo)
					uni.hideLoading();
				});
			},
			gotoBind() {
				this.gotoPage("/pages/user/modify-phone/modify-phone");
			},
            changenickname() {
                this.gotoPage("/pages/user/set/changenickname");
            },
            changewechat() {
                this.gotoPage("/pages/user/set/changewechat");
            },
			logout() {
				uni.removeStorageSync('token');
				uni.removeStorageSync('user_id');
				this.gotoPage('/pages/index/index');
			},
			/*获取上传的图片*/
			getImgsFunc(e) {
				let self = this;
				if (e && typeof(e) != 'undefined') {
					let self = this;
					self.userInfo.avatarUrl = e[0].file_path;
					self.update();
					self.isUpload = false;
				}
			},
			/*上传图片*/
			uploadFile: function(tempList) {
				let self = this;
				let i = 0;
				let img_length = tempList.length;
				let params = {
					token: uni.getStorageSync('token'),
					app_id: self.getAppId()
				};
				uni.showLoading({
					title: '图片上传中'
				});
				tempList.forEach(function(filePath, fileKey) {
					uni.uploadFile({
						url: self.websiteUrl + '/index.php?s=/api/file.upload/image',
						filePath: filePath,
						name: 'iFile',
						formData: params,
						success: function(res) {
							let result = typeof res.data === 'object' ? res.data : JSON.parse(res.data);
							if (result.code === 1) {
								self.imageList.push(result.data);
							} else {
								self.showError(result.msg);
							}
						},
						complete: function() {
							i++;
							if (img_length === i) {
								uni.hideLoading();
								// 所有文件上传完成
								self.getImgsFunc(self.imageList);
							}
						}
					});
				});
			},
			update() {
				let self = this;
				if (self.loading) {
					return
				}
				uni.showLoading({
					title:'加载中'
				})
				let params = self.userInfo;
				self.loading = true;
				self._post('user.user/updateUser', params, function(res) {
					self.showSuccess('修改成功', function() {
							self.loading = false;
							self.isPopup = false;
							uni.hideLoading();
							self.getData();
						},
						function(err) {
							uni.hideLoading();
							self.loading = false;
							self.isPopup = false;
						}
					)
			
				});
			},
			hidePopupFunc() {
				this.newName = '';
				this.isPopup = false;
			},
			changeinput(e) {
				this.newName = e.target.value;
			},
			clearName() {
				this.newName = '';
			},
			subName(e) {
				let self = this;
				if (self.loading) {
					return
				}
				self.newName = e.detail.value.newName;
				self.userInfo[self.type] = this.newName;
				self.update()
				
			},
		}
	};
</script>

<style>
    .tfont{
        font-size: 28rpx;
        color: #6E6564;
        
    }
	.address-form .key-name {
		width: 200rpx;
	}

	.address-form .btn-red {
		height: 88rpx;
		line-height: 88rpx;
		border-radius: 44rpx;
		box-shadow: 0 8rpx 16rpx 0 rgba(226, 35, 26, .6);
	}

	.setup-btn {
		position: fixed;
		bottom: 20rpx;
		left: 5%;
		width: 90%;
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 80rpx;
		background-color: #fd3826;
		color: #fff;
		font-size: 30rpx;
		display: flex;
		justify-content: center;
		margin: 0 auto;
	}
	.info-image {
		width: 90rpx;
		height: 90rpx;
		border-radius: 10rpx;
		margin-right: 20rpx;
	
	}
	.avatarUrl-image {
		width: 100%;
		height: 90rpx;
		border-radius: 100rpx;
		line-height: 90rpx;
	}
	.pop-input {
		width: 100%;
		margin: 26rpx 0;
		box-sizing: border-box;
		border-bottom: 2rpx solid #D9D9D9;
	}
	
	.pop-input input {
		width: 100%;
		padding-left: 15rpx;
	
		font-size: 26rpx;
		color: #333333;
		margin: 16rpx 0;
		text-align: left;
		height: 60rpx;
		line-height: 60rpx;
	}
	
	.pop-input .icon.icon-guanbi {
		width: 38rpx;
		height: 38rpx;
		background-color: #999999;
		color: #FFFFFF;
		font-size: 22rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 50%;
		opacity: 0.8;
	}
	
	.sub-box {
		padding: 40rpx 0;
	}
	.btn-red.code-btn {
		height: 54rpx;
		line-height: 54rpx;
	}
	
	.btn-red.code-btn.issend {
		background: #666666;
		border: none;
		color: #FFFFFF;
		box-shadow: none;
	}
	.cancel-button {
		width: 100rpx;
		height: 65rpx;
		background: #FFFFFF;
		border: 1px solid #CCCCCC;
		color: #333333;
		border-radius: 10rpx;
		line-height: 65rpx;
	}
	.define-btn {
		width: 100rpx;
		height: 65rpx;
		background: #fd3826;
		border: 1px solid #CCCCCC;
		color: white;
		border-radius: 10rpx;
		line-height: 65rpx;
	}
</style>
