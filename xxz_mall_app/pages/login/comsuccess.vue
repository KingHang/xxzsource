<template>
    <view class="container" style="">


        <view style="width: 85%;margin:30rpx auto 0;position: relative">
            <image src="http://img.pighack.com/20220325104413d70380380.png" style="width: 100%" mode="widthFix"></image>
            <view style="position: absolute;top: 70rpx;left: 30rpx">

                <view  style="color: white">
                    {{setmsg}}
                </view>
                <view>
                    <image src="/static/images/commission/sf.png" style="width: 200rpx" mode="widthFix"></image>
                </view>

            </view>
        </view>
        <view class="hlbblock30" style="margin-top: 0rpx" >
            <u-steps :list="line" :current="current" mode="number" active-color="#f53630" un-active-color="#999999"></u-steps>



            <view v-if="chose.type==1">
                <view style="background-color: rgba(0,0,0,0.02);padding: 0 20rpx;margin-top: 30rpx;border-radius: 10rpx">

                    <u-input v-model="referee_id" placeholder="邀请人手机号/推广码" />
                </view>
                <view @click="next" class="hlbbutton" style="width: 300rpx;margin:50rpx auto;height: 96rpx;line-height: 96rpx;border-radius: 100rpx" >下一步</view>
                <view @click="nextpass" class="font2599" style="text-align: center">
                    跳过
                </view>

            </view>
            <view v-if="chose.type==2">


                <view style="margin-top: 30rpx;position: relative">
                    <image src="http://img.pighack.com/20220325104039e44431007.png" style="width: 100%" mode="widthFix"></image>
                    <view style="padding: 30rpx;position: absolute;top: 0;left: 0;width: 100%;text-align: center">
                        <image :src="img" style="width: 60%;display: inline-block;margin-top: 110rpx" mode="widthFix" @click="viewPreviewImage(img)"></image>
                    </view>
                </view>

                <view  @click="nextpassaddkf" class="hlbbutton" style="width: 300rpx;margin:50rpx auto" >我已添加</view>
            </view>
            <view v-if="chose.type==3">
                <view style="padding: 30rpx;position: relative">
                    <image src="https://img.dfhlyl.com/20220902011549d4fd05535.png" style="width: 100%" mode="widthFix"></image>
                </view>
                <view style="text-align: center;width: 70%;margin: auto" class="font2599">
                    您的店主申请已提交，
                </view>
                <view style="text-align: center;width: 70%;margin: auto" class="font2599">
                    我们将于1-2个工作日内尽快为您审核
                </view>
            </view>
            <view v-if="chose.type==4">
                <view @click="actionbuy"   class="hlbbutton" style="width: 500rpx;margin:50rpx auto;height: 96rpx;border-radius: 50rpx" >
                    <view style="font-size: 32rpx">
                        去购买
                    </view>
                    <view style="font-size: 24rpx">
                        已完成 ￥{{expend_money}}
                    </view>
                </view>
            </view>


        </view>

        <view class="hlbblock30" v-if="chose.type==5">
            <view style="text-align: center">
               商品列表
            </view>
            <view style="text-align: center;width: 70%;margin: auto" class="font2599">
                购买以下任意一件商品即可成为店主
            </view>

            <block v-for="item in product">
                <navigator :url="'/pages/product/detail/detail?product_id='+item.product_id" open-type="redirect"  style="display: flex;width: 100%;justify-content: space-between;">
                    <view  style="width: 40%">
                        <image :src="item.image" style="width: 200rpx;height: 190rpx;border-radius: 15rpx"  mode="aspectFill"></image>
                    </view>
                    <view style="width: 65%;margin-left: 10rpx">
                        <view style="height: 100rpx">
                            <view class="linedot">
                                {{item.product_name}}
                            </view>
                        </view>
                        <view style="display: flex;justify-content: space-between;height: 55rpx;align-items: baseline">
                            <view style="color: #F63E36">
                                <text style="font-size: 20rpx">¥</text><text style="font-size: 35rpx">{{item.product_price}}</text>
                            </view>

                            <view  class="hlbbutton"  >立即购买</view>


                        </view>
                    </view>
                </navigator>
            </block>


        </view>



        <view style="position: fixed;bottom: 80rpx;width: 100%;text-align: center">
            <view style="color: #999999;font-size: 24rpx;text-align: center">
                申请店主即表示您同意
            </view>
            <view style="color: #F63E36;font-size: 24rpx;text-align: center">
                《店主服务协议》、《隐私政策》
            </view>
        </view>
    </view>
</template>

<script>
    var _self;
    export default{
        computed: {
        },
        data() {
            return {
                ish5: false,
                line: [],
                chose: [],
                cominfo: [],
                img: '',
                current: 0,
                referee_id: '',
                agentid: '',
                setmsg: '',
                product: [],
                expend_money: 0
            };
        },
        onShow(){
            // _self.isLogin();
            //#ifdef H5
            this.ish5 = true
            //#endif
        },
        async onLoad(options){
            _self = this;
            _self.referee_id = options.referee_id;
			_self.getdata();
        },
        methods:{
            actionbuy(){
                setTimeout(function () {
                    uni.switchTab({
                        url:'/pages/index/index'
                    })
                },200)
            },

            nextpass(){
                _self.current++;
                _self.chose = _self.line[_self.current]
                _self.referee_id = 0
            },
			
            //添加客服才发起申请
            nextpassaddkf(){
                _self.current++;
                _self.chose = _self.line[_self.current]
                console.log(_self.chose)
                //直接发起申请
                _self._post('plugin.agent.apply/apply', {
                    mobile:_self.referee_id
                }, function(res) {
                    uni.hideLoading()
                    console.log(res)
                });
            },
			
            next(){
                _self.current++;
                _self.chose = _self.line[_self.current]
            },
			
            getdata(){
                let self = this;
                uni.showLoading({
                    title: '加载中',
                    mask:true
                });
				
                self._post('user.agent/process', {
                }, function(res) {
                    uni.hideLoading()
                    self.line = res.data.step
                    self.img = res.data.kefu;

                    _self.current = res.data.current;
                    self.expend_money = res.data.expend_money

                    _self.setmsg = res.data.setmsg;

                    _self.chose = _self.line[_self.current]

                    _self.product = res.data.product_list
                });
            },
			
			// 预览图片
			viewPreviewImage(img){
				let images = [];
				images.push(img);
				uni.previewImage({
					current: 0,
					urls: images,
					longPressActions: {
						itemList: ['保存图片'],
						success: (data) => {
							console.log(data);
						},
						fail: (err) => {
							console.log(err.errMsg);
						}
					}
				});
			}
        },
    }
</script>

<style lang='scss'>

.bag{
    background-color: #F63E36;
    color: white;width:30rpx;
    height: 30rpx;
    border-radius: 30rpx;
    line-height: 30rpx;
    position: absolute;
    right: 0;
    top: 0;
    font-size: 20rpx;
    z-index: 99
}
.disable{
    background-color: #e9e9e9 !important;
}
    page{
        background: #F2F2F2;
        padding-bottom: 160upx;
    }
    .choseladder{
        background-color: #ff4a4b !important;
        color: white;
    }
    .icon-you{
        font-size: $font-base + 2upx;
        color: #888;
    }
    .carousel {
        height: 722upx;
        position:relative;
        swiper{
            height: 100%;
        }
        .image-wrapper{
            width: 100%;
            height: 100%;
        }
        .swiper-item {
            display: flex;
            justify-content: center;
            align-content: center;
            height: 750upx;
            overflow: hidden;
            image {
                width: 100%;
                height: 100%;
            }
        }

    }
    .confirm-btn{
        width: 700rpx;
        height: 80rpx;
        line-height: 80rpx;
        border-radius: 50px;
        background: #CC2E2F;
        color: #E4E4E4;
        font-size: $font-lg;
        &:after{
            border-radius: 100px;
        }
    }

    /* 标题简介 */
    .introduce-section{
        background: #fff;
        padding: 20upx 30upx;


        .price-box{
            display:flex;
            align-items:baseline;
            justify-content: space-between;
            height: 64upx;
            padding: 10upx 0;
            font-size: 26upx;
            color:$uni-color-primary;
        }
        .price{
            font-size: $font-lg + 2upx;
            .m-price{
                font-size: 25rpx;
                margin:0 12upx;
                color: $font-color-light;
                text-decoration: line-through;
            }
        }

        .coupon-tip{
            align-items: center;
            padding: 4upx 10upx;
            background: $uni-color-primary;
            font-size: $font-sm;
            color: #fff;
            border-radius: 6upx;
            line-height: 1;
            transform: translateY(-4upx);
        }
        .bot-row{
            display:flex;
            align-items:center;
            height: 50upx;
            font-size: $font-sm;
            color: $font-color-light;
            text{
                flex: 1;
            }
        }
    }
    /* 分享 */
    .share-section{
        display:flex;
        align-items:center;
        color: $font-color-base;
        background: linear-gradient(left, #fdf5f6, #fbebf6);
        padding: 12upx 30upx;
        .share-icon{
            display:flex;
            align-items:center;
            width: 70upx;
            height: 30upx;
            line-height: 1;
            border: 1px solid $uni-color-primary;
            border-radius: 4upx;
            position:relative;
            overflow: hidden;
            font-size: 22upx;
            color: $uni-color-primary;
            &:after{
                content: '';
                width: 50upx;
                height: 50upx;
                border-radius: 50%;
                left: -20upx;
                top: -12upx;
                position:absolute;
                background: $uni-color-primary;
            }
        }
        .icon-xingxing{
            position:relative;
            z-index: 1;
            font-size: 24upx;
            margin-left: 2upx;
            margin-right: 10upx;
            color: #fff;
            line-height: 1;
        }
        .tit{
            font-size: $font-base;
            margin-left:10upx;
        }
        .icon-bangzhu1{
            padding: 10upx;
            font-size: 30upx;
            line-height: 1;
        }
        .share-btn{
            flex: 1;
            text-align:right;
            font-size: $font-sm;
            color: $uni-color-primary;
        }
        .icon-you{
            font-size: $font-sm;
            margin-left: 4upx;
            color: $uni-color-primary;
        }
    }

    .c-list{
        font-size: $font-sm + 2upx;
        color: $font-color-base;
        background: #fff;
        .c-row{
            display:flex;
            align-items:center;
            padding: 20upx 30upx;
            position:relative;
        }
        .tit{
            width: 140upx;
        }
        .con{
            flex: 1;
            color: $font-color-dark;
            .selected-text{
                margin-right: 10upx;
            }
        }
        .bz-list{
            height: 40upx;
            font-size: $font-sm+2upx;
            color: $font-color-dark;
            text{
                display: inline-block;
                margin-right: 30upx;
            }
        }
        .con-list{
            flex: 1;
            display:flex;
            flex-direction: column;
            color: $font-color-dark;
            line-height: 40upx;
        }
        .red{
            color: $uni-color-primary;
        }
    }

    /* 评价 */
    .eva-section{
        display: flex;
        flex-direction: column;
        padding: 20upx 30upx;
        background: #fff;
        margin-top: 16upx;
        .e-header{
            display: flex;
            align-items: center;
            height: 70upx;
            font-size: $font-sm + 2upx;
            color: $font-color-light;
            .tit{
                font-size: $font-base + 2upx;
                color: $font-color-dark;
                margin-right: 4upx;
            }
            .tip{
                flex: 1;
                text-align: right;
            }
            .icon-you{
                margin-left: 10upx;
            }
        }
    }
    .eva-box{
        display: flex;
        padding: 20upx 0;
        .portrait{
            flex-shrink: 0;
            width: 80upx;
            height: 80upx;
            border-radius: 100px;
        }
        .right{
            flex: 1;
            display: flex;
            flex-direction: column;
            font-size: $font-base;
            color: $font-color-base;
            padding-left: 26upx;
            .con{
                font-size: $font-base;
                color: $font-color-dark;
                padding: 20upx 0;
            }
            .bot{
                display: flex;
                justify-content: space-between;
                font-size: $font-sm;
                color:$font-color-light;
            }
        }
    }
    /*  详情 */
    .detail-desc{
        background: #fff;
        margin-top: 16upx;
        .d-header{
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80upx;
            font-size: $font-base + 2upx;
            color: $font-color-dark;
            position: relative;

            text{
                padding: 0 20upx;
                background: #fff;
                position: relative;
                z-index: 1;
            }
            &:after{
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translateX(-50%);
                width: 300upx;
                height: 0;
                content: '';
                border-bottom: 1px solid #ccc;
            }
        }
    }

    /* 规格选择弹窗 */
    .attr-content{
        padding: 10upx 30upx;
        .a-t{
            display: flex;

            .right{
                display: flex;
                flex-direction: column;
                padding-left: 24upx;
                font-size: $font-sm + 2upx;
                color: $font-color-base;
                line-height: 42upx;
                .price{
                    font-size: $font-lg;
                    color: $uni-color-primary;
                    margin-bottom: 10upx;
                }
                .selected-text{
                    margin-right: 10upx;
                }
            }
        }
        .attr-list{
            display: flex;
            flex-direction: column;
            font-size: $font-base + 2upx;
            color: $font-color-base;
            padding-top: 30upx;
            padding-left: 10upx;
        }
        .item-list{
            padding: 20upx 0 0;
            display: flex;
            flex-wrap: wrap;
            text{
                display: flex;
                align-items: center;
                justify-content: center;
                background: #eee;
                margin-right: 20upx;
                margin-bottom: 20upx;
                border-radius: 100upx;
                min-width: 60upx;
                height: 60upx;
                padding: 0 20upx;
                font-size: $font-base;
                color: $font-color-dark;
            }
            .selected{
                background: #fbebee;
                color: $uni-color-primary;
            }
        }
    }

    /*  弹出层 */
    .popup {
        position: fixed;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 199;

        &.show {
            display: block;
            .mask{
                animation: showPopup 0.2s linear both;
            }
            .layer {
                animation: showLayer 0.2s linear both;
            }
            .layer2 {
                animation: showLayer 0.2s linear both;
            }
        }
        &.hide {
            .mask{
                animation: hidePopup 0.2s linear both;
            }
            .layer {
                animation: hideLayer 0.2s linear both;
            }
            .layer2 {
                animation: hideLayer 0.2s linear both;
            }
        }
        &.none {
            display: none;
        }
        .mask{
            position: fixed;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .layer {
            position: fixed;
            z-index: 99;
            bottom: 0;
            width: 100%;
            min-height: 40vh;
            border-radius: 30rpx 30rpx 0 0;
            background-color: #fff;
            .btn{
                height: 66upx;
                line-height: 66upx;
                border-radius: 100upx;
                background: $uni-color-primary;
                font-size: $font-base + 2upx;
                color: #fff;
                margin: 30upx auto 20upx;
            }
        }
        .layer2 {
            position: fixed;
            z-index: 10075;
            bottom: 0;
            width: 100%;
            height: 350rpx;
            border-radius: 50rpx 50rpx 0 0;
            background-color: #fff;

        }
        @keyframes showPopup {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
        @keyframes hidePopup {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
        @keyframes showLayer {
            0% {
                transform: translateY(120%);
            }
            100% {
                transform: translateY(0%);
            }
        }
        @keyframes hideLayer {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(120%);
            }
        }
    }

    /* 底部操作菜单 */
    .page-bottom{
        position:fixed;
        left: 30upx;
        bottom:30upx;
        z-index: 95;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 690upx;
        height: 100upx;
        background: rgba(255,255,255,.9);
        box-shadow: 0 0 20upx 0 rgba(0,0,0,.5);
        border-radius: 16upx;

        .p-b-btn{
            display:flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: $font-sm;
            color: $font-color-base;
            width: 96upx;
            height: 80upx;
            .yticon{
                font-size: 40upx;
                line-height: 48upx;
                color: $font-color-light;
            }
            &.active, &.active .yticon{
                color: $uni-color-primary;
            }
            .icon-fenxiang2{
                font-size: 42upx;
                transform: translateY(-2upx);
            }
            .icon-shoucang{
                font-size: 46upx;
            }
        }
        .action-btn-group{
            display: flex;
            height: 76upx;
            border-radius: 100px;
            overflow: hidden;
            box-shadow: 0 20upx 40upx -16upx #fa436a;
            box-shadow: 1px 2px 5px rgba(219, 63, 96, 0.4);
            background: linear-gradient(to right, #ffac30,#fa436a,#F56C6C);
            margin-left: 20upx;
            position:relative;
            &:after{
                content: '';
                position:absolute;
                top: 50%;
                right: 50%;
                transform: translateY(-50%);
                height: 28upx;
                width: 0;
                border-right: 1px solid rgba(255,255,255,.5);
            }
            .action-btn{
                display:flex;
                align-items: center;
                justify-content: center;
                width: 180upx;
                height: 100%;
                font-size: $font-base ;
                padding: 0;
                border-radius: 0;
                background: transparent;
            }
        }
    }

</style>
