<template>
	<view class="login-container">

        <view class="wrapper">
            <block v-if="uid>0">
                <view style="text-align: center;padding-top: 200rpx">
                    <image :src="ag.avatarUrl" style="width: 100rpx;border-radius: 50%;display: inline-block" mode="widthFix"></image>
                    <view style="padding: 30rpx">
                        {{ag.nickName}}
                    </view>
                    <view class="font2599" style="margin-bottom: 50rpx">
                        邀请你成为分销商
                    </view>
                    <button class="hlbbutton" style="margin: auto;height: 96rpx;width: 600rpx;line-height: 96rpx;border-radius: 100rpx"   @click="wechatLogin"  >
                        立即加入
                    </button>
                </view>
            </block>
            <block v-else>
                <view style="text-align: center;margin: 150rpx">
                    <image :src="shopimg" style="width: 112rpx;border-radius: 50%;margin: auto" mode="widthFix"></image>
                </view>
                <block v-if="showmp">
                    <button class="hlbbutton" style="margin: auto;height: 96rpx;width: 600rpx;line-height: 96rpx;border-radius: 100rpx"   @click="wechatLogin"  >
                        登录/注册
                    </button>
                    <!-- <view @click="tosms()" class="hlbbuttonempty" style="width: 75%;margin: 50rpx auto;border: 1px #F0F0F0 solid;color: #333333">
                         手机号登录/注册
                     </view>-->
                </block>
                <block v-if="showh5">
                    <view @click="toh5()" class="hlbbutton" style="width: 75%;margin: auto">
                        微信一键登录
                    </view>
                </block>
            </block>
			
            <u-popup  v-model="popshow" mode="bottom" height="500rpx" border-radius="50">
                <view style="margin: 40rpx">
                    <view style="font-size: 35rpx;margin: 40rpx">
                        手机号快速登录
                    </view>
                    <view style="border-top: 1px #e7e7e7 solid;">
                        <view style="margin: 40rpx">
                            为了提供更好的服务体验，我们将获取你的手机号。请放心，我们绝不会泄露你的个人信息。
                        </view>
                    </view>
                    <view style="border-top: 1px #e7e7e7 solid;">
                        <view style="margin: 20rpx;display: flex">
                            <view style="width: 50%">
                                <button class="hlbbutton" style="width: 80%;background-color: #19a318;border: none"  type='primary' open-type="getPhoneNumber"  @getphonenumber="getPhoneNumber">
                                    同意
                                </button>
                            </view>
                        </view>
                    </view>
                </view>
            </u-popup>
        </view>
		
        <view style="text-align: center;position: fixed;bottom: 100rpx;width: 90%;text-align: center">
            <view>
                <view class="font2599">
                    登录注册即表示同意
                </view>
                <view class="font2599" style="color: #F63E36;" @click="goToAgreement">
                    {{ agreementTitle }}
                </view>
            </view>
        </view>
		
		<u-popup  v-model="popshowMobile" mode="bottom" height="500rpx" border-radius="50">
		    <view style="margin: 40rpx">
		        <view style="font-size: 35rpx;margin: 40rpx">
		            手机号快速登录
		        </view>
		        <view style="border-top: 1px #e7e7e7 solid;">
		            <view style="margin: 40rpx">
		                为了提供更好的服务体验，我们将获取你的手机号。请放心，我们绝不会泄露你的个人信息。
		            </view>
		        </view>
		        <view style="border-top: 1px #e7e7e7 solid;">
		            <view style="margin: 40rpx;display: flex">
		                <view style="width: 50%;margin: 0 auto;">
		                    <button class="hlbbutton" style="width: 80%;background-color: #19a318;border: none"  type='primary' open-type="getPhoneNumber"  @getphonenumber="getPhoneNumberLogin">
		                        同意
		                    </button>
		                </view>
		            </view>
		        </view>
		    </view>
		</u-popup>
	</view>
</template>

<script>
    var _self;
	export default {
		data() {
			return {
				background: '',
				listData: [],
                SessionKey: '',
                mobile: '',
                password: '',
                popshow: false,
                logining: false,
                showmp: false,
                showh5: false,
                wait: true,
                openid: '',
                shopimg: '',
                sessionKey: '',
                openId: '',
                nickName: null,
                avatarUrl: null,
                userInfo: {},
                back: 0,
                uid: 0,
                ag: [],
				agreementTitle: '',
				popshowMobile: false,
				encryptedData: '',
				iv: '',
				signature: '',
				info: {
					openId: '',
					avatarUrl: '',
					gender: '',
					nickName: ''
				}
			}
		},
		onShow(){
			//wx.login(); //重新登录
			
            //#ifdef MP
            //如果是小程序  例如显示返回按钮
            this.showmp = true
			
            // #endIf
			
			//获取系统配置
			_self._post(
			    'index/base', {
			    },
			    function(res) {
					_self.shopimg = res.data.settings.shop_logo;
					_self.agreementTitle = res.data.agreementTitle ? '《' + res.data.agreementTitle + '》' : '';
			    }
			);
			
            _self.uid = uni.getStorageSync('referee_id')
			
            if (_self.uid > 0) {
                _self._post(
                    'plugin.articlepromotion.article/userShareMsg', {
                        share_id: _self.uid
                    },
                    function(res) {
                        _self.ag = res.data
                    }
                );
            }
		},
		onLoad(t) {
            _self = this;
            _self.back = t.back
		},
		methods: {
			goToAgreement() {
				uni.navigateTo({
					url: '/pages/login/agreement'
				})
			},
			
			/*改变发送验证码按钮文本*/
			changeMsg() {
				if (this.second > 0) {
					this.send_btn_txt = this.second + '秒';
					this.second--;
					setTimeout(this.changeMsg, 1000);
				} else {
					this.send_btn_txt = '获取验证码';
					this.second = 60;
					this.is_send = false;
				}
			},
			
			onNotLogin() {
				this.gotoPage('/pages/index/index')
			},
			
            /*getPhoneNumber(e){
                console.log(e)
                if (e.detail.errMsg != "getPhoneNumber:ok"){
                    uni.showToast({
                        title: '已取消',
                        icon: 'none',
                        duration: 2000
                    })
                    return;
                }
                _self.popshow = false
                //用户允许授权
                uni.showLoading({
                    title: '加载中',
                    mask:true
                });
                _self.$http.post("wxapp.getmobile", {
                    data: e.detail.encryptedData,
                    iv: e.detail.iv,
                    sessionKey: _self.SessionKey,
                }).then(function (response) {
                    console.log(response);
                    uni.hideLoading();
                    if (response.error>0){
                        uni.showToast({
                            title: response.message,
                            icon: 'none',
                            duration: 2000
                        })
                        return
                    }else {
                        uni.showToast({
                            title: '获取号码成功',
                            icon: 'none',
                            duration: 2000
                        })
                        _self.$http.post('wxapp.newauth', {
                            nickName: _self.userInfo.nickName,
                            avatarUrl: _self.userInfo.avatarUrl,
                            gender: _self.userInfo.gender,
                            city: _self.userInfo.city,
                            province: _self.userInfo.province,
                            country: _self.userInfo.country,
                            openid2:_self.openid,
                            mobile:response.data.res.phoneNumber
                        }).then(function (response) {
                            console.log(response);
                            _self.login(response.data);
                            setTimeout(function(){
                                uni.switchTab({
                                    url:'/pages/index/member',
                                })
                            },500)
                        }).catch(function (error) {
                            console.log(error);
                        });
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            },*/
			
            getPhoneNumber(e) {
                var self = this;
                if (e.detail.errMsg !== 'getPhoneNumber:ok') {
                    return false;
                }
                uni.showLoading({
                    title: '加载中'
                })
                uni.login({
                    success(res) {
                        // 发送用户信息
                        self._post('user.user/bindMobile', {
                            code: res.code,
                            encrypted_data: encodeURIComponent(e.detail.encryptedData),
                            iv: encodeURIComponent(e.detail.iv),
                        }, result => {
                            uni.showToast({
                                title: '绑定成功'
                            });
                            // 执行回调函数
                            if (self.back==1){
                                uni.navigateBack();
                            }else{
                                // 记录token user_id
                                setTimeout(function(){
                                    uni.switchTab({
                                        url:'/pages/index/member',
                                    })
                                },500)
                            }
                        }, false, () => {
                            uni.hideLoading()
                        });
                    }
                });
            },
			
			getUserInfo() {
				let self = this;
				debugger
				wx.getUserProfile({
					lang: 'zh_CN',
					desc: '用于完善会员资料', 
					success: (res) => {
						if (res.errMsg !== 'getUserProfile:ok') {
							return false;
						}
						uni.showLoading({
							title: "正在登录",
							mask: true
						});
						// 执行微信登录
						wx.login({
							success(res_login) {
								debugger
								// 发送用户信息
								self._post('user.user/login', {
									code: res_login.code,
									user_info: res.rawData,
									encrypted_data: encodeURIComponent(res.encryptedData),
									iv: encodeURIComponent(res.iv),
									signature: res.signature,
									referee_id: uni.getStorageSync('referee_id'),
									source: 'wx'
								}, result => {
                                    uni.setStorageSync('token', result.data.token);
                                    uni.setStorageSync('user_id', result.data.user_id);
									
                                    if (!self.$u.test.mobile(result.data.mobile)) {
                                        _self.popshow = true;
                                    } else {
                                        if (self.back == 1) {
                                            uni.navigateBack();
                                        } else {
                                            // 记录token user_id
                                            setTimeout(function(){
                                                uni.switchTab({
                                                    url:'/pages/index/member',
                                                })
                                            },500)
                                        }
                                    }
									
                                    //
									// // 执行回调函数
									// uni.navigateBack();
								}, false, () => {
									uni.hideLoading();
								});
							}
						});
					}
				});
			},
			wechatLogin() {
				let _this = this;
				
				uni.getUserProfile({
					desc: '登录',
				    success: function(infoRes) {
						console.log(infoRes)
						debugger
						_this.info.nickName = infoRes.userInfo.nickName
						_this.info.avatarUrl = infoRes.userInfo.avatarUrl
						_this.info.gender = infoRes.userInfo.gender
						_this.encryptedData = infoRes.encryptedData
						_this.signature = infoRes.signature
						_this.iv = infoRes.iv
						
						_this.popshowMobile = true
				    },
				    fail(res) {
					}
				});
			},
			getPhoneNumberLogin(e) {
			    var self = this;
				
				console.log('************');
				console.log(e);
				
				
			    if (e.detail.errMsg !== 'getPhoneNumber:ok') {
			        return false;
			    }
				
				self.popshowMobile = false
				
			    uni.showLoading({
			        title: '加载中'
			    })
				
				uni.login({
				    success(res) {
				        self._post('user.user/login', {
				        	code: res.code,
				        	user_info: JSON.stringify(self.info),
				        	encrypted_data: encodeURIComponent(e.detail.encryptedData),
				        	iv: encodeURIComponent(e.detail.iv),
				        	signature: self.signature,
				        	referee_id: uni.getStorageSync('referee_id'),
				        	source: 'wx'
				        }, result => {
				            uni.setStorageSync('token', result.data.token);
				            uni.setStorageSync('user_id', result.data.user_id);
				        	debugger
				            if (!self.$u.test.mobile(result.data.mobile)) {
				                self.popshowMobile = true;
				            } else {
				                if (self.back == 1) {
				                    uni.navigateBack();
				                } else {
				                    // 记录token user_id
				                    setTimeout(function(){
				                        uni.switchTab({
				                            url:'/pages/index/member',
				                        })
				                    },500)
				                }
				            }
				        	
				            //
				        	// // 执行回调函数
				        	// uni.navigateBack();
				        }, false, () => {
				        	uni.hideLoading();
				        });
				    }
				});
			},
		},
	}
</script>

<style>
    page{
        background-color: white;
    }
	.login-container {
		padding: 30rpx;
	}

	.wechatapp {
		padding: 80rpx 0 48rpx;
		border-bottom: 1rpx solid #e3e3e3;
		margin-bottom: 72rpx;
		text-align: center;
	}

	.wechatapp .header {
		width: 190rpx;
		height: 190rpx;
		border: 2px solid #fff;
		margin: 0rpx auto 0;
		border-radius: 50%;
		overflow: hidden;
		box-shadow: 1px 0px 5px rgba(50, 50, 50, 0.3);
	}

	.auth-title {
		color: #585858;
		font-size: 34rpx;
		margin-bottom: 40rpx;
	}

	.auth-subtitle {
		color: #888;
		margin-bottom: 88rpx;
		font-size: 28rpx;
	}

	.login-btn {
		padding: 0 20rpx;
	}

	.login-btn button {
		height: 88rpx;
		line-height: 88rpx;
		background: #04be01;
		color: #fff;
		font-size: 30rpx;
		border-radius: 999rpx;
		text-align: center;
	}

	.no-login-btn {
		margin-top: 20rpx;
		padding: 0 20rpx;
	}

	.no-login-btn button {
		height: 88rpx;
		line-height: 88rpx;
		background: #dfdfdf;
		color: #fff;
		font-size: 30rpx;
		border-radius: 999rpx;
		text-align: center;
	}
</style>
