<template>
  <div class="product">
    <!--添加产品分类-->
    <div class="common-level-rail">
      <el-button v-auth="'/goods/category/add'" size="small" type="primary" icon="el-icon-plus" @click="addClick">添加分类</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table
          v-loading="loading"
          size="small"
          :data="tableData"
          row-key="category_id"
          default-expand-all
          :tree-props="{children: 'child'}"
          style="width: 100%"
        >
          <el-table-column prop="name" label="分类名称" width="180" />

          <el-table-column prop="" label="图片" width="180">
            <template slot-scope="scope">
              <img v-img-url="hasImages(scope.row.images)" alt="" width="50px">
            </template>
          </el-table-column>

          <el-table-column prop="sort" label="分类排序" />

          <el-table-column prop="disabled" label="分类状态">
            <template slot-scope="scope">
              <el-tag v-if="scope.row.disabled === 0" size="small" effect="dark">显示</el-tag>
              <el-tag v-else type="info" size="small" effect="dark">隐藏</el-tag>
            </template>
          </el-table-column>

          <el-table-column prop="params" label="参数" />

          <el-table-column prop="create_time" label="添加时间" />

          <el-table-column fixed="right" label="操作" width="100">
            <template slot-scope="scope">
              <el-button v-auth="'/goods/category/edit'" type="text" size="small" @click="editClick(scope.row)">编辑</el-button>
              <el-button v-auth="'/goods/category/delete'" type="text" size="small" @click="deleteClick(scope.row)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>

    <!--添加-->
    <Add v-if="open_add" :open_add="open_add" :addform="categoryModel" @closeDialog="closeDialogFunc($event, 'add')" />

    <!--修改-->
    <Edit v-if="open_edit" :open_edit="open_edit" :editform="categoryModel" @closeDialog="closeDialogFunc($event, 'edit')" />
  </div>
</template>

<script>
import GoodsApi from '@/api/goods.js'
import Add from './Add.vue'
import Edit from './Edit.vue'

export default {
  components: {
    Add,
    Edit
  },
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      /* 列表数据 */
      tableData: [],
      /* 是否打开添加弹窗 */
      open_add: false,
      /* 是否打开编辑弹窗 */
      open_edit: false,
      /* 当前编辑的对象 */
      categoryModel: {
        catList: [],
        model: {}
      }
    }
  },
  created() {
    /* 获取列表 */
    this.getData()
  },
  methods: {
    hasImages(e) {
      if (e) {
        return e.file_path
      } else {
        return ''
      }
    },
    /** 获取列表 **/
    getData() {
      const self = this
      GoodsApi.catList({}, true)
        .then(data => {
          self.loading = false
          self.tableData = data.data.list
          self.categoryModel.catList = self.tableData
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 打开添加 **/
    addClick() {
      this.open_add = true
    },
    /** 打开编辑 **/
    editClick(item) {
      this.categoryModel.model = item
      this.open_edit = true
    },
    /** 关闭弹窗 **/
    closeDialogFunc(e, f) {
      if (f === 'add') {
        this.open_add = e.openDialog
        if (e.type === 'success') {
          this.getData()
        }
      }
      if (f === 'edit') {
        this.open_edit = e.openDialog
        if (e.type === 'success') {
          this.getData()
        }
      }
    },
    /** 删除分类 **/
    deleteClick(row) {
      const self = this
      self.$confirm('删除后不可恢复，确认删除该记录吗?', '提示', {
        type: 'warning'
      }).then(() => {
        GoodsApi.catDel({
          category_id: row.category_id
        }).then(data => {
          self.$message({
            message: '删除成功',
            type: 'success'
          })
          self.getData()
        })
      })
    }
  }
}
</script>
