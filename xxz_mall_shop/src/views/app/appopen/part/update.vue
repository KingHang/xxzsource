<template>
  <div class="user">
    <!--添加管理员-->
    <div class="common-level-rail">
      <el-button v-auth="'/appsetting/appupdate/add'" size="small" type="primary" icon="el-icon-plus" @click="addClick">添加升级</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" size="small" :data="tableData" border style="width: 100%">
          <el-table-column prop="version" label="版本号" />

          <el-table-column prop="wgt_url" label="热更包" />

          <el-table-column prop="pkg_url_android" label="全量包-android" />

          <el-table-column prop="pkg_url_ios" label="全量包-ios" />

          <el-table-column prop="create_time" label="添加时间" />

          <el-table-column fixed="right" label="操作" width="90">
            <template slot-scope="scope">
              <el-button v-auth="'/appsetting/appupdate/edit'" type="text" size="small" @click="editClick(scope.row)">编辑</el-button>
              <el-button v-auth="'/appsetting/appupdate/delete'" type="text" size="small" @click="deleteClick(scope.row)">删除</el-button>
            </template>
          </el-table-column>
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

    <Add :open="open_add" @close="closeAdd" />

    <Edit :open="open_edit" :model="curModel" @close="closeEdit" />
  </div>
</template>

<script>
import AppApi from '@/api/app.js'
import Add from './update/Add.vue'
import Edit from './update/Edit.vue'

export default {
  components: {
    Add,
    Edit
  },
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      /* 列表数据 */
      tableData: [],
      /* 一页多少条 */
      pageSize: 15,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      /* 当前是第几页 */
      curPage: 1,
      /* 是否打开添加弹窗 */
      open_add: false,
      /* 是否打开编辑弹窗 */
      open_edit: false,
      /* 当前编辑的对象 */
      curModel: {}
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
      const Params = {}
      Params.page = self.curPage
      Params.list_rows = self.pageSize
      AppApi.appUpdateList(Params, true)
        .then(data => {
          self.loading = false
          self.tableData = data.data.list.data
          self.totalDataNumber = data.data.list.total
        })
        .catch(() => {})
    },
    /** 打开添加 **/
    addClick() {
      this.open_add = true
    },
    /** 关闭添加 **/
    closeAdd(e) {
      this.open_add = false
      if (e.type === 'success') {
        this.getTableList()
      }
    },
    /** 打开编辑 **/
    editClick(row) {
      this.curModel = row
      this.open_edit = true
    },
    /** 关闭添加 **/
    closeEdit(e) {
      this.open_edit = false
      if (e.type === 'success') {
        this.getTableList()
      }
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
        AppApi.delAppUpdate(
          {
            update_id: row.update_id
          },
          true
        ).then(data => {
          self.loading = false
          if (data.code === 1) {
            self.$message({
              message: '恭喜你，删除成功',
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
