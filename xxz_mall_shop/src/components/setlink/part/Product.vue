<template>
  <div class="marketing-box">
    <el-tabs v-model="activeTab">
      <el-tab-pane label="分类" name="type" />
      <el-tab-pane label="详情" name="detail" />
    </el-tabs>

    <div v-if="activeTab === 'type'" class="product">
      <!--内容-->
      <div v-if="!loading" class="product-content">
        <div class="table-wrap type-table">
          <el-cascader
            ref="cascader"
            v-model="categoryActive"
            class="ww100"
            :options="categoryList"
            :props="{ children: 'child', value: 'category_id', label: 'name' }"
            @change="changeCategory"
          />
        </div>
      </div>
    </div>

    <div v-if="activeTab === 'detail' && !loading" class="product-list">
      <!--搜索表单-->
      <div class="common-seach-wrap">
        <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
          <el-form-item label="商品名称">
            <el-input v-model="searchForm.product_name" size="small" placeholder="请输入商品名称" />
          </el-form-item>
          <el-form-item>
            <el-button size="small" icon="el-icon-search" @click="onSubmit">查询</el-button>
          </el-form-item>
        </el-form>
      </div>

      <!--内容-->
      <div class="product-content">
        <div class="table-wrap setlink-product-table">
          <el-table size="mini" :data="tableData" border style="width: 100%">
            <el-table-column prop="product_name" label="产品">
              <template slot-scope="scope">
                <div class="product-info">
                  <div class="pic"><img v-img-url="scope.row.image[0].file_path" alt=""></div>
                  <div class="info">
                    <div class="name text-ellipsis">{{ scope.row.product_name }}</div>
                  </div>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="product_price" label="价格" width="100">
              <template slot-scope="scope">
                <span class="red">{{ scope.row.product_price }}</span>
              </template>
            </el-table-column>
            <el-table-column label="操作" width="80">
              <template slot-scope="scope">
                <el-button size="mini" @click="changeFunc(scope.row)">选择</el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>
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
import GoodsApi from '@/api/goods.js'

export default {
  data() {
    return {
      /* 是否正在加载 */
      loading: true,
      /* tab切换选择中值 */
      activeTab: 'type',
      /* 产品类别列表 */
      categoryList: [],
      /* 当前选中 */
      categoryActive: [],
      /* 搜索表单对象 */
      searchForm: {
        product_name: ''
      },
      /* 产品数据表 */
      tableData: [],
      /* 一页多少条 */
      pageSize: 5,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      /* 当前是第几页 */
      curPage: 1,
      /* 选中的页面值 */
      activePage: null
    }
  },
  watch: {
    activeTab: function(n, o) {
      if (n !== o) {
        this.tableData = []
        if (n === 'type') {
          this.autoType()
        }
        if (n === 'detail') {
          this.getData()
        }
      }
    }
  },
  created() {
    /* 获取产品类别 */
    this.getCategory()
  },
  methods: {
    /** 获取列表 **/
    getCategory() {
      const self = this
      self.loading = true
      GoodsApi.catList({}, true)
        .then(res => {
          self.categoryList = res.data.list
          self.autoType()
          self.loading = false
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 自动选择类别 **/
    autoType(i) {
      i = i | 0
      this.categoryActive = []
      if (this.categoryList.length > 0) {
        const item = this.categoryList[i]
        this.categoryActive.push(item.category_id)
        if (item.child && typeof item.child !== 'undefined' && item.child.length > 0) {
          this.categoryActive.push(item.child[0].category_id)
          this.changeFunc(item.child[0])
        } else {
          i++
          this.autoType(i)
        }
      }
    },
    /** 选择类别 **/
    changeCategory(e) {
      const item = this.$refs['cascader'].getCheckedNodes()
      this.changeFunc(item[0].data)
    },
    /** 选择第几页 **/
    handleCurrentChange(val) {
      const self = this
      self.curPage = val
      self.getData()
    },
    /** 每页多少条 **/
    handleSizeChange(val) {
      this.pageSize = val
      this.curPage = 0
      this.getData()
    },
    /** 获取列表 **/
    getData() {
      const self = this
      self.loading = true
      const Params = {}
      Params.page = self.curPage
      Params.list_rows = self.pageSize
      Params.product_name = self.searchForm.product_name
      GoodsApi.productList(Params, true)
        .then(data => {
          self.loading = false
          self.tableData = data.data.list.data
          self.totalDataNumber = data.data.list.total
          if (self.curPage === 1 && self.tableData.length > 0) {
            self.changeFunc(self.tableData[0])
          }
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 搜索查询 **/
    onSubmit() {
      this.curPage = 0
      this.getData()
    },
    /** 选中的值 **/
    changeFunc(e) {
      const obj = {}
      if (this.activeTab === 'type') {
        obj.name = e.name
        obj.url = 'pages/product/list/list?category_id=' + e.category_id
        obj.type = '商品分类'
      }
      if (this.activeTab === 'detail') {
        obj.name = e.product_name
        obj.url = 'pages/product/detail/detail?product_id=' + e.product_id
        obj.type = '商品详情'
      }
      this.$emit('changeData', obj)
    }
  }
}
</script>

<style scoped>
.table-wrap.setlink-product-table .product-info .pic {
  width: 20px;
  height: 20px;
  background: #FFFFFF;
}

.table-wrap.setlink-product-table .product-info .pic img {
  width: 20px;
  height: 20px;
}

.marketing-box .el-tree-node__content {
  height: 30px;
}

.marketing-box .el-tree-node__content {
  margin-bottom: 10px;
}
</style>
