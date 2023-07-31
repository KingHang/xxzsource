<template>
  <div class="product-add">
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" label-width="250px">
      <!--小程序设置-->
      <div class="common-form">app设置</div>

      <el-form-item label="AppID 应用ID">
        <el-input v-model="form.openapp_id" class="max-w460" />
      </el-form-item>

      <el-form-item label="AppSecret 应用密钥">
        <el-input v-model="form.openapp_secret" type="password" class="max-w460" />
      </el-form-item>

      <el-form-item label="logo" :rules="[{required: true,message: '请输入上传logo'}]">
        <el-button @click="chooseImg">选择图片</el-button>
        <img v-img-url="form.logo" class="mt10" width="50">
        <div class="tips">logo会用在登录页和一些分享页</div>
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

      <div class="common-form">支付宝支付设置</div>

      <el-form-item label="是否开启" prop="customer">
        <el-checkbox v-model="form.is_alipay">是否开启支付宝支付</el-checkbox>
      </el-form-item>

      <el-form-item label="支付宝 appid">
        <el-input v-model="form.alipay_appid" class="max-w460" />
      </el-form-item>

      <el-form-item label="支付宝公钥">
        <el-input v-model="form.alipay_publickey" type="textarea" :rows="4" class="max-w460" />
      </el-form-item>

      <el-form-item label="应用私钥">
        <el-input v-model="form.alipay_privatekey" type="textarea" :rows="4" class="max-w460" />
      </el-form-item>

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button type="primary" @click="onSubmit">提交</el-button>
      </div>
    </el-form>

    <!--上传图片-->
    <Upload v-if="isupload" :isupload="isupload" :config="{ total: 1 }" @returnImgs="returnImgsFunc" />
  </div>
</template>

<script>
import AppApi from '@/api/app.js'
import Upload from '@/components/file/Upload'

export default {
  components: {
    Upload
  },
  data() {
    return {
      /* form表单数据 */
      form: {},
      /* 是否打开图片选择 */
      isupload: false
    }
  },
  created() {
    this.getData()
  },
  methods: {
    getData() {
      const self = this
      AppApi.appopenDetail({}, true)
        .then(data => {
          if (data.data.data != null) {
            self.form = data.data.data
            self.form.is_alipay = self.form.is_alipay === 1
          }
        })
        .catch(() => {})
    },
    /** 提交表单 **/
    onSubmit() {
      const self = this
      const params = this.form
      AppApi.editAppOpen(params, true)
        .then(data => {
          self.$message({
            message: '恭喜你，设置成功',
            type: 'success'
          })
        })
        .catch(() => {
        })
    },
    /** 选择图片 **/
    chooseImg() {
      this.isupload = true
    },
    /** 关闭选择图片 **/
    returnImgsFunc(e) {
      this.isupload = false
      this.form.logo = e[0].file_path
    }
  }
}
</script>

<style>
  .tips {
    color: #ccc;
  }
</style>
