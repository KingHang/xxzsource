<template>
	<view style="padding-bottom: 100rpx">
		<!-- #ifdef APP-PLUS -->
		<header-bar></header-bar>
		<!-- #endif -->
		
        <view v-if="!loadding" class="container" style="">
            <block v-if="tableData.length > 0">
                <block v-for="(supplier_item, supplier_index) in tableData" :key="supplier_index">
                    <view class="hlbblock" style="padding: 20rpx;">
                        <view style="display: flex;justify-content: space-between;align-items: center;margin-bottom: 20rpx">
                            <view style="display: flex;align-items: center">
                                <label class="d-c-c" >
                                    <u-checkbox  @change="checkStprItem(supplier_item, supplier_index)"  v-model="supplier_item.checked" active-color="#f53630" shape="circle" ></u-checkbox>
                                </label>
                                <view>
                                    <image src="/static/images/cart/shop.png" style="width: 40rpx" mode="widthFix"></image>
                                </view>
                                <view style="color: #999999;font-size: 26rpx;margin-left: 15rpx" @click="gotoPage('/pagesShop/shop/shop?shop_supplier_id='+supplier_item.supplier.shop_supplier_id)">{{supplier_item.supplier.name}}</view>
                            </view>
                            <view class="wlkarrow"></view>
                        </view>
                        <block v-for="(item, index) in supplier_item.productList" :key="index">
                            <view style="display: flex;align-items: center;padding:30rpx 15rpx;border-top: 1px #F2F2F2 solid">
                                <view style="width: 10%" >
                                    <u-checkbox  @change="checkItem(item, supplier_index, index)"  v-model="item.checked" active-color="#f53630" shape="circle" ></u-checkbox>
                                </view>
                                <view style="width: 90%;display: flex">
                                    <view @click="gotoPage('/pages/product/detail/detail?product_id='+item.product_id)" style="width: 40%">
                                        <image :src="item.product_image" style="width: 200rpx;height: 190rpx;border-radius: 15rpx"  mode="aspectFill"></image>
                                    </view>
                                    <view style="width: 60%;margin-left: 10rpx">
                                        <view style="height: 80rpx">
                                            <view class="linedot">
                                                {{item.product_name}}
                                            </view>
                                            <view  style="font-size: 25rpx;color: #999999" >
                                                {{item.product_sku.product_attr}}
                                            </view>
                                        </view>
                                        <view style="display: flex;justify-content: space-between;height: 55rpx">
                                            <view style="color: #F63E36">
                                                <text style="font-size: 20rpx">¥</text><text style="font-size: 35rpx;font-weight: bold">{{item.product_price}}</text>
                                            </view>
                                            <view @click="onDelete(item)">
                                                <image src="/static/images/cart/delete.png" style="width: 30rpx" mode="widthFix"></image>
                                            </view>
                                        </view>
                                        <view style="text-align: right">
                                            <u-number-box :disabled="number_loading" disabled-input="true" @minus="reduceFunc(item)" @plus="addFunc(item)" style="width: 170rpx;" size="20rpx" bg-color="#f0f1f3" color="#323233" :max="item.product_sku.stock_num" :min="1" v-model="item.total_num"></u-number-box>
                                        </view>
                                    </view>
                                </view>
                            </view>
                        </block>
                    </view>
                </block>

                <view  style="bottom: 0rpx;background-color: white;height: 120rpx;position: fixed;width: 100%;z-index: 999;right: 0">
                   <view class="tool" style="display: flex;justify-content: space-between;align-items: center;padding: 10rpx 25rpx">
                        <view class="">
                            <u-checkbox size="40" @change="onCheckedAll" v-model="checkedAll" active-color="#f53630" shape="circle">全选</u-checkbox>
                        </view>
                        <view style="display: flex;width: 74%;align-items: center;justify-content: flex-end;">
                            <view  style="">
                                <view  style="height: 50rpx;text-align: right">合计：
                                    <text class="chosecolor" style="font-size: 38rpx;font-weight: bold"><text style="font-size: 20rpx;margin-left: 10rpx;margin-right: 3rpx">¥</text>{{totalPrice}}</text>
                                </view>
                                <view style="text-align: right;font-size: 20rpx;color: #999999">不含运费</view>
                            </view>
                            <view @click="Submit" :class="'hlbbutton '+(total>0?'':'disable')" style="width: 150rpx;text-align: center;height: 70rpx;line-height: 70rpx">
                                结算
                            </view>
                        </view>

                    </view>
                </view>
            </block>
            <block v-else>
                <view class="center" >
                    <view style="padding-top: 250rpx">
                        <image class="light" src="http://img.pighack.com/202203251056291d3fb2310.png" style="width: 100%" mode="widthFix"></image>
                        <view style="color:#999999;font-size: 24rpx">您的购物车中没有商品哦！</view>
                        <navigator class="hlbbutton" style="width: 40%;margin:70rpx auto;" openType="switchTab" url="/pages/index/index">去首页逛逛吧</navigator>
                    </view>
                </view>
            </block>
        </view>

        <!--底部按钮-->
		<request-loading :loadding='isloadding'></request-loading>
	</view>
</template>

<script>
	import recommendProduct from '@/components/recommendProduct/recommendProduct.vue';
	export default {
		components: {
			recommendProduct
		},
		data() {
			return {
				isloadding: true,
				/*滚动组件高度*/
				scrollviewHigh: 0,
				/*是否加载完成*/
				loadding: true,
				isEdit: false,
				/*购物车各商铺商品*/
				tableData: [],
				arrIds: [],
				// 是否全选
				checkedAll: false,
				totalPrice: 0,
				totalProduct: 0,
				store_open: 1,
				number_loading: false,
        is_auto: 0
			};
		},
		onLoad() {},
		onShow() {
			/*获取产品详情*/
			this.getData();
		},
		mounted() {
			this.init();
		},
		methods: {
			/*初始化*/
			init() {
				let _this = this;
				uni.getSystemInfo({
					success(res) {
						let ratio = res.windowWidth / 750;
						let h = res.windowHeight - ratio * 98;
						_this.scrollviewHigh = h;
					}
				});
			},
			/*获取数据*/
      getData() {
        let self = this;
        self.isloadding = true;
        self._get('order.cart/lists', {

        }, function(res) {
          let auto = uni.getStorageSync('TabBar').is_auto && uni.getStorageSync('TabBar').is_auto != 0;
          self.is_auto = auto;
          self.isloadding = false;
          self.tableData = res.data.productList;
          //#ifdef MP-WEIXIN
          self.store_open = res.data.store_open;
          //#endif
          self.tableData.forEach((item, index) => {
            item.checked = false;
          })
          self.loadding = false;
          self._initGoodsChecked();
        });
      },
			/**
			 * 初始化商品选中状态
			 */
			_initGoodsChecked() {
				let self = this;
				let checkedData = self.getCheckedData();
				// 将商品设置选中
				let productCount = 0;
				self.tableData.forEach(item => {
					item.productList.forEach(product => {
						productCount++;
						product.checked = self.inArray(`${product.product_id}_${product.product_sku_id}`, checkedData);
					});
				});
				self.totalProduct = productCount;
				self.isEdit = false;
				self.checkedAll = checkedData.length == self.totalProduct;
				self.tableData.forEach((item, index) => {
					this.onUpsupChecked(this.tableData, index)
				})
				// 更新购物车已选商品总价格
				self.updateTotalPrice();
			},
			/**
			 * 获取选中的商品
			 */
			getCheckedData() {
				let checkedData = uni.getStorageSync('checkedData');
				return checkedData ? checkedData : [];
			},
			/*单选*/
			checkItem(e, supplier_index, index) {
                console.log(e.checked)
				// e.checked = !e.checked;
				this.$set(this.tableData[supplier_index].productList, index, e);
				console.log(this.tableData)
				// 更新店铺全选状态
				this.onUpsupChecked(this.tableData, supplier_index);
				// 更新选中记录
				this.onUpdateChecked();
				// 更新购物车已选商品总价格
				this.updateTotalPrice();
				// 更新全选状态
				console.log(this.getCheckedData().length);
				this.checkedAll = this.getCheckedData().length == this.totalProduct;
			},
			// 更新店铺全选状态
			onUpsupChecked(item, index) {
				let supplier_flag = true;
				for (var i = 0; i < item[index].productList.length; i++) {
					if (!item[index].productList[i].checked) {
						supplier_flag = false;
					}
				}
				this.$set(item[index], 'checked', supplier_flag);
				console.log("item=====" + supplier_flag);
			},
			/**
			 * 更新商品选中记录
			 */
			onUpdateChecked() {
				let self = this,
					checkedData = [];
				self.tableData.forEach(item => {
					item.productList.forEach(product => {
						if (product.checked == true) {
							checkedData.push(`${product.product_id}_${product.product_sku_id}`);
						}
					});
				});
				uni.setStorageSync('checkedData', checkedData);
			},
			/* 店铺全选 */
			checkStprItem(itemp, index) {
				let self = this;
				console.log(itemp)
				// itemp.checked = !itemp.checked;
				itemp.productList.forEach((item, index) => {
					item.checked = itemp.checked;
				})
				self.updateTotalPrice();
				// 更新选中记录
				self.onUpdateChecked();
				// 更新全选状态
				console.log(this.getCheckedData().length);
				this.checkedAll = this.getCheckedData().length == this.totalProduct;
			},
			/*全选*/
			onCheckedAll() {
				let self = this;
				console.log(self.checkedAll)
				// self.checkedAll = !self.checkedAll;
				self.tableData.forEach(item => {
					this.$set(item, 'checked', self.checkedAll);
					item.productList.forEach(product => {
						product.checked = self.checkedAll;
					});
				});
				self.updateTotalPrice();
				// 更新选中记录
				self.onUpdateChecked();
			},
			updateTotalPrice: function() {
				let total_price = 0;
				let items = this.tableData;
				for (let i = 0; i < items.length; i++) {
					for (let j = 0; j < items[i].productList.length; j++) {
						if (items[i].productList[j]['checked'] == true) {
							total_price += items[i].productList[j]['total_num'] * items[i].productList[j]['product_price'];
						}
					}
				}
				/*保留2位小数*/
				this.totalPrice = total_price.toFixed(2);
			},
			/*去结算*/
			Submit() {
				let arrIds = this.getCheckedIds();
				if (arrIds.length == 0) {
					uni.showToast({
						title: '请选择商品',
						icon: 'none'
					});
					return false;
				}
				this.gotoPage('/pages/order/confirm-order?order_type=cart&cart_ids=' + arrIds);
			},
			/*添加*/
			addFunc(item) {
				let self = this;
				let product_id = item.product_id;
				let product_sku_id = item.product_sku_id;
				self.number_loading = true;
				uni.showLoading({
					title: '加载中'
				});
				self._post(
					'order.cart/add', {
						product_id: product_id,
						product_sku_id: product_sku_id,
						total_num: 1
					},
					function(res) {
						uni.hideLoading();
						self.loadding = false;
						self.getData();
					},
					function() {
						self.loadding = false;
					}
				);
			},
			/*减少*/
			reduceFunc(item) {
				let self = this;
				let product_id = item.product_id;
				let product_sku_id = item.product_sku_id;
				self.number_loading = true;
				/*if (item.total_num <= 1) {
					return;
				}*/
				uni.showLoading({
					title: '加载中'
				});
				self._post(
					'order.cart/sub', {
						product_id: product_id,
						product_sku_id: product_sku_id
					},
					function(res) {
						uni.hideLoading();
						self.loadding = false;
						self.getData();
					},
					function() {
						self.loadding = false;
					}
				);
			},
			/*删除购物车*/
			onDelete(item) {
				let self = this;
                let cartIds = [];
                cartIds.push(`${item.product_id}_${item.product_sku_id}`);
				uni.showModal({
					title: '提示',
					content: '您确定要移除选择的商品吗?',
					success(e) {
						e.confirm &&
							self._post(
								'order.cart/delete', {
									product_sku_id: cartIds
								},
								function(res) {
									// 删除选中的商品
									self.onDeleteEvent(cartIds);
									self.getData();
								}
							);
					}
				});
			},
			/**
			 * 获取已选中的商品
			 */
			getCheckedIds() {
				let self = this;
				let arrIds = [];
				self.tableData.forEach(item => {
					item.productList.forEach(product => {
						if (product.checked == true) {
							arrIds.push(`${product.product_id}_${product.product_sku_id}`);
						}
					});
				});
				return arrIds;
			},
			/**
			 * 商品删除事件
			 */
			onDeleteEvent(cartIds) {
				let self = this;
				cartIds.forEach(cartIndex => {
					self.tableData.forEach((item, productIndex) => {
						if (cartIndex == `${item.product_id}_${item.product_sku_id}`) {
							self.tableData.splice(productIndex, 1);
						}
					});
				});
				// 更新选中记录
				self.onUpdateChecked();
				return true;
			},
			/**
			 * 是否在数组内
			 */
			inArray(search, array) {
				for (var i in array) {
					if (array[i] == search) {
						return true;
					}
				}
				return false;
			},
			/*去购物*/
			gotoShop() {
				let url = '/pages/index/index';
				this.gotoPage(url);
			}
		}
	};
</script>

<style lang="scss" scoped>
    page{
        background: #F2F2F2;
    }
	
	.foot_ {
		height: 98rpx;
		width: 100%;
	}

	.card .scroll-Y {
		position: relative;
	}

	.card .checkbox {
		color: red;
		transform: scale(0.7);
	}

	.address-bar {
		padding: 20rpx;
		background-color: #f2f2f2;
	}

	.address-bar button {
		border: none;
		background: none;
		color: #333333;
	}

	.section {
		background: #f2f2f2;
		padding: 20rpx;
	}

	.section .item {
		margin: 0 26rpx;
		display: flex;
		align-items: center;
		// border: 1px solid #f2f2f2;
		padding-right: 19rpx;
		padding-bottom: 29rpx;
		padding-top: 29rpx;
	}

	.section .cover {
		width: 150rpx;
		height: 150rpx;
		border-radius: 8px;
	}

	.section .info {
		flex: 1;
		padding-left: 30rpx;
		box-sizing: border-box;
		overflow: hidden;
	}

	.section .title {
		width: 100%;
		font-size: 26rpx;
		display: -webkit-box;
		overflow: hidden;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
	}

	.vender .list .describe {
		width: 100%;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.section .describe {
		margin-top: 20rpx;
		font-size: 24rpx;
		color: #999999;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 3;
		overflow: hidden;
	}

	.section .price {
		color: #F6220C;
		font-size: 24rpx;
	}

	.section .price .num {
		padding: 0 4rpx;
		font-size: 32rpx;
	}

	.section .level-box {
		margin-top: 20rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.section .level-box .key {
		font-size: 24rpx;
		color: #999999;
	}

	.section .level-box .num-wrap {
		display: flex;
		justify-content: flex-end;
		align-items: center;
	}

	.section .level-box .icon-box {
		width: 33rpx;
		height: 33rpx;
		border: 1px solid #c5c5c5;
		background: #f2f2f2;
	}

	.section .level-box .icon-box .gray {
		color: #cccccc;
	}

	.section .level-box .icon-box .gray3 {
		color: #333333;
	}

	.section .level-box .text-wrap {
		margin: 0 20rpx;
		height: 33rpx;
		border: none;
		background: none;
	}

	.section .level-box .text-wrap input {
		padding: 0 4rpx;
		height: 33rpx;
		line-height: 1;
		width: 40rpx;
		font-size: 32rpx;
		text-align: center;
		display: flex;
		align-items: center;
		min-height: 33rpx;
	}

	.bottom-btns {
		position: fixed;
		width: 100%;
		padding: 0 0 0 20rpx;
		box-sizing: border-box;
		display: flex;
		justify-content: space-between;
		align-items: center;
		height: 90rpx;
		// /* bottom: calc(var(--window-bottom); */
		bottom: var(--window-bottom, 0);
		// bottom: 50px;
		left: 0;
		box-shadow: 0 -2rpx 8rpx rgba(0, 0, 0, 0.1);
		background: #ffffff;
		z-index: 1000;
	}

	.bottom-btns .delete-btn {
		margin: 0;
		padding: 0 30rpx;
		height: 60rpx;
		line-height: 60rpx;
		border-radius: 30rpx;
		background: linear-gradient(90deg, #FF6B6B 4%, #F6220C 100%);
		font-size: 26rpx;
	}

	.bottom-btns .buy-btn {
		margin: 0;
		padding: 0 24rpx;
		height: 60rpx;
		line-height: 60rpx;
		border-radius: 30rpx;
		background: linear-gradient(90deg, #FF6B6B 4%, #F6220C 100%);
		font-size: 26rpx;
	}

	.bottom-btns .price {
		color: $dominant-color;
	}

	.supplier_list {
		overflow: hidden;
		background-color: #FFFFFF;
		border-radius: 15rpx;
		margin-bottom: 30rpx;
	}

	.supplier_list_tit {
		display: flex;
		align-items: center;
		margin-left: 28rpx;
		margin-top: 28rpx;
	}

	.icon-dianpu1 {
		font-size: 34rpx;
		color: #333333;
		margin: 0 17rpx;
	}

	.cart_none .cart_none_img {
		width: 348rpx;
		height: 222rpx;
	}

	.none_btn {
		font-size: 32rpx;
		border-radius: 40rpx;
	}

	.add_icon,
	.reduce_icon {
		width: 32rpx;
		height: 32rpx;
	}
</style>
