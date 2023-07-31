<template>
  <div>
    <div class="common-level-rail">
      <el-button size="small" type="primary" icon="el-icon-plus" @click="addMenu">添加菜单</el-button>
    </div>

    <div class="table-wrap">
      <el-table v-loading="loading" :data="tableData" style="width: 100%">
        <el-table-column prop="menu_id" label="ID" width="100" />

        <el-table-column prop="title" label="菜单名称">
          <template slot-scope="scope">
            <div class="text-ellipsis" :title="scope.row.title">{{ scope.row.title }}</div>
          </template>
        </el-table-column>

        <el-table-column prop="address" label="图标" width="250">
          <template slot-scope="scope">
            <img v-img-url="scope.row.image_url" width="50" height="50">
          </template>
        </el-table-column>

        <el-table-column prop="sort" label="排序" width="100" />

        <el-table-column prop="status" label="是否显示" width="80">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.status"
              :active-value="1"
              :inactive-value="0"
              active-color="#13ce66"
              inactive-color="#cccccc"
              @change="changeStatus(scope.row)"
            />
          </template>
        </el-table-column>

        <el-table-column prop="create_time" label="添加时间" width="140" />

        <el-table-column prop="update_time" label="更新时间" width="140" />

        <el-table-column prop="name" label="操作" width="90">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="editMenu(scope.row)">编辑</el-button>
            <el-button v-if="scope.row.app_id>0" type="text" size="small" @click="deleteMenu(scope.row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

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
import HomeApi from '@/api/home.js'

export default {
  components: {},
  data() {
    return {
      /* 表单数据 */
      tableData: [],
      /* 是否打开添加弹窗 */
      open_add: false,
      /* 是否打开编辑弹窗 */
      open_edit: false,
      /* 当前编辑的对象 */
      userModel: {},
      commentData: [],
      loading: true,
      /* 一页多少条 */
      pageSize: 10,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      /* 当前是第几页 */
      curPage: 1
    }
  },
  created() {
    /* 获取列表 */
    this.getTableList()
  },
  methods: {
    /** 获取列表 **/
    getTableList() {
      const self = this
      const Params = {}
      Params.page = self.curPage
      Params.list_rows = self.pageSize
      HomeApi.menuList(Params, true)
        .then(data => {
          self.loading = false
          self.tableData = data.data.list.data
          self.totalDataNumber = data.data.list.total
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 添加 **/
    addMenu() {
      this.$router.push({
        path: '/home/mymenu/add'
      })
    },
    /** 编辑 **/
    editMenu(row) {
      const params = row.menu_id
      this.$router.push({
        path: '/home/mymenu/edit',
        query: {
          menu_id: params
        }
      })
    },
    /** 修改状态 **/
    changeStatus(item) {
      const self = this
      const loading = self.$loading({
        lock: true,
        text: '正在处理',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)'
      })
      HomeApi.editMenu({
        menu_id: item.menu_id,
        status: item.status
      })
        .then(data => {
          loading.close()
        })
        .catch(data => {
          loading.close()
          self.$message({
            message: '处理失败，请重试',
            type: 'warning'
          })
        })
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
      this.getTableList()
    },
    /** 删除 **/
    deleteMenu(row) {
      const self = this
      self.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        self.loading = true
        HomeApi.deleteMenu(
          {
            menu_id: row.menu_id
          },
          true
        ).then(data => {
          self.$message({
            message: data.msg,
            type: 'success'
          })
          self.loading = false
          self.getTableList()
        }).catch(() => {})
      }).catch(() => {})
    },
    handleClick(tab, event) {
    },
    /** 关闭弹窗 **/
    closeDialogFunc(e, f) {
      if (f === 'add') {
        this.open_add = e.openDialog
        if (e.type === 'success') {
          this.getTableList()
        }
      }
      if (f === 'edit') {
        this.open_edit = e.openDialog
        if (e.type === 'success') {
          this.getTableList()
        }
      }
    }
  }
}
</script>
