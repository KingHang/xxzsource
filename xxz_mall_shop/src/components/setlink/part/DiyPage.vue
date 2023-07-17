<template>
  <el-select v-model="activePage" v-loading="loading" placeholder="请选择" class="percent-w100" value-key="url" @change="changeFunc">
    <el-option v-for="item in pages" :key="item.url" :label="item.name" :value="item" />
  </el-select>
</template>

<script>
import LinkApi from '@/api/link.js'

export default {
  data() {
    return {
      /* 是否正在加载 */
      loading: true,
      /* 页面数据 */
      pages: [],
      /* 选中的值 */
      activePage: {}
    }
  },
  created() {
    this.getData()
  },
  methods: {
    /** 获取自定义页面 **/
    getData() {
      const self = this
      LinkApi.getPageList({}, true)
        .then(res => {
          self.loading = false
          const list = []
          for (let i = 0, length = res.data.list.length; i < length; i++) {
            const item = res.data.list[i]
            const url = 'pages/diy-page/diy-page?page_id=' + item.page_id
            const LinkList = {
              url: url,
              name: item.page_name,
              type: '自定义页面'
            }
            list.push(LinkList)
          }
          self.pages = list
          if (self.pages.length > 0) {
            self.activePage = self.pages[0]
            self.changeFunc(self.activePage)
          }
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 选中的值 **/
    changeFunc(e) {
      this.$emit('changeData', e)
    }
  }
}
</script>
