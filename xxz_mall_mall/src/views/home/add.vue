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
      <el-button size="small" type="info" @click="gotoBack">返回上一页</el-button>
      <el-button size="small" type="primary" :loading="loading" @click="Submit()">保存</el-button>
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
    /** 获取初始化数据 **/
    getData() {
      const self = this
      HomeApi.toAddPage({}, true)
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
    /**
     * 新增Diy组件
     * @param key
     */
    onAddItem: function(key) {
      // 复制默认diy组件数据
      const item = deepClone(this.defaultData[key])
      this.diyData.items.push(item)
      // 编辑当前选中的元素
      this.$refs.model.onEditer(this.diyData.items.length - 1)
    },
    /** 添加页面 **/
    Submit() {
      const self = this
      const params = self.diyData
      if (params.items.length < 1) {
        self.$message({
          message: '至少要选择一个组件',
          type: 'warning'
        })
        return
      }
      self.loading = true
      HomeApi.addPage(
        {
          params: JSON.stringify(params)
        },
        true
      )
        .then(data => {
          self.loading = false
          self.$message({
            message: '恭喜你，添加成功',
            type: 'success'
          })
          self.gotoBack()
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 返回上一页面 **/
    gotoBack() {
      this.$router.back(-1)
    }
  }
}
</script>

<style lang="scss">
@import "~@/styles/page/diy.scss";
</style>
