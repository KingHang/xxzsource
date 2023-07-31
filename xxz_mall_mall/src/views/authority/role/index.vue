<template>
  <div class="user">
    <div class="common-level-rail">
      <el-button v-auth="'/authority/role/add'" size="small" type="primary" icon="el-icon-plus" @click="addClick">添加角色</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" size="small" :data="tableData" border style="width: 100%">
          <el-table-column prop="role_id" label="角色ID" />

          <el-table-column prop="role_name_h1" label="角色名称" />

          <el-table-column prop="sort" label="排序" />

          <el-table-column prop="create_time" label="添加时间" />

          <el-table-column fixed="right" label="操作" width="90">
            <template slot-scope="scope">
              <el-button v-auth="'/authority/role/edit'" type="text" size="small" @click="editClick(scope.row)">编辑</el-button>
              <el-button v-auth="'/authority/role/delete'" type="text" size="small" @click="deleteClick(scope.row)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!--分页-->
      <!-- <div class="pagination">
        <el-pagination
          background
          :current-page="curPage"
          :page-size="pageSize"
          layout="total, prev, pager, next, jumper"
          :total="totalDataNumber"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div> -->
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
      // pageSize: 20,
      /* 一共多少条数据 */
      // totalDataNumber: 0,
      /* 当前是第几页 */
      // curPage: 1,
      /* 横向表单数据模型 */
      formInline: {
        user: '',
        region: ''
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
    /** 选择第几页 **/
    // handleCurrentChange(val) {
    //   let self = this
    //   self.curPage = val
    //   self.loading = true
    //   self.getTableList()
    // },
    /** 每页多少条 **/
    // handleSizeChange(val) {
    //   this.curPage = 1
    //   this.pageSize = val
    //   this.getTableList()
    // },
    /** 获取列表 **/
    getTableList() {
      const self = this
      const Params = {}
      // Params.page = self.curPage
      // Params.list_rows = self.pageSize
      AuthorityApi.roleList(Params, true)
        .then(data => {
          self.loading = false
          self.tableData = data.data.list
          // self.totalDataNumber = data.data.list.length
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 打开添加 **/
    addClick() {
      this.$router.push('/authority/role/add')
    },
    /** 打开编辑 **/
    editClick(row) {
      this.$router.push({
        path: '/authority/role/edit',
        // name: 'mallList',
        query: {
          role_id: row.role_id
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
        AuthorityApi.roleDelete(
          {
            role_id: row.role_id
          },
          true
        )
          .then(data => {
            self.loading = false
            if (data.code === 1) {
              self.$message({
                message: '恭喜你，该角色删除成功',
                type: 'success'
              })
              self.getTableList()
            } else {
              self.loading = false
            }
          })
          .catch(() => {
            self.loading = false
          })
      }).catch(() => {})
    }
  }
}
</script>
