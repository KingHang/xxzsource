<template>
  <div class="product-add">
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" label-width="250px">
      <!--小程序设置-->
      <div class="common-form">小程序设置</div>

      <el-form-item label="AppID 小程序ID">
        <el-input v-model="form.wxapp_id" class="max-w460" />
      </el-form-item>

      <el-form-item label="AppSecret 小程序密钥">
        <el-input v-model="form.wxapp_secret" type="password" class="max-w460" />
      </el-form-item>

      <div class="common-form">微信支付设置</div>

      <el-form-item label="微信支付商户号 MCHID">
        <el-input v-model="form.mchid" class="max-w460" />
      </el-form-item>

      <el-form-item label="微信支付密钥 APIKEY">
        <el-input v-model="form.apikey" class="max-w460" />
      </el-form-item>

      <el-form-item label="apiclient_cert.pem">
        <el-input v-model="form.cert_pem" type="textarea" :rows="4" placeholder="使用文本编辑器打开apiclient_cert.pem文件，将文件的全部内容复制进来" class="max-w460" />
        <div class="tips">使用文本编辑器打开apiclient_key.pem文件，将文件的全部内容复制进来</div>
      </el-form-item>

      <el-form-item label="apiclient_key.pem">
        <el-input v-model="form.key_pem" type="textarea" :rows="4" placeholder="使用文本编辑器打开apiclient_cert.pem文件，将文件的全部内容复制进来" class="max-w460" />
        <div class="tips">使用文本编辑器打开apiclient_key.pem文件，将文件的全部内容复制进来</div>
      </el-form-item>

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button type="primary" @click="onSubmit">提交</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
import AppApi from '@/api/app.js'

export default {
  data() {
    return {
      /* 切换菜单 */
      // activeIndex: '1',
      /* form表单数据 */
      form: {}
    }
  },
  created() {
    this.getData()
  },
  methods: {
    getData() {
      const self = this
      AppApi.appwxDetail({}, true)
        .then(data => {
          if (data.data.data != null) {
            self.form = data.data.data
          }
        })
        .catch(() => {})
    },
    /** 提交表单 **/
    onSubmit() {
      const self = this
      const params = this.form
      AppApi.editAppWx(params, true)
        .then(data => {
          self.$message({
            message: '恭喜你，设置成功',
            type: 'success'
          })
          self.$router.push('/app/appwx/index')
        })
        .catch(() => {
        })
    }
  }
}
</script>

<style>
  .tips {
    color: #ccc;
  }
</style>
