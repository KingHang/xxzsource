<template>
	<view class="content ">
        <view style="padding: 30rpx;background-color: white">
            <u-search  v-model="keyword" @custom="searchfun" @search="searchfun"  :show-action="true" action-text="搜索"  :animation="true" shape="round" search-icon="search" ></u-search>
        </view>

        <view class="hlbblock" @click="radioChange(item)" v-for="(item, index) in list" style="display: flex;align-items: center;margin: 15rpx auto;padding: 20rpx" >
<!--            <view style="width: 10%">-->
<!--                <u-radio @change="radioChange(item)"  :name="item.id"   active-color="#f53630" shape="circle" ></u-radio>-->
<!--            </view>-->
            <view style="width: 80%">
                <view style="display: flex">
                    <view class="linedot" style="font-weight: bold">
                        {{item.store_name}}
                    </view>
                    <view v-if="item.nearest==1">
                        <u-tag style="margin-left: 15rpx"  size="mini" text="距离最近" type="success" border-color="#FFEBE9" bg-color="#FFEBE9" color="#F63E36" />
                    </view>
                </view>
                <view style="font-size: 25rpx;color: #999999">
                    {{item.region.province}}{{item.region.city}}{{item.region.region}}{{item.address}}
                </view>

            </view>
            <view style="">
                <view style="font-size: 25rpx;color: #999999">
                    {{item.distance_unit}}
                </view>
                <view style="display: flex;justify-content: flex-end">
                    <image src="/static/images/order/store/info.png" style="width: 40rpx" mode="widthFix"></image>

                </view>
            </view>
        </view>



        <u-popup v-model="pop" mode="bottom" border-radius="14">
            <view style="padding: 30rpx">
                <view style="text-align: center;margin-bottom: 30rpx">
                    门店信息
                </view>
                <u-section :title="choseitem.store_name" line-color="#F63E36"  :right="false" :bold="false"></u-section>

                <view style="display: flex;margin: 30rpx 0">
                    <view style="width: 17%;color: #999999">
                        地   址：
                    </view>
                    <view style="display: flex;justify-content: space-between;width: 80%">
                        <view>
                            {{choseitem.region.province}}{{choseitem.region.city}}{{choseitem.region.region}}{{choseitem.address}}
                        </view>
                        <image @click="tomap(choseitem.latitude,choseitem.longitude)" src="/static/images/order/store/map.png" style="width: 30rpx" mode="widthFix"></image>
                    </view>

                </view>
                <view v-if="choseitem.phone" style="display: flex;margin: 30rpx 0">
                    <view style="width: 17%;color: #999999">
                        电   话：
                    </view>
                    <view style="display: flex;justify-content: space-between;width: 80%">
                        <view>
                            {{choseitem.phone}}
                        </view>
                        <image @click="tomobile(choseitem.phone)" src="/static/images/order/store/mobile.png" style="width: 30rpx" mode="widthFix"></image>
                    </view>
                </view>
                <view v-if="choseitem.linkman" style="display: flex;margin: 30rpx 0">
                    <view style="width: 17%;color: #999999">
                        联系人：
                    </view>
                    <view style="display: flex;justify-content: space-between">
                        <view>
                            {{choseitem.linkman}}
                        </view>
                    </view>
                </view>

                <view @click="submit" style="margin: 30rpx auto;width: 85%; text-align: center;color: #FFFFFF;line-height: 70rpx;border-radius: 35rpx;background-color: #F63E36;" >
                    确定
                </view>


            </view>
        </u-popup>


	</view>
</template>

<script>
    var _self ;
	export default {
		data() {
			return {
                options:[],
                list:[],
                itemid:'',
                choseitem:[],
                pop:false,
                url:'',
                keyword:''

			}
		},
		onLoad(option){
		    _self = this;
		    _self.options = option
		},
        onShow() {

            this.getData();

        },
		methods: {
            tomobile(mobile){
                _self.phone(mobile)
            },
            radioChange(item){
                console.log(item)
              _self.choseitem = item;
              _self.pop =true;
            },
            searchfun(){
                let self = this;
                self._get('store.store/lists', {
                    longitude: self.longitude,
                    latitude: self.latitude,
                    shop_supplier_id:_self.options.chooseSotr,
                    url: self.url,
                    keyword:self.keyword
                }, function(res) {
                    console.log(res)
                    self.isLoading = false;
                    self.list = res.data.list;

                });
            },

            getData(isFirst) {
                let self = this;
                self.isLoading = true;
                uni.showLoading({
                    title: '加载中',
                    mask:true
                });


                uni.getLocation({
                    type: 'wgs84',
                    success: function (res) {
                        self.longitude = res.longitude;
                        self.latitude = res.latitude;
                        self._get('store.store/lists', {
                            longitude: self.longitude,
                            latitude: self.latitude,
                            shop_supplier_id:_self.options.chooseSotr,
                            url: self.url,
                            keyword:self.keyword
                        }, function(res) {
                            console.log(res)
                            self.isLoading = false;
                            self.list = res.data.list;

                        });
                    },
                    fail: (e) => {
                        uni.hideLoading()
                        console.log(e)
                        uni.showModal({
                            title: '提示',
                            content: '位置获取失败',   //此处不能为空否则在ios上无法弹出
                            showCancel: false,
                            success: function (res) {
                                wx.navigateBack();
                            }
                        })
                    }
                });
            },
            phone : function (mobile) {
                uni.makePhoneCall({
                    phoneNumber: mobile,
                    success: (res) => {
                        console.log('调用成功!')
                    },
                    fail: (res) => {
                        console.log('调用失败!')
                    }
                });
            },
            tomap : function(lat,lng){
                console.log(lat)
                if (lat==null||lat==''){
                    uni.showToast({
                        title: '暂无位置信息',
                        duration: 2000,
                        icon:'none'
                    });
                    return
                }
                uni.openLocation({
                    latitude: parseFloat(lat),
                    longitude: parseFloat(lng),
                    success: function () {
                    }
                });
            },
            submit: function() {
                uni.setStorageSync('orderShop',_self.choseitem)
                uni.navigateBack()
            },

		}
	}
</script>

<style lang='scss'>
	page{
        background: #F2F2F2;
		padding-bottom: 120upx;
	}
	.content{
		position: relative;
	}
	.list{
		display: flex;
		align-items: center;
		padding: 20upx 30upx;;
		background: #fff;
		position: relative;
	}
	.wrapper{
		display: flex;
		flex-direction: column;
		flex: 1;
	}
	.address-box{
		display: flex;
		align-items: center;
		.tag{
			font-size: 24upx;
			color: $base-color;
			margin-right: 10upx;
			background: #fffafb;
			border: 1px solid #ffb4c7;
			border-radius: 4upx;
			padding: 4upx 10upx;
			line-height: 1;
		}
		.address{
			font-size: 30upx;
			color: $font-color-dark;
		}
	}
	.u-box{
		font-size: 28upx;
		color: $font-color-light;
		margin-top: 16upx;
		.name{
			margin-right: 30upx;
		}
	}
	.icon-bianji{
		display: flex;
		align-items: center;
		height: 80upx;
		font-size: 40upx;
		color: $font-color-light;
		padding-left: 30upx;
	}

	.add-btn{
		position: fixed;
		left: 30upx;
		right: 30upx;
		bottom: 16upx;
		z-index: 95;
		display: flex;
		align-items: center;
		justify-content: center;
		width: 690upx;
		height: 80upx;
		font-size: 32upx;
		color: #fff;
		background-color: $base-color;
		border-radius: 10upx;
		box-shadow: 1px 2px 5px rgba(219, 63, 96, 0.4);
	}
</style>
