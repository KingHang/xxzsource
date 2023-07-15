<template>
  <div class="user">
    <!--新建协议-->
    <div class="common-level-rail">
      <el-button v-auth="'/setting/protocol/add'" size="small" type="primary" icon="el-icon-plus" @click="addClick">{{ $t('page.addProtocol') }}</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" size="small" :data="tableData" border style="width: 100%">
          <el-table-column prop="agreement_title" label="协议标题" />

          <el-table-column prop="keyword" label="关键字" />

          <el-table-column fixed="right" label="操作" width="160">
            <template slot-scope="scope">
              <el-button v-auth="'/setting/protocol/edit'" type="text" size="small" @click="editClick(scope.row.id)">{{ $t('page.edit') }}</el-button>
              <el-button v-auth="'/setting/protocol/delete'" type="text" size="small" @click="delClick(scope.row.id)">{{ $t('page.del') }}</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!--分页-->
      <div class="pagination">
        <el-pagination
          background
          :current-page="curPage"
          :page-sizes="[10, 20, 50, 100]"
          :page-size="pageSize"
          layout="total, sizes, prev, pager, next, jumper"
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
      curPage: 1
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
      Params.pageSize = self.pageSize
      self.loading = true
      SettingApi.agreementList(Params, true)
        .then(res => {
          self.tableData = res.data.list.data
          self.totalDataNumber = res.data.list.total
          self.loading = false
        })
        .catch(() => {})
    },
    /** 新建 **/
    addClick() {
      this.$router.push('/setting/protocol/add')
    },
    /** 编辑 **/
    editClick(id) {
      this.$router.push({
        path: '/setting/protocol/edit',
        query: {
          id: id
        }
      })
    },
    /** 删除 **/
    delClick(id) {
      const self = this
      self.$confirm('确定要删除此协议吗?', '提示', {
        confirmButtonText: '删除',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          SettingApi.deleteAgreement({ id: id }).then(data => {
            self.$message({
              message: self.$t('msg.success'),
              type: 'success'
            })
            self.getData()
          })
        })
        .catch(() => {})
    }
  }
}
</script>
