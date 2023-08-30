<template>
	<view>
        <view style="background-color: #FF4C44;height: 100rpx;padding: 30rpx 30rpx 100rpx;display: flex;justify-content: space-between;">
            <view style="display: flex;justify-content: space-between;align-items: center;width: 100%">
                <view class="" style="color: white" @click="timeshow=true">
                    {{choseyear}}年{{chosemonth}}月
                    <u-icon name="arrow-down" color="white" style="margin-left: 10rpx"></u-icon>
                </view>
                <view style="">
                    <view style="display: flex;justify-content: space-between">
                        <view style="color: rgba(255,255,255,0.7);font-size: 24rpx">
                            总收入：
                        </view>
                        <view style="font-size: 24rpx;color: rgba(255,255,255,0.7);">
                            {{ growth_value }} {{ growth_name }}
                        </view>
                    </view>
                </view>
            </view>
        </view>
		
        <view class="hlbblock" v-if="tableData.length > 0" style="padding: 20rpx;margin-top: -70rpx">
            <view class="fsc" style="padding: 20rpx 0;border-bottom: 1px #F2F2F2 solid" v-for="(item, index) in tableData">
                <view>
                    <view style="display: flex;align-items: center">
                        <view style="color: white;font-size: 20rpx;background-color: #F63E36;padding: 5rpx 10rpx;border-radius: 8rpx">
                            {{item.type.text}}
                        </view>
                        <view style="font-weight: bold;margin-left: 15rpx">
                            {{item.describe}}
                        </view>
                    </view>
                    <view style="color: #C9C9C9;font-size: 24rpx">
                        {{item.create_time}}
                    </view>
                </view>
                <view>
                    <text v-if="item.value>0" style="color: #F63E36;font-size: 32rpx">
                        +{{item.value}}
                    </text>
                    <text v-else style="color: #999999;font-size: 32rpx">
                        {{item.value}}
                    </text>
                </view>
            </view>
        </view>
		
        <view class="d-c-c p30" v-if="tableData.length == 0 && !loading">
            <text class="iconfont icon-wushuju"></text>
            <text class="cont">亲，暂无相关记录哦</text>
        </view>
		
        <u-picker mode="time" v-model="timeshow"  :params="params"  @confirm="timeconfirm"></u-picker>
	</view>
</template>

<script>
    import uniLoadMore from "@/components/uni-load-more.vue";
	export default {
        components: {
            uniLoadMore
        },
		data() {
			return {
				/*是否加载完成*/
				loadding: true,
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				/*顶部刷新*/
				topRefresh: false,
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				/*数据列表*/
				tableData: [],
				/*最后一页码数*/
				last_page: 0,
				/*当前页面*/
				page: 1,
				/*每页条数*/
				list_rows: 20,
				no_more: false,
				loading: true,
				growth_value: 0,
				growth_name: '',
                timeshow: false,
                choseyear: '',
                chosemonth: '',
                params: {
                    year: true,
                    month: true,
                    day: false,
                    hour: false,
                    minute: false,
                    second: false,
                    // 选择时间的时间戳
                    timestamp: true,
                },
			};
		},
        onLoad(e) {
            let self = this;
            this.type = e.type;
            let nowDate = new Date();
            self.choseyear = nowDate.getFullYear();
            self.chosemonth = nowDate.getMonth()+1;
            self.chosemonth= self.chosemonth < 10 ?  '0'  + self.chosemonth: self.chosemonth;
        },
        computed: {
			/*加载中状态*/
            loadingType(){
                if (this.loading) {
                    return 1;
                } else {
                    if (this.tableData.length != 0 && this.no_more) {
                        return 2;
                    } else {
                        return 0;
                    }
                }
            }
        },
		mounted() {
			/*获取数据*/
			this.getData();
		},
		onReachBottom() {
			let self = this;
			if (self.page < self.last_page) {
				self.page++;
				self.getData();
			}
			self.no_more = true;
		},
		methods: {
            timeconfirm(e) {
                let self = this;
                self.choseyear = e.year
                self.chosemonth = e.month
                self.page = 1;
                self.tableData = [];
                self.getData();
            },
			/*获取数据*/
			getData() {
				let self = this;
				let page = self.page;
				let list_rows = self.list_rows;
				self._get('exchangepurch.log/grow', {
					page: page || 1,
					list_rows: list_rows,
                    month:self.choseyear+'-'+self.chosemonth
				}, function(data) {
					self.loading = false;
					self.growth_value = data.data.growth_value;
					self.growth_name = data.data.growth_name;
					self.tableData = self.tableData.concat(data.data.list.data);
					self.last_page = data.data.list.last_page;
					if (data.data.list.last_page <= 1) {
						self.no_more = true;
						return false;
					}
				});
			}
		}
	};
</script>

<style>
	.points-top {
		height: 100rpx;
		padding: 0 30rpx;
		color: #FFFFFF;
		background: #f44f47;
	}
</style>
