<template>
	<view>
		<block v-for="(item,index) in diyItems" :key="index">

			<!--搜索框-->
			<!-- 	<template v-if="item.type==='search'">
				<search :itemData="item"></search>
			</template> -->

			<!--轮播图-->
			<template v-if="item.type==='banner'&&item.data!=null">
				<banner :itemData="item"></banner>
			</template>

			<!--单组图-->
			<template v-if="item.type==='imageSingle'&&item.data!=null">
				<imagesingle :itemData="item"></imagesingle>
			</template>

			<!-- 橱窗-->
			<template v-if="item.type == 'window'&&item.data!=null">
				<windows :itemData="item"></windows>
			</template>

			<!-- 视频组-->
			<template v-if="item.type == 'video'">
				<videos :itemData="item"></videos>
			</template>

			<!--文章-->
			<template v-if="item.type == 'article'&&item.data!=null">
				<articles :itemData="item"></articles>
			</template>

			<!--头条快报-->
			<template v-if="item.type == 'special'&&item.data!=null">
				<special :itemData="item"></special>
			</template>

			<!--公告组-->
			<template v-if="item.type == 'notice'">
				<notice :itemData="item"></notice>
			</template>

			<!--导航组-->
			<template v-if="item.type==='navBar'&&item.data!=null">
				<navBar :itemData="item"></navBar>
			</template>

			<!--商品组-->
			<template v-if="item.type==='product'&&item.data!=null">
				<product :itemData="item"></product>
			</template>

			<!--优惠券-->
			<template v-if="item.type==='coupon'&&item.data!=null">
				<coupon :itemData="item"></coupon>
			</template>

			<!--门店-->
			<template v-if="item.type == 'store'&&item.data!=null">
				<store :itemData="item"></store>
			</template>

			<!--客服-->
			<template v-if="item.type == 'service'">
				<service :itemData="item"></service>
			</template>

			<!--富文本-->
			<template v-if="item.type==='richText'">
				<richText :itemData="item"></richText>
			</template>

			<!--辅助空白-->
			<template v-if="item.type == 'blank'">
				<blank :itemData="item"></blank>
			</template>

			<!--辅助线-->
			<template v-if="item.type == 'guide'">
				<guide :itemData="item"></guide>
			</template>

			<!--秒杀-->
			<template v-if="item.type == 'seckillProduct'&&item.data!=null">
				<seckillProduct :itemData="item"></seckillProduct>
			</template>

			<!--拼团-->
			<template v-if="item.type == 'assembleProduct'&&item.data!=null">
				<assembleProduct :itemData="item"></assembleProduct>
			</template>

			<!--砍价-->
			<template v-if="item.type == 'bargainProduct'&&item.data!=null">
				<bargainProduct :itemData="item"></bargainProduct>
			</template>

			<!--直播-->
			<template v-if="item.type == 'live'&&item.data!=null">
				<live :itemData="item"></live>
			</template>

		</block>
	</view>
</template>

<script>
	import search from './search/search';
	import banner from './banner/banner';
	import imagesingle from './imagesingle/imagesingle';
	import windows from './window/window';
	import videos from './videos/videos';
	import articles from './article/article';
	import special from './special/special';
	import notice from './notice/notice';
	import coupon from './coupon/coupon';
	import richText from './richText/richText';
	import navBar from './navBar/navBar';
	import store from './store/store';
	import service from './service/service';
	import blank from './blank/blank';
	import guide from './guide/guide';
	import product from './product/product';
	import seckillProduct from './seckillProduct/seckillProduct';
	import assembleProduct from './assembleProduct/assembleProduct';
	import bargainProduct from './bargainProduct/bargainProduct';
	import live from './live/live';

	export default {
		components: {
			search,
			banner,
			imagesingle,
			windows,
			videos,
			articles,
			special,
			notice,
			coupon,
			richText,
			navBar,
			store,
			service,
			blank,
			guide,
			product,
			seckillProduct,
			assembleProduct,
			bargainProduct,
			live
		},
		data() {
			return {

			}
		},
		props: ['diyItems'],
		created() {}
	}
</script>

<style>
</style>
