<template>
  <div class="user">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="formInline" class="demo-form-inline">
        <el-form-item label="用户昵称">
          <el-input v-model="formInline.search" placeholder="请输入昵称" />
        </el-form-item>
        <el-form-item label="余额变动场景">
          <el-select v-model="formInline.scene" placeholder="请选择">
            <el-option label="全部" value="0" />
            <el-option v-for="(item,index) in Scene" :key="index" :label="item.name" :value="item.value" />
          </el-select>
        </el-form-item>
        <el-form-item label="起始日期">
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
          <el-table-column prop="user_id" label="ID" width="80" />

          <el-table-column prop="user.nickName" label="微信头像" width="70">
            <template slot-scope="scope">
              <img :src="scope.row.user.avatarUrl" width="30" height="30">
            </template>
          </el-table-column>

          <el-table-column prop="user.nickName" label="昵称">
            <template slot-scope="scope">
              <span>{{ scope.row.user.nickName }}</span>
              <span class="gray9">(用户ID：{{ scope.row.user.user_id }})</span>
            </template>
          </el-table-column>

          <el-table-column prop="scene.text" label="余额变动场景">
            <template slot-scope="scope">
              <span v-if="scope.row.scene.value === 10" style="color: #409EFF">{{ scope.row.scene.text }}</span>
              <span v-if="scope.row.scene.value === 20" style="color: #67C23A">{{ scope.row.scene.text }}</span>
              <span v-if="scope.row.scene.value === 30" style="color: #F56C6C">{{ scope.row.scene.text }}</span>
              <span v-if="scope.row.scene.value === 40" style="color: #E6A23C">{{ scope.row.scene.text }}</span>
            </template>
          </el-table-column>

          <el-table-column prop="money" label="变动金额	">
            <template slot-scope="scope">
              <p v-if="scope.row.money > 0">+{{ scope.row.money }}</p>
              <p v-else>{{ scope.row.money }}</p>
            </template>
          </el-table-column>

          <el-table-column prop="describe" label="描述/说明" width="200" />

          <el-table-column prop="remark" label="管理员备注">
            <template slot-scope="scope">
              <p v-if="scope.row.remark === ''">--</p>
              <p v-else>{{ scope.row.remark }}</p>
            </template>
          </el-table-column>

          <el-table-column prop="create_time" label="创建时间" width="140" />
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
import UserApi from '@/api/user.js'

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
        nick_name: '',
        scene: '',
        value1: ''
      },
      /* 场景 */
      Scene: [],
      /* 时间 */
      value1: ''
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
      UserApi.BalanceLog({
        Params: Params
      }, true).then(data => {
        self.loading = false
        self.tableData = data.data.list.data
        self.totalDataNumber = data.data.list.total
        self.Scene = data.data.attributes.scene
      }).catch(() => {
      })
    },
    /** 搜索查询 **/
    onSubmit() {
      const self = this
      self.loading = true
      self.getTableList()
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
