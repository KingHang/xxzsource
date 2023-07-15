<template>
  <div class="user">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item>
          <el-input v-model="searchForm.search" size="small" placeholder="请输入用户名和真实姓名" />
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="el-icon-search" @click="searchSubmit">查询</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" size="small" :data="tableData" border style="width: 100%">
          <el-table-column prop="login_log_id" label="ID" />

          <el-table-column prop="ip" label="IP" />

          <el-table-column prop="result" label="登录状态" />

          <el-table-column prop="username" label="用户名" />

          <el-table-column prop="create_time" label="添加时间" />
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
import AuthorityApi from '@/api/authority.js'

export default {
  inject: ['reload'],
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      /* 列表数据 */
      tableData: [],
      /* 一页多少条 */
      pageSize: 20,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      /* 当前是第几页 */
      curPage: 1,
      /* 横向表单数据模型 */
      searchForm: {
        search: ''
      },
      /* 是否打开添加弹窗 */
      open_add: false,
      /* 是否打开编辑弹窗 */
      open_edit: false,
      /* 当前编辑的对象 */
      userModel: {}
    }
  },
  created() {
    /* 获取列表 */
    this.getTableList()
  },
  methods: {
    /** 搜索 **/
    searchSubmit() {
      this.curPage = 1
      this.getTableList()
    },
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
      const Params = {
        page: self.curPage,
        list_rows: self.pageSize,
        username: self.searchForm.search
      }
      AuthorityApi.loginlog(Params, true)
        .then(data => {
          self.loading = false
          self.tableData = data.data.list.data
          self.totalDataNumber = data.data.list.total
        })
        .catch(() => {})
    },
    /** 打开添加 **/
    addClick() {
      this.$router.push('/authority/user/add')
    },
    /** 打开编辑 **/
    editClick(row) {
      this.$router.push({
        path: '/authority/user/edit',
        query: {
          shop_user_id: row.shop_user_id
        }
      })
    },
    /** 刷新 **/
    refresh() {
      // 直接使用reload方法
      this.reload()
    },
    /** 删除 **/
    deleteClick(row) {
      const self = this
      self.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        self.loading = true
        AuthorityApi.userDelete(
          {
            shop_user_id: row.shop_user_id
          },
          true
        ).then(data => {
          self.loading = false
          if (data.code === 1) {
            self.$message({
              message: '恭喜你，该管理员删除成功',
              type: 'success'
            })
            // 刷新页面
            self.getTableList()
          } else {
            self.loading = false
          }
        }).catch(() => {
          self.loading = false
        })
      }).catch(() => {})
    }
  }
}
</script>
