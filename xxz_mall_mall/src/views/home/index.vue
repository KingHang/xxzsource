<template>
  <div class="user">
    <!--添加页面-->
    <div class="common-level-rail">
      <el-button v-auth="'/home/add'" size="small" type="primary" icon="el-icon-plus" @click="addClick()">添加页面</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" size="small" :data="tableData" border style="width: 100%">
          <el-table-column prop="page_id" label="页面ID" width="80" />

          <el-table-column prop="page_name" label="页面名称">
            <template slot-scope="scope">
              <span>{{ scope.row.page_name }}</span>
              <!-- <span class="gray">（首页）</span> -->
            </template>
          </el-table-column>

          <el-table-column prop="page_type" label="页面类型">
            <template slot-scope="scope">
              <span v-if="scope.row.page_type === 10">商城首页</span>
              <span v-if="scope.row.page_type === 20">自定义页</span>
            </template>
          </el-table-column>

          <el-table-column prop="create_time" label="添加时间" />

          <el-table-column prop="update_time" label="更新时间" />

          <el-table-column fixed="right" label="操作" width="90">
            <template slot-scope="scope">
              <el-button v-auth="'/home/edit'" type="text" size="small" @click="editClick(scope.row.page_id)">编辑</el-button>
              <el-button v-if="scope.row.page_type === 20" v-auth="'/home/delete'" type="text" size="small" @click="deleteClick(scope.row.page_id)">删除</el-button>
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
import HomeApi from '@/api/home.js'

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
      curPage: 1
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
      HomeApi.PageList(Params, true).then(data => {
        self.loading = false
        self.tableData = data.data.list.data
        self.totalDataNumber = data.data.list.total
      }).catch(() => {
      })
    },
    /** 删除数据 **/
    deleteClick(row) {
      const self = this
      self.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        self.loading = true
        HomeApi.deletePage({
          page_id: row
        }, true).then(data => {
          if (data.code === 1) {
            self.loading = false
            self.$message({
              message: '恭喜你，删除成功',
              type: 'success'
            })
            self.getTableList()
          } else {
            self.loading = false
          }
        }).catch(() => {
          self.loading = false
        })
      }).catch(() => {})
    },
    /** 添加页面 **/
    addClick() {
      this.$router.push('/home/add')
    },
    /** 设为首页 **/
    setHomeClick(page_id) {
      const self = this
      self.$confirm('确定要将此页面设置为默认首页吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        self.loading = true
        HomeApi.setHome({
          page_id: page_id
        }, true).then(data => {
          self.loading = false
          if (data.code === 1) {
            self.$message({
              message: '恭喜你，设置成功',
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
    },
    /** 编辑 **/
    editClick(page_id) {
      const self = this
      self.$router.push({
        path: '/home/edit',
        query: {
          page_id: page_id
        }
      })
    }
  }
}
</script>
