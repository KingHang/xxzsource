<template>
  <div class="user">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="formInline" class="demo-form-inline">
        <el-form-item label="会员信息">
          <el-input v-model="formInline.search" placeholder="请输入昵称/手机号" />
        </el-form-item>
        <el-form-item label="注册时间">
          <div class="block">
            <span class="demonstration" />
            <el-date-picker
              v-model="formInline.value1"
              type="daterange"
              value-format="yyyy-MM-dd"
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
          <el-table-column prop="log_id" label="ID" width="60" />

          <el-table-column prop="nickName" label="微信头像" width="70">
            <template slot-scope="scope">
              <img :src="scope.row.user.avatarUrl" width="30" height="30">
            </template>
          </el-table-column>

          <el-table-column prop="" label="昵称">
            <template slot-scope="scope">
              <span>{{ scope.row.user.nickName }}</span>
              <span class="gray9">(用户ID：{{ scope.row.user.user_id }})</span>
            </template>
          </el-table-column>

          <el-table-column prop="value" label="变动数量" />

          <el-table-column prop="describe" label="描述/说明" />

          <el-table-column prop="remark" label="管理员备注">
            <template slot-scope="scope">
              <span v-if="scope.row.remark === ''">--</span>
            </template>
          </el-table-column>

          <el-table-column prop="create_time" label="创建时间" />
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
import PointsApi from '@/api/points.js'

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
      PointsApi.GetUserList(Params, true).then(data => {
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
