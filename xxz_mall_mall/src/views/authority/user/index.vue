<template>
  <div class="user">
    <!--添加管理员-->
    <div class="common-level-rail">
      <el-button v-auth="'/authority/user/add'" size="small" type="primary" icon="el-icon-plus" @click="addClick">添加管理员</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" size="small" :data="tableData" border style="width: 100%">
          <el-table-column prop="mall_user_id" label="管理员ID" />

          <el-table-column prop="user_name" label="用户名" />

          <el-table-column prop="role.role_name" label="所属角色">
            <template slot-scope="scope">
              <div v-if="scope.row.is_super === 0">
                <span v-for="(item, index) in scope.row.userRole" :key="index" class="mr10 green">{{ item.role.role_name }}</span>
              </div>
              <div v-if="scope.row.is_super === 1" class="gray">
                超级管理员
              </div>
            </template>
          </el-table-column>

          <el-table-column prop="create_time" label="添加时间" />

          <el-table-column fixed="right" label="操作" width="90">
            <template slot-scope="scope">
              <el-button v-if="scope.row.is_super < 1" v-auth="'/authority/user/edit'" type="text" size="small" @click="editClick(scope.row)">编辑</el-button>
              <el-button v-if="scope.row.is_super < 1" v-auth="'/authority/user/delete'" type="text" size="small" @click="deleteClick(scope.row)">删除</el-button>
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

    <Add :open="open_add" :role-list="roleList" @close="closeAdd" />

    <Edit :open="open_edit" :role-list="roleList" :mall_user_id="curModel.mall_user_id" @close="closeEdit" />
  </div>
</template>

<script>
import AuthorityApi from '@/api/authority.js'
import Add from './dialog/Add.vue'
import Edit from './dialog/Edit.vue'

export default {
  components: {
    Add,
    Edit
  },
  inject: ['reload'],
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      /* 角色是否加载完成 */
      role_loading: true,
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
      curModel: {},
      /* 角色列表 */
      roleList: []
    }
  },
  created() {
    /* 获取列表 */
    this.getTableList()
    this.getAllrole()
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
      AuthorityApi.userList(Params, true)
        .then(data => {
          self.loading = false
          self.tableData = data.data.list.data
          self.totalDataNumber = data.data.list.total
        })
        .catch(() => {})
    },
    /** 获取角色 **/
    getAllrole() {
      const self = this
      AuthorityApi.userAddInfo()
        .then(data => {
          self.role_loading = false
          self.roleList = data.data.roleList
        })
        .catch(() => {
          self.role_loading = false
        })
    },
    /** 打开添加 **/
    addClick() {
      if (!this.role_loading) {
        this.open_add = true
      }
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
        AuthorityApi.userDelete(
          {
            mall_user_id: row.mall_user_id
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
