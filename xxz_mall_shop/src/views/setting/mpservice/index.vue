<template>
  <div>
    <!--form表单-->
    <el-form ref="formData" size="small" :model="formData" label-width="150px">
      <!--客服设置-->
      <div class="common-form">客服设置</div>

      <el-form-item label="QQ" :rules="[{required: true,message: '请输入QQ'}]">
        <el-input v-model="formData.qq" type="number" placeholder="请输入QQ" class="max-w460" />
      </el-form-item>

      <el-form-item label="微信号" :rules="[{required: true,message: '请输入微信号'}]">
        <el-input v-model="formData.wechat" placeholder="请输入微信号" class="max-w460" />
      </el-form-item>

      <el-form-item label="公众号二维码" :rules="[{required: true,message: '请输入上传公众号二维码'}]">
        <el-button @click="chooseImg">选择图片</el-button>
        <img v-img-url="formData.mp_image" class="mt10" width="200">
      </el-form-item>

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" type="primary" :disabled="loading" @click="onSubmit">提交</el-button>
      </div>
    </el-form>

    <!--上传图片-->
    <Upload v-if="isupload" :isupload="isupload" :config="{ total: 3 }" @returnImgs="returnImgsFunc" />
  </div>
</template>

<script>
import SettingApi from '@/api/setting.js'
import Upload from '@/components/file/Upload'

export default {
  components: {
    Upload
  },
  data() {
    return {
      /* 是否正在加载 */
      loading: true,
      /* 表单数据对象 */
      formData: {
        qq: '',
        wechat: '',
        mp_image: ''
      },
      /* 是否打开图片选择 */
      isupload: false
    }
  },
  created() {
    this.getParams()
  },
  methods: {
    /** 获取配置数据 **/
    getParams() {
      const self = this
      SettingApi.getMpService({}, true).then(res => {
        if (res.code === 1) {
          self.formData = res.data.vars.values
          self.loading = false
        }
      }).catch(() => {
      })
    },
    /** 提交 **/
    onSubmit() {
      const self = this
      const params = this.formData
      self.$refs.formData.validate((valid) => {
        if (valid) {
          self.loading = true
          SettingApi.setMpService(params, true)
            .then(data => {
              self.loading = false
              self.$message({
                message: '恭喜你， 客服设置成功',
                type: 'success'
              })
            })
            .catch(() => {
              self.loading = false
            })
        }
      })
    },
    /** 选择图片 **/
    chooseImg() {
      this.isupload = true
    },
    /** 关闭选择图片 **/
    returnImgsFunc(e) {
      this.isupload = false
      this.formData.mp_image = e[0].file_path
    }
  }
}
</script>
