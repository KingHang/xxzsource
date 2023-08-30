<template>
	<view class="">


        <view style="text-align: center;padding-top: 200rpx">
            <image :src="ag.avatarUrl" style="width: 100rpx;border-radius: 50%;display: inline-block" mode="widthFix"></image>
            <view style="padding: 30rpx">
                {{ag.nickName}}
            </view>
            <view class="font2599" style="margin-bottom: 50rpx">
                邀请你成为分销商
            </view>

            <view v-if="!islogin" class="confirm-btn" style="margin: auto"  @click="tologin()" >去注册/登入</view>
            <view v-else class="confirm-btn" style="margin: auto"  @click="addag" >立即加入</view>

        </view>



	</view>
</template>

<script>
    var _self;
	import {  
        mapMutations  
    } from 'vuex';
	export default{
		data(){
			return {
				mobile: '',
				password: '',
				logining: false,
                showmp:false,
                wait:true,
                showh5:false,
                islogin:false,
                uid:0,
                step:-1,
                text: '获取验证码',
                time: '',
                code: '',
                ag: [],
			}
		},
		onLoad(t){
			let _this = this;
			_self = this;
            let scene = decodeURIComponent(t.scene);
            scene = scene.split('&');
            let data = {};
            scene.forEach(item => {
                let arr = item.split(':');
                if (arr.length == 2) {
                    data[arr[0]] = arr[1];
                }
            });
            
            console.log(data)

			if (data.uid>0){
				_this.uid = data.uid
			}else{
			    uni.switchTab({
                    url:'/pages/index/index'
                })
            }
            let value = uni.getStorageSync('userInfo');
            if (Object.keys(value).length>0) {
                _self.openid = value.openid;
            }else{
                _self.openid ='';



            }
		},
        onShow(t){
            //#ifdef MP
            //如果是小程序  例如显示返回按钮
            this.showmp = true
            // #endIf
            //#ifdef H5
            this.showh5 = true
            //#endif
            _self.getdata()
        },
		methods: {
			...mapMutations(['login']),
            tologin(){
                _self.gotoPage('/pages/login/login?back=1');
            },
            getdata(){
                uni.showLoading({
                    title: '加载中',
                    mask:true
                });
                _self._get('user.index/detail', {
                    url: ''
                }, function(res) {
                    console.log(res)
                    if (res.data.userInfo!=false){
                        _self.islogin =true;
                    }
                });



                _self._post(
                    'plugin.articlepromotion.article/userShareMsg', {
                        share_id: _self.uid
                    },
                    function(res) {
                        console.log(res)
                        _self.ag = res.data

                    }
                );
            },
            async toLoginh5(){
                uni.showLoading({
                    title: '加载中',
                    mask:true
                });
                this.$http.post('groupearn.loginh5', {openid2: _self.openid} ).then(res => {
                    uni.hideLoading()
                    console.log(res.data);
                    if(res.data.error === 0){
                        if (_self.showh5){

                            if (res.data.mobile==''||res.data.mobile==undefined){
                            _self.step=0
                            } else{
                                this.login(res.data);

                            }
                        }else{
                            this.login(res.data);
                        }

                    }else{
                        this.$api.msg(res.data.message);

                    }
                }).catch(err => {
                })
            },
			inputChange(e){
                if (e.length==11){
                    _self.wait = false;
                }else{
                    _self.wait = true;
                }

			},
            addag(e) {

                this.$http.post('groupearn.addag', {
                    mmid:_self.mid
                }).then(function (res) {
                    if(res.data.error === 0){
                        _self.$api.msg('加入成功');
                        setTimeout(function(){
                            uni.navigateTo({
                                url:'/pages/public/comsuccess',
                            })
                        },1500)
                    }else{
                        _self.$api.msg(res.data.message);
                    }
                }).catch(function (error) {
                });
            },
             finish(e) {
                 // _self.mobile=15057322779
                this.$http.post('groupearn.comregister', {
                    newaccount:_self.mobile,
                    code: e,
                    mmid:_self.mid
                }).then(function (res) {
                    if(res.data.error === 0){
                        _self.$api.msg('加入成功');
                        setTimeout(function(){
                            uni.navigateTo({
                                url:'/pages/public/comsuccess',
                            })
                        },1500)
                    }else{
                        console.log(res.data.message)
                        _self.$api.msg(res.data.message);

                    }
                }).catch(function (error) {
                });
            },
            async get_code() {
                let that = this;
                this.$http.post('sms.register', {
                    mobile:_self.mobile,
                }).then(function (response) {
                    console.log(response);
                    if(response.data.error < 0){
                        that.$api.msg(response.data.message);
                    }else{
                        that.$api.msg('发送成功');
                        _self.setInterValFunc();
                    }

                }).catch(function (error) {
                });



            },
            setInterValFunc() {
                this.time = 60;
                this.text = '重新发送';
                this.setTime = setInterval(() => {
                    if (this.time - 1 <= 0) {
                        this.time = '';
                        this.text = '重新获取';
                        this.code = '';
                        this.disabled = false;
                        clearInterval(this.setTime);
                    } else {
                        this.time--;
                    }
                }, 1000);
            },
            nextstep(){

			    if ( _self.wait == true){
			        return
                }


                this.$http.post('sms.register', {
                    mobile:this.mobile,
                }).then(function (response) {
                    console.log(response);
                    if(response.data.error < 0){
                        _self.$api.msg(response.data.message);
                    }else{
                        _self.$api.msg('发送成功');
                       _self.step++;
                        _self.setInterValFunc();
                    }
                }).catch(function (error) {
                });
            },


		},
    }
</script>

<style lang='scss'>
	page{
		background: #fff;
	}
	.container{
		padding-top: 115px;
		position:relative;
		width: 100vw;
		height: 100vh;
		overflow: hidden;
		background: #fff;
	}
	.wrapper{
		position:relative;
		z-index: 90;
		background: #fff;
		padding-bottom: 40upx;
	}
	.back-btn{
		position:absolute;
		left: 40upx;
		z-index: 9999;
		padding-top: var(--status-bar-height);
		top: 40upx;
		font-size: 40upx;
		color: $font-color-dark;
	}
	.left-top-sign{
		font-size: 120upx;
		color: $page-color-base;
		position:relative;
		left: -16upx;
	}
    .input-empty{
        color: #C2C2C2;
    }
	.right-top-sign{
		position:absolute;
		top: 80upx;
		right: -30upx;
		z-index: 95;
		&:before, &:after{
			display:block;
			content:"";
			width: 400upx;
			height: 80upx;
			background: #b4f3e2;
		}
		&:before{
			transform: rotate(50deg);
			border-radius: 0 50px 0 0;
		}
		&:after{
			position: absolute;
			right: -198upx;
			top: 0;
			transform: rotate(-50deg);
			border-radius: 50px 0 0 0;
			/* background: pink; */
		}
	}
	.left-bottom-sign{
		position:absolute;
		left: -270upx;
		bottom: -320upx;
		border: 100upx solid #d0d1fd;
		border-radius: 50%;
		padding: 180upx;
	}
	.welcome{
		position:relative;
		left: 50upx;
		top: -90upx;
		font-size: 46upx;
		color: #555;
		text-shadow: 1px 0px 1px rgba(0,0,0,.3);
	}
	.input-content{
		padding: 0 60upx;
	}
	.input-item{
		display:flex;
		flex-direction: column;
		align-items:flex-start;
		justify-content: center;
		padding: 0 30upx;
		background:#F2F2F2;
		height: 88rpx;
		border-radius: 44rpx;
		margin-bottom: 40rpx;
		&:last-child{
			margin-bottom: 0;
		}
		.tit{
			height: 50upx;
			line-height: 56upx;
			font-size: $font-sm+2upx;
			color: $font-color-base;
		}
		input{
			height: 60upx;
			font-size: $font-base + 2upx;
			color: $font-color-dark;
			width: 100%;
		}	
	}

	.confirm-btn{
		width: 630rpx;
		height: 88rpx;
		line-height: 88rpx;
		border-radius: 50px;
		margin-top: 80rpx;
        background: #F63E36;
		color: #fff;
		font-size: $font-lg;
		&:after{
			border-radius: 100px;
		}
	}
    .empty-btn{
        width: 630rpx;
        height: 88rpx;
        line-height: 88rpx;
        border-radius: 50px;
        border: #D0021B solid 1px;
        margin-top: 80rpx;
        color: #D0021B;
        font-size: $font-lg;
        &:after{
            border-radius: 100px;
        }
    }


    .waitclass{
        opacity: 0.5;

    }
	.forget-section{
		font-size: $font-sm+2upx;
		color: $font-color-spec;
		text-align: center;
		margin-top: 40upx;
	}
	.register-section{
		position:absolute;
		left: 0;
		bottom: 50upx;
		width: 100%;
		font-size: $font-sm+2upx;
		color: $font-color-base;
		text-align: center;
		text{
			color: $font-color-spec;
			margin-left: 10upx;
		}
	}
</style>
