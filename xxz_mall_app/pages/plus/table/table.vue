<template>
	<view>
		<view v-for="(item,index) in tableData" :key='index'>
			<template v-if="item.type == 'radio'">
				<!-- 单选框 -->
				<view class="table-item">
					<view class="make-top">
						<view class="fb color-28 f26"><text
								:class="item.is_required!='false'?'red':'white'">*</text>{{item.name}}</view>
					</view>
					<view class="uni-list">
						<radio-group @change="changeRadio($event,index)">
							<label class="d-s-c make-item mb20" v-for="(ritem,rindex) in getName(item.select_value)"
								:key='rindex'>
								<view>
									<radio style="transform:scale(0.7)" color="#EE1413" :checked="item.value == ritem"
										:value="ritem" />
								</view>
								<view class="f26 color-57">{{ritem}}</view>
							</label>
						</radio-group>
					</view>
				</view>
			</template>
			<template v-if="item.type == 'radio-group'">
				<!-- 多选框 -->
				<view class="table-item">
					<view class="d-s-c make-top">
						<view class="fb color-28 f26"><text
								:class="item.is_required!='false'?'red':'white'">*</text>{{item.name}}</view>
					</view>
					<view class="uni-list">
						<label class="d-s-c make-item mb20" v-for="(ritem,rindex) in getName(item.select_value)"
							:key='ritem'>
							<view>
								<radio @click="changeRadioGroup(ritem,index)" style="transform:scale(0.7)"
									color="#EE1413" :checked="getRadioGroup(index,ritem) != -1 " :value="ritem" />
							</view>
							<view class="f26 color-57">{{ritem}}</view>
						</label>
					</view>
				</view>
			</template>
			<template v-if="item.type == 'text'">
				<!-- 单行文本 -->
				<view class="table-item">
					<view class="d-s-c make-top">
						<view class="fb color-28 f26"><text
								:class="item.is_required!='false'?'red':'white'">*</text>{{item.name}}</view>
					</view>
					<view class="pr">
						<input class="make-item selectpicker input-box" v-model="item.value" type="text"
							placeholder="请输入" />
					</view>
				</view>
			</template>
			<template v-if="item.type == 'textarea'">
				<!-- 多行文本 -->
				<view class="table-item">
					<view class="d-s-c make-top">
						<view class="fb color-28 f26"><text
								:class="item.is_required!='false'?'red':'white'">*</text>{{item.name}}</view>
					</view>
					<view class="pr">
						<textarea class="textarea-box" v-model="item.value" placeholder="请输入" />
					</view>
				</view>
			</template>
			<template v-if="item.type == 'select'">
				<!-- 下拉框 -->
				<view class="table-item">
					<view class="d-s-c make-top">
						<view class="fb color-28 f26"><text
								:class="item.is_required!='false'?'red':'white'">*</text>{{item.name}}</view>
					</view>
					<view class="uni-list">
						<picker class="" value="" @change="changeSelect($event,index)"
							:range="getName(item.select_value)">
							<view class="selectpicker">
								<view class="make-item input-box flex-1">{{item.value || '请选择'}}</view>
								<view class="icon iconfont icon-jiantou"></view>
							</view>
						</picker>
					</view>
				</view>
			</template>
			<template v-if="item.type == 'time'">
				<!-- 时间选择 -->
				<view class="table-item">
					<view class="d-s-c make-top">
						<view class="fb color-28 f26"><text
								:class="item.is_required!='false'?'red':'white'">*</text>{{item.name}}</view>
					</view>
					<view class="uni-list">
						<picker class="datapicker" mode="date" value="" @change="changeDate($event,index)">
							<view class="make-item selectpicker input-box">{{item.value || '请选择'}}</view>
						</picker>
					</view>
				</view>
			</template>
			<template v-if="item.type == 'area'">
				<!-- 地区选择 -->
				<view class="table-item">
					<view class="d-s-c make-top">
						<view class="fb color-28 f26"><text
								:class="item.is_required!='false'?'red':'white'">*</text>{{item.name}}</view>
					</view>
					<view class="uni-list make-item">
						<view class="input-box flex-1 selectpicker mb20">
							<input class="" type="text" placeholder-class="grary9" placeholder="请选择地区"
								v-model="item.value " disabled="true" @click="changeArea(index)" />
						</view>
						<view class="pr selectpicker">
							<input class="make-item input-box" v-model="item.address" type="text"
								placeholder="请输入详情地址" />
						</view>
					</view>
				</view>
			</template>
			<template v-if="item.type == 'image'">
				<!-- 图片上传 -->
				<view class="table-item">
					<view class="d-s-c make-top">
						<view class="fb color-28 f26"><text
								:class="item.is_required!='false'?'red':'white'">*</text>{{item.name}}</view>
					</view>
					<view class="uni-list">
						<button type="default" class="btn-red mb20" @click="openUpload(index)">上传图片</button>
						<view v-if="item.value!=''">
							<image style="width: 300rpx;" :src="item.value" mode="widthFix"></image>
						</view>
					</view>

				</view>
			</template>
		</view>
		<view class="ww100">
			<view class="submit" @click="submit">提交</view>
		</view>

		<mpvue-city-picker v-if="is_load" ref="mpvueCityPicker" :province="province" :city="city" :area="area"
			:pickerValueDefault="cityPickerValueDefault" @onConfirm="onConfirm"></mpvue-city-picker>
		<Upload v-if="isupload" @getImgs="getImgsFunc">上传图片</Upload>
	</view>
</template>

<script>
	import Upload from '@/components/upload/upload';
	import utils from '@/common/utils.js';
	import mpvueCityPicker from '@/components/mpvue-citypicker/mpvueCityPicker.vue';
	export default {
		components: {
			mpvueCityPicker,
			Upload
		},
		data() {
			return {
				table_id: 0,
				name: '',
				tableData: [],
				/* 地区选择数据 */
				province: [],
				city: [],
				area: [],
				is_load: false,
				cityPickerValueDefault: [0, 0, 0],
				areaIndex: 0,
				/* 上传图片 */
				isupload: false,
				uploadIndex: 0,
			}
		},
		mounted() {
			this.getData();
		},
		onLoad(e) {
			this.table_id = e.table_id
		},
		methods: {
			getData() {
				let self = this;
				this._get('plus.table.table/add', {
					table_id: self.table_id
				}, function(res) {
					self.tableData = res.data.model.tableData;
					self.tableData.forEach((item, index) => {
						self.$set(self.tableData[index], 'value', '')
					})
					/* 地区数据 */
					self.province = res.data.regionData[0];
					self.city = res.data.regionData[1];
					self.area = res.data.regionData[2];
					self.is_load = true;
					/* 修改标题 */
					self.name = res.data.model.name;
					uni.setNavigationBarTitle({
						title: self.name
					});
				})
			},
			getName(str) {
				return str.split(',')
			},
			getRadioGroup(index, e) {
				let arr = this.tableData[index].value.split(',');
				return arr.indexOf(e)
			},
			/* 单选框 */
			changeRadio(e, index) {
				this.tableData[index].value = e.detail.value;
			},
			/* 多选框 */
			changeRadioGroup(e, index) {
				let arr = [];
				if (this.tableData[index].value != '') {
					arr = this.tableData[index].value.split(',');
				}
				let n = arr.indexOf(e);
				if (n != -1) {
					arr.splice(n, 1);
					this.$set(this.tableData[index], 'value', arr.join(','))
				} else {
					arr.push(e);
					this.$set(this.tableData[index], 'value', arr.join(','))
				}
			},
			/* 下拉框 */
			changeSelect(e, index) {
				this.tableData[index].value = this.getName(this.tableData[index].select_value)[e.detail.value];
			},
			/* 日期选择 */
			changeDate(e, index) {
				this.tableData[index].value = e.detail.value;
			},
			/* 地区选择 */
			changeArea(index) {
				this.areaIndex = index;
				this.$refs.mpvueCityPicker.show();
				this.tableData[index].value = e.detail.value;
			},
			/*确定选择的省市区*/
			onConfirm(e) {
				this.tableData[this.areaIndex].value = e.label;
				this.cityPickerValueDefault = [0, 0, 0];
				this.areaIndex = 0;
			},
			/*上传*/
			openUpload(i) {
				this.uploadIndex = i;
				this.isupload = true;
			},
			/*获取图片*/
			getImgsFunc(e) {
				if (e != null && e.length > 0) {
					this.tableData[this.uploadIndex].value = e[0].file_path;
				}
				this.uploadIndex = 0;
				this.isupload = false;
			},
			submit() {
				let self = this;
				let flag = true;
				let text = ''
				this.tableData.forEach((item, index) => {
					if (item.is_required != 'false') {
						switch (item.rule) {
							case 'mobile':
								if (!utils.isPoneAvailable(item.value)) {
									text = '请输入正确的手机号';
									flag = false;
								}
								break;
							case 'idcard':
								if (!utils.isVail(item.value)) {
									text = '请输入正确的身份证号码';
									flag = false;
								}
								break;
							case 'phone':
								if (!utils.isTelAvailable(item.value)) {
									text = '请输入正确的座机或手机号';
									flag = false;
								}
								break;
							case 'email':
								if (!utils.isMail(item.value)) {
									text = '请输入正确的电子邮箱';
									flag = false;
								}
								break;
							case 'number':
								if (!utils.isNum(item.value)) {
									text = '请输入正确的数字';
									flag = false;
								}
								break;
							case 'no':
								if (item.value == '') {
									text = '必填项不能为空';
									flag = false;
								}
								break;
						}
					}

				})
				if (!flag) {
					self.showError(text);
					return false
				}
				let params = [...self.tableData];
				params.forEach((item, index) => {
					if (item.type == 'area') {
						item.value = item.value + item.address
					}
				})
				params = JSON.stringify(params);
				self._post('plus.table.table/add', {
					tableData: params,
					table_id: self.table_id,
				}, function(res) {
					self.showSuccess('提交成功', function() {
						// #ifndef H5
						uni.navigateBack({
							delta: parseInt(1)
						});
						// #endif
						// #ifdef H5
						history.go(-1);
						// #endif
					})
				})
			}
		}
	}
</script>

<style>
	.table-item {
		margin-bottom: 16rpx;
		background-color: #FFFFFF;
		border-radius: 8rpx;
		padding: 26rpx;
		box-sizing: border-box;
	}

	.make-top {
		font-size: 26rpx;
		color: #333333;
		margin-bottom: 33rpx;
		font-weight: 600;
	}

	.uni-list {
		flex: 1;
	}

	.radio_group {
		width: 44rpx;
		height: 44rpx;
		box-sizing: border-box;
		border-radius: 50%;
		background-color: #ffffff;
		border: 2rpx solid #E2231A;
	}

	.radio_group.active {
		background-color: #ffffff;
		border: 11rpx solid #E2231A;
	}

	.selectpicker {
		width: 693rpx;
		height: 65rpx;
		line-height: 65rpx;
		padding: 0 23rpx;
		box-sizing: border-box;
		border: 2rpx solid #BFBFBF;
		border-radius: 5rpx;
		font-size: 26rpx;
		font-weight: 500;
		color: #575757;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.selectpicker .jiantou {
		color: #575757;
		font-size: 22rpx;
	}

	.textarea-box {
		width: 695rpx;
		border: 2rpx solid #BFBFBF;
		border-radius: 5rpx;
		font-size: 26rpx;
		font-weight: 500;
		color: #575757;
		padding: 26rpx;
		box-sizing: border-box;
	}

	.table-item-box {
		width: 695rpx;
		border: 2rpx solid #BFBFBF;
		border-radius: 5rpx;
		font-size: 26rpx;
		font-weight: 500;
		color: #575757;
		padding: 0 15rpx;
		box-sizing: border-box;
	}

	.btn-red {
		width: 160rpx;
		height: 60rpx;
		border-radius: 12rpx;
		padding: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		font-size: 26rpx;

	}

	.submit {
		width: 699rpx;
		height: 93rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		background: #EE1413;
		box-shadow: 7rpx 12rpx 13rpx 0rpx #EE141309;
		border-radius: 10rpx;
		font-size: 32rpx;
		font-family: PingFang;
		font-weight: 800;
		color: #FFFFFF;
		margin: 70rpx auto;
	}
</style>
