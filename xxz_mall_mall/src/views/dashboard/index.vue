<template>
  <div v-if="!loading" class="home">
    <div class="operation-wrap-home" style="background-color: #FFFFFF;">
      <el-row>
        <el-col :span="6" class="d-c-c">
          <div class="grid-content user">
            <div class="info">
              <h3>{{ top_data.user_total }}</h3>
              <p>用户总量</p>
            </div>
            <div class="show-icon">
              <svg class="icon" aria-hidden="true">
                <use xlink:href="#icon-gongzuotai_yonghu" />
              </svg>
              <div class="user-bg" />
            </div>
          </div>
        </el-col>
        <el-col :span="6" class="d-c-c">
          <div class="grid-content goods">
            <div class="info">
              <h3>{{ top_data.product_total }}</h3>
              <p>商品总量</p>
            </div>
            <div class="show-icon">
              <svg class="icon" aria-hidden="true">
                <use xlink:href="#icon-gongzuotai_shangpin" />
              </svg>
              <div class="goods-bg" />
            </div>
          </div>
        </el-col>
        <el-col :span="6" class="d-c-c">
          <div class="grid-content order">
            <div class="info ">
              <h3>{{ top_data.order_total }}</h3>
              <p>订单总量</p>
            </div>
            <div class="show-icon">
              <svg class="icon" aria-hidden="true">
                <use xlink:href="#icon-gongzuotai_dingdan" />
              </svg>
              <div class="order-bg" />
            </div>
          </div>
        </el-col>
      </el-row>
    </div>

    <div class="home-index">
      <!--main-index-->
      <div class="flex-1">
        <div class="main-index">
          <div class="common-form mt16" style="font-size: 18px;">
            今日概况
          </div>
          <el-row class="border-b-l">
            <el-col :span="6">
              <div class="grid-content">
                <div class="info t-c">
                  <p class="des">销售额(元)</p>
                  <h3>{{ today_data.order_total_price.tday }}</h3>
                  <p class="yesterday">昨日：{{ today_data.order_total_price.ytd }}</p>
                </div>
              </div>
            </el-col>
            <el-col :span="4">
              <div class="grid-content">
                <div class="info">
                  <p class="des">支付订单数</p>
                  <h3>{{ today_data.order_total.tday }}</h3>
                  <p class="yesterday">昨日：{{ today_data.order_total.ytd }}</p>
                </div>
              </div>
            </el-col>
            <el-col :span="4">
              <div class="grid-content">
                <div class="info">
                  <p class="des">新增用户数</p>
                  <h3>{{ today_data.new_user_total.tday }}</h3>
                  <p class="yesterday">昨日：{{ today_data.new_user_total.ytd }}</p>
                </div>
              </div>
            </el-col>
          </el-row>
          <div>
            <Transaction v-if="!loading" />
          </div>
        </div>
      </div>

      <!--待办事项-->
      <div class="matters-wrap">
        <div class="common-form mt16" style="font-size: 18px;">
          待办事项<span class="ml10 f14 gray" style="font-weight: normal;">请尽快处理，以免影响营业</span>
        </div>
        <el-row class="matters_box">
          <el-col :span="24">
            <div class="matters">
              <div class="box">
                <div class="title">订单</div>
                <ul class="matters_item">
                  <li><span class="fb">{{ wait_data.order.plate }}</span>平台维权数量</li>
                  <li><span class="fb">{{ wait_data.order.disposal }}</span>待处理订单</li>
                  <li><span class="fb">{{ wait_data.order.refund }}</span>待售后订单</li>
                </ul>
              </div>
            </div>
          </el-col>
          <el-col :span="24">
            <div class="matters">
              <div class="box">
                <div class="title">商品待审核</div>
                <ul class="matters_item">
                  <li><span class="fb">{{ wait_data.audit.goods }}</span>商品审核</li>
                  <li><span class="fb">{{ wait_data.audit.comment }}</span>评价审核</li>
                </ul>
              </div>
            </div>
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>

<script>
import IndexApi from '@/api/index.js'
import Transaction from './part/Transaction.vue'

export default {
  components: {
    Transaction
  },
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      /* 统计信息 */
      top_data: [],
      /* 待办事项 */
      wait_data: {
        order: {},
        supplier: {},
        activity: {},
        audit: {}
      },
      /* 今日数据 */
      today_data: {
        order_total_price: {},
        order_total: {},
        new_user_total: {},
        new_supplier_total: {},
        apply_supplier_total: {}
      }
    }
  },
  created() {
    /* 获取数据 */
    this.getData()
  },
  methods: {
    /** 获取数据 **/
    getData() {
      const self = this
      IndexApi.getCount(true).then(data => {
        self.loading = false
        self.top_data = data.data.data.top_data
        self.wait_data = data.data.data.wait_data
        self.today_data = data.data.data.today_data
      }).catch(() => {
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  .operation-wrap-home {
    width: 100%;
    height: 164px;
    border-radius: 8px;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    overflow: hidden;
    background-size: 100% 100%;
  }

  .operation-wrap-home .grid-content {
    width: 90%;
    height: 150px;
    display: -ms-flexbox;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    -ms-flex-direction: row;
    flex-direction: row;
    border-radius: 5px;
    opacity: 1;
    padding-top: 25px;
  }

  .operation-wrap-home .grid-content.store {
    background: #FFFFFF linear-gradient(316deg, #FFF1E5 0%, rgba(255,241,229,0.3) 100%);
  }

  .operation-wrap-home .grid-content.user {
    background: #FFFFFF linear-gradient(316deg, #E6EDFD 0%, rgba(230,237,253,0.3) 100%);
  }

  .operation-wrap-home .grid-content.goods {
    background: #FFFFFF linear-gradient(316deg, #FFE5E7 0%, rgba(255,229,231,0.3) 100%);
  }

  .operation-wrap-home .grid-content.order {
    background: #FFFFFF linear-gradient(316deg, #E7FCF5 0%, rgba(231,252,245,0.3) 100%);
  }

  .operation-wrap-home .grid-content .info {
    min-width: 56px;
    height: 100px;
    margin-left: 25px;
    font-size: 14px;
    color: #828282;
  }

  .operation-wrap-home .grid-content .info h3 {
    font-size: 32px;
    line-height: 32px;
    color: #333333;
    margin-bottom: 50px;
  }

  .operation-wrap-home .grid-content .show-icon {
    text-align: right;
  }

  .operation-wrap-home .grid-content svg {
    width: 26px;
    height: 24px;
    margin-right: 25px;
  }

  .operation-wrap-home .grid-content.store .store-bg {
    width: 106px;
    height: 96px;
    background: url(../../assets/img/home_store.png) no-repeat;
    background-size: 100% 100%;
    border-radius: 0 0 5px 0;
  }

  .operation-wrap-home .grid-content.user .user-bg {
    width: 106px;
    height: 96px;
    background: url(../../assets/img/home_user.png) no-repeat;
    background-size: 100% 100%;
    border-radius: 0 0 5px 0;
  }

  .operation-wrap-home .grid-content.goods .goods-bg {
    width: 106px;
    height: 96px;
    background: url(../../assets/img/home_goods.png) no-repeat;
    background-size: 100% 100%;
    border-radius: 0 0 5px 0;
  }

  .operation-wrap-home .grid-content.order .order-bg {
    width: 106px;
    height: 96px;
    background: url(../../assets/img/home_order.png) no-repeat;
    background-size: 100% 100%;
    border-radius: 0 0 5px 0;
  }

  .home-index {
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: horizontal;
    -webkit-box-direction: normal;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    min-width: 1000px;
    overflow-x: auto;
  }

  .main-index {
    flex: 1;
    margin: 20px;
  }

  .main-index .grid-content {
    display: -ms-flexbox;
    display: flex;
    // -webkit-box-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    justify-content: center;
    align-items: center;
  }

  .main-index .grid-content {
    height: 120px;
  }

  .main-index .grid-content .pic {
    margin-right: 10px;
  }

  .main-index .grid-content h3 {
    font-size: 20px;
    font-weight: normal;
  }

  .main-index .grid-content .yesterday {
    color: #ccc;
  }

  .main-index .grid-content .svg-icon {
    color: #3a8ee6;
  }

  .matters-wrap {
    padding-bottom: 15px;
    width: 40%;
  }

  .matters .box {
    width: 100%;
  }

  .matters-wrap .matters {
    display: -ms-flexbox;
    display: flex;
    // -webkit-box-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    // height: 120px;
    margin-bottom: 30px;
  }

  .matters-wrap .matters .title {
    font-size: 16px;
    color: #333333;
    display: inline-block;
    height: 20px;
    line-height: 0;
    padding: 11px;
    text-align: center;
    margin-bottom: 20px;
  }

  .matters-wrap .matters ul {
    color: #999999;
  }

  .matters-wrap .matters ul span {
    padding-right: 6px;
    color: #3a8ee6;
  }

  .border-b {
    display: flex;
    flex-direction: column;
  }

  .border-b-l {
    flex-direction: initial;
  }

  .matters_item {
    display: flex;
    justify-content: flex-start;
    align-items: center;
  }

  .matters_item li {
    width: 72px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-right: 16px;
  }

  .grid-content .info h3 {
    font-weight: bold;
    color: #5d75e3;
    text-align: center;
  }

  .grid-content .info .des {
    font-size: 16px;
    margin-bottom: 6px;
  }

  .grid-content .info .yesterday {
    font-size: 13px;
    text-align: center;
  }

  .matters_box {
    width: 90%;
    border-top: 1px solid #d9d9d9;
    padding-top: 20px;
  }

  .matters-wrap .matters_item li .fb {
    font-size: 16px;
    color: #5d75e3;
  }
</style>
