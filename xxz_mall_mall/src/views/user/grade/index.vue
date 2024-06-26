<template>
  <div class="user">
    <!--添加等级-->
    <div class="common-level-rail">
      <el-button v-auth="'/user/grade/add'" size="small" type="primary" icon="el-icon-plus" @click="addClick">添加等级</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" size="small" :data="tableData" border style="width: 100%">
          <el-table-column prop="name" label="等级名称" width="300" />

          <el-table-column prop="weight" label="权重" />

          <el-table-column prop="equity" label="折扣" width="200">
            <template slot-scope="scope">
              <span class="red fb">{{ scope.row.equity }}%</span>
            </template>
          </el-table-column>

          <el-table-column prop="remark" label="升级条件">
            <template slot-scope="scope">
              <div v-html="keepTextStyle(scope.row.remark)" />
            </template>
          </el-table-column>

          <el-table-column fixed="right" label="操作" width="90">
            <template slot-scope="scope">
              <el-button v-auth="'/user/grade/edit'" type="text" size="small" @click="editClick(scope.row)">编辑</el-button>
              <el-button v-if="scope.row.is_default === 0" v-auth="'/user/grade/delete'" type="text" size="small" @click="deleteClick(scope.row)">删除</el-button>
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

    <!--添加-->
    <Add v-if="open_add" :open_add="open_add" @closeDialog="closeDialogFunc($event, 'add')" />

    <!--编辑-->
    <Edit v-if="open_edit" :open_edit="open_edit" :form="userModel" @closeDialog="closeDialogFunc($event, 'edit')" />
  </div>
</template>

<script>
import UserApi from '@/api/user.js'
import Edit from './Edit.vue'
import Add from './Add.vue'
import { deepClone } from '@/utils/base.js'

export default {
  components: {
    Edit,
    Add
  },
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
    this.getTableList()
  },
  methods: {
    /** 换行 **/
    keepTextStyle(val) {
      return val.replace(/(\\r\\n)/g, '<br/>')
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
      const Params = {}
      Params.page = self.curPage
      Params.list_rows = self.pageSize
      UserApi.gradelist(Params, true)
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
      this.open_add = true
    },
    /** 打开编辑 **/
    editClick(item) {
      this.userModel = deepClone(item)
      this.open_edit = true
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
    },
    /** 删除等级 **/
    deleteClick(row) {
      const self = this
      self.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        self.loading = true
        UserApi.deletegrade({
          grade_id: row.grade_id
        }, true).then(data => {
          self.loading = false
          if (data.code === 1) {
            self.$message({
              message: data.msg,
              type: 'success'
            })
            self.getTableList()
          } else {
            self.$message.error('错了哦，这是一条错误消息')
          }
        }).catch(() => {
          self.loading = false
        })
      }).catch(() => {
      })
    }
  }
}
</script>
