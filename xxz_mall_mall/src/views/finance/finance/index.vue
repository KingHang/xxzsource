<template>
  <div v-if="!loading" class="home">
    <div class="cash cash_t_common cash_t_brief">
      <div class="cash-header">
        <div>
          <span class="cash_t_title">平台概况 </span>
        </div>
      </div>
      <div class="cash-body">
        <div class="content">
          <p class="title">订单销售总金额（元）</p>
          <p class="money">{{ tj_data.total_money }}</p>
        </div>
        <div class="content">
          <p class="title">平台订单抽成（元）</p>
          <p class="money">{{ tj_data.sys_money }}</p>
        </div>
      </div>
    </div>

    <div class="cash cash_t_common cash_t_brief">
      <div class="cash-header">
        <div>
          <span class="cash_t_title">店铺概况</span>
        </div>
      </div>
      <div class="cash-body">
        <div class="content">
          <p class="title">店铺结算总金额(元)</p>
          <p class="money">{{ supplier_data.total_money }}</p>
        </div>
        <div class="content">
          <p class="title">店铺待结算费用(元)</p>
          <p class="money">{{ supplier_data.nosettled_money }}</p>
        </div>
        <div class="content">
          <p class="title">店铺总保证金(元)</p>
          <p class="money">{{ supplier_data.deposit_money }}</p>
        </div>
        <div class="content">
          <p class="title">已提现金额(元)</p>
          <p class="money">{{ supplier_data.cash_money }}</p>
        </div>
        <div class="content">
          <p class="title">提现中金额(元)</p>
          <p class="money">{{ supplier_data.freeze_money }}</p>
        </div>
        <div class="content">
          <p class="title">剩余可提现(元)</p>
          <p class="money">{{ supplier_data.money }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FinanceApi from '@/api/finance.js'

export default {
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      /* 统计数据 */
      tj_data: {},
      supplier_data: {}
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
      self.loading = true
      FinanceApi.index({}, true).then(res => {
        self.tj_data = res.data.tj_data
        self.supplier_data = res.data.supplier_data
        self.loading = false
      }).catch(() => {
        self.loading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  .apply-container {
    padding: 50px 0;
  }

  .cash {
    background-color: #fff;
    font-size: 14px;
  }

  .cash_t_common {
    margin-top: 15px;
    margin-bottom: 0;
    box-shadow: initial;

    .cash_t_title {
      font-size: 16px;
      font-weight: 600;
    }

    .cash-header {
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
  }

  .cash_t_brief .cash-header {
    border-bottom: 0;
    padding-bottom: 0;
  }

  .cash-header {
    position: relative;
    height: 42px;
    line-height: 42px;
    padding: 0 15px;
    border-bottom: 1px solid #f6f6f6;
    color: #333;
    border-radius: 2px 2px 0 0;
    font-size: 14px;
  }

  .cash_t_brief .cash_t_title {
    position: relative;
    padding-left: 10px;
  }

  .cash_t_brief .cash_t_title::before {
    content: '';
    display: inline-block;
    width: 3px;
    height: 14px;
    background-color: #3a8ee6;
    position: absolute;
    left: 0;
    top: 50%;
    border-radius: 5px;
    transform: translateY(-50%);
  }

  .cash_t_common .cash-body {
    padding: 20px;
  }

  .cash-body {
    display: flex;
    justify-content: flex-start;
    flex-wrap: wrap;
    position: relative;
    padding: 10px 15px;
    line-height: 24px;

    .content {
      width: 33.3%;
      display: flex;
      flex-direction: column;
      margin-bottom: 30px;
      justify-content: center;
    }

    .content {
      width: 33.3%;
      display: flex;
      flex-direction: column;
      margin-bottom: 30px;
      justify-content: center;
    }

    .money {
      font-size: 20px;
      color: #666;
      font-weight: bold;
      margin-top: 10px;
      max-width: 250px;
    }

    .subhead {
      font-size: 12px;
      margin-left: 3px;
      cursor: pointer;
    }
  }

  .ns-text-color {
    color: #3a8ee6;
  }

  .date_section {
    display: flex;
    justify-content: flex-end;
    align-items: center;
  }
</style>
