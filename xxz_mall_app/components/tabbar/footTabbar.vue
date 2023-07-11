<template>
	<view class="">
		<view class="foot-tavbar-container d-a-c" :style="'background:'+detail.backgroundColor+';' " v-if="detail.list!=''">
			<view v-if="platform != 'wx' || (item.is_show && platform == 'wx')" class="item d-c-c" :class="{'active':item.text===activeTabber}" v-for="(item, index) in detail.list" :key="index" @tap="tabBarFunc(item)">
				<view class="inner d-c-c d-c">
					<image :src="item.text===activeTabber?item.selectedIconPath:item.iconPath" mode="aspectFill" v-if="item.type!=2"></image>
					<text :style="item.text==activeTabber?'color:'+detail.textHoverColor+';':'color:'+detail.textColor+';'" class="text-name" v-if="item.type!=1">{{ item.text}}</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			/*当前选中*/
			activeTabber:'首页',
			/*打开直播菜单*/
			open_liveMenu:false,
			/*底部菜单*/
			detail:{},
			/*是否微信小程序*/
			platform: 'wx'
		};
	},
	watch:{
		'footTabberData':{
			handler(n, o) {
				if(n!=o){
					this.activeTabber=n.active;
				}
	　　　　	},
	　　　　	deep: true,
			immediate:true
		}
	},
	created() {
		let pages = getCurrentPages();
		if (pages.length) {
			let currentPage = pages[pages.length - 1];
			if(currentPage.route == 'pages/index/index'){
				this.activeTabber = '首页';
			}
		}
		this.platform = this.getPlatform();
		this.detail=uni.getStorageSync('TabBar');
		this.getData();
	},
	methods:{
		/*点击菜单*/
		tabBarFunc(e){
			this.footTabberData.active=e.text;
			this.gotoPage(e.link.wap_url);
		},
		gopage(url){
			this.gotoPage(url)
		},
		/*获取首页分类*/
		getData() {
			let self = this;
			self._get('index/nav', {}, function(res) {
				self.detail=res.data.nav.data;
				//uni.setStorageSync('TabBar', res.data.data);
			});
		},
		
	}
};
</script>

<style lang="scss">
.foot-tavbar-container {
	box-shadow: 0 0 6rpx 0 rgba(0, 0, 0, 0.3);
	position: fixed;
	right: 0;
	bottom: 0;
	left: 0;
	height: 98rpx;
	background: #2c2c2c;
	z-index: 90;
}
.foot-tavbar-container .item{
	flex: 1;
	height: 98rpx;
}
.foot-tavbar-container .item.add-btn .inner{
	margin-bottom: 70rpx;
	width: 100rpx;
	height: 100rpx;
	border-radius: 50%;
	background: $dominant-color;
	box-shadow: 0 0 10rpx 0 rgba(232,38,100,.6);
}
.foot-tavbar-container image{
	width: 50rpx;
	height: 50rpx;
}
.foot-tavbar-container .text-name{
	font-size: 24rpx;
	color: #666666;
}
.foot-tavbar-container .item.active .text-name{
	color: #f8c341;
}
</style>
