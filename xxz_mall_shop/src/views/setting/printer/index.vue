<template>
  <div class="user">
    <div class="common-form">小票打印机列表</div>

    <!--添加等级-->
    <div class="common-level-rail">
      <el-button v-auth="'/setting/printer/add'" size="small" type="primary" @click="addClick">添加</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" size="small" :data="tableData" border style="width: 100%">
          <el-table-column prop="printer_id" label="打印机ID" />

          <el-table-column prop="printer_name" label="打印机名称" />

          <el-table-column prop="printer_type" label="打印机类型" />

          <el-table-column prop="sort" label="排序" />

          <el-table-column prop="create_time" label="添加时间" />

          <el-table-column fixed="right" label="操作" width="90">
            <template slot-scope="scope">
              <el-button v-auth="'/setting/printer/edit'" type="text" size="small" @click="editClick(scope.row)">编辑</el-button>
              <el-button v-auth="'/setting/printer/delete'" type="text" size="small" @click="deleteClick(scope.row)">删除</el-button>
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
  </div>
</template>

<script>
import SettingApi from '@/api/setting.js'

export default {
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
    this.getData()
  },
  methods: {
    /** 选择第几页 **/
    handleCurrentChange(val) {
      const self = this
      self.loading = true
      self.curPage = val
      self.getData()
    },
    /** 每页多少条 **/
    handleSizeChange(val) {
      this.curPage = 1
      this.pageSize = val
      this.getData()
    },
    /** 获取列表 **/
    getData() {
      const self = this
      const Params = {}
      Params.page = self.curPage
      Params.list_rows = self.pageSize
      SettingApi.printerList(Params, true)
        .then(data => {
          self.loading = false
          self.tableData = data.data.list.data
          self.totalDataNumber = data.data.list.total
        })
        .catch(() => {
        })
    },
    /** 打开添加 **/
    addClick() {
      this.$router.push('/setting/printer/add')
    },
    /** 打开编辑 **/
    editClick(item) {
      this.$router.push({
        path: '/setting/printer/edit',
        query: {
          printer_id: item.printer_id
        }
      })
    },
    /** 删除用户 **/
    deleteClick(row) {
      const self = this
      self.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        self.loading = true
        SettingApi.deletePrinter({
          printer_id: row.printer_id
        }, true).then(data => {
          self.loading = false
          self.$message({
            message: data.msg,
            type: 'success'
          })
          self.getData()
        }).catch(() => {
          self.loading = false
        })
      }).catch(() => {
        self.loading = false
      })
    }
  }
}
</script>
