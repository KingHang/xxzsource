<template>
    <view>
        <view class="shop_head" style="position: relative">
            <view style="position: absolute;height: 500rpx;width: 100%">
                <image class="bg_topimg" src="http://img.pighack.com/202203182258182055d9805.png" style="width: 100%" mode="widthFix"></image>
            </view>
            <!-- #ifdef MP-WEIXIN || APP-PLUS -->
            <view class="ww100" :style="'height:'+topBarTop()+'px;'"></view>
            <view class="tc  head_top" :style="topBarHeight() == 0 ? '': 'height:'+topBarHeight()+'px;'">
                <view class="reg180" @click="goback"><text class="icon iconfont icon-jiantou"></text></view>
                <view class="fb">我的店铺</view>
            </view>
            <!-- #endif -->
			<!-- #ifdef H5 -->
			<!-- #endif -->
            <view style="position: absolute;left: 0;top:190rpx;width: 100%">
                <view class="fsc"  style="position: relative; width: 93%;border-radius: 15rpx;padding: 10rpx;margin: auto">

                    <view style="display: flex;align-items: center">
                        <view style="">
                            <image :src="shop_info.logos||'/static/images/shop/shopdefault.png'" style="width: 100rpx;height:100rpx;border-radius: 15rpx" mode="widthFix"></image>
                        </view>
                        <view style="margin-left: 20rpx">
                            <view style="color: white;font-weight: bold">
                                {{shop_info.store_name}}
                            </view>
                            <view style="display: flex">
                                <view v-if="shop_info.category_name">
                                    <view style="background:#F63E36;color: white;font-size: 20rpx;padding: 0rpx 10rpx;height: 32rpx">
                                        {{shop_info.category_name?shop_info.category_name:''}}
                                    </view>
                                </view>
                                <view style="margin-left: 10rpx">
                                    <view style="display: flex;background:#D8DEDE;color: white;font-size: 20rpx;padding: 5rpx 15rpx;border-radius: 20rpx;align-items: center;height: 32rpx">
                                        <!-- #ifdef MP-WEIXIN || APP-PLUS -->
										<view style="font-size: 20rpx;">
                                            店铺评分
                                        </view>
										<!-- #endif -->
                                        <view style="margin-bottom: 8rpx;margin-left: 3rpx">
                                            <u-rate active-color="#F63E36"  gutter="5" size="20" v-model="shop_info.server_score"   ></u-rate>
                                        </view>
                                    </view>
                                </view>
                                <view style="font-size: 20rpx;color: white;margin-left: 3rpx">
                                    销量{{shop_info.product_sales}}
                                </view>
                            </view>

                        </view>

                    </view>
                    <view @click="guanzhu()">
                        <view v-if="isfollow == 0" style="display: flex;background:white;color: #F63E36;font-size: 24rpx;padding:5rpx 30rpx;border-radius: 50rpx;align-items: center;">
                            <u-icon name="plus" color="#F63E36" style="margin-right: 0rpx"></u-icon>
                            <view style="font-weight: bold">
                                关注
                            </view>
                        </view>
                        <view v-else>
                            <view  style="display: flex;background:white;font-size: 24rpx;padding:5rpx 30rpx;border-radius: 50rpx;align-items: center;">
                                <view style="font-weight: bold;color: #999999">
                                    已关注
                                </view>
                            </view>
                        </view>
                    </view>
                </view>
            </view>
            <!-- 优惠券 -->
			<diy :style="'position: relative;top: ' + top_height + 'px;'" :diyItems="items" v-if="showDiy"></diy>
            <!-- 商品导航栏 -->
            <view style="width: 100%;margin-top: 270rpx" v-else>
                <view style="padding: 30rpx;" >
                    <view style="width: 100%;display: flex;align-items: center">
                        <view style="width: 80%">
                            <u-search border-color="#F63E36" v-model="search" @custom="onsearch" @search="onsearch"  :show-action="true" action-text="搜索"  :animation="true" shape="round" search-icon="search" ></u-search>
                        </view>
                        <view @click="distype==0?distype=1:distype=0" style="margin-left: 40rpx">
                            <image :src="'/static/images/goods/dis'+distype+'.png'" style="width: 45rpx;" mode="widthFix"></image>
                        </view>
                    </view>
                </view>
                <view style="display: flex;justify-content: space-around;align-items: center">
                    <view @click="tabTypeFunc(0)" :class="type_active==0?'chosecolor':''" style="text-align: center">
                        综合
                    </view>
                    <view @click="tabTypeFunc(1)" :class="type_active==1?'chosecolor':''" style="text-align: center">
                        销量
                        <image :src="'/static/images/goods/sort'+salesort+'.png'" style="width: 15rpx;margin-left: 5rpx;height: 30rpx" ></image>
                    </view>
                    <view @click="tabTypeFunc(2)" :class="type_active==2?'chosecolor':''" style="text-align: center">
                        价格
                        <image :src="'/static/images/goods/sort'+pricesort+'.png'" style="width: 15rpx;margin-left: 5rpx;height: 30rpx" ></image>
                    </view>
                </view>
            </view>
        </view>
        <scroll-view style="margin-top: 30rpx" v-if="" scroll-y="true" :style="'height:' + scrollviewHigh + 'px;'" class="scroll-Y pr bg-f2"
                     lower-threshold="50" @scrolltolower="scrolltolowerFunc" v-if="shop_info!='' && !showDiy">
            <view class="shop">
                <view class="" v-if="distype ==1&&nav_type==0">

                    <u-row gutter="25" style="">
                        <block v-for="(item,index) in product_list" :key="index">
                            <u-col span="6">
                                <navigator @click="goto_product(item.product_id)" style="background-color: white;border-radius: 15rpx;overflow: hidden;margin-top: 30rpx" >
                                    <image  :src="item.product_image" style="width: 100%;height: 300rpx" mode="aspectFill"></image>
                                    <view style="height: 170rpx;position: relative;padding: 20rpx;">
                                        <view class="linedot" style="font-size: 25rpx;line-height: 33rpx;">
                                            {{item.product_name}}
                                        </view>
                                        <view class="fsc" style="bottom: 20rpx;color: #F63E36;margin-top: 50rpx">
                                            <view>
                                                ¥{{item.product_price}}
                                            </view>
                                            <view style="font-size: 24rpx;color: #999999;">
                                                已售{{item.product_sales}}件
                                            </view>

                                        </view>

                                    </view>
                                </navigator>
                            </u-col>
                        </block>
                    </u-row>
                </view>
                <view class=""  v-if="distype ==0&&nav_type==0">
                    <view class="hlbblock30"  v-for="item in product_list">
                        <view @click="goto_product(item.product_id)"  style="display: flex;width: 100%;justify-content: space-between;">
                            <view  style="width: 40%">
                                <image :src="item.product_image" style="width: 200rpx;height: 190rpx;border-radius: 15rpx"  mode="aspectFill"></image>
                            </view>
                            <view style="width: 65%;margin-left: 10rpx">
                                <view style="height: 130rpx">
                                    <view class="linedot">
                                        {{item.product_name}}
                                    </view>
                                    <view style="font-size: 24rpx;color: #999999;margin-top: 40rpx">
                                        已售{{item.product_sales}}件
                                    </view>
                                </view>
                                <view style="display: flex;justify-content: space-between;height: 55rpx;align-items: baseline">
                                    <view style="color: #F63E36">

                                        <text style="font-size: 28rpx;color: #F63E36">¥{{item.product_price}}</text><text style="font-size: 35rpx"></text>
                                        <text class="huaxianjia" style="font-size: 24rpx;color: #999999">¥{{item.line_price}}</text>
                                    </view>

                                </view>
                            </view>
                        </view>
                    </view>
                </view>
            </view>
            <view v-if="nav_type==0 && !showDiy">
                <view class="d-c-c p30" v-if="product_list.length==0 && !loading">
                    <text class="iconfont icon-wushuju"></text>
                    <text class="cont">亲，暂无相关记录哦</text>
                </view>
                <uni-load-more v-else :loadingType="loadingType"></uni-load-more>
                <view class="" style="width: 100%;height: 100rpx;">

                </view>
            </view>
        </scroll-view>
        <!-- 底部导航 -->
       <!-- <view class="d-a-c nav_bottom">
            <view :class="nav_type==0?'active':''" @click="nav_type=0">
                <view class="icon iconfont icon-Homehomepagemenu"></view>
                <view>首页</view>
            </view>
            <view v-if="is_record==1&&is_open==1" :class="nav_type==1?'active':''" @click="nav_type=1">
                <view class="icon iconfont icon-dianpu1"></view>
                <view>直播</view>
            </view>
            <view v-if="listData.length>0" :class="nav_type==2?'active':''" @click="nav_type=2">
                <view class="icon iconfont icon-quan"></view>
                <view>优惠券</view>
            </view>
            <view v-if="service_open" :class="nav_type==3?'active':''" @click="toSevice">
                <view class="icon iconfont icon-kefu2"></view>
                <view>客服</view>
            </view>
        </view>-->
    </view>
</template>

<script>
	import diy from '@/components/diy/diy.vue';
    import uniLoadMore from "@/components/uni-load-more.vue";
    export default {
        components: {
            uniLoadMore
        },
        data() {
            return {
                isLieBiao: true,
                shop_info: '', //店铺信息
                product_list: '', //商品列表
                adList: '', //banner列表
                dataList: '',
                shop_supplier_id: '', //店铺ID
                isfollow: '', // 是否关注本店
                // ***********
                /*是否显示点*/
                indicatorDots: false,
                /*是否自动*/
                autoplay: true,
                /*切换时间*/
                interval: 5000,
                /*动画过渡时间*/
                duration: 1000,
                /*数据列表*/
                listData: [], //优惠券列表
                // ************
                /*顶部刷新*/
                topRefresh: false,
                /*底部加载*/
                loading: true,
                /*没有更多*/
                no_more: false,
                /*类别选中*/
                type_active: 'all',

                /*店铺列表*/
                shopData: [],
                /*当前页面*/
                page: 1,
                search: '',
                last_page: 0,
                /*可滚动视图区域高度*/
                scrollviewHigh: 0,
                nav_type: 0,
                is_open: 0,
                is_record: 0,
                liveList: [],
                dataModel: {
                    qq: '',
                    wechat: '',
                    phone: ''
                },
                service_type: 0,
                service_open: 0,
                statusBarHeight: 0,
                titleBarHeight: 0,
                distype:0,
                pricesort:'',
                salesort:'',
                price_top: false,
                sale_top: false,
                sortPrice: 0,
                sortsale: 0,
				items: [],
				showDiy: false,
				pageTitle: '',
				top_height: 120
            }
        },
        computed: {
            /*加载中状态*/
            loadingType() {
                if (this.loading) {
                    return 1;
                } else {
                    if (this.product_list.length != 0 && this.no_more) {
                        return 2;
                    } else {
                        return 0;
                    }
                }
            }
        },
        onLoad(option) {
            let self = this;
			//#ifdef H5
			self.top_height = 180;
			// #endif
            self.GetStatusBarHeight();
			console.log(option)
            self.shop_supplier_id = option.shop_supplier_id;
        },
        onShow() {

        },
        mounted() {
			let self = this;
			self.getData();
			self.getProduct(self.type_active);
			
        },
        onPullDownRefresh() {
            /*下拉到顶，页面值还原初始化*/
            this.restoreData();
            this.getData();
            this.getProduct(self.type_active);
        },
		created() {
			
		},
        methods: {
            GetStatusBarHeight() {
                // #ifdef MP-WEIXIN
                let that = this;
                const SystemInfo = uni.getSystemInfoSync();
                let statusBarHeight = SystemInfo.statusBarHeight;
                this.statusBarHeight = uni.getMenuButtonBoundingClientRect().top;
                this.titleBarHeight = uni.getMenuButtonBoundingClientRect().height;
                // #endif
                // #ifndef MP-WEIXIN
                const SystemInfo = uni.getSystemInfoSync();
                this.statusBarHeight = SystemInfo.statusBarHeight;
                this.titleBarHeight = 30
                // #endif
            },
            /*初始化*/
            init() {
                let self = this;
                uni.getSystemInfo({
                    success(res) {
                        self.phoneHeight = res.windowHeight;
                        // 计算组件的高度
                        let view = uni.createSelectorQuery().in(self).select('.shop_head');
                        view.boundingClientRect(data => {
                            let h = self.phoneHeight - data.height;
                            self.scrollviewHigh = h;
                        }).exec();
                    }
                });
            },
            /*可滚动视图区域到底触发*/
            scrolltolowerFunc() {
                let self = this;
                self.bottomRefresh = true;
                self.page++;
                self.loading = true;
                if (self.page > self.last_page) {
                    self.loading = false;
                    self.no_more = true;
                    return;
                }
                self.getProduct(self.type_active);
            },
            getProduct() {
                let self = this;
                let page = self.page;
                self.loading = true;
                self._get('goods.goods/lists', {
                    page: page || 1,
                    sortType: self.sortType,
                    sortPrice: self.sortPrice,
                    sortSale: self.sortsale,
                    search: self.search,
                    shop_supplier_id: self.shop_supplier_id,
                }, function(res) {
                    self.loading = false;
                    self.product_list = [...self.product_list, ...res.data.list.data];
                    self.last_page = res.data.list.last_page;
                    if (res.data.list.last_page <= 1) {
                        self.no_more = true;
                    }
                });
            },
            /*还原初始化*/
            restoreData() {
                this.shopData = [];
                this.product_list = [];
                this.page = 1;
                this.category_id = 0;
                this.search = '';
                this.sortType = '';
                this.sortPrice = 0;
            },
            getservice() {
                let self = this;
                self.isloding = true;
                self._get(
                    'index/mpService', {
                        shop_supplier_id: self.shop_supplier_id
                    },
                    function(res) {
                        self.dataModel = res.data.mp_service;
                        self.isloding = false;
                    }
                );
            },
            /*类别切换*/
            tabTypeFunc(e) {
                
                console.log(e)

                let self = this;
                    self.product_list = [];
                    self.page = 1;
                    self.no_more = false;
                    self.loading = true;
                    self.type_active = e;

                    if (e == 2) {
                        self.price_top = !this.price_top;
                        if (self.price_top == true) {
                            self.sortPrice = 0;
                            self.pricesort ='up';
                        } else {
                            self.sortPrice = 1;
                            self.pricesort ='down';
                        }
                        self.salesort ='';
                        self.sortType = 'price';
                    } else if (e == 1) {
                        self.sale_top = !this.sale_top;
                        if (self.sale_top == true) {
                            self.sortsale = 0;
                            self.salesort ='up';
                        } else {
                            self.sortsale = 1;
                            self.salesort ='down';
                        }
                        console.log(self.salesort)
                        self.pricesort ='';
                        self.sortType = 'sales';
                    }else {
                        self.pricesort='';
                            self.salesort='';
                            self.price_top= false;
                            self.sale_top= false;
                    }


                    self.getProduct();

            },
            onsearch(){
                let self = this;
                self.product_list = [];
                self.page = 1;
                self.no_more = false;
                self.loading = true;
                self.getProduct();
            },

            //获取数据列表
            getData() {
                let self = this;
                self.loading = true;
                self._post('purveyor.index/index', {
                    shop_supplier_id: self.shop_supplier_id,
                    visitcode: self.getVisitcode()
                }, (res) => {
                    self.loading = false;
                    // self.page = res.data.productList.current_page
                    // self.last_page = res.data.productList.last_page
                    // self.product_list = [...self.product_list, ...res.data.productList.data];
					
					// self.pageTitle = res.data.page.page.params.name
					// console.log(111111)
					// console.log(self.pageTitle)
					// uni.setNavigationBarTitle({
					// 	title: self.pageTitle
					// })
                    self.shop_info = res.data.detail;
                    self.adList = res.data.adList;
                    self.isfollow = res.data.detail.isfollow;
                    self.listData = res.data.couponList;
                    self.is_record = res.data.liv_status.is_record;
                    self.is_open = res.data.liv_status.is_open;
                    self.liveList = res.data.liveList.data;
                    self.service_open = res.data.service_open;
					if (res.data.page && res.data.page.items) {
						self.items = res.data.page.items;
						self.showDiy = true
					}
					
                    if (res.data.mp_service) {
                        self.service_type = res.data.mp_service.service_type;
                    } else {
                        self.service_type = 10
                    }
                    this.$nextTick(function() {
                        self.init();
                    })
                    self.shop_info.server_score = parseFloat(self.shop_info.server_score);

                    self.getservice();
                })
            },

            //选择图标模式或者列表模式    true 为列表模式  false 为图表模式
            select_type() {
                let self = this;
                self.isLieBiao = !self.isLieBiao
            },
            //跳转商品页面
            goto_product(product_id) {
                this.gotoPage('/pages/product/detail/detail?product_id=' + product_id);
            },
            //关注店铺/取消关注
            guanzhu() {
                let self = this;
                self._post('user.Favorite/add', {
                    pid: self.shop_supplier_id,
                    type: 10
                }, (res) => {
                    if (self.isfollow == 0) {
                        self.isfollow = 1
                    } else if (self.isfollow == 1) {
                        self.isfollow = 0
                    }
                })
            },
            /**
             * 领取优惠券
             */
            receiveCoupon: function(index) {
                let self = this;
                let item = self.listData[index];
                if (item.state.value == 0) {
                    return false;
                }
                self._post(
                    'user.coupon/receive', {
                        coupon_id: item.coupon_id
                    },
                    function(result) {
                        uni.showToast({
                            title: '领取成功',
                            icon: 'success',
                            mask: true,
                            duration: 2000
                        });
                        item.state.value = 0;
                        item.state.text = '已领取';
                    }
                );
                self.getData(self.type_active);
            },
            /*复制*/
            copyQQ(message) {
                //#ifdef MP-WEIXIN
                uni.setClipboardData({
                    //准备复制的数据
                    data: message,
                    success: function(res) {
                        uni.showToast({
                            title: '复制成功',
                            icon: 'success',
                            mask: true,
                            duration: 2000
                        });
                    }
                });
                //#endif
                //#ifdef H5
                var input = document.createElement("input");
                input.value = message;
                document.body.appendChild(input);
                input.select();
                input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
                document.body.removeChild(input);
                uni.showToast({
                    title: '复制成功',
                    icon: 'success',
                    mask: true,
                    duration: 2000
                });
                //#endif
            },
            /*拨打电话*/
            callPhone(phone) {
                uni.makePhoneCall({
                    phoneNumber: phone
                });
            },
            toRoom(item) {
                if (item.record_url != '') {
                    this.gotoPage('/pagesLive/live/live?room_id=' + item.room_id + "&sence=join");
                } else {
                    return false
                }
            },
            toSevice() {
                if (this.service_type == 10 || this.shop_info.user_id == uni.getStorageInfoSync('user_id')) {
                    this.nav_type = 3;
                } else if (this.service_type == 20) {
                    this.gotoPage('/pagesChat/chat/chat?user_id=' + this.shop_info.supplier_user_id + '&shop_supplier_id=' + this.shop_info.shop_supplier_id +
                        '&nickName=' + this.shop_info.store_name);
                }
            },
            goback() {
                uni.navigateBack({

                })
            }
        }
    }
</script>

<style lang="scss">
    page {
        background: #f2f2f2;
    }
    image{
        display: inline-block;
    }
    .goods_box{
        width: 95%;
        margin: 0 auto;
        .goods_item{
            display: flex;
            align-items: center;
            width: 100%;
            height: 220rpx;
            margin-top: 35rpx;
            background-color: white;
            .img{
                width: 30%;
                image{

                    width: 100%;
                    height: 220rpx;
                }
            }
            .nav{
                width: 60%;
                height: 200rpx;
                margin-left: 5%;
                position: relative;
                .title{
                    font-size: 27rpx;
                }

                .tobuy{
                    text-align: center;
                    background-color: #F63E36;
                    color: #FFFFFF;
                    padding: 2rpx 30rpx;
                    border-radius: 30rpx;
                    width: 150rpx;
                }
            }
        }
    }

    .bg-f2 {
        background-color: #F2F2F2;
    }

    .h1 {
        font-size: 32rpx;
    }

    .h2 {
        font-size: 28rpx;
    }

    .h3 {
        font-size: 24rpx;
    }

    .h4 {
        font-size: 20rpx;
    }

    .h5 {
        font-size: 16rpx;
    }

    .h6 {
        font-size: 12rpx;
    }

    .red {
        color: #E2231A;
    }

    .huaxianjia {
        text-decoration: line-through;
        color: #999;
        margin-left: 5rpx;
    }

    .shop_head {}

    .prodcut-list-wrap {
        padding-bottom: 100rpx;
    }

    .shop_head_info {
        width: 100%;
        margin: 0 auto;
        position: relative;
        padding: 80rpx 20rpx 50rpx 20rpx;
        box-sizing: border-box;
        // background-color: white;
    }

    .shop_list_body_item_shop {
        width: 100%;
        height: 120rpx;
        display: flex;
        justify-content: space-between;
    }

    .shop_list_body_item_shop_logo {
        width: 120rpx;
        height: 120rpx;
    }

    .shop_list_body_item_shop_logo image {
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 15rpx;
    }

    .shop_list_body_item_shop_info {
        // padding: 10rpx;
        flex: 1;
        margin-left: 32rpx;
        box-sizing: border-box;
        // margin-left: -10%;
        padding-top: 0;
        display: flex;
        justify-content: space-between;
        flex-direction: column;
    }

    .shop_list_body_item_shop_others {
        padding: 10rpx;
        box-sizing: border-box;
        display: flex;
        justify-content: flex-start;
        flex-direction: column;
        text-align: right;
        padding-top: 0;
    }

    .brand {
        position: relative;
        color: #FFFFFF;
    }

    .sales {
        color: #FFFFFF;
    }

    .collect {
        color: #FFFFFF;
    }

    .shop_list_body_item_shop_others button {
        // width: 140rpx;
        height: 60rpx;
        line-height: 60rpx;
        font-size: 30rpx;
        color: #FFFFFF;
        border-radius: 30rpx;
        padding: 0 40rpx;
        background-color: #F6220C;
        color: white;
    }

    .shop_head_banner {
        width: 100%;
        // background-color: #FFFFFF;
    }

    .shop_head_banner swiper {
        width: 710rpx;
        height: 200rpx;
        margin: 0 auto;
        border-radius: 12rpx;
        background: linear-gradient(-57deg, #C3C0FF 1%, #FEEBFF 100%);
        overflow: hidden;
    }

    .swiper-item {
        width: 100%;
        height: 360rpx;
    }

    .swiper-item image {
        width: 100%;
        height: 100%;
    }

    .shop_body {
        width: 100%;
        background-color: #ffffff;
        padding: 0rpx 0rpx;
        box-sizing: border-box;
    }

    .shop_body_l_item {
        // width: 90%;
        // height: 250rpx;
        margin: 0 auto;
        background-color: white;
        // border-radius: 15rpx;
        // margin-top: 20rpx;
        display: flex;
        padding: 40rpx 0;
        box-sizing: border-box;
        border-bottom: 1rpx solid #D9D9D9;
    }

    .shop_body_l_item image {
        width: 150rpx;
        height: 150rpx;
        background-color: rgba(0, 0, 0, 0.1);
    }

    .shop_body_l_item_info {
        // width: 70%;
        // height: 100%;
        flex: 1;
        display: flex;
        justify-content: space-between;
        flex-direction: column;
        padding-left: 20rpx;
        box-sizing: border-box;
    }

    .shop_body_l_item_info_title {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        text-overflow: ellipsis;
        -webkit-box-orient: vertical;
        word-wrap: break-word;
        word-break: break-all;
        overflow: hidden;
    }

    .shop_body_l_item_info_price {
        display: flex;
        align-items: flex-end;
    }

    .shop_body_l_item_info_price view {
        margin-right: 15rpx;
    }

    .shop_body_l_item_info_others {
        // width: 100%;
        height: 30rpx;
        display: flex;
        justify-content: space-between;
    }

    .shop_body_l_item_info_others_activity {
        width: 150rpx;
        height: 30rpx;
        line-height: 30rpx;
        border: 1rpx #E22319 solid;
        border-radius: 30rpx;
        /* font-size: 16rpx; */
        color: #E22319;
        text-align: center;
        box-sizing: border-box;
    }

    .shop_body_l_item_info_others_sales {
        color: #333333;
    }

    .shop_body2 {
        width: 100%;
        display: flex;
        justify-content: flex-start;
        flex-wrap: wrap;
        background-color: #f2f2f2;
    }

    .shop_body_t_item {
        width: 45%;
        margin: 0 2.5%;
        margin-top: 20rpx;
        height: 520rpx;
        overflow: hidden;
        background-color: white;
    }

    .collect text {
        color: #FFFFFF;
    }

    .shop_body_t_item image {
        width: 100%;
        height: 337.5rpx;
        background-color: rgba(0, 0, 0, 0.1);
    }

    .shop_body_t_item_info {
        height: 182.5rpx;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 10rpx;
        box-sizing: border-box;
    }

    .shop_body_t_item_info_title {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        text-overflow: ellipsis;
        -webkit-box-orient: vertical;
        word-wrap: break-word;
        word-break: break-all;
        overflow: hidden;
    }

    .shop_body_t_item_info_price {
        display: flex;
        align-items: flex-end;
    }

    .shop_body_t_item_info_others {
        display: flex;
        justify-content: space-between;
    }

    .shop_body_t_item_info_others_activity {}

    .shop_body_t_item_info_others_sales {
        color: #585858;
    }

    /* ***************************************** */
    /* ***************************************** */
    /* ***************************************** */
    .diy-coupon {
        margin: 20rpx;
    }

    .diy-coupon .swiper {
        width: 750rpx;
        height: 168rpx;
    }

    .diy-coupon .coupon-item {
        width: 710rpx;
        height: 200rpx;
        align-items: stretch;
        align-content: stretch;
        color: #ffffff;
    }

    .diy-coupon .coupon-item.bg-red {
        background: #e62423;
    }

    .diy-coupon .coupon-item.bg-blue {
        background: #178ed9;
    }

    .diy-coupon .coupon-item.bg-yellow {
        background: #f4a50b;
    }

    .diy-coupon .coupon-item.bg-violet {
        background: #ab0bf6;
    }

    .diy-coupon .coupon-item .left-type {
        padding: 0 30rpx 0 40rpx;
        width: 40rpx;
        font-size: 40rpx;
        line-height: 40rpx;
        text-align: center;
        font-weight: bold;
        border-right: 4rpx dashed rgba(255, 255, 255, .4);
    }

    .diy-coupon .left-side-line {
        position: absolute;
        width: 20rpx;
        top: 0;
        left: -15rpx;
        overflow: hidden;
    }

    .diy-coupon .right-side-line {
        position: absolute;
        width: 20rpx;
        top: 0;
        right: -15rpx;
        overflow: hidden;
    }

    .diy-coupon .side-line .round {
        display: block;
        width: 20rpx;
        height: 20rpx;
        border-radius: 50%;
        margin: 4rpx 0;
        background: #ffffff;
    }

    .diy-coupon .center-content::before,
    .diy-coupon .center-content::after {
        position: absolute;
        display: block;
        content: '';
        width: 30rpx;
        height: 15rpx;
        background: #FFFFFF;
    }

    .diy-coupon .center-content::before {
        top: 0;
        right: -16rpx;
        border-radius: 0 0 15rpx 15rpx;
    }

    .diy-coupon .center-content::after {
        bottom: 0;
        right: -16rpx;
        border-radius: 15rpx 15rpx 0 0;
    }

    .diy-coupon .coupon-item .center-content {
        padding: 20rpx 40rpx;
        display: flex;
        justify-content: space-between;
        flex-direction: column;
        align-items: flex-start;
        flex: 1;
    }

    .diy-coupon .coupon-item .center-content .content-top {
        height: 50rpx;
        line-height: 50rpx;
    }

    .diy-coupon .coupon-item .center-content .content-datatime {
        padding: 4rpx 10rpx;
        border-radius: 30rpx;
        font-size: 20rpx;
        background: rgba(0, 0, 0, .2);
    }

    .diy-coupon .coupon-item .right-receive {
        padding: 0 40rpx 0 30rpx;
        width: 30rpx;
        text-align: center;
        font-size: 30rpx;
        line-height: 30rpx;
        text-align: center;
        border-left: 4rpx dashed rgba(255, 255, 255, .4);
        background: rgba(0, 0, 0, .6);
    }

    .diy-coupon .coupon-item .no-receive {
        background: #acacac;
        color: #787878;
    }

    /* ***************************** */
    .inner-tab {
        position: relative;
        height: 80rpx;
        display: flex;
        justify-content: space-around;
        align-items: center;
        // border-bottom: 1px solid #dddddd;
        background: #ffffff;
        z-index: 9;
    }

    .inner-tab .item {
        flex: 1;
        font-size: 30rpx;
        color: #999999;
        font-size: 32rpx;
        font-family: PingFang SC;
    }

    .inner-tab .item.active,
    .inner-tab .item .arrow.active .iconfont {
        color: $dominant-color;
    }

    .inner-tab .item.active {
        color: #333333;
        font-size: 32rpx;
        font-weight: bold;
        position: relative;
    }

    .inner-tab .item.active::after {
        content: '';
        position: absolute;
        bottom: -8rpx;
        width: 72rpx;
        height: 4rpx;
        background: #EE1414;
        border-radius: 2rpx;
        left: 0;
        right: 0;
        margin: 0 auto;
    }

    .inner-tab .item .box {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: row;
    }

    .inner-tab .item .arrows {
        margin-left: 10rpx;
        line-height: 0;
    }

    .inner-tab .item .iconfont {
        line-height: 24rpx;
        font-size: 24rpx;
    }

    .inner-tab .item .arrow,
    .inner-tab .item .svg-icon {
        width: 20rpx;
        height: 20rpx;
    }

    .box image {
        width: 36rpx;
        height: 36rpx;
    }

    .nav_bottom {
        height: 100rpx;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        text-align: center;
        background-color: #FFFFFF;
    }

    .nav_bottom .icon {
        font-size: 50rpx;
    }

    .nav_bottom .active {
        color: #E2231A;
    }

    .nav_bottom .active .icon {
        color: #E2231A;
    }

    .live_list {
        background-color: #FFFFFF;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        padding: 0 30rpx;
    }

    .live_item {
        position: relative;
        margin: 10rpx 0;
    }

    .live_img {
        width: 100%;
    }

    .live_img image {
        width: 335rpx;
        height: 375rpx;
        border-radius: 15rpx;
    }

    .live_name {
        font-size: 34rpx;
        margin: 15rpx 0;
    }

    .record {
        position: absolute;
        top: 0;
        width: 150rpx;
        font-size: 22rpx;
        height: 33rpx;
        line-height: 32rpx;
        text-align: center;
        background: #fdd933;
        color: #ffffff;
    }

    .record_off {
        position: absolute;
        top: 0;
        width: 150rpx;
        font-size: 22rpx;
        height: 33rpx;
        line-height: 32rpx;
        text-align: center;
        background: #fdd933;
        color: #ffffff;
    }

    .mpservice-wrap {
        width: 100%;
        box-sizing: border-box;
    }

    .mpservice-wrap .mp-image {
        width: 560rpx;
        margin-top: 40rpx;
    }

    .mpservice-wrap .mp-image image {
        width: 100%;
    }

    .icon-qq {
        color: #1296db;
        font-size: 64rpx;
    }

    .icon-weixin {
        color: #1afa29;
        font-size: 64rpx;
    }

    .icon-guanbi {
        font-size: 26rpx;
    }

    .icon-002dianhua {
        color: #1296db;
        font-size: 52rpx;
    }

    .kf-close {
        justify-content: flex-end;
    }

    .noDatamodel {
        font-size: 30rpx;
        width: 100%;
        text-align: center;
        height: 200rpx;
        line-height: 128rpx;
        color: #929292;
    }

    .reg180 {
        padding-right: 20rpx;
        text-align: right;
        transform: rotateY(180deg);
        position: absolute;
        bottom: 0;
    }

    .icon-jiantou {

        color: #FFFFFF;
        font-size: 30rpx;
    }

    .head_top {
        position: relative;
        height: 30px;
        line-height: 30px;
        color: #FFFFFF;
        font-size: 32rpx;
    }



    .shop {
        background: #f2f2f2;
        margin-top: 16rpx;
    }

    .shop_red {
        color: #F6220C;
    }

    .coupon {
        position: relative;
        background-color: #FFFFFF;
        overflow: hidden;
    }

    .shop_list_body_item_shop_others .collected {
        height: 60rpx;
        line-height: 60rpx;
        text-align: center;
        border: 1rpx solid #FFFFFF;
        color: #FFFFFF;
        background: none;
        border-radius: 30rpx;
        font-size: 26rpx;
    }

    .shop_list_body_item_shop_others .collect_btn {
        height: 60rpx;
        line-height: 60rpx;
        text-align: center;
        border: 1rpx solid #F6220C;
        background: #F6220C;
        color: #FFFFFF;
        border-radius: 30rpx;
        font-size: 26rpx;
    }

    .noborder {
        border: none;
    }
    .range_item{
        border: 1rpx solid #D9D9D9;
        border-top: none;
        padding: 8rpx;
        border-bottom-left-radius:10rpx ;
        border-bottom-right-radius:10rpx ;
        color: #666666;
        box-shadow: 0 0 8rpx rgba(0, 0, 0, 0.1);
    }
</style>
