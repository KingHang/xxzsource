<template>
	<view :class="Visible?'product-popup open':'product-popup close'" @touchmove.stop.prevent="" @click="closePopup">
		<view class="popup-bg"></view>
		<view class="main" v-on:click.stop>
			<view class="header">
				<image :src="form.show_sku.sku_image" mode="aspectFit" class="avt"></image>
				<view class="">{{ selectSpec }}</view>
				<view class="close-btn" @click="closePopup">
					<text class="icon iconfont icon-guanbi"></text>
				</view>
			</view>
			<view class="body">
				<!--规格-->
				<view>
					<scroll-view scroll-y="true" class="specs mt20" style="max-height: 600rpx;"
						v-if="form.specData !=null">
						<view class="specs mt20" v-for="(item_attr,attr_index) in form.specData.spec_attr"
							:key="attr_index">
							<view class="specs-hd p-20-0">
								<text class="f24 gray9">{{item_attr.group_name}}</text>
								<text class="ml10 red" v-if="form.productSpecArr[attr_index]==null">
									请选择{{item_attr.group_name}}
								</text>
							</view>
							<view class="specs-list">
								<button type="primary" :class="item.checked ? 'btn-red' : 'btn-gray-border'"
									v-for="(item,item_index) in item_attr.spec_items" :key="item_index"
									@click="selectAttr(attr_index, item_index)">{{item.spec_value}}
								</button>
							</view>
						</view>
					</scroll-view>
				</view>
			</view>
			<view class="btns">
				<button type="primary" class="confirm-btn" @click="closePopup()">选好了</button>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				/*是否可见*/
				Visible: false,
				/*表单对象*/
				form: {
					detail: {

					},
					show_sku: {
						sku_image: ''
					}
				},
				/*当前商品总库存*/
				stock: 0,
				/*选择提示*/
				selectSpec: '',
				/*是否打开过多规格选择框*/
				isOpenSpec: false,
				type: ''
			}

		},
		props: ['isPopup', 'productModel'],
		onLoad() {

		},
		mounted() {

		},
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
					this.form = this.productModel;
					this.initShowSku();
					this.form.type = this.productModel.type;
				}
			},
			'form.specData': {
				handler(n, o) {
					let str = '',
						select = '';
					this.isAll = true;
					if (n) {
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
						if (!this.isAll) {
							str = '请选择: ' + str;
						} else {
							select = '已选: ' + select;
						}
					}
					this.selectSpec = this.isAll ? select : str;
				},
				deep: true,
				immediate: true
			}
		},
		methods: {

			/*初始化*/
			initShowSku() {
				this.form.show_sku.sku_image = this.form.detail.image[0].file_path;
				this.form.show_sku.product_price = this.form.detail.product_price;
				this.form.show_sku.product_sku_id = 0;
				this.form.show_sku.line_price = this.form.detail.line_price;
				this.form.show_sku.stock_num = this.form.detail.product_stock;
				this.form.show_sku.sum = 1;
				this.stock = this.form.detail.product_stock;
			},

			/*选择属性*/
			selectAttr(attr_index, item_index) {
				let self = this;
				let items = self.form.specData.spec_attr[attr_index].spec_items;
				let curItem = items[item_index];
				if (curItem.checked) {
					// curItem.checked = false;
					self.$set(self.form.specData.spec_attr[attr_index].spec_items[item_index],'checked',false);
					self.form.productSpecArr[attr_index] = null;
				} else {
					for (let i = 0; i < items.length; i++) {
						self.$set(self.form.specData.spec_attr[attr_index].spec_items[i],'checked',false);
					}
					self.$set(self.form.specData.spec_attr[attr_index].spec_items[item_index],'checked',true);
					self.form.productSpecArr[attr_index] = curItem.item_id;
				}

				for (let i = 0; i < self.form.productSpecArr.length; i++) {
					if (self.form.productSpecArr[i] == null) {
						self.initShowSku();
						return;
					}
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
					skuItem = spec_list.find((val) => {
						return val.spec_sku_id == specSkuId;
					});
				// 记录product_sku_id
				// 更新商品价格、划线价、库存
				if (typeof skuItem === 'object') {
					self.stock = skuItem.spec_form.stock_num;
					if (self.form.show_sku.sum > self.stock) {
						self.form.show_sku.sum = self.stock > 0 ? self.stock : 1;
					}
					self.form.show_sku.product_sku_id = specSkuId;
					self.form.show_sku.product_price = skuItem.spec_form.product_price;
					self.form.show_sku.line_price = skuItem.spec_form.line_price;
					self.form.show_sku.stock_num = skuItem.spec_form.stock_num;
					if (skuItem.spec_form.image_id > 0) {
						self.form.show_sku.sku_image = skuItem.spec_form.image_path;
					} else {
						self.form.show_sku.sku_image = self.form.detail.image[0].file_path;
					}
				}
			},

			/*关闭弹窗*/
			closePopup() {
				if (this.form.specData != null) {
					for (let i = 0; i < this.form.productSpecArr.length; i++) {
						if (this.form.productSpecArr[i] == null) {
							uni.showToast({
								title: '请选择规格',
								icon: 'none',
								duration: 2000
							});
							return;
						}
					}
				}
				let product_num = this.form.show_sku.sum;
				let params = {
					product_id: this.form.detail.product_id,
					product_sku_id: this.form.show_sku.product_sku_id
				}
				this.$emit('close', this.form.specData, params);
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
	}
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
		min-height: 200rpx;
		background-color: #fff;
		transform: translate3d(0, 980rpx, 0);
		transition: transform .2s cubic-bezier(0, 0, .25, 1);
		bottom: env(safe-area-inset-bottom);
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
		height: 120rpx;
		padding: 10rpx 0 10rpx 250rpx;
		position: relative;
		border-bottom: 1px solid #EEEEEE;
	}

	.product-popup .header .avt {
		position: absolute;
		top: -80rpx;
		left: 30rpx;
		width: 200rpx;
		height: 200rpx;
		border: 2px solid #FFFFFF;
		background: #FFFFFF;
	}

	.product-popup .header .stock {
		font-size: 24rpx;
		color: #999999;
	}

	.product-popup .close-btn {
		position: absolute;
		width: 40rpx;
		height: 40rpx;
		top: 10rpx;
		right: 10rpx;
	}

	.product-popup .price {
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
		padding: 20rpx 30rpx 100rpx 30rpx;
		max-height: 600rpx;
		overflow-y: auto;
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
		width: 60rpx;
		height: 60rpx;
		border: 1px solid #DDDDDD;
		background: #f7f7f7;
	}

	.product-popup .num-wrap .iconfont {
		color: #666;
	}

	.product-popup .num-wrap.no-stock .iconfont {
		color: #CCCCCC;
	}

	.product-popup .level-box .text-wrap {
		margin: 0 4rpx;
		height: 60rpx;
		border: 1px solid #DDDDDD;
		background: #f7f7f7;
	}

	.product-popup .level-box .text-wrap input {
		padding: 0 10rpx;
		height: 60rpx;
		line-height: 60rpx;
		width: 80rpx;
		text-align: center;
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
		height: 88rpx;
		line-height: 88rpx;
		border: 0;
		border-radius: 0;
	}

	.product-popup .btns .confirm-btn {
		margin: 0 auto;
		width: 500rpx;
		height: 70rpx;
		line-height: 70rpx;
		border-radius: 35rpx;
		background: $dominant-color;
	}
</style>
