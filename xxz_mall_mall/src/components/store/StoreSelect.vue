<template>
  <el-dialog
    title="选择门店"
    :visible.sync="dialogVisible"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    width="900px"
    @close="dialogFormVisible"
  >
    <div class="common-seach-wrap">
      <el-form :inline="true" size="small" :model="formInline" class="demo-form-inline">
        <el-form-item label="商品分类">
          <el-select v-model="formInline.category_id" placeholder="请选择商品分类">
            <template v-for="cat in cateList">
              <el-option :key="cat.category_id" :value="0" :label="'全部'" />
              <el-option :key="cat.category_id" :value="cat.category_id" :label="cat.name" />
              <template v-if="cat.child !== undefined">
                <template v-for="two in cat.child">
                  <el-option :key="two.category_id" :value="two.category_id" :label="two.name" style="padding-left: 30px;" />
                  <template v-if="two.child !== undefined">
                    <template v-for="three in two.child">
                      <el-option :key="three.category_id" :value="three.category_id" :label="three.name" style="padding-left: 60px;" />
                    </template>
                  </template>
                </template>
              </template>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item label="商品名称">
          <el-input v-model="formInline.search" placeholder="请输入商品名称">
            <!--  <el-select v-model="formInline.status" slot="prepend" placeholder="请选择商品状态" style="width: 80px;">
              <el-option label="全部" value="-1"></el-option>
                <el-option v-for="(item,index) in status" :key="item.id" :label="item.name" :value="item.id"></el-option>
            </el-select>-->
            <el-button slot="append" icon="el-icon-search" @click="getData">查询</el-button>
          </el-input>
        </el-form-item>
      </el-form>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table v-loading="loading" size="small" :data="productList" border style="width: 100%" highlight-current-row @selection-change="tableCurrentChange">
          <el-table-column width="70" label="商品图片">
            <template slot-scope="scope">
              <img :src="scope.row.image[0].file_path" width="30" height="30">
            </template>
          </el-table-column>

          <el-table-column prop="product_name" label="商品名称" />

          <el-table-column prop="category.name" width="100" label="商品分类" />

          <el-table-column prop="create_time" width="140" label="添加时间" />

          <el-table-column type="selection" :selectable="selectableFunc" width="44" />
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
      <el-button size="small" @click="dialogVisible=false">取 消</el-button>
      <el-button size="small" type="primary" @click="addClerk">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
import storeApi from '@/api/store.js'

export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['isstore', 'excludeIds', 'islist'],
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      /* 当前是第几页 */
      curPage: 1,
      /* 一页多少条 */
      pageSize: 20,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      formInline: {},
      // 商品分类列表
      cateList: [],
      // 商品列表
      productList: [],
      // 类别列表
      status: [
        {
          id: 10,
          name: '上架'
        },
        {
          id: 20,
          name: '下架'
        }
      ],
      multipleSelection: [],
      /* 左边长度 */
      formLabelWidth: '120px',
      /* 是否显示 */
      dialogVisible: false,
      /* 结果类别 */
      type: 'error',
      /* 传出去的参数 */
      params: null
    }
  },
  watch: {
    isstore: function(n, o) {
      if (n !== o) {
        if (n) {
          this.dialogVisible = n
          this.type = 'error'
          this.params = null
          this.getData()
        }
      }
    }
  },
  created() {
  },
  methods: {
    /** 是否可以勾选 **/
    selectableFunc(e) {
      if (typeof e.noChoose !== 'boolean') {
        return true
      }
      return e.noChoose
    },
    /** 选择第几页 **/
    handleCurrentChange(val) {
      this.curPage = val
      this.getData()
    },
    /** 每页多少条 **/
    handleSizeChange(val) {
      this.curPage = 1
      this.pageSize = val
      this.getData()
    },
    /** 获取商品列表 **/
    getData() {
      const self = this
      const params = self.formInline
      params.page = self.curPage
      params.list_rows = self.pageSize
      storeApi.storeLists(params, true)
        .then(res => {
          if (res.code === 1) {
            self.loading = false
            self.cateList = res.data.catgory
            /* 判断是否需要去重 */
            if (self.excludeIds && typeof (self.excludeIds) !== 'undefined' && self.excludeIds.length > 0) {
              res.data.list.data.forEach(item => {
                item.noChoose = self.excludeIds.indexOf(item.product_id) <= -1
              })
            }
            self.productList = res.data.list.data
            self.totalDataNumber = res.data.list.total
          }
        })
        .catch(() => {})
    },
    /** 点击确定 **/
    addClerk() {
      const self = this
      let params = null
      if (self.multipleSelection.length < 1) {
        self.$message({
          message: '请至少选择一件产品商品！',
          type: 'error'
        })
        return
      }
      if (self.islist && typeof (self.islist) !== 'undefined') {
        self.multipleSelection.forEach(item => {
          item.image = item.image[0].file_path
        })
        params = self.multipleSelection
      } else {
        params = self.multipleSelection[0]
        params.image = params.image[0].file_path
      }
      self.params = params
      self.type = 'success'
      self.dialogVisible = false
    },
    /** 关闭弹窗 **/
    dialogFormVisible() {
      this.$emit('closeDialog', {
        type: this.type,
        openDialog: false,
        params: this.params
      })
    },
    /** 监听复选按钮选中事件 **/
    tableCurrentChange(val) {
      this.multipleSelection = val
    }
  }
}
</script>
