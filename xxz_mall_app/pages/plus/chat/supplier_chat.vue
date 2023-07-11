<template>
	<view class="im_interface">
		<scroll-view id="scrollview" scroll-y="true" :style="'height: '+scrollviewHigh + 'px'" :scroll-with-animation="true"
		 :scroll-top="scrollTop" @scrolltoupper='newdata'>

			<view class="im_interface_content" ref='container'>
				<view :class="item.msg_type == 2?'im_text2':'im_text'" class="m-item" v-for="(item,index) in content_list" :key="index">
					<image v-if="item.type!=4&&item.type!='off'&&item.type!=3" class="avatar" :src="item.msg_type == 2?item.user.avatarUrl:myavatarUrl"
					 mode=""></image>
					<view>
						<view class="my_date">{{item.create_time}}</view>
						<view v-if="item.type==0" :class="item.msg_type == 2?'my_content my_text_content':'you_content you_text_content'">
							{{item.content}}
						</view>
						<view v-if="item.type==1" :class="item.msg_type == 2?'my_content':'you_content'">
							<image @click="preview(item.content,0)" class="cont_img" :src="item.content" mode="">
							</image>
						</view>
						<view v-if="item.type==2" :class="item.msg_type == 2?'my_content':'you_content'">
							<view class="product_txtitem">
								<view>
									<image class="pro_img" :src="getJSON(item.content).product_img" mode=""></image>
								</view>
								<view>
									<view class="pro_txtname">{{getJSON(item.content).product_name}}</view>
									<view class="pro_price">￥{{getJSON(item.content).product_price}}</view>
								</view>
							</view>
						</view>
						<view v-if="item.type==3" :class="item.msg_type == 2?'my_content':'you_content'">
							<view class="o-h">
								<view class="fb mb20">
									你正在咨询的订单
								</view>
								<view class="product_txtitem mb20">
									<view>
										<image class="pro_img" :src="getJSON(item.content).product_img" mode=""></image>
									</view>
									<view>
										<view class="pro_txtname">{{getJSON(item.content).product_name}}</view>
										<view class="f24 gray6">
											共计{{getJSON(item.content).order_num}}件商品：合计￥{{getJSON(item.content).order_price}}
										</view>
									</view>
								</view>
								<view class="f24 gray6 ">订单号{{getJSON(item.content).order_no}}</view>
								<view class="f24 gray6 ">创建时间{{getJSON(item.content).create_time}}</view>
								<button class="orderdetail_btn" @click="gotoPage('/pages/order/order-detail?order_id='+getJSON(item.content).order_id)">查看</button>
							</view>
						</view>
						<view v-if="item.type==4">
							<view class="top_pro">
								<view class="top_product" v-if="is_product">
									<view>
										<image class="pro_img" :src="productDetail.product_image" mode=""></image>
									</view>
									<view>
										<view class="pro_name">{{productDetail.product_name}}</view>
										<view class="pro_price sendpro_price">￥{{productDetail.product_price}}</view>
									</view>
									<button class="pro_btn" @click="sendProduct">发送商家</button>
									<view class="close_pro" @click="is_product=false"><text class="icon iconfont icon-guanbi"></text></view>
								</view>
							</view>
						</view>
						<view v-if="item.type==5">
							<view class="top_pro">
								<view class="top_order" v-if="is_order">
									<view>
										<image class="pro_img" :src="order_chat.product[0].image.file_path" mode="">
										</image>
									</view>
									<view>
										<view class="pro_name mb20">你可能想咨询该订单</view>
										<view class="f24 gray6 sendord_price">
											共{{order_chat.product.length}}件商品:合计￥{{order_chat.order_price}}</view>
									</view>
									<button class="ord_btn" @click="sendOrder">发送订单</button>
									<view class="close_pro" @click="is_order=false"><text class="icon iconfont icon-guanbi"></text></view>
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view style="width: 100%;height: 130rpx;"></view>
		</scroll-view>

		<view class="buttom" :style="'bottom:' +inputBottom+'px;'">
			<input type="text" v-model="content" @confirm="send_content()" confirm-type="send" @focus="inputFocus" @blur="inputBlur"
			 :adjust-position="false" placeholder="请输入....." />
			<view class="upload_box">
				<view class="upload" @click="upload('license')"><text class="icon iconfont icon-jingmeihaibao" style="color: #FFFFFF;"></text></view>
				<!-- #ifdef APP-PLUS -->
				<button v-if="!is_Ios" @click="send_content()">发送</button>
				<!-- #endif -->
			</view>
		</view>

		<Upload v-if="isupload" :isupload="isupload" :type="type" @getImgs="getImgsFunc">上传图片</Upload>
	</view>
</template>

<script>
	import Upload from '@/components/upload/upload';
	export default {
		data() {
			return {
				user_id: '', 
				shop_supplier_id: '',
				supplier_user_id: '',
				myavatarUrl: '',
				avatarUrl: '',
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				content: '', //用户输入的内容
				content_list: [], //聊天信息数据
				style: {
					pageHeight: 0,
					contentViewHeight: 0,
					footViewHeight: 90,
					mitemHeight: 0
				},
				isupload: false,
				type: 'license',
				scrollTop: 0,
				img_path: '',
				is_product: false,
				product_id: 0,
				productDetail: {},
				socketTask: null,
				// 确保websocket是打开状态
				is_open_socket: false,
				// 心跳定时器
				intervalId: null,
				page: 1,
				nomore: false,
				scrollHeight: 0,
				nickName: '',
				url: '',
				status: '离线',
				/* 初次进入 */
				is_live: false,
				inputBottom: 0,
				is_Ios: true,
				order_chat: {},
				is_order: false
			}
		},
		components: {
			/*编辑组件*/
			Upload
		},
		created() {
			let self = this;
			const res = uni.getSystemInfoSync(); //获取手机可使用窗口高度     api为获取系统信息同步接口
			this.style.pageHeight = res.windowHeight;
			this.style.contentViewHeight = res.windowHeight - uni.getSystemInfoSync().screenWidth / 750 * (100) - 70; //像素
		},
		onShow() {
			this.getAvatarUrl();
			this.init();
			this.isuserAgent();
		},
		onLoad(option) {
			let self = this;
			self.user_id = option.user_id;
			self.nickName = option.nickName;
			uni.setNavigationBarTitle({
				title: self.nickName + '(离线)'
			})
			this.get_content_list();
		},
		beforeDestroy() {
			console.log('beforeDestroy');
			// 销毁监听
			this.closeSocket();
			this.is_live = true;
		},
		methods: {
			/*初始化*/
			init() {
				let self = this;
				uni.getSystemInfo({
					success(res) {
						self.phoneHeight = res.windowHeight;
						// 计算组件的高度
						self.scrollviewHigh = self.phoneHeight;
					}
				});
			},
			initData() {
				this.page++;
				this.get_content_list();
			},
			socketInit() {
				let self = this;
				if(self.is_open_socket){
					return;
				}
				self.socketTask = null;
				self.socketTask = uni.connectSocket({
					url: self.url + '/socket?user_id=' + self.user_id + '&usertype=supplier',
					success() {
						console.log('Socket连接成功！');
					}
				});
				// 消息的发送和接收必须在正常连接打开中,才能发送或接收【否则会失败】
				self.socketTask.onOpen((res) => {
					console.log("WebSocket连接正常打开中...！");
					self.is_open_socket = true;
					// 开始发送心跳
					self.startHeart();
					// 注：只有连接正常打开中 ，才能正常收到消息
					self.socketTask.onMessage(function(res) {
						console.log("收到服务器内容：");
						console.log(res);
						self.getNewcontent(res);
					});
				});
				// 这里仅是事件监听【如果socket关闭了会执行】
				self.socketTask.onClose(() => {
					console.log("已经被关闭了");
					//重连机制
					self.socketTask = null;
					self.is_open_socket = false;
					clearInterval(self.intervalId);
					!self.is_live && self.socketInit();
				});
			},
			send: function(data) {
				let self = this;
				if (self.is_open_socket) {
					self.socketTask.send({
						data: data,
						success() {

						}
					});
				} else {
					console.log("处于离线状态");
					self.socketTask = null;
					self.is_open_socket = false;
					clearInterval(self.intervalId);
					self.socketInit();
				}
			},
			startHeart() {
				let self = this;
				let data = JSON.stringify({
					type: 'ping',
					app_id: self.getAppId(),
					user_id: self.user_id,
					shop_supplier_id: self.shop_supplier_id,
					supplier_user_id: self.supplier_user_id,
					msg_type: 1,
				});
				self.intervalId = setInterval(function() {
					console.log('发送心跳');
					console.log(data);
					self.send(data);
				}, 10000);
			},
			closeSocket: function() {
				let self = this;
				let data = JSON.stringify({
					type: 'close',
					app_id: self.getAppId(),
					supplier_user_id: self.supplier_user_id,
					user_id: self.user_id,
					shop_supplier_id: self.shop_supplier_id,
					msg_type: 1,
				});
				self.send(data);
				self.socketTask.close({
					success(res) {
						console.log("关闭成功", res)
					},
					fail(err) {
						console.log("关闭失败", err)
					}
				});
				self.socketTask = null;
				self.is_open_socket = false;
				clearInterval(self.intervalId);
			},
			// 发送消息
			send_content() {
				if (this.content == '') {
					uni.showToast({
						title: '发送内容不能为空！',
						icon: 'none'
					})
					return false
				}
				let self = this;
				let data = JSON.stringify({
					user_id: self.user_id,
					shop_supplier_id: self.shop_supplier_id,
					supplier_user_id: self.supplier_user_id,
					msg_type: 1,
					type: 0,
					content: self.content,
					app_id: self.getAppId()
				})
				let newdata = JSON.parse(data)
				let item = {
					msg_type: 1,
					content: newdata.content,
					user_id: self.user_id,
					type: newdata.type,
					create_time: self.formatDate(),
					user: {
						avatarUrl: self.myavatarUrl
					}
				}
				this.content_list = [...this.content_list, item];
				this.$nextTick(function() {
					this.scrollToBottom()
				})
				console.log(data)
				self.send(data);
				self.content = '';
			},
			getNewcontent(res) {
				let newdata = JSON.parse(res.data);
				if (newdata.Online == 'off' && !this.is_live) {
					this.status = '离线';
					console.log("对方离线")
					uni.setNavigationBarTitle({
						title: this.nickName + '(离线)'
					})
				}
				if (newdata.Online == 'on' && !this.is_live) {
					this.status = '在线';
					console.log("对方在线")
					uni.setNavigationBarTitle({
						title: this.nickName + '(在线)'
					})
				}
				console.log(newdata)
				if (newdata.content) {
					let item = {
						content: newdata.content,
						user_id: newdata.user_id,
						type: newdata.type,
						msg_type: 2,
						user: {
							avatarUrl: this.avatarUrl
						}
					}
					console.log("解析数据");
					this.content_list = [...this.content_list, item];
					this.$nextTick(function() {
						this.scrollToBottom()
					})
				}
				//绑定用户
				if (newdata.type == 'init') {
					let self = this;
					self._post(
						'plus.chat.supplierChat/bindClient', {
							client_id: newdata.client_id,
							user_id: self.user_id
						},
						function(res) {
							if (res.data.data.Online == 'off' && !self.is_live) {
								self.status = '离线';
								console.log("对方离线")
								uni.setNavigationBarTitle({
									title: self.nickName + '(离线)'
								})
							} else if (res.data.data.Online == 'on' && !self.is_live) {
								self.status = '在线';
								console.log("对方在线")
								uni.setNavigationBarTitle({
									title: self.nickName + '(在线)'
								})
							}
							console.log("init---绑定uid")
						}
					);
				}
			},
			getProduct() {
				let self = this;
				self._get(
					'product.product/detail', {
						product_id: self.product_id,
						url: '',
						visitcode: self.getVisitcode()
					},
					function(res) {
						self.is_product = true;
						self.content_list = [...self.content_list, {
							type: 4
						}];
						self.productDetail = res.data.detail;
					}
				);
			},
			getOrder() {
				let self = this;
				self._get(
					'user.order/detail', {
						order_id: self.order_id
					},
					function(res) {
						self.is_order = true;
						self.content_list = [...self.content_list, {
							type: 5
						}];
						self.order_chat = res.data.order;
					}
				);
			},
			upload(e) {
				this.type = e;
				this.isupload = true;
			},
			getAvatarUrl() {
				let self = this;
				self._get(
					'plus.chat.supplierChat/getInfo', {
						user_id: self.user_id
					},
					function(res) {
						self.myavatarUrl = res.data.info.logo;
						self.avatarUrl = res.data.info.avatarUrl;
						self.url = res.data.info.url;
						self.shop_supplier_id = res.data.info.shop_supplier_id;
						self.supplier_user_id = res.data.info.supplier_user_id;
						self.$nextTick(function() {
							self.socketInit();
						});
					}
				);
			},
			getImgsFunc(e) {
				let self = this;
				if (e != null && e.length > 0) {
					this.img_path = e[0].file_path;
					let data = JSON.stringify({
						supplier_user_id: self.supplier_user_id,
						user_id: self.user_id,
						shop_supplier_id: self.shop_supplier_id,
						msg_type: 1,
						usertype: 'supplier',
						type: 1,
						content: self.img_path,
						app_id: this.getAppId()
					})
					let newdata = JSON.parse(data)
					let item = {
						content: newdata.content,
						user_id: newdata.supplier_user_id,
						type: newdata.type,
						msg_type: 1,
						create_time: self.formatDate(),
						user: {
							avatarUrl: self.myavatarUrl
						}
					}
					this.content_list = [...this.content_list, item];
					self.send(data)
					self.$nextTick(function() {
						self.scrollToBottom()
					})
				}
				this.isupload = false;
			},
			//获取聊天记录
			get_content_list() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				})
				self._post('plus.chat.supplierChat/message', {
					//被聊天人的user_id
					page: self.page,
					user_id: self.user_id,
				}, (res) => {
					console.log(self.content_list)
					let list = res.data.list.data.reverse();
					self.content_list = [...list, ...self.content_list];
					console.log(self.content_list)
					if (res.data.list.last_page <= self.page) {
						self.nomore = true;
					}
					if (self.page == 1) {
						self.$nextTick(() => {
							self.scrollToBottom();
						});
					} else {
						self.$nextTick(() => {
							const newquery = uni.createSelectorQuery().in(self);
							newquery.select('.im_interface_content').boundingClientRect(data => {
								console.log(data)
								self.scrollTop = data.height - self.scrollHeight;
							}).exec();
						});
					}
					uni.hideLoading()
				})
			},
			//打开图片预览
			preview(e, index) {
				let self = this;
				let image_path_arr = [];
				let image_path_list = e;
				image_path_arr.push(image_path_list)
				let picnum = index * 1;
				uni.previewImage({
					urls: image_path_arr,
					current: picnum,
					indicator: 'default',
				});
			},
			scrollToBottom: function() {
				let self = this;
				let query = uni.createSelectorQuery();
				query.selectAll('.m-item').boundingClientRect();
				query.select('#scrollview').boundingClientRect();
				query.exec((res) => {
					self.style.mitemHeight = 0;
					res[0].forEach((rect) => self.style.mitemHeight = self.style.mitemHeight + rect.height +
						40)
					setTimeout(() => {
						if (self.style.mitemHeight > (self.style.contentViewHeight - 100)) {
							self.scrollTop = self.style.mitemHeight - self.style.contentViewHeight +
								150
						}
					}, 300)
				})
			},
			sendProduct() {
				let self = this;
				self.is_product = false;
				let params = {
					product_name: self.productDetail.product_name,
					product_img: self.productDetail.product_image,
					product_price: self.productDetail.product_price
				}
				params = JSON.stringify(params)
				let data = JSON.stringify({
					supplier_user_id: this.you_user_id,
					user_id: this.user_id,
					shop_supplier_id: this.shop_supplier_id,
					msg_type: 2,
					usertype: 'user',
					content: params,
					type: 2,
					app_id: this.getAppId()
				})
				let newdata = JSON.parse(data)
				let item = {
					content: newdata.content,
					user_id: newdata.supplier_user_id,
					create_time: self.formatDate(),
					type: newdata.type,
					msg_type: 2,
					user: {
						avatarUrl: self.myavatarUrl
					}
				}
				this.content_list = [...this.content_list, item];
				self.send(data)
				self.$nextTick(function() {
					self.scrollToBottom()
				})
			},
			sendOrder() {
				let self = this;
				self.is_product = false;
				let params = {
					order_num: self.order_chat.product.length,
					order_price: self.order_chat.order_price,
					order_no: self.order_chat.order_no,
					create_time: self.order_chat.create_time,
					order_id: self.order_chat.order_id,
					product_name: self.order_chat.product[0].product_name,
					product_img: self.order_chat.product[0].image.file_path,
				}
				params = JSON.stringify(params)
				let data = JSON.stringify({
					supplier_user_id: this.you_user_id,
					user_id: this.user_id,
					shop_supplier_id: this.shop_supplier_id,
					msg_type: 2,
					usertype: 'user',
					content: params,
					type: 3,
					app_id: this.getAppId()
				})
				let newdata = JSON.parse(data)
				let item = {
					content: newdata.content,
					user_id: newdata.supplier_user_id,
					create_time: self.formatDate(),
					type: newdata.type,
					msg_type: 2,
					user: {
						avatarUrl: self.myavatarUrl
					}
				}
				this.content_list = [...this.content_list, item];
				self.send(data)
				self.$nextTick(function() {
					self.scrollToBottom()
				})
			},
			getJSON(str) {
				return JSON.parse(str)
			},
			newdata() {
				let self = this;
				this.page++;
				const query = uni.createSelectorQuery().in(this);
				query.select('.im_interface_content').boundingClientRect(data => {
					this.scrollHeight = data.height;
				}).exec();
				this.get_content_list();
			},
			inputFocus(e) {
				this.inputBottom = e.detail.height;
			},
			inputBlur() {
				this.inputBottom = 0;
			},
			isuserAgent() {
				let self = this;
				switch (uni.getSystemInfoSync().platform) {
					case 'android':
						self.is_Ios = false;
						console.log('运行Android上')
						break;
					case 'ios':
						console.log('运行iOS上')
						break;
					default:
						console.log('运行在开发者工具上')
						break;
				}
			},
			formatDate() {
				let date = new Date();
				let year = date.getFullYear(); // 年
				let month = date.getMonth() + 1; // 月
				let day = date.getDate(); // 日
				let week = date.getDay(); // 星期
				let weekArr = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];
				let hour = date.getHours(); // 时
				hour = hour < 10 ? "0" + hour : hour; // 如果只有一位，则前面补零
				let minute = date.getMinutes(); // 分
				minute = minute < 10 ? "0" + minute : minute; // 如果只有一位，则前面补零
				let second = date.getSeconds(); // 秒
				second = second < 10 ? "0" + second : second; // 如果只有一位，则前面补零
				return `${year}-${month}-${day} ${hour}:${minute}:${second}`;
			},
		}
	}
</script>

<style>
	page {
		background-color: #EDEDED;
	}

	.im_interface {
		width: 750rpx;
	}

	.im_interface_content {
		padding: 20rpx 50rpx;
	}

	.buttom {
		width: 750rpx;
		height: 130rpx;
		background-color: #F7F7F7;
		border-top: 1px #D2D2D2 solid;
		display: flex;
		justify-content: flex-start;
		align-items: flex-start;
		position: fixed;
		bottom: 0;
		padding-top: 20rpx;
		box-sizing: border-box;
	}

	.buttom input {
		width: 605rpx;
		height: 75rpx;
		line-height: 75rpx;
		background-color: white;
		display: block;
		padding: 5rpx;
		box-sizing: border-box;
		margin-left: 30rpx;
		border-radius: 10rpx;
		vertical-align: middle;
		position: relative;
		border: 1rpx solid #CCCCCC;

	}

	.buttom button {
		width: 125rpx;
		height: 50rpx;
		line-height: 200%;
		display: block;
		margin: 0 15rpx;
		vertical-align: middle;
		position: relative;
		background-color: #05C160;
		color: white;
	}

	.im_text {
		/* width: 100%; */
		display: flex;
		margin-top: 75rpx;
		flex-direction: row-reverse;
		position: relative;
	}

	.im_text2 {
		/* width: 100%; */
		display: flex;
		margin-top: 75rpx;
		position: relative;
	}

	.im_text .avatar {
		width: 84rpx;
		height: 84rpx;
		margin-left: 3%;
		border-radius: 10rpx;
		background-color: #000000;
		align-items: flex-start;
	}

	.im_text2 .avatar {
		width: 84rpx;
		height: 84rpx;
		margin-right: 3%;
		border-radius: 10rpx;
		background-color: #000000;
		align-items: flex-start;
	}

	.im_text .my_content {
		max-width: 550rpx;
		align-items: flex-start;
		border-radius: 10rpx;
		padding: 17rpx 20rpx;
		box-sizing: border-box;
		word-break: break-all;
	}

	.im_text2 .my_content {
		max-width: 450rpx;
		align-items: flex-start;
		border-radius: 10rpx;
		padding: 10rpx;
		box-sizing: border-box;
		word-break: break-all;
	}

	.im_text .you_content {
		max-width: 450rpx;
		align-items: flex-start;
		border-radius: 10rpx;
		padding: 17rpx 20rpx;
		box-sizing: border-box;
	}

	.im_text2 .you_content {
		max-width: 450rpx;
		align-items: flex-start;
		border-radius: 10rpx;
		padding: 10rpx;
		box-sizing: border-box;
	}

	.my_content {
		background-color: #9EEA6A;
		margin-top: 25rpx;
	}

	.you_content {
		background-color: white;
		margin-top: 25rpx;
	}

	.im_icon {
		position: absolute;
		bottom: -2rpx;
		right: 41px;
		transform: rotate(270deg);

	}

	.im_icon2 {
		position: absolute;
		bottom: 0;
	}

	.im_icon .icon-sanjiao1 {
		color: #9EEA6A;
	}

	.im_icon2 .icon-sanjiao1 {
		position: absolute;
		bottom: 0;
	}

	.upload {
		width: 50rpx;
		height: 50rpx;
		background-color: #007AFF;
		border-radius: 50%;
		line-height: 50rpx;
		text-align: center;
	}

	.cont_img {
		width: 200rpx;
		height: 200rpx;
		border-radius: 10rpx;
	}

	.top_pro {
		/* margin-left: 85rpx; */
	}

	.top_product {
		width: 610rpx;
		height: 200rpx;
		border-radius: 10rpx;
		margin: 0 auto;
		display: flex;
		align-items: flex-start;
		justify-content: flex-start;
		background-color: #FFFFFF;
		padding: 10rpx 20rpx;
		position: relative;
	}

	.top_order {
		width: 610rpx;
		/* height: 200rpx; */
		border-radius: 10rpx;
		margin: 0 auto;
		display: flex;
		align-items: flex-start;
		justify-content: flex-start;
		background-color: #FFFFFF;
		padding: 10rpx 20rpx;
		position: relative;
	}

	.pro_img {
		height: 150rpx;
		width: 150rpx;
		border-radius: 10rpx;
		margin-right: 20rpx;
		background-color: #FFFFFF;
	}

	.pro_name {
		font-size: 28rpx;
		text-overflow: -o-ellipsis-lastline;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 3;
		line-clamp: 3;
		-webkit-box-orient: vertical;
		margin-bottom: 50rpx;
		width: 400rpx;
	}

	.pro_price {
		font-size: 24rpx;
		color: #E2231A;
	}

	.pro_btn {
		position: absolute;
		bottom: 12rpx;
		right: 15rpx;
		width: 180rpx;
		height: 50rpx;
		line-height: 50rpx;
		border-radius: 25rpx;
		text-align: center;
		font-size: 24rpx;
		color: #FFFFFF;
		background-color: #FF6633;
	}

	.orderdetail_btn {
		width: 180rpx;
		height: 50rpx;
		line-height: 50rpx;
		border-radius: 25rpx;
		text-align: center;
		font-size: 24rpx;
		color: #FFFFFF;
		background-color: #FF6633;
		margin-left: 270rpx;
		margin-top: 20rpx;
	}

	.ord_btn {
		position: absolute;
		bottom: 20rpx;
		right: 15rpx;
		width: 180rpx;
		height: 50rpx;
		line-height: 50rpx;
		border-radius: 25rpx;
		text-align: center;
		font-size: 24rpx;
		color: #FFFFFF;
		background-color: #FF6633;
	}

	.close_pro {
		position: absolute;
		top: 12rpx;
		right: 15rpx;
	}

	.product_item {
		width: 400rpx;
		height: 200rpx;
	}

	.product_txtitem {
		display: flex;
		width: 550rpx;
		/* height: 150rpx; */
	}

	.pro_txtname {
		font-size: 28rpx;
		text-overflow: -o-ellipsis-lastline;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 1;
		line-clamp: 1;
		-webkit-box-orient: vertical;
		margin-bottom: 50rpx;
	}

	.my_text_content {
		/* height: 100%; */
		max-width: 430rpx;
	}

	.you_text_content {
		/* height: 100%; */
		max-width: 430rpx;
	}

	.my_date {
		color: #cccccc;
		font-size: 24rpx;
		position: absolute;
		top: -10rpx;
	}

	.im_text .my_date {
		right: 100rpx;
	}

	.sendpro_price {
		position: absolute;
		bottom: 25px;
	}

	.sendord_price {
		position: absolute;
		bottom: 40px;
	}

	.upload_box {
		/* width: 50rpx; */
		height: 75rpx;
		display: flex;
		align-items: center;
		margin-left: 20rpx;
		justify-content: space-around;
		flex: 1;
	}
</style>
