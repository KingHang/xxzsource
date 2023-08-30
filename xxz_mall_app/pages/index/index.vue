<template>
	<view>
		<view class="top_head pr">

            <block v-if="shareid>0&&shareag.user_id>0">
                <u-navbar showtitle="false" :isBack="false">
                    <view slot="left">
                        <view style="padding: 10rpx;display: flex;align-items: center">
                            <image :src="shareag.avatarUrl" style="width: 60rpx;border-radius: 50%" mode="widthFix"></image>
                            <view style="margin-left: 10rpx;">
                                {{shareag.nickName}}的店铺
                            </view>
                        </view>
                    </view>
                    <view slot="right"></view>
                </u-navbar>
            </block>
            <block v-else>
                <u-navbar :title="title_name ? title_name : '首页'" :isBack="false"></u-navbar>
            </block>
		</view>
        <view class="hlbblock30" v-if="member.is_agent">
            <view style="display: flex;justify-content: space-between;align-items: center">
                <view style="display: flex">
                    <view>
                        <image :src="member.avatarUrl" style="width: 80rpx;border-radius: 50%" mode="widthFix"></image>

                    </view>
                    <view style="margin-left: 20rpx">
                        <view>
                            {{member.nickName}}
                        </view>
                        <view  class="font2599" style="margin-top: -10rpx">
                            {{member.levelname || '无等级'}}
                        </view>

                    </view>
                </view>
                <view class="hlbbutton" @click="actionshare=true">
                    <image src="/static/images/common/share.png" style="width: 35rpx;display: inline-block" mode="widthFix"></image>
                    <text style="font-size: 26rpx;margin-left: 10rpx">
                        转发店铺
                    </text>

                </view>

            </view>

        </view>


        <u-popup  v-model="actionshare" mode="bottom" height="" border-radius="50">
            <view style="padding: 30rpx;margin-top: 50rpx">
                <u-divider bg-color="white">分享至</u-divider>
            </view>

            <view style="display: flex;justify-content: space-around;margin-bottom: 30rpx">


                <view v-if="showh5" @click="sharefriend" style="text-align: center">
                    <view>
                        <image src="/static/images/common/wechat.png" style="width: 80rpx" mode="widthFix"></image>
                    </view>
                    <view style="font-size: 25rpx;margin-top: 10rpx">
                        群或好友
                    </view>
                </view>
                <view v-if="showmp"  style="text-align: center;position: relative">
                    <button open-type="share"  style="width: 150rpx;height: 150rpx;position: absolute">

                    </button>
                    <view>
                        <image src="/static/images/common/wechat.png" style="width: 80rpx" mode="widthFix"></image>
                    </view>
                    <view style="font-size: 25rpx;margin-top: 10rpx">
                        群或好友
                    </view>
                </view>
                <view @click="shareline" style="text-align: center">
                    <view>
                        <image src="/static/images/common/line.png" style="width: 80rpx" mode="widthFix"></image>
                    </view>
                    <view style="font-size: 25rpx;margin-top: 10rpx">
                        朋友圈
                    </view>
                </view>
            </view>
            <view style="width: 100%;height: 20rpx;background-color:#eeeeee ">
            </view>
            <view style="width: 100%;text-align: center;line-height: 100rpx" @click="actionshare=false">
                取消
            </view>
        </u-popup>

        <u-mask :show="friendshow" @click="friendshow = false">
            <view class="">
                <image src="/static/images/common/arrow.png" style="width: 120rpx;position: absolute;top: 10rpx;right: 10rpx" mode="widthFix"></image>
                <view class='content' style="color: white;width: 100%;text-align: center;margin-top: 150rpx">请点击右上角<br/>通过【发送给朋友】<br/>邀请好友</view>
            </view>
        </u-mask>

        <u-mask :show="lineshow" @click="lineshow = false">
            <view class="" style="text-align: center">
				<image src="https://img.dfhlyl.com/20220801140536c7ba48919.png" style="width: 390rpx;margin: 80px auto 0;" mode="widthFix"></image>
                <image :src="img" style="width: 65%;margin:0 auto 30rpx;border-radius:20rpx " mode="widthFix" :show-menu-by-longpress="true"></image>
                <view @click="saveimg" class="hlbbutton" style="margin: auto;width: 280rpx">
                    去发朋友圈
                </view>
            </view>
        </u-mask>


		<!--<view class="top_bg" :style="'background-color: '+bgcolor+';'">
		</view>-->
		<swiper class="pr bg-f2" :duration="500" :current="thisindex" :data-index='thisindex' @change="toggle" circular
		 :style="'height:'+scrollviewHigh+ 'px;'">
			<swiper-item class="swiper-item pr" @touchmove.stop="stopTouchMove">
				<scroll-view scroll-y="true" :style="'height:'+scrollviewHigh+ 'px;'" :scroll-top="indexStop">
					<diy style="position: relative;" :diyItems="items"></diy>
                    <view style="height: 100rpx"></view>
				</scroll-view>
			</swiper-item>
			<swiper-item style="" class="swiper-item bg-f2" @touchmove.stop="stopTouchMove" v-for="(item,index) in category_list" :key='index'
			 v-if='index!=0'>
				<scroll-view scroll-y="true" :style="'height:'+scrollviewHigh+ 'px;'" lower-threshold="50" @scrolltolower="scrolltolowerFunc">
					<!-- 没有记录 -->
					<view class="d-c-c p30 ww100" v-if="product_list.length == 0 && !loading">
						<text class="iconfont icon-wushuju"></text>
						<text class="cont">亲，暂无相关记录哦</text>
					</view>
					<uni-load-more v-else :loadingType="loadingType"></uni-load-more>
				</scroll-view>
			</swiper-item>
		</swiper>
		<!-- </scroll-view> -->

        <u-popupopt v-model="agpop" mode="center" width="600">
           <image v-if="agpoptype!=3" @click="agpop=false" src="https://img.pighack.com/202203301757039407f3442.png" style="width: 100%" mode="widthFix" ></image>
           <image v-else @click="agpop=false" src="https://img.pighack.com/20220330180025974638642.png" style="width: 100%" mode="widthFix" ></image>
        </u-popupopt>


		<!--点击收藏-->
		<view class="collection-box" v-if="is_collection" :style="'top:'+(statusBarHeight + topBarHeight() + 10)+'px;'">
			<view class="inner">
				<text>
					点击“</text>
				<text class="point">.</text>
				<text class="point point-big">.</text>
				<text class="point">.</text>
				<text>”添加到我的小程序，\n微信首页下拉即可快速访问店铺
				</text>
			</view>
			<button type="primary" class="close-btn" @click="is_collection=false">x</button>
		</view>

		<!--关注公众号-->
		<!-- #ifdef MP-WEIXIN -->
		<view class="follow-gzh" v-if="is_follow=='1'">
			<text class="icon iconfont icon-guanbi" @click="is_follow=0"></text>
			<official-account></official-account>
		</view>
		<!-- #endif -->

		<!--首页推送-->
		<Homepush v-if="is_homepush" :homepush_data="homepush_data"></Homepush>
		<!-- <footTabber></footTabber> -->
		<request-loading :loadding='isloadding'></request-loading>
	</view>
</template>

<script>
	import diy from '@/components/diy/diy.vue';
	import Homepush from './home-push/home-push.vue';
	import navBar from '@/components/navBar/navBar.vue'
	import uniLoadMore from '@/components/uni-load-more.vue';
	import config from '@/config.js';
    import {wxShare} from '@/config/share';
    var _self;
	export default {
		components: {
			diy,
			Homepush,
			navBar,
			uniLoadMore
		},
		data() {
			return {
				statusBarHeight: 0,
				titleBarHeight: 0,
				/*是否正在加载*/
				loading: false,
				isloadding: false,
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				background: '',
				listData: [],
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				items: [],
				/*收藏引导*/
				is_collection: false,
				/*关注*/
				is_follow: '0',
				/*是否首页推送*/
				is_homepush: false,
				/*首页推送数据*/
				homepush_data: {},
				triggered: true,
				target: 0,
				thisindex: 0,
				category_list: [],
				product_list: [],
				page: 1,
				last_page: 0,
				no_more: false,
				indexStop: 0,
				title_name: '',
				bgcolor: '',
				msgNum: 0,
				title_image: false,
				toplogo: '',
				url: '',
				jweixin: null,
                img:'',
                shareag:[],
                shareid:0,
                member:[],
                actionshare:false,
                showh5:false,
                showmp:false,
                friendshow:false,
                lineshow:false,
                agpop:false,
                agpoptype:0,
			};
		},
		watch: {
			target(val) {
				this.setIndex(val)
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
		onTabItemTap() {
			//#ifdef H5
			if (process.env.NODE_ENV === 'production') {
				window.location.href = config.app_url + config.h5_addr + '/pages/index/index';
			}
			//#endif
		},
		onLoad(e) {
            _self = this;


			//#ifdef H5
			if (this.isWeixin()) {
				this.url = window.location.href;
			}
			//#endif
			this._freshing = false;

            //#ifdef H5
            this.showh5 = true
            //#endif
            // #ifdef MP-WEIXIN
            this.showmp = true
            // #endif
			
            _self.shareid =  uni.getStorageSync('share_id')
			
            if ( _self.shareid > 0) {
                _self._post(
                    'plugin.articlepromotion.article/userShareMsg', {
                        share_id: _self.shareid
                    },
                    function(res) {
                        _self.shareag = res.data
                    }
                );
            }
			
            _self._post(
                'user.agent/check', {},
                function(res) {
                    // res.data.is_pop = true;
                    // res.data.type = 3;
                    _self.agpop = res.data.is_pop;
                    _self.agpoptype = res.data.type;
                    console.log(_self.agpoptype)
                }
            );
			
			/*获取订单列表*/
			this.getList();
			this.getData();
		},
		mounted() {
			this.init();
		},

		methods: {

            sharefriend(){
                _self.actionshare =false;
                if (_self.showh5){
                    _self.friendshow =true;
                }
            },
            shareline(){
                _self.actionshare =false;
                _self.lineshow =true;
                uni.showLoading({
                    title: '加载中',
                    mask:true
                });

                _self._get(
                    'plugin.agent.qrcode/qrcode', {
                        source:_self.getPlatform()
                    },
                    function(res) {
                        uni.hideLoading()
                        console.log(res)
                        _self.img = res.data.qrcode

                    }
                );


              /*  _self.$http.post('commission.unicode', {
                    ish5:_self.showh5?1:0
                }).then(function (res) {
                    uni.hideLoading()
                    console.log(res)
                    if (res.data.error==0){
                        _self.img = res.data.img

                    }else{
                        _self.$api.msg(res.data.message)
                    }

                }).catch(function (error) {
                });*/

            },
            saveimg(){
                uni.downloadFile({
                    url: _self.img,//图片地址
                    success: (res) =>{
                        if (res.statusCode === 200){
                            uni.saveImageToPhotosAlbum({
                                filePath: res.tempFilePath,
                                success: function() {
                                    uni.showToast({
                                        title: "保存成功",
                                        icon: "none"
                                    });
                                },
                                fail: function() {
                                    uni.showToast({
                                        title: "保存失败",
                                        icon: "none"
                                    });
                                }
                            });
                        }
                    }
                })
            },

			scrolltolowerFunc() {
				let self = this;
				self.page++;
				self.loading = true;
				if (self.page > self.last_page) {
					self.loading = false;
					self.no_more = true;
					return;
				}
				self.getProduct();
			},
			/*初始化*/
			init() {
				let _this = this;
				uni.getSystemInfo({
					success(res) {
						_this.phoneHeight = res.windowHeight;
						let view = uni.createSelectorQuery().select('.top_head');
						view.boundingClientRect(data => {
							let h = _this.phoneHeight - data.height;
							_this.scrollviewHigh = h;
						}).exec();
					}
				});
			},

			/*获取首页分类*/
			getData() {
				let self = this;
				// #ifndef  APP-PLUS
				if (!self._freshing) {
					self.isloadding = true;
				}
				// #endif
                


				self._get('index/index', {
					url: self.url
				}, function(res) {
                    self.member = res.data.userInfo;
                    self.member.is_agent = res.data.is_agent;
                    self.member.is_applying = res.data.is_applying;
                    
                    console.log(res.data)
                    
                    if (Object.keys(res.data.agentInfo).length>0){
                        self.member.levelname = res.data.agentInfo.grade.name
                    }

					self.listData = res.data.list;
					self.background = res.data.background;
					self.items = res.data.items;
					

					self.title_name = res.data.page.params.name;
					self.bgcolor = res.data.page.style.titleBackgroundColor;
					self.msgNum = res.data.msgNum;
					self.toplogo = res.data.page.style.toplogo;
					self.setPage(res.data.page);
					// self.setTabBarItem('2');
					//弹出收藏
					// #ifdef  MP-WEIXIN
					let isFirst = uni.getStorageSync('isFirst');
					if (isFirst == '' && res.data.setting.collection.status == '1') {
						self.is_collection = true;
						uni.setStorageSync('isFirst', 1);
					}
					self.is_follow = res.data.setting.officia.status;
					// #endif
					// 首页推送
					let homepush_name = uni.getStorageSync('homepush_name');
					if (res.data.setting.homepush.is_open && homepush_name != res.data.setting.homepush.name) {
						self.homepush_data = res.data.setting.homepush;
						self.is_homepush = true;
						self.is_homepush = true;
					}
					// 配置微信扫一扫参数
					//#ifdef H5
					if (self.url != '') {
						self.jweixin = self.configWxScan(res.data.signPackage);
					}
					//#endif
					self.isloadding = false;
					self.loadding = false;
				});
			},
			/*获取数据*/
			getList() {
				let self = this;
				self._get('goods.category/index', {}, function(res) {
					self.category_list = res.data.list;
					let fistIndex = {
						name: '推荐',
						category_id: '0'
					}
					self.category_list.unshift(fistIndex)
				});
			},
			/*获取数据*/
			getProduct() {
				let self = this;
				let page = self.page;
				self.loading = true;
				self._get('goods.goods/lists', {
					page: page || 1,
					category_id: self.category_id,
					search: '',
					sortType: 'all',
					sortPrice: 0,
					list_rows: 10,
				}, function(res) {
					self.loading = false;
					self.product_list = self.product_list.concat(res.data.list.data);
					self.last_page = res.data.list.last_page;
					if (res.data.list.last_page <= 1) {
						self.no_more = true;
					}
				});
			},
			/*设置页面*/
			setPage(page) {

				uni.setNavigationBarTitle({
					title: page.params.name
				});

				let colors = '#000000';
				if (page.style.titleTextColor == 'white') {
					//字母要小写
					colors = '#ffffff'
				}
				uni.setNavigationBarColor({
					frontColor: colors,
					backgroundColor: page.style.titleBackgroundColor
				})

			},

			/*分享当前页面*/
			onShareAppMessage() {
				let self = this;
                self.actionshare =false;
                return wxShare({
					id: self.member.user_id,
					title: self.items.page.params.share_title
				});

			},
			onRefresh() {
				if (this._freshing) return;
				this._freshing = true;
				this.getData();
				setTimeout(() => {
					this.triggered = false;
					this._freshing = false;
				}, 2000)
			},
			onRestore() {
				this.triggered = 'restore'; // 需要重置
			},
			setTabBarItem(n) {
				if (n == 2) {
					console.log('订单')
					uni.setTabBarItem({
						index: 2,
						pagePath: '/pages/index/order/myorder',
						text: '订单',
						iconPath: 'static/order.png',
						selectedIconPath: 'static/order_active.png',
					})
				}

			},
			// 滑動切换触发的事件
			toggle(e) {
				let index = e.detail.current
				this.target = index;
				this.category_id = this.category_list[index].category_id;
				this.toggleInit();
				if (index != 0) {
					this.getProduct()
				}
			},
			toggleInit() {
				this.page = 1;
				this.last_page = 0;
				this.no_more = false;
				this.product_list = [];
			},
			//点击nav控制下面的展示
			setIndex(e) {
				this.thisindex = e
			},
			/* 禁止手动滑动 */
			stopTouchMove() {
				return true
			},
			/*扫一扫核销*/
			scanQrcode: function() {
				let self = this;
				//#ifdef H5
				// 只允许通过相机扫码
				self.jweixin.scanQRCode({
					needResult: 1,
					scanType: ["qrCode", "barCode"],
					success: function(res) {
						self.gotoPage('/pages/store/clerkorder?order_no=' + res.resultStr);
					},
					error: function(res) {
						uni.showToast({
							title: '扫码失败，请重试'
						})
					}
				});
				//#endif
				//#ifndef H5
				// 只允许通过相机扫码
				uni.scanCode({
					onlyFromCamera: true,
					success: function(res) {
						if (res.errMsg == 'scanCode:ok') {
							self.gotoPage('/pages/store/clerkorder?order_no=' + res.result);
						} else {
							uni.showToast({
								title: '扫码失败，请重试'
							})
						}
					}
				});
				//#endif
			},
		}
	};
</script>

<style>
	.bg-f2 {
		background-color: #F2F2F2;
	}

	.top_head {
		line-height: 30px;
		z-index: 1;
		padding-left: 26rpx;
	}

	.head_top {
		width: 100%;
		height: var(--status-bar-height);
	}

	.index_log {
		/* width: 154rpx; */
		min-height: 46rpx;
		line-height: 46rpx;
		font-size: 36rpx;
		color: #FFFFFF;
	}

	.index_log image {
		width: 60rpx;
		height: 60rpx;
	}

	.top_search {
		width: 380rpx;
		height: 60rpx;
		line-height: 60rpx;
		background: #FFFFFF;
		color: #999999;
		border-radius: 6rpx 0 0 6rpx;
		font-size: 26rpx;
		margin-left: 10rpx;
	}

	.top_search.special {
		width: 100%;
	}

	.top_search_right {
		background: #FFFFFF;
		border-radius: 0 6rpx 6rpx 0;
		height: 60rpx;
		/* padding: 0 20rpx; */
		/* border-left: 1rpx solid #D9D9D9; */
	}

	.top_search_right .icon-saoyisao1 {
		padding: 0 20rpx;
		border-left: 1rpx solid #D9D9D9;
	}

	.top_search .icon-sousuo {
		font-size: 26rpx;
		color: #999999;
		margin-left: 20rpx;
		margin-right: 10rpx;
	}

	.top_bg {
		position: absolute;
		top: 0;
		z-index: 0;
		width: 750rpx;
		height: 350rpx;
	}

	.top_bg image {
		width: 750rpx;
		height: 350rpx;
	}

	.nuter {
		width: 100%;
		height: 80rpx;
		line-height: 80rpx;
		display: flex;
		justify-content: space-around;
		font-size: 35rpx;
		padding-bottom: 10rpx;
	}

	.nuter view {
		flex: 1;
		font-size: 30rpx;
		text-align: center;
		transition: all 0.5s ease .1s;
	}

	.active {
		box-sizing: border-box;
		color: #007AFF;
		border-bottom: 5rpx solid #00aaff;
		border-radius: 10rpx;
		box-shadow: 3px 3px 5px #888888;
	}

	swiper {
		height: 80vh;
		width: 100%;
		overflow: hidden;
	}

	.swiper-item {
		overflow-y: scroll;
		width: 100%;
		height: 99%;
		box-sizing: border-box;
	}

	.foot_ {
		height: 98rpx;
		width: 100%;
	}

	.banner {
		height: 240upx;
		width: 710upx;
	}

	.banner image {
		width: 710rpx;
		height: 240rpx;
	}

	.new-people .group-bd {
		display: flex;
		justify-content: space-between;
	}

	.new-people .list {
		padding-right: 30rpx;
	}

	.new-people .list .item {
		width: 180rpx;
		text-align: center;
	}

	.new-people .list .price {
		font-size: 24rpx;
	}

	.new-people .list .price .num {
		font-size: 34rpx;
		font-weight: bold;
	}

	.new-people .list button {
		font-size: 24rpx;
		background: #6170ff;
	}

	.new-people .list image {
		width: 180rpx;
		height: 180rpx;
		border: 1px dashed #cccccc;
	}

	.new-people .other {
		width: 230rpx;
	}

	.new-people .other image {
		width: 100%;
	}

	.group-seckill .left .iconfont {
		margin-right: 8rpx;
		color: rgb(226, 35, 26);
		font-size: 40rpx;
	}

	.group-seckill .list .item {
		display: flex;
		flex-direction: column;
		justify-content: flex-start;
		align-items: center;
		width: 150rpx;
		height: 230rpx;
		border: 1px dashed #cccccc;
	}

	.group-seckill .list text {
		line-height: 60rpx;
		color: #e2231a;
	}

	.group-seckill .list image {
		width: 150rpx;
		height: 150rpx;
	}

	.group-hd .datetime .time {
		padding: 4rpx;
		background: #e2231a;
		color: #ffffff;
		border-radius: 4rpx;
	}

	.group-hd .datetime .point {
		padding: 0 10rpx;
		color: #e2231a;
	}

	.every-day-hard .list .item {
		width: 200rpx;
	}

	.every-day-hard .list image {
		width: 200rpx;
		height: 200rpx;
	}

	.every-day-hard .list .pic {
		position: relative;
		width: 200rpx;
		height: 200rpx;
		border: 1px dashed #cccccc;
	}

	.every-day-hard .list .tips {
		position: absolute;
		left: -1px;
		bottom: -1px;
		padding: 0 10rpx;
		display: flex;
		justify-content: flex-start;
		align-items: center;
		border-radius: 0 4rpx 0 0;
		color: #ffffff;
		background: #ff8a00;
		font-size: 22rpx;
	}

	.every-day-hard .list .tips .iconfont {
		margin-right: 6rpx;
		font-size: 24rpx;
		color: #ffffff;
	}

	.every-day-hard .list .tips .svg-icon {
		width: 20rpx;
		height: 20rpx;
		margin-right: 6rpx;
		color: #ffffff;
	}

	.every-day-hard .list .bottom-box {
		display: flex;
		height: 80rpx;
		justify-content: space-between;
		align-items: center;
	}

	.every-day-hard .list .bottom-box .people {
		font-size: 24rpx;
		color: #fb8138;
	}

	.every-day-hard .list .bottom-box .unit {
		font-size: 22rpx;
		color: #e2231a;
	}

	.every-day-hard .list .bottom-box .price {
		font-size: 30rpx;
		color: #e2231a;
	}

	.collection-box {
		position: fixed;
		width: 380rpx;
		padding: 20rpx;
		top: 20rpx;
		right: 20rpx;
		line-height: 40rpx;
		font-size: 24rpx;
		border-radius: 16rpx;
		background: #ffffff;
		border: 1px solid #eeeeee;
		box-shadow: 0 0 6rpx 0 rgba(0, 0, 0, .08);
		z-index: 99;
	}

	.collection-box::after {
		position: absolute;
		content: '';
		display: block;
		right: 140rpx;
		top: -15rpx;
		transform: rotate(45deg);
		width: 30rpx;
		height: 30rpx;
		transform: rotate;
		background: #FFFFFF;
		border-left: 1px solid #eeeeee;
		border-top: 1px solid #eeeeee;
	}

	.collection-box .point {
		width: 20rpx;
		height: 20rpx;
		font-size: 60rpx;
		line-height: 0;
		color: #666666;
	}

	.collection-box .point-big {
		font-size: 80rpx;
	}

	.collection-box .close-btn {
		position: absolute;
		padding: 0;
		right: 10rpx;
		top: 10rpx;
		width: 40rpx;
		height: 40rpx;
		line-height: 30rpx;
		background: #FFFFFF;
		color: #999999;
		border-radius: 50%;
	}

	.follow-gzh {
		position: fixed;
		left: 0;
		right: 0;
		bottom: calc(var(--window-bottom));
		border-radius: 16rpx;
		box-shadow: 0 0 20rpx 0 rgba(0, 0, 0, .1);
		background: #FFFFFF;
		z-index: 10;
	}

	.follow-gzh .iconfont {
		display: block;
		position: absolute;
		right: 10rpx;
		top: 10rpx;
		z-index: 99;
	}

	.product-list {
		display: flex;
		justify-content: space-between;
		flex-wrap: wrap;
		align-items: center;
	}

	.product_item {
		width: 345rpx;
		margin: 20rpx;
		border-radius: 12rpx;
		background-color: #ffffff;
	}

	.product_item.product_item_right {
		margin-left: 0;
	}

	.product_item .pro_name {
		height: 68rpx;
		line-height: 34rpx;
	}

	.diy-seckillProduct .sharpproduct-head .datetime::v-deep .box {
		padding: 4rpx 10rpx;
		font-size: 22rpx;
		background: #FFEBEB;
		color: #F6220C;
	}

	.bargainProduct-head .datetime::v-deep .box {
		padding: 4rpx 10rpx;
		font-size: 22rpx;
		background: #FFFFFF;
		color: #4B30FF;
	}

	.chat {
		width: 40rpx;
		height: 40rpx;
	}

	.newsnum {
		position: absolute;
		top: -8rpx;
		right: -16rpx;
		z-index: 100;
		border-radius: 50%;
		width: 25rpx;
		height: 25rpx;
		text-align: center;
		line-height: 25rpx;
		color: #FFFFFF;
		background-color: #ff6633;
		padding: 5rpx;
		font-size: 20rpx;
	}
</style>
