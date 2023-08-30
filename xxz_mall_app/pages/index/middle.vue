<template>
	<view v-if="!loading">
		<template v-if="status==2">
			<myorder></myorder>
		</template>
		<template v-if="status==1">
			<shoplist></shoplist>
		</template>
	</view>
</template>

<script>
	import myorder from './order/myorder.vue'
	import shoplist from './shop_list/shop_list.vue'
	export default {
		components: {
			myorder,
			shoplist
		},
		data() {
			return {
				status: 1,
				loading: true
			}
		},
		onReady() {
			let tit = ''
			if (this.status == 1) {
				tit = '店铺'
			} else if (this.status == 2) {
				tit = '订单'
			}

			uni.setNavigationBarTitle({
				title: tit
			});
		},
		onShow() {
			let self = this;
			console.log(1)
			self.loading = true;
			uni.showLoading({
				title: ''
			})
			setTimeout(function() {
				self.loading = false;
				uni.hideLoading()
			}, 100);
		},
		onLoad() {
			this.status = uni.getStorageSync('middle');
			console.log(this.status)
		},
		mounted() {

		},
		methods: {

		}
	}
</script>

<style>

</style>
