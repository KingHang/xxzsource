<template>
  <div>
    <div class="common-form">
      <span>{{ curItem.name }}</span>
    </div>
    <el-form size="small" :model="curItem" label-width="100px">
      <!-- 文章分类 -->
      <el-form-item label="文章分类：">
        <el-select v-model="curItem.params.auto.category">
          <el-option label="全部分类" :value="''+0" />
          <el-option v-for="item in category" :key="item.category_id" :label="item.name" :value="''+item.category_id" />
        </el-select>
      </el-form-item>
      <!-- 显示数量 -->
      <el-form-item label="显示数量：">
        <el-input v-model="curItem.params.auto.showNum" type="number" style="width: auto;" class="w-auto" />
        <div>
          文章数据请到
          <a href="#" target="_blank">内容管理 - 文章列表</a>
          中管理
        </div>
      </el-form-item>
      <!--显示类型-->
      <el-form-item label="显示类型：">
        <el-radio-group v-model="curItem.style.display">
          <el-radio label="10">有图模式</el-radio>
          <el-radio label="20">无图模式</el-radio>
        </el-radio-group>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import ArticleApi from '@/api/article.js'

export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['curItem', 'selectedIndex', 'opts'],
  data() {
    return {
      category: {}
    }
  },
  created() {
    /* 获取文章栏目 */
    this.getData()
  },
  methods: {
    /** 获取文章栏目 **/
    getData() {
      const self = this
      ArticleApi.getCategory({
        page_id: self.page_id
      }, true).then(data => {
        self.category = data.data.category
      }).catch(() => {
        self.loading = false
      })
    }
  }
}
</script>
