<template>
  <div class="mt30">
    <div class="common-form">交易统计</div>
    <el-tabs v-model="activeName" @tab-click="handleClick">
      <el-tab-pane label="访问数" name="visit" />
      <el-tab-pane label="收藏数" name="fav" />
    </el-tabs>

    <div class="d-b-c">
      <div style="color:green ;">
        <span v-if="activeName === 'visit'">访客总数</span>
        <span v-else>店铺收藏总数</span>：{{ visit_count }}
        &nbsp;&nbsp;&nbsp;&nbsp;
        <span v-if="activeName === 'visit'">访问量总数</span>
        <span v-else>商品收藏总数</span>：{{ visit_user }}
      </div>

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
import echarts from 'echarts'

export default {
  data() {
    const endDate = new Date()
    const startDate = new Date()
    startDate.setTime(startDate.getTime() - 3600 * 1000 * 24 * 7)
    return {
      /* 是否正在加载 */
      loading: true,
      /* 类别 */
      activeName: 'visit',
      visit_count: 0,
      visit_user: 0,
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
      this.myChart = echarts.init(document.getElementById('TransactionChart'))
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
        if (this.activeName === 'visit') {
          names = ['访客数', '访问量']
          this.dataList.data.forEach(item => {
            series1.push(item.visit_user)
            series2.push(item.visit_count)
            this.visit_user = this.visit_user + Number(item.visit_user)
            this.visit_count = this.visit_count + Number(item.visit_count)
          })
        } else if (this.activeName === 'fav') {
          names = ['店铺收藏数', '商品收藏数']
          this.dataList.data.forEach(item => {
            series1.push(item.supplier_count)
            series2.push(item.product_count)
            this.visit_user = this.visit_user + Number(item.supplier_count)
            this.visit_count = this.visit_count + Number(item.product_count)
          })
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
      self.loading = true
      this.visit_count = 0
      this.visit_user = 0
      DataApi.getAccessByDate(
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
