<template>
  <el-dialog
    title="选择商品"
    :visible.sync="dialogVisible"
    append-to-body
    :close-on-press-escape="false"
    width="800px"
    @close="cancelFunc"
  >
    <!-- <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="formInline" class="demo-form-inline">
        <el-form-item label="等级">
          <el-select v-model="formInline.grade_id" placeholder="请选择会员等级" style="width: 120px;">
            <el-option label="全部" value="0" />
            <el-option v-for="(item, index) in gradeList" :key="index" :label="item.name" :value="item.grade_id" />
          </el-select>
        </el-form-item>
        <el-form-item label="性别">
          <el-select v-model="formInline.sex" placeholder="请选择性别" style="width: 120px;">
            <el-option label="全部" value="-1" />
            <el-option v-for="(item, index) in sex" :key="index" :label="item" :value="index" />
          </el-select>
        </el-form-item>
        <el-form-item label="微信昵称">
          <el-input v-model="formInline.nickName" placeholder="请输入微信昵称" />
        </el-form-item>
        <el-form-item>
          <el-button icon="el-icon-search" @click="search">查询</el-button>
        </el-form-item>
      </el-form>
    </div> -->

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" :data="tableData" size="small" border style="width: 100%" @selection-change="handleSelectionChange">
          <el-table-column type="selection" width="45" />

          <el-table-column prop="product_name" label="产品" width="400px">
            <template slot-scope="scope">
              <div class="product-info">
                <div class="pic"><img v-img-url="scope.row.image[0].file_path" alt=""></div>
                <div class="info">
                  <div class="name">{{ scope.row.product_name }}</div>
                  <div class="price">销售价：{{ scope.row.product_price }}</div>
                  <div class="price">供应价：{{ scope.row.supplier_price }}</div>
                </div>
              </div>
            </template>
          </el-table-column>

          <el-table-column prop="supplier.name" label="商户名称" />

          <el-table-column prop="category.name" label="分类" />

          <el-table-column prop="sales_actual" label="实际销量" />

          <el-table-column prop="product_stock" label="库存" />

          <el-table-column prop="view_times" label="点击数" />

          <el-table-column prop="product_status.text" label="状态">
            <template slot-scope="scope">
              <span :class="{ green: scope.row.product_status.value === 10, gray: scope.row.product_status.value === 20 }">{{ scope.row.product_status.text }}</span>
            </template>
          </el-table-column>

          <el-table-column prop="product_status.text" label="商品属性">
            <template slot-scope="scope">
              <span v-if="scope.row.is_virtual === 0">实物商品</span>
              <span v-if="scope.row.is_virtual === 1" class="green">虚拟商品</span>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!--分页-->
      <div class="pagination">
        <el-pagination
          background
          :current-page="curPage"
          :page-sizes="[2, 10, 20, 50, 100]"
          :page-size="pageSize"
          layout="total, prev, pager, next, jumper"
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
import GoodsApi from '@/api/goods.js'

export default {
  props: {
    // eslint-disable-next-line vue/prop-name-casing
    is_open: Boolean
  },
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
      /* 搜索表单对象 */
      formInline: {
        /* 等级 */
        grade_id: '',
        /* 昵称 */
        nickName: '',
        /* 性别 */
        sex: ''
      },
      /* 会员等级列表 */
      gradeList: [],
      /* 会员列表 */
      tableData: [],
      /* 性别列表 */
      sex: ['女', '男', '未知'],
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
          this.getTableList()
        }
      }
    }
  },
  created() {},
  methods: {
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
      GoodsApi.productList(params, true)
        .then(data => {
          self.loading = false
          self.tableData = data.data.list.data
          self.categoryList = data.data.category
          self.totalDataNumber = data.data.list.total
          self.product_count = data.data.product_count
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 查询 **/
    search() {
      this.curPage = 1
      this.tableData = []
      this.getTableList()
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
    /** 监听单选按钮选中事件 **/
    selectUser(val) {
      this.multipleSelection = val
      this.confirmFunc()
    },
    /** 选择用户 **/
    handleSelectionChange(e) {
      this.multipleSelection = e
    }
  }
}
</script>
