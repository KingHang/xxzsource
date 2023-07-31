<template>
  <el-form label-width="80px" :model="formModel">
    <el-form-item label="appid：">
      <el-input v-model="formModel.name" @input="changeFunc" />
    </el-form-item>
    <el-form-item label="页面路径：">
      <el-input v-model="formModel.url" @input="changeFunc" />
    </el-form-item>
    <el-form-item label="版本类型：">
      <el-select v-model="formModel.envVersion" style="width: 100%" placeholder="请选择版本类型" @change="changeFunc">
        <el-option label="体验版 trail" value="trail" />
        <el-option label="正式版 release" value="release" />
      </el-select>
    </el-form-item>
  </el-form>
</template>

<script>
export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['currentData'],
  data() {
    return {
      formModel: {
        name: '',
        url: '',
        envVersion: 'trail',
        type: '小程序'
      }
    }
  },
  created() {
    if (this.currentData.appid) {
      this.formModel.name = this.currentData.appid
    }
    this.formModel.url = this.currentData.linkUrl
    this.formModel.envVersion = this.currentData.envVersion
    this.changeFunc()
  },
  methods: {
    /** 选中的值 **/
    changeFunc() {
      this.$emit('changeData', this.formModel)
    }
  }
}
</script>
