<template>
  <div class="product-list">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-tabs v-model="activeName" @tab-click="handleClick">
        <el-tab-pane label="出售中" name="sell">
          <span slot="label">出售中 <el-tag size="mini">{{ product_count.sell }}</el-tag></span>
        </el-tab-pane>
        <el-tab-pane label="仓库中" name="lower">
          <span slot="label">仓库中 <el-tag size="mini">{{ product_count.lower }}</el-tag></span>
        </el-tab-pane>
        <el-tab-pane label="回收站" name="recovery">
          <span slot="label">回收站 <el-tag size="mini">{{ product_count.recovery }}</el-tag></span>
        </el-tab-pane>
        <el-tab-pane label="待审核" name="audit">
          <span slot="label">待审核 <el-tag size="mini">{{ product_count.audit }}</el-tag></span>
        </el-tab-pane>
        <el-tab-pane label="未通过" name="no_audit">
          <span slot="label">未通过 <el-tag size="mini">{{ product_count.no_audit }}</el-tag></span>
        </el-tab-pane>
      </el-tabs>

      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="商品分类">
          <el-select v-model="searchForm.category_id" size="small" placeholder="所有分类">
            <el-option label="全部" value="0" />
            <el-option v-for="(item, index) in categoryList" :key="index" :label="item.name" :value="item.category_id" />
          </el-select>
        </el-form-item>
        <el-form-item label="商品属性">
          <el-select v-model="searchForm.product_type" size="small" placeholder="所有属性">
            <el-option label="全部" value="0" />
            <el-option v-for="(item, index) in productTypeList" :key="index" :label="item.val" :value="item.key" />
          </el-select>
        </el-form-item>
        <el-form-item label="商品名称">
          <el-input v-model="searchForm.product_name" size="small" placeholder="请输入商品名称" />
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="el-icon-search" @click="onSubmit">查询</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!--添加产品-->
    <div class="common-level-rail">
      <el-button v-auth="'/goods/goods/add'" size="small" type="primary" icon="el-icon-plus" @click="addClick">添加产品</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <div v-if="(activeName === 'sell' || activeName === 'lower') && tableData.length > 0" class="batch-group">
          <el-checkbox v-model="checkAll" :indeterminate="isIndeterminate" @change="toggleSelection(tableData)">{{ $t('page.selectAll') }}</el-checkbox>
          <el-button v-auth="'/goods/goods/handle'" size="small" type="primary" class="mg-left20" @click="batchOperateClick('1')">{{ $t('page.onShelf') }}</el-button>
          <el-button v-auth="'/goods/goods/handle'" size="small" type="primary" class="mg-left20" @click="batchOperateClick('2')">{{ $t('page.offShelf') }}</el-button>
        </div>

        <el-table ref="multipleTable" v-loading="loading" size="small" :data="tableData" border style="width: 100%" @selection-change="handleSelectionChange">
          <el-table-column type="selection" width="55" align="center" />

          <el-table-column prop="product_name" label="产品" width="400px">
            <template slot-scope="scope">
              <div class="product-info">
                <div class="pic">
                  <img v-img-url="scope.row.image[0].file_path" alt="">
                </div>
                <div class="info">
                  <div class="name">{{ scope.row.product_name }}</div>
                  <div class="price">¥ {{ scope.row.product_price }}</div>
                </div>
              </div>
            </template>
          </el-table-column>

          <el-table-column prop="supplier.name" label="商户名称" />

          <el-table-column prop="category.name" label="分类" />

          <el-table-column prop="sales_actual" label="实际销量" />

          <el-table-column prop="product_stock" label="库存" />

          <el-table-column prop="view_times" label="点击数" />

          <el-table-column prop="product_type" label="商品属性">
            <template slot-scope="scope">
              <span v-if="scope.row.product_type === 1">实物商品</span>
              <span v-if="scope.row.product_type === 2" class="green">虚拟商品</span>
              <span v-if="scope.row.product_type === 3" class="orange">计次商品</span>
            </template>
          </el-table-column>

          <el-table-column prop="create_time" label="发布时间" />

          <el-table-column prop="product_sort" label="排序" />

          <el-table-column fixed="right" label="操作" width="80">
            <template slot-scope="scope">
              <div class="table-btn-column">
                <el-button v-if="scope.row.audit_status === 10" v-auth="'/goods/goods/edit'" type="text" size="small" @click="editClick(scope.row)">编辑商品</el-button>
                <el-button v-if="scope.row.audit_status === 0" v-auth="'/goods/goods/edit'" type="text" size="small" @click="editClick(scope.row)">审核商品</el-button>
                <el-button v-auth="'/goods/goods/delete'" type="text" size="small" @click="delClick(scope.row)">删除商品</el-button>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>

    <!--分页-->
    <div class="pagination mg-top30">
      <div v-if="(activeName === 'sell' || activeName === 'lower') && tableData.length > 0" class="batch-group-bottom">
        <el-checkbox v-model="checkAll" :indeterminate="isIndeterminate" @change="toggleSelection(tableData)">{{ $t('page.selectAll') }}</el-checkbox>
        <el-button v-auth="'/goods/goods/handle'" size="small" type="primary" class="mg-left20" @click="batchOperateClick('1')">{{ $t('page.onShelf') }}</el-button>
        <el-button v-auth="'/goods/goods/handle'" size="small" type="primary" class="mg-left20" @click="batchOperateClick('2')">{{ $t('page.offShelf') }}</el-button>
      </div>

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

    <!--显示二维码-->
    <Show v-if="open_show" :scene="scene" :image="scene_url" @close="closeShow" />
  </div>
</template>

<script>
import GoodsApi from '@/api/goods.js'
import Show from './part/Show.vue'
import { productTypeList } from '@/utils/sys'

export default {
  components: { Show },
  data() {
    return {
      /* 切换菜单 */
      activeName: 'sell',
      /* 切换选中值 */
      activeIndex: '0',
      /* 是否正在加载 */
      loading: true,
      /* 一页多少条 */
      pageSize: 10,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      /* 当前是第几页 */
      curPage: 1,
      /* 搜索参数 */
      searchForm: {
        product_name: '',
        product_type: '',
        category_id: ''
      },
      /* 列表数据 */
      tableData: [],
      /* 选中数据 */
      multipleSelection: [],
      /* 全部分类 */
      categoryList: [],
      /* 商品属性 */
      productTypeList: productTypeList(),
      /* 商品统计 */
      product_count: {},
      checkAll: false,
      isIndeterminate: false,
      /* 二维码 */
      scene: 'qr',
      scene_url: '',
      open_show: false
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
      this.pageSize = val
      this.getData()
    },
    /** 切换菜单 **/
    handleClick(tab, event) {
      const self = this
      self.curPage = 1
      self.getData()
    },
    /** 选中checkbox **/
    handleSelectionChange(val) {
      this.multipleSelection = val
      const checkedCount = this.multipleSelection.length
      this.checkAll = checkedCount > 0 && checkedCount === this.tableData.length
      this.isIndeterminate = checkedCount > 0 && checkedCount < this.tableData.length
    },
    /** 全选 **/
    toggleSelection(rows) {
      if (rows) {
        rows.forEach(row => {
          this.$refs.multipleTable.toggleRowSelection(row)
        })
      } else {
        this.$refs.multipleTable.clearSelection()
      }
    },
    /** 获取列表 **/
    getData() {
      const self = this
      const Params = self.searchForm
      Params.page = self.curPage
      Params.list_rows = self.pageSize
      Params.type = self.activeName
      self.loading = true
      GoodsApi.productList(Params, true)
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
    /** 搜索查询 **/
    onSubmit() {
      this.curPage = 1
      this.getData()
    },
    /** 打开添加 **/
    addClick() {
      this.$router.push('/goods/goods/add')
    },
    /** 打开编辑 **/
    editClick(row) {
      this.$router.push({
        path: '/goods/goods/edit',
        query: {
          goods_id: row.goods_id,
          scene: 'edit'
        }
      })
    },
    /** 删除 **/
    delClick(row) {
      const self = this
      self.$confirm('删除后将移动到回收站，确定删除该记录吗?', '提示', {
        type: 'warning'
      }).then(() => {
        GoodsApi.delProduct({
          goods_id: row.goods_id
        }).then(data => {
          self.$message({
            message: '删除成功',
            type: 'success'
          })
          self.getData()
        })
      })
    },
    /** 1-批量上架、2-批量下架 **/
    batchOperateClick(type) {
      const self = this
      if (!self.multipleSelection.length) {
        self.$message.error(self.$t('msg.selectGoods'))
        return false
      }
      const dataObj = self.keepProductId(self.multipleSelection)
      const buttonText = self.buttonText(type)
      const confirmText = dataObj.num > 1 ? '确定要' + buttonText + '“' + dataObj.firstName + '”等' + dataObj.num + '个商品吗?' : '确定要' + buttonText + '“' + dataObj.firstName + '”吗?'
      self.$confirm(confirmText, '提示', {
        confirmButtonText: buttonText,
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        GoodsApi.handleProduct({
          goods_id: dataObj.arr.join(','),
          type: type
        }).then(data => {
          self.$message({
            message: self.$t('msg.success'),
            type: 'success'
          })
          self.getData()
        })
      }).catch(() => {
      })
    },
    /** 保存商品id **/
    keepProductId(list) {
      const arr = []
      let firstName = ''
      const num = list.length
      for (let i = 0; i < num; i++) {
        arr.push(list[i].goods_id)
        if (i === 0) {
          firstName = list[i].product_name
        }
      }
      return { arr: arr, firstName: firstName, num: num }
    },
    /** 文案 **/
    buttonText(type) {
      let text = ''
      switch (type) {
        case '1':
          text = '上架'
          break
        case '2':
          text = '下架'
          break
      }
      return text
    },
    /** 推广 **/
    promoteClick(row) {
      this.scene = 'poster'
      GoodsApi.getPromoteImage({
        goods_id: row.goods_id,
        app_id: 10001
      }).then(data => {
        this.scene_url = data.data.url
      })
      this.open_show = true
    },
    /** 关闭弹窗 **/
    closeShow() {
      this.open_show = false
    }
  }
}
</script>

<style lang="scss" scoped>
  .batch-group {
    padding: 10px 21px 20px;
  }
  .batch-group-bottom {
    float: left;
    padding: 0 21px 0;
  }
  .mg-left20 {
    margin-left: 20px;
  }
  .mg-top30 {
    margin-top: 30px;
  }
</style>
