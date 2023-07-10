<template>
  <div class="right-box d-s-s d-c">
    <div class="lh30 f16 tl">商品排行榜</div>
    <div class="ww100 mt10">
      <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
        <el-tab-pane label="销量TOP10" name="sale" />
        <el-tab-pane label="浏览TOP10" name="view" />
        <el-tab-pane label="退款TOP10" name="refund" />
      </el-tabs>
    </div>

    <div v-if="activeName === 'sale'">
      <el-date-picker
        v-model="datePicker"
        size="small"
        type="daterange"
        align="right"
        unlink-panels
        format="yyyy-MM-dd"
        value-format="yyyy-MM-dd"
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        :picker-options="pickerOptions"
        @change="changeDate"
      />
    </div>

    <div class="list ww100">
      <ul v-if="listData.length>0">
        <li v-for="(item, index) in listData" :key="index" class="d-s-c p-6-0 border-b-d">
          <span class="key-box">{{ index + 1 }}</span>
          <span>
            <template v-if="activeName === 'sale'">
              <img v-img-url="item.image.file_path" alt="" class="ml10">
            </template>
            <template v-if="activeName === 'refund'">
              <img v-img-url="item.orderproduct.image.file_path" alt="" class="ml10">
            </template>
            <template v-if="activeName === 'view'">
              <img v-img-url="item.image[0].file_path" alt="" class="ml10">
            </template>
          </span>
          <span class="text-ellipsis-2 flex-1 ml10">{{ item.product_name }}</span>
          <span class="gray9 tr" style="width: 80px;">
            <template v-if="activeName === 'sale'">
              销量：{{ item.total_sales_num }}
            </template>
            <template v-if="activeName === 'view'">
              浏览：{{ item.view_times }}
            </template>
            <template v-if="activeName === 'refund'">
              退款：{{ item.refund_count }}
            </template>
          </span>
        </li>
      </ul>

      <div v-else class="tc pt30">暂无上榜记录</div>
    </div>

    <div v-if="activeName === 'sale'" style="color:green ;">总销售量：{{ total_num }} &nbsp;&nbsp;&nbsp;&nbsp;总销售额：{{ total_pay_price }}</div>
  </div>
</template>

<script>
import DataApi from '@/api/data.js'
import { formatDate } from '@/utils/dateTime.js'

export default {
  data() {
    const endDate = new Date()
    const startDate = new Date()
    startDate.setTime(startDate.getTime() - 3600 * 1000 * 24 * 7)
    return {
      activeName: 'sale',
      total_pay_price: 0,
      total_num: 0,
      /* 列表数据 */
      listData: [],
      /* 时间快捷选项 */
      pickerOptions: {
        shortcuts: [
          {
            text: '最近一周',
            onClick(picker) {
              const end = new Date()
              const start = new Date()
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
              picker.$emit('pick', [start, end])
            }
          },
          {
            text: '最近一个月',
            onClick(picker) {
              const end = new Date()
              const start = new Date()
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
              picker.$emit('pick', [start, end])
            }
          },
          {
            text: '最近三个月',
            onClick(picker) {
              const end = new Date()
              const start = new Date()
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
              picker.$emit('pick', [start, end])
            }
          }
        ]
      },
      datePicker: [formatDate(startDate, 'yyyy-MM-dd'), formatDate(endDate, 'yyyy-MM-dd')]
    }
  },
  created() {
  },
  mounted() {
    this.getData()
  },
  methods: {
    handleClick() {
      this.getData()
    },
    getData() {
      const self = this
      self.loading = true
      self.listData = []
      self.total_pay_price = 0
      self.total_num = 0
      DataApi.getSaleRankingByDate(
        {
          search_time: self.datePicker,
          type: self.activeName
        },
        true
      )
        .then(res => {
          self.listData = res.data
          self.loading = false
          if (self.activeName === 'sale') {
            self.createOption()
          }
        })
        .catch(() => {})
    },
    /** 格式数据 **/
    createOption() {
      if (!this.loading) {
        this.listData.forEach(item => {
          this.total_pay_price = this.total_pay_price + parseFloat(item.pay_price)
          this.total_num = this.total_num + Number(item.total_sales_num)
        })
      }
    },
    /** 选择时间 **/
    changeDate() {
      this.getData()
    }
  }
}
</script>

<style scoped="scoped">
.right-box {
  padding: 10px 20px;
  width: 30%;
  box-sizing: border-box;
}
.Echarts > div {
  height: 400px;
}
.right-box .list .key-box {
  width: 20px;
  height: 20px;
  line-height: 20px;
  border-radius: 50%;
  font-weight: bold;
  text-align: center;
  color: #ffffff;
  background: red;
}
.right-box .list img {
  width: 30px;
  height: 30px;
}
</style>
