<template>
  <div v-if="!loading" class="diy-container clearfix">
    <!--类别选择-->
    <div class="diy-menu">
      <Type v-if="!loading" :default-data="defaultData" />
    </div>

    <!--手机diy容器-->
    <div class="diy-phone">
      <Model v-if="!loading" ref="model" :form="form" :default-data="defaultData" :diy-data="diyData" />
    </div>

    <!--参数设置-->
    <div class="diy-info">
      <Params v-if="!loading" :form="form" :default-data="defaultData" :diy-data="diyData" />
    </div>

    <!--提交-->
    <div class="common-button-wrapper">
      <el-button v-auth="'/home/preview'" size="small" type="danger" @click="goEdit">进入编辑器</el-button>
      <el-button size="small" type="primary" :loading="loading" @click="submitData()">保存</el-button>
    </div>
  </div>
</template>

<script>
import { deepClone } from '@/utils/base.js'
import HomeApi from '@/api/home.js'
import Type from './diy/Type.vue'
import Model from './diy/Model.vue'
import Params from './diy/Params.vue'

export default {
  components: {
    /* 组件类别 */
    Type,
    /* 组件信息 */
    Model,
    /* 参数信息 */
    Params
  },
  data() {
    return {
      /* 是否正在加载 */
      loading: true,
      /* 默认数据 */
      defaultData: {},
      /* 组件数据列表 */
      diyData: {
        items: []
      },
      opts: {},
      /* 表单对象 */
      form: {
        umeditor: {},
        /* 当前选中 */
        curItem: {},
        /* 当前选中的元素（下标） */
        selectedIndex: -1
      }
    }
  },
  created() {
    /* 获取列表 */
    this.getData()
  },
  methods: {
    /** 获取列表 **/
    getData() {
      const self = this
      HomeApi.editHome({}, true)
        .then(res => {
          self.defaultData = res.data.defaultData
          self.diyData = res.data.jsonData
          self.form.curItem = self.diyData.page
          self.opts = res.data.opts
          self.loading = false
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 新增Diy组件 **/
    onAddItem: function(key) {
      // 复制默认diy组件数据
      const item = deepClone(this.defaultData[key])
      let cur_index = 0
      if (this.form.selectedIndex < 0) {
        cur_index = 0
        this.diyData.items.unshift(item)
      } else {
        cur_index = this.form.selectedIndex + 1
        this.diyData.items.splice(cur_index, 0, item)
      }
      // 编辑当前选中的元素
      this.$refs.model.onEditer(cur_index)
    },
    /** 上架 **/
    submitData() {
      const self = this
      self.loading = true
      const params = self.diyData
      const page_id = self.page_id
      HomeApi.SavePage(
        {
          params: JSON.stringify(params),
          page_id: page_id
        },
        true
      )
        .then(data => {
          self.loading = false
          self.$message({
            message: '恭喜你，修改成功',
            type: 'success'
          })
          self.getData()
          self.form.selectedIndex = -1
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 进入编辑器模式 **/
    goEdit() {
      this.$router.push('/preview')
    }
  }
}
</script>

<style lang="scss">
@import "~@/styles/page/diy.scss";
</style>
