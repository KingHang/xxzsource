<template>
	<view class="search-wrap">

		<!-- 搜索框 -->
		<view class="" id="searchBox" style="margin-top: 30rpx;padding: 15rpx">
			<!--<view class="index-search t-c flex-1">
				<span class="icon iconfont icon-sousuo"></span>
				<input type="text" v-model="form.keyWord" class="flex-1 ml10 f30 gray3" value="" placeholder-class="f24 gray6"
				 placeholder="输入你要的商品" confirm-type="search" @confirm="search()"/>
			</view>-->

            <u-search style="width: 100%"  border-color="#F63E36" height="50"  v-model="form.keyWord" @custom="search" @search="search"  :show-action="true" action-text="搜索"  :animation="true" shape="round" search-icon="search" ></u-search>

            <!--<button class="btn ml10" @click="search()" type="default">搜索</button>-->
		</view>

		<view class="p-0-20 ">
			<view class="group-hd">
				<view class="left"><text class="min-name">历史搜索</text></view>
				<view class="right">
					<span class="icon iconfont icon-lajitong" @click='clearStorage'></span>
				</view>
			</view>
			<view class="before-search">
                <block v-for="(item,index) in arr">
                    <u-tag @click="search(arr[index])"     :text="arr[index]"  type="success" mode="dark" shape="circle" bg-color="#E8E8E8" color="#505050" style="margin-right: 20rpx"/>
                </block>

                <!--<text class="item" v-for="(item,index) in arr" :key="index" @click="search(arr[index])">{{arr[index]}}</text>-->
			</view>
		</view>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				form: {
					 
				},
				arr:[],
			}
		},
        mounted() {
			/*获取缓存数据*/
            this.getData();
        },
		methods: {
			/*获取缓存里的搜索历史*/
            getData(){
                let self=this;
				/*获取搜索记录*/
                uni.getStorage({
                    key: 'search_list',
                    success: function (res) {
						if(res!=null&&res.data!=null){
							self.arr = res.data;
						}
                    }
                });

			},
			/*搜索*/
			search(str) {

				let self = this;
				let search=null;
				if(str!=null){
					search=str;
				}else{
					search= this.form.keyWord;

				}

                let arrs=this.arr;
                if(typeof search != "undefined" && search!=null && search!=''){
					if (arrs.indexOf(search) < 0) {
						arrs.push(search);
					}
                }else{
                    uni.showToast({
                        title: '请输入搜索的关键字',
                        icon: 'none',
                        duration: 2000
                    });
                    return false;
                }
                /*设置搜索记录*/
                uni.setStorage({
                    key: 'search_list',
                    data: arrs,
                    success: function () {
                        console.log('success');
                    }
                });





				let category_id = 0;
				let sortType = 'all';
				
				self.gotoPage('/pages/product/list/list?search=' + search + '&category_id=' + category_id + '&sortType=' + sortType);
				
			},
			/*清楚缓存*/
			clearStorage(){
				let self=this;
				uni.removeStorage({
				    key: 'search_list',
				    success: function (res) {
				        self.arr=[];
				    }
				});
			}
		}
	}
</script>

<style>
	.search-wrap .index-search-box .search-box {
			padding: 0 20rpx;
			height: 64rpx;
			line-height: 64rpx;
			background: #f7f7f7;
			border-radius: 50rpx;
			overflow: hidden;
			font-size: 28rpx;
			color: #999;
			box-sizing: border-box;
		}
		.search-wrap .index-search-box button{ height: 78rpx; line-height: 78rpx; border:solid 1rpx #CCCCCC; color:#333333; background: #FFFFFF;}
	
		.before-search {
			display: flex;
			justify-content: flex-start;
			align-items: center;
			flex-flow: wrap;
		}
	
		.before-search .item {
			padding: 16rpx;
			margin-right: 16rpx;
			margin-bottom: 16rpx;
			border-radius: 8rpx;
			color: #686868;
			background: #f0f2f5;
			font-size: 24rpx;
		}
</style>
