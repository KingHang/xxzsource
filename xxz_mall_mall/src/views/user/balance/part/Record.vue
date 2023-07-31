<template>
  <div class="user">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="formInline" class="demo-form-inline">
        <el-form-item label="昵称">
          <el-input v-model="formInline.search" placeholder="请输入昵称" />
        </el-form-item>
        <el-form-item label="注册时间">
          <div class="block">
            <span class="demonstration" />
            <el-date-picker
              v-model="formInline.value1"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
            />
          </div>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSubmit">查询</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" size="small" :data="tableData" border style="width: 100%">
          <el-table-column prop="order_no" label="订单号" />

          <el-table-column prop="user.nickName" label="微信昵称" />

          <el-table-column prop="nickName" label="微信头像" width="70">
            <template slot-scope="scope">
              <img :src="scope.row.user ? scope.row.user.avatarUrl : ''" width="30" height="30" alt="">
            </template>
          </el-table-column>

          <el-table-column prop="snapshot.plan_name" label="充值套餐" />

          <el-table-column prop="pay_price" label="支付金额" />

          <el-table-column prop="real_money" label="到账数量" />

          <el-table-column prop="pay_type.text" label="支付方式" />

          <el-table-column label="付款状态">
            <template slot-scope="scope">
              <span v-if="scope.row.pay_status.value === 10">{{ scope.row.pay_status.text }}</span>
              <span v-if="scope.row.pay_status.value === 20" class="green">{{ scope.row.pay_status.text }}</span>
            </template>
          </el-table-column>

          <el-table-column prop="create_time" label="充值时间" />
        </el-table>
      </div>

      <!--分页-->
      <div class="pagination">
        <el-pagination
          background
          :current-page="curPage"
          :page-size="pageSize"
          layout="total, prev, pager, next, jumper"
          :total="totalDataNumber"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </div>
  </div>
</template>

<script>
import PlanApi from '@/api/balance.js'

export default {
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      /* 列表数据 */
      tableData: [],
      /* 一页多少条 */
      pageSize: 10,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      /* 当前是第几页 */
      curPage: 1,
      /* 横向表单数据模型 */
      formInline: {
        search: '',
        value1: ''
      }
    }
  },
  created() {
    /* 获取列表 */
    this.getTableList()
  },
  methods: {
    /** 选择第几页 **/
    handleCurrentChange(val) {
      const self = this
      self.curPage = val
      self.loading = true
      self.getTableList()
    },
    /** 每页多少条 **/
    handleSizeChange(val) {
      this.curPage = 1
      this.pageSize = val
      this.getTableList()
    },
    /** 获取列表 **/
    getTableList() {
      const self = this
      const Params = self.formInline
      Params.page = self.curPage
      Params.list_rows = self.pageSize
      PlanApi.log(Params, true).then(data => {
        self.loading = false
        self.tableData = data.data.list.data
        self.totalDataNumber = data.data.list.total
      }).catch(() => {
      })
    },
    /** 搜索查询 **/
    onSubmit() {
      const self = this
      self.loading = true
      self.getTableList()
    }
  }
}
</script>
