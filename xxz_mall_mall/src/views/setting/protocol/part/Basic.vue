<template>
  <div class="basic-setting-content pl16 pr16">
    <!--基本信息-->
    <div class="common-form">基本信息</div>

    <el-form-item label="协议标题" :rules="[{ required: true, message: '请输入协议标题' }]" prop="agreement_title">
      <el-input v-model="form.agreement_title" class="max-w460" placeholder="请输入协议标题" />
    </el-form-item>

    <el-form-item label="关键字" :rules="[{ required: true, message: '请输入关键字' }]" prop="keyword">
      <el-input v-model="form.keyword" class="max-w460" placeholder="请输入关键字" />
      <div class="gray9">关键字用于接口查询协议内容，请输入英文字母，避免重复</div>
    </el-form-item>

    <el-form-item label="协议内容" prop="agreement_content">
      <div class="edit_container">
        <Uediter ref="ue" :text="form.agreement_content" :config="ueditor.config" @contentChange="contentChangeFunc" />
      </div>
    </el-form-item>
  </div>
</template>

<script>
import Uediter from '@/components/UE.vue'

export default {
  components: {
    /* 编辑器 */
    Uediter
  },
  data() {
    return {
      /* 富文本框配置 */
      ueditor: {
        text: '',
        config: {
          initialFrameWidth: 600,
          initialFrameHeight: 500
        }
      }
    }
  },
  inject: ['form'],
  created() {
  },
  methods: {
    /** 获取富文本内容 **/
    contentChangeFunc(e) {
      this.form.agreement_content = e
    }
  }
}
</script>

<style>
  .edit_container {
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    line-height: 20px;
    color: #2c3e50;
  }
</style>
