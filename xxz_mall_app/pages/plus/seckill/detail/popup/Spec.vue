<template>
	<view :class="Visible ? 'product-popup open' : 'product-popup close'" @touchmove.stop.prevent="" @click="closePopup">
		<view class="popup-bg"></view>
		<view class="main" v-on:click.stop>
			<view class="header">
				<image :src="form.show_sku.sku_image" mode="aspectFit" class="avt"></image>
				<view class="price d-s-c">
					<template v-if="form.specData == null||isAll">
						<text>¥</text>
						<text class="num fb">{{ form.show_sku.seckill_price }}</text>
						<text class="old-price">¥{{ form.show_sku.line_price }}</text>
					</template>
					<template v-else>
						<text class="f22">¥</text>
						<text class="f40 fb">{{ form.detail.seckill_price }}</text>
					</template>
				</view>
				<view class="stock">库存：{{ form.show_sku.seckill_stock }}</view>
				<view class="p-20-0 select_spec">{{ selectSpec }}</view>
				<view class="close-btn" @click="closePopup"><text class="icon iconfont icon-guanbi"></text></view>
			</view>

			<view class="body">

				<view v-if="form.specData">
					<scroll-view scroll-y="true" class="specs mt20" style="max-height: 600rpx;" v-if="form.specData !=null">
						<view class="specs mt20" v-for="(item_attr,attr_index) in form.specData.spec_attr" :key="attr_index">
							<view class="specs-hd p-20-0">
								<text class="f24 gray9">{{item_attr.group_name}}</text>
								<text class="ml10 red" v-if="form.productSpecArr[attr_index]==null">
									请选择{{item_attr.group_name}}
								</text>
							</view>
							<view class="specs-list">
								<button :class="item.checked ? 'btn-checked' : 'btn-checke'" v-for="(item,item_index) in item_attr.spec_items"
								 :key="item_index" @click="selectAttr(attr_index, item_index)">{{item.spec_value}}
								</button>
							</view>

						</view>
					</scroll-view>
				</view>
				<!--选择数量-->
				<view class="level-box count_choose">
					<text class="key">数量</text>
					<view class="d-s-c">
						<view class="icon-box minus d-c-c" @click="sub()" :class="{ 'num-wrap': !issub }"><text class="icon iconfont icon-jian"
							 style="font-size: 20rpx;color: #333333;"></text></view>
						<view class="text-wrap"><input type="text" v-model="form.show_sku.sum" value="" /></view>
						<view class="icon-box plus d-c-c" :class="{ 'num-wrap': !isadd }" @click="add()"><text class="icon iconfont icon-jia"
							 style="font-size: 20rpx;color: #333333;"></text></view>
					</view>
				</view>
			</view>
			<view class="btns white"><button class="confirm-btn" @click="confirmFunc(form)">确认</button></view>
		</view>
	</view>
</template>

<script>
	import {
		judgeSelect
	} from '@/common/specSelect.js';
	export default {
		data() {
			return {
				/*是否可见*/
				Visible: false,
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 400,
				form: {
					detail: {
						product_sku: {},
						show_sku: {},
						show_point_sku: {
							seckill_price: 0
						}
					},
					show_sku: {
						sum: 1
					},
					specData: null
				},
				/*当前商品总库存*/
				stock: 0,
				/*选择提示*/
				selectSpec: '',
				/*规格是否选择完整*/
				isAll: false
			};
		},
		props: ['isPopup', 'productModel'],
		onLoad() {},
		mounted() {},
		computed: {
			/*判断增加数量*/
			isadd: function() {
				return this.form.show_sku.sum >= this.stock || this.form.show_sku.sum >= this.form.detail.limit_num;
			},

			/*判断减少数量*/
			issub: function() {
				return this.form.show_sku.sum <= 1;
			}
		},
		watch: {
			'isPopup': function(n, o) {
				if (n != o) {
					this.Visible = n;
					if (!this.isOpenSpec) {
						this.form = this.productModel;
						this.isOpenSpec = true;
						this.initShowSku();
						this.form.specData.spec_attr.forEach((item,index)=>{
							this.selectAttr(index,0);
						})
					}
					this.form.type = this.productModel.type;
				}
			},

			'form.specData': {
				handler(n, o) {
					let str = '',
						select = '';
					this.isAll = true;
					if (n) {
						if (n != '') {
							for (let i = 0; i < n.spec_attr.length; i++) {
								if (this.form.productSpecArr[i] == null) {
									this.isAll = false;
									str += n.spec_attr[i].group_name + ' ';
								} else {
									n.spec_attr[i].spec_items.forEach(item => {
										if (this.form.productSpecArr[i] == item.item_id) {
											select += '\"' + item.spec_value + '\" ';
										}
									});
								}
							}
						}
						if (!this.isAll) {
							str = '请选择: ' + str;
						} else {
							select = '已选: ' + select;
						}
						this.selectSpec = this.isAll ? select : str;
					} else {

					}
				},
				deep: true,
				immediate: true
			}
		},
		methods: {
			/*初始化*/
			init() {
				let _this = this;
				_this.phoneHeight = res.scrollviewHigh;
			},
			/*关闭弹窗*/
			closePopup() {
				this.$emit('close', this.form.specData, null);
			},
			initShowSku() {
				this.form.show_sku.sku_image = this.form.detail.product.image[0].file_path;
				this.form.show_sku.seckill_price = this.form.detail.seckill_price;
				this.form.show_sku.product_sku_id = 0;
				this.form.show_sku.line_price = this.form.detail.line_price;
				this.form.show_sku.seckill_stock = this.form.detail.stock;
				this.form.show_sku.sum = 1;
				this.stock = this.form.detail.product_stock;
			},
			/*确认提交*/
			confirmFunc() {
				if (this.form.specData != null && !this.isAll) {
					uni.showToast({
						title: '请选择规格',
						icon: 'none',
						duration: 2000
					});
					return;
				}
				this.createdOrder();
			},

			/*选择属性*/
			selectAttr(attr_index, item_index) {
				let self = this;
				let items = self.form.specData.spec_attr[attr_index].spec_items;
				let curItem = items[item_index];
				if (curItem.checked) {
					curItem.checked = false;
					self.form.productSpecArr[attr_index] = null;
				} else {
					for (let i = 0; i < items.length; i++) {
						items[i].checked = false;
					}
					curItem.checked = true;
					self.form.productSpecArr[attr_index] = curItem.item_id;
				}

				/*判断哪些规格可以选*/
				judgeSelect(self.form.specData.spec_attr, attr_index, self.form.productSpecArr, self.form.productSku);

				let isall = true;
				for (let i = 0; i < self.form.productSpecArr.length; i++) {
					let item = self.form.productSpecArr[i];
					if (item == null) {
						isall = false;
						self.initShowSku();
						return;
					}
				}

				if (!isall) {
					self.initShowSku();
					return;
				}

				// 更新商品规格信息
				self.updateSpecProduct();
			},

			/*更新商品规格信息*/
			updateSpecProduct() {
				let self = this;
				let specSkuId = self.form.productSpecArr.join('_');
				// 查找skuItem
				let spec_list = self.form.specData.spec_list,
					skuItem = spec_list.find(val => {
						return val.spec_sku_id == specSkuId;
					});
				// 记录product_sku_id
				// 更新商品价格、划线价、库存
				if (typeof skuItem === 'object') {
					let point_sku_list = self.form.detail.seckillSku,
						seckillSkuItem = point_sku_list.find(val => {
							return val.product_sku_id == skuItem.product_sku_id;
						});
					/*保存当前规格*/
					self.stock = seckillSkuItem.seckill_stock;
					if (self.form.show_sku.sum > self.stock) {
						self.form.show_sku.sum = self.stock > 0 ? self.stock : 1;
					}
					self.form.show_sku.line_price = skuItem.spec_form.product_price;
					self.form.show_sku.product_sku_id = skuItem.spec_sku_id;
					self.form.show_sku.seckill_price = seckillSkuItem.seckill_price;
					self.form.show_sku.seckill_product_sku_id = seckillSkuItem.seckill_product_sku_id;
					self.form.show_sku.seckill_stock = seckillSkuItem.seckill_stock;
					if (skuItem.spec_form.image_id > 0) {
						self.form.show_sku.sku_image = skuItem.spec_form.image_path;
					} else {
						self.form.show_sku.sku_image = self.form.detail.product.image[0].file_path;
					}
				}
			},

			/*创建订单*/
			createdOrder() {
				let self = this;
				self.$emit('confirm')
				let seckill_product_id = self.form.detail.seckill_product_id;
				let num = self.form.show_sku.sum;
				let product_sku_id = self.form.show_sku.product_sku_id;
				let seckill_product_sku_id = self.form.show_sku.seckill_product_sku_id;
				self.gotoPage('/pages/order/confirm-order?product_num=' +
						num +
						'&seckill_product_id=' +
						seckill_product_id +
						'&product_sku_id=' +
						product_sku_id +
						'&seckill_product_sku_id=' +
						seckill_product_sku_id +
						'&order_type=seckill');
			},

			/*商品增加*/
			add() {
				if (this.stock <= 0) {
					return;
				}
				if (this.form.show_sku.sum >= this.stock) {
					uni.showToast({
						title: '数量超过了库存',
						icon: 'none',
						duration: 2000
					});
					return false;
				}
				if (this.form.show_sku.sum >= this.form.detail.limit_num) {
					uni.showToast({
						title: '数量超过了限购数量',
						icon: 'none',
						duration: 2000
					});
					return false;
				}
				this.form.show_sku.sum++;
			},

			/*商品减少*/
			sub() {
				if (this.stock <= 0) {
					return;
				}
				if (this.form.show_sku.sum < 2) {
					uni.showToast({
						title: '商品数量至少为1',
						icon: 'none',
						duration: 2000
					});
					return false;
				}
				this.form.show_sku.sum--;
			}
		}
	};
</script>

<style lang="scss">
	.product-popup {}

	.product-popup .popup-bg {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background: rgba(0, 0, 0, .6);
		z-index: 99;
	}

	.product-popup .main {
		position: fixed;
		width: 100%;
		// bottom: 0;
		min-height: 200rpx;
		// max-height: 900rpx;
		background-color: #fff;
		transform: translate3d(0, 980rpx, 0);
		transition: transform .2s cubic-bezier(0, 0, .25, 1);
		bottom: env(safe-area-inset-bottom);
		border-radius: 12rpx;
		z-index: 99;
	}

	.product-popup.open .main {
		transform: translate3d(0, 0, 0);
		z-index: 99;
	}

	.product-popup.close .popup-bg {
		display: none;
	}

	.product-popup.close .main {
		display: none;
		z-index: -1;
	}


	.product-popup .header {
		min-height: 120rpx;
		padding: 40rpx 0 40rpx 250rpx;
		position: relative;
		border-bottom: 1px solid #eeeeee;
	}

	.product-popup .header .avt {
		position: absolute;
		top: 10rpx;
		left: 30rpx;
		width: 180rpx;
		height: 180rpx;
		border: 2px solid #ffffff;
		background: #ffffff;
		border-radius: 12rpx;
	}

	.product-popup .header .stock {
		font-size: 26rpx;
		color: #999999;
	}

	.product-popup .close-btn {
		position: absolute;
		width: 40rpx;
		height: 40rpx;
		top: 40rpx;
		right: 30rpx;
	}

	.product-popup .price {
		height: 80rpx;
		color: $dominant-color;
		font-size: 30rpx;
	}

	.product-popup .price .num {
		padding: 0 4rpx;
		font-size: 50rpx;
	}

	.product-popup .old-price {
		margin-left: 10rpx;
		font-size: 30rpx;
		color: #999999;
		text-decoration: line-through;
	}

	.product-popup .body {
		padding: 20rpx 30rpx 39rpx 30rpx;
		// max-height: 600rpx;
		overflow-y: auto;
		padding-bottom: 88rpx;
	}

	.product-popup .level-box {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.product-popup .level-box .key {
		font-size: 24rpx;
		color: #999999;
	}

	.product-popup .level-box .icon-box {
		width: 48rpx;
		height: 40rpx;
		background: #e0e0e0;
	}

	.product-popup .num-wrap .iconfont {
		color: #666;
	}

	.product-popup .num-wrap.no-stock .iconfont {
		color: #cccccc;
	}

	.product-popup .level-box .text-wrap {
		margin: 0 4rpx;
		height: 60rpx;
		border: none;
		background: #ffffff;
	}

	.product-popup .level-box .text-wrap input {
		padding: 0 10rpx;
		height: 60rpx;
		line-height: 60rpx;
		width: 80rpx;
		text-align: center;
		font-size: 32rpx;
	}

	.specs .specs-list {
		display: flex;
		justify-content: flex-start;
		align-items: center;
		flex-wrap: wrap;
	}

	.specs .specs-list button {
		margin-right: 10rpx;
		margin-bottom: 10rpx;
		font-size: 24rpx;
	}

	.specs .specs-list button:after,
	.product-popup .btns button,
	.product-popup .btns button:after {
		border: 0;
		border-radius: 0;
		margin-bottom: 55rpx;
	}

	.product-popup .btns .confirm-btn {
		width: 710rpx;
		height: 80rpx;
		background: linear-gradient(90deg, #FF6B6B 4%, #F6220C 100%);
		border-radius: 40rpx;
		margin: 0 auto;
		margin-bottom: 55rpx;
		background-color: #FFFFFF;
		color: #FFFFFF;
		line-height: 80rpx;
		font-size: 32rpx;
	}

	.select_spec {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.btn-checked {
		border: 1px solid #F6220C;
		border-radius: 6px;
		color: #F6220C;
		font-size: 26rpx;
		background-color: #FFFFFF;
	}

	.btn-checke {
		border: 1rpx solid #D9D9D9;
		border-radius: 6rpx;
		font-size: 26rpx;
		color: #333333;
		background-color: #FFFFFF;
	}
</style>
