<template>
	<view>
		<register v-if="supplierStatus == 0"></register>
		<application v-if="supplierStatus == 1"></application>
	</view>
</template>

<script>
import register from './register.vue';
import application from './application_status.vue';
export default {
	components: {
		register,
		application
	},
	data() {
		return {
			supplierStatus: -1
		};
	},
	onShow() {
		this.getData();
	},
	methods: {
		getData() {
			let self = this;
			uni.showLoading({
				title: '加载中...'
			});
			self._get('user.index/detail',{
				
			}, res => {
				self.supplierStatus = res.data.supplierStatus;
				if (self.supplierStatus == 2) {
					self.gotoPage('pages/user/my_shop/my_shop', 'redirect');
				} else if (self.supplierStatus == 3) {
					uni.hideLoading();
					uni.showModal({
						content: '店铺异常,请联系客服处理'
					});
				} else {
					let title = '';
					if(self.supplierStatus == 0){
						title = '申请入驻';
					}else{
						title = '申请审核中';
					}
					uni.setNavigationBarTitle({
					    title: title
					});
					
					uni.hideLoading();
				}
			});
		}
	}
};
</script>

<style></style>
