<template>
	<view class=" ">

        <view style="background-color: #F6645E;height: 100rpx;padding: 30rpx;display: flex;justify-content: space-between">

        </view>

        <view class="hlbblock30" style="margin-top: -100rpx">
            <view style="margin-bottom: 10rpx;font-weight: bold;font-size: 28rpx">
                当前余额（元）
            </view>
            <view style="display: flex;padding: 20rpx 0 0;border-top: 1px #F2F2F2 solid">
                <view style="width: 45%;border-right: 1px #F2F2F2 solid">
                    <view style="color: #999999;font-size: 24rpx">
                        总余额
                    </view>
                    <view style="color: #F63E36;font-size: 24rpx">
                        {{balance}}
                    </view>
                </view>
                <view style="width: 45%;margin-left: 30rpx">
                    <view style="color: #999999;font-size: 24rpx">
                        赠金
                    </view>
                    <view style="color: #F63E36;font-size: 24rpx">
                        {{bonus}}
                    </view>
                </view>

            </view>

        </view>
        <u-picker mode="time" v-model="timeshow"  :params="params"  @confirm="timeconfirm"></u-picker>


        <view class="hlbblock30">
            <view style="height: 150rpx;display: flex;justify-content: space-between;align-items: center;border-bottom: 1px #F2F2F2 solid;margin-top: -30rpx">
                <view class="" @click="timeshow=true"  >
                    {{choseyear}}年{{chosemonth}}月
                    <u-icon name="arrow-down" style="margin-left: 10rpx"></u-icon>
                </view>
                <view style="width: 40%">
                    <view style="display: flex;justify-content: space-between">
                        <view style="color: #999999;font-size: 24rpx">
                            支出：
                        </view>
                        <view style="font-size: 24rpx;color: #333333">
                            ¥{{totalde}}
                        </view>
                    </view>
                    <view style="display: flex;justify-content: space-between">
                        <view style="color: #999999;font-size: 24rpx">
                            收入：
                        </view>
                        <view style="font-size: 24rpx;color: #333333">
                            ¥{{totalprice}}
                        </view>
                    </view>

                </view>
            </view>
            <view v-for="item in tableData" class="nedd" style="display: flex;width: 100%;justify-content: space-between;margin-bottom: 20rpx;border-bottom: 1px #F2F2F2 solid;">
                <view>
                    <view>
                        {{item.scene.text}}
                    </view>
                    <view class="font2599">
                        {{item.create_time}}
                    </view>
                </view>
                <view v-if="item.money>0" style="color: #F63E36">
                    +{{item.money}}
                </view>
                <view v-else style="color: #999999">
                    {{item.money}}
                </view>
            </view>


        </view>

        <!--列表-->
	<!--	<view class="d-b-c border-b p-30-0" v-for="(item, index) in tableData" :key="index">
			<view class="d-s-s f-w d-c flex-1">
				<text class="30">{{ item.scene.text }}</text>
				<text class="pt10 gray9 f22">{{ item.create_time }}</text>
			</view>
			<view class="red" v-if="item.money > 0">+{{ item.money }}元</view>
			<view class="red" v-else="">{{ item.money }}元</view>
		</view>-->
		<!-- 没有记录 -->
		<view class="d-c-c p30" v-if="tableData.length == 0 && !loading">
			<text class="iconfont icon-wushuju"></text>
			<text class="cont">亲，暂无相关记录哦</text>
		</view>
		<uni-load-more v-else :loadingType="loadingType"></uni-load-more>
	</view>
</template>

<script>
	import uniLoadMore from '@/components/uni-load-more.vue';
	export default {
		components: {
			uniLoadMore
		},
		data() {
			return {
				/*是否加载完成*/
				loading: true,
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
				type: 'all',
                timeshow: false,
                choseyear:'',
                chosemonth:'',
                params : {
                    year: true,
                    month: true,
                    day: false,
                    hour: false,
                    minute: false,
                    second: false,
                    // 选择时间的时间戳
                    timestamp: true,
                },
                totalprice:0,
                totalde:0,
                balance:0,
                bonus:0
			};
		},
		computed: {
			/*加载中状态*/
			loadingType() {
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
		onLoad(e) {

		    let self = this;
			this.type = e.type;


            let nowDate = new Date();
            self.choseyear = nowDate.getFullYear();
            self.chosemonth = nowDate.getMonth()+1;
            self.chosemonth= self.chosemonth < 10 ?  '0'  + self.chosemonth: self.chosemonth;


            /*获取数据*/
			this.getData();


            self._get(
                'balance.log/index', {},
                function(res) {
                    console.log(res)
                    self.balance = res.data.balance;
                    self.bonus = res.data.bonus;

                }
            );


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
                console.log(e)
                self.choseyear = e.year
                self.chosemonth = e.month
                self.page =1;
                self.tableData = [];
                self.getData();
            },


			/*获取数据*/
			getData() {
				let self = this;
				let page = self.page;
				let list_rows = self.list_rows;
				self.loading = true;
				
				console.log(self.choseyear+'-'+self.chosemonth)
				self._get(
					'balance.log/lists', {
						page: page || 1,
						list_rows: list_rows,
						type: self.type,
                        month:self.choseyear+'-'+self.chosemonth
					},
					function(data) {
					    console.log(data)
						self.loading = false;
					    self.totalprice =data.data.totalprice;
					    self.totalde =data.data.totalde;
						self.tableData = self.tableData.concat(data.data.list.data);
						self.last_page = data.data.list.last_page;
						if (data.data.list.last_page <= 1) {
							self.no_more = true;
							return false;
						}
					}
				);
			}
		}
	};
</script>

<style></style>
