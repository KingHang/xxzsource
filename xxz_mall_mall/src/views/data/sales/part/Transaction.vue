<template>
  <div class="mt30">
    <div class="common-form">交易统计</div>
    <el-tabs v-model="activeName" @tab-click="handleClick">
      <el-tab-pane label="订单数" name="order" />
      <el-tab-pane label="退款数" name="refund" />
    </el-tabs>

    <div class="d-b-c">
      <div style="color:green ;">总数：{{ total_num }} &nbsp;&nbsp;&nbsp;&nbsp;总额：{{ total_money }}</div>
      <div>
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
    </div>

    <div>
      <div class="Echarts">
        <div id="TransactionChart" />
      </div>
    </div>
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
      /* 是否正在加载 */
      loading: true,
      /* 类别 */
      activeName: 'order',
      /* 订单数合计 */
      total_money: 0,
      total_num: 0,
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
      datePicker: [formatDate(startDate, 'yyyy-MM-dd'), formatDate(endDate, 'yyyy-MM-dd')],
      /* 数据对象 */
      dataList: null,
      /* 交易统计图表对象 */
      myChart: null,
      /* 图表数据 */
      option: {
        title: {
          // text: 'ECharts 入门示例'
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '3%',
          containLabel: true
        },
        tooltip: {
          trigger: 'axis'
        },
        yAxis: {}
      }
    }
  },
  created() {
  },
  mounted() {
    this.myEcharts()
  },
  methods: {
    /** 切换 **/
    handleClick(e) {
      this.getData()
    },
    /** 选择时间 **/
    changeDate() {
      this.getData()
    },
    /** 创建图表对象 **/
    myEcharts() {
      // 基于准备好的dom，初始化echarts实例
      this.myChart = this.$echarts.init(document.getElementById('TransactionChart'))
      /* 获取列表 */
      this.getData()
    },
    /** 格式数据 **/
    createOption() {
      if (!this.loading) {
        let names = []
        const xAxis = this.dataList.days
        const series1 = []
        const series2 = []
        this.dataList.data.forEach(item => {
          series1.push(item.total_money)
          series2.push(item.total_num)
          this.total_money = this.total_money + parseFloat(item.total_money)
          this.total_num = this.total_num + Number(item.total_num)
        })
        // total_money: 0,
        // total_num: 0,
        if (this.activeName === 'order') {
          names = ['成交额', '成交量']
        } else if (this.activeName === 'refund') {
          names = ['退单额', '退单量']
        }

        // 指定图表的配置项和数据
        this.option.xAxis = {
          type: 'category',
          boundaryGap: false,
          data: xAxis
        }

        this.option.color = ['red', '#409EFF']

        this.option.legend = {
          data: [{ name: names[0], color: '#ccc' }, { name: names[1] }]
        }

        this.option.series = [
          {
            name: names[0],
            type: 'line',
            data: series1,
            lineStyle: {
              color: 'red'
            }
          },
          {
            name: names[1],
            type: 'line',
            data: series2,
            lineStyle: {
              color: '#409EFF'
            }
          }
        ]

        this.myChart.setOption(this.option)
        this.myChart.resize()
      }
    },
    /** 获取列表 **/
    getData() {
      const self = this
      self.total_money = 0
      self.total_num = 0
      self.loading = true
      DataApi.getOrderByDate(
        {
          search_time: self.datePicker,
          type: self.activeName
        },
        true
      )
        .then(res => {
          self.dataList = res.data
          self.loading = false
          self.createOption()
        })
        .catch(() => {})
    }
  }
}
</script>

<style>
.Echarts {
  box-sizing: border-box;
}
.Echarts > div {
  width: 100%;
  height: 400px;
  box-sizing: border-box;
}
</style>
