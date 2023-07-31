<template>
  <div class="marketing-box">
    <el-tabs v-model="activeTab">
      <el-tab-pane label="签到" name="clockin" />
      <el-tab-pane label="积分商城" name="exchangepurch" />
      <el-tab-pane label="秒杀" name="flashsell" />
      <el-tab-pane label="拼团" name="groupsell" />
      <el-tab-pane label="砍价" name="pricedown" />
      <el-tab-pane label="领券中心" name="voucher" />
      <el-tab-pane label="幸运转盘" name="raffle" />
      <el-tab-pane label="万能表单" name="table" />
    </el-tabs>
    <el-select v-model="activePage" placeholder="请选择" class="percent-w100" value-key="id" @change="changeFunc">
      <el-option v-for="(item, index) in pages" :key="index" :label="item.name" :value="item" />
    </el-select>
  </div>
</template>

<script>
import LinkApi from '@/api/link.js'

export default {
  data() {
    return {
      /* tab切换选择中值 */
      activeTab: 'clockin',
      /* 页面数据 */
      pages: [],
      /* 选中的值 */
      activePage: null,
      /* 秒杀数据 */
      flashsellList: [
        {
          id: 0,
          url: 'pages/plus/seckill/list/list',
          name: '秒杀',
          type: '营销'
        }
      ],
      /* 签到数据 */
      clockinList: [
        {
          id: 0,
          url: 'pages/plus/signin/signin',
          name: '签到',
          type: '营销'
        }
      ],
      /* 积分商城数据 */
      exchangepurchList: [
        {
          id: 0,
          url: 'pages/plus/points/list/list',
          name: '积分商城',
          type: '营销'
        }
      ],
      /* 拼团 */
      groupsellList: [
        {
          id: 0,
          url: 'pages/plus/assemble/list/list',
          name: '拼团',
          type: '营销'
        }
      ],
      /* 砍价 */
      pricedownList: [
        {
          id: 0,
          url: 'pages/plus/bargain/list/list',
          name: '砍价',
          type: '营销'
        }
      ],
      /* 领券中心 */
      voucherList: [
        {
          id: 0,
          url: 'pages/coupon/coupon',
          name: '领券中心',
          type: '营销'
        }
      ],
      /* 幸运转盘 */
      raffleList: [
        {
          id: 0,
          url: 'pages/plus/lottery/lottery',
          name: '幸运转盘',
          type: '营销'
        }
      ],
      // 万能表单
      tableList: []
    }
  },
  watch: {
    activeTab: function(n, o) {
      const self = this
      self.pages = []
      if (n !== o) {
        if (n === 'clockin') {
          self.pages = self.clockinList
        } else if (n === 'exchangepurch') {
          self.pages = self.exchangepurchList
        } else if (n === 'flashsell') {
          self.pages = self.flashsellList
        } else if (n === 'groupsell') {
          self.pages = self.groupsellList
        } else if (n === 'pricedown') {
          self.pages = self.pricedownList
        } else if (n === 'voucher') {
          self.pages = self.voucherList
        } else if (n === 'raffle') {
          self.pages = self.raffleList
        } else if (n === 'table') {
          self.pages = self.tableList
        }
        self.autoSend()
      }
    }
  },
  created() {
    this.pages = this.clockinList
    this.getData()
    this.autoSend()
  },
  methods: {
    /** 获取数据 **/
    getData() {
      const self = this
      LinkApi.getList(
        {
          activeName: self.activeTab
        },
        true
      ).then(res => {
        self.tableList = res.data.tableList
      }).catch(() => {})
    },
    /** 自动发送 **/
    autoSend() {
      if (this.pages.length > 0) {
        this.activePage = this.pages[0]
        this.changeFunc()
      }
    },
    /** 选中的值 **/
    changeFunc(e) {
      this.$emit('changeData', this.activePage)
    }
  }
}
</script>
