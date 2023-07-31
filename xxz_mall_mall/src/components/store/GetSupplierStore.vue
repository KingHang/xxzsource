<template>
  <el-dialog
    title="选择门店"
    :visible.sync="dialogVisible"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="cancelFunc"
  >
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="formInline" class="demo-form-inline">
        <el-form-item>
          <el-input v-model="formInline.search" size="small" prefix-icon="el-icon-search" placeholder="请输入关键词搜索" />
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" @click="resetForm('formInline')">{{ $t('page.reset') }}</el-button>
          <el-button size="small" type="primary" icon="el-icon-search" @click="search">{{ $t('page.search') }}</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" :data="tableData" size="small" border style="width: 100%" @selection-change="handleSelectionChange">
          <el-table-column type="selection" width="55" align="center" />

          <el-table-column label="门店ID" width="180">
            <template slot-scope="scope">
              <div class="table-user">
                <img v-img-url="scope.row.logo ? scope.row.logo.file_path : ''" style="width: 70px; height: 70px" alt="">
                <div class="user-right">{{ scope.row.store_id }}</div>
              </div>
            </template>
          </el-table-column>

          <el-table-column label="商户名称">
            <template slot-scope="scope">
              {{ scope.row.supplier ? scope.row.supplier.name : '' }}
            </template>
          </el-table-column>

          <el-table-column label="门店名称/地址">
            <template slot-scope="scope">
              <div>{{ scope.row.store_name }}</div>
              <div>{{ scope.row.detail_address }}</div>
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

    <div slot="footer" class="dialog-footer">
      <el-button size="small" @click="dialogVisible = false">取 消</el-button>
      <el-button size="small" type="primary" @click="confirmFunc">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
import StoreApi from '@/api/store.js'

export default {
  // eslint-disable-next-line vue/prop-name-casing,vue/require-prop-types
  props: ['is_open', 'shop_supplier_id', 'storeList'],
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      /* 当前是第几页 */
      curPage: 1,
      /* 一页多少条 */
      pageSize: 15,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      /* 列表数据 */
      tableData: [],
      /* 搜索表单对象 */
      formInline: {
        search: ''
      },
      /* 选中的 */
      multipleSelection: [],
      /* 是否显示 */
      dialogVisible: false
    }
  },
  watch: {
    is_open: function(n, o) {
      if (n !== o) {
        this.dialogVisible = n
        if (n) {
          this.multipleSelection = this.storeList
          this.getTableList()
        }
      }
    },
    storeList: function(val) {
      this.multipleSelection = val
    }
  },
  created() {
  },
  methods: {
    /** 查询 **/
    search() {
      this.curPage = 1
      this.tableData = []
      this.getTableList()
    },
    /** 重置 **/
    resetForm(form) {
      this.$refs[form].resetFields()
    },
    /** 选择第几页 **/
    handleCurrentChange(val) {
      this.curPage = val
      this.getTableList()
    },
    /** 每页多少条 **/
    handleSizeChange(val) {
      this.curPage = 1
      this.pageSize = val
      this.getTableList()
    },
    /** 获取数据 **/
    getTableList() {
      const self = this
      self.loading = true
      const params = self.formInline
      params.page = self.curPage
      params.list_rows = self.pageSize
      params.supplier_filter = 1
      params.shop_supplier_id = this.shop_supplier_id
      StoreApi.shoplist(params, true)
        .then(res => {
          self.tableData = res.data.list.data
          self.totalDataNumber = res.data.list.total
          self.loading = false
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 点击确定 **/
    confirmFunc() {
      const params = this.multipleSelection
      this.emitFunc(params)
    },
    /** 关闭弹窗 **/
    cancelFunc(e) {
      this.emitFunc()
    },
    /** 发送事件 **/
    emitFunc(e) {
      this.dialogVisible = false
      if (e && typeof e !== 'undefined') {
        this.$emit('close', {
          type: 'success',
          params: e
        })
      } else {
        this.$emit('close', {
          type: 'error'
        })
      }
    },
    /** 选择门店 **/
    handleSelectionChange(e) {
      this.multipleSelection = e
    }
  }
}
</script>

<style lang="scss" scoped>
  .table-user {
    display: flex;
    align-items: center;
  }
  .table-user .user-right {
    margin-left: 10px;
  }
</style>
