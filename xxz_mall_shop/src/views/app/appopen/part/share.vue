<template>
  <div class="product-add">
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" label-width="250px">
      <!--小程序设置-->
      <div class="common-form">分享设置</div>

      <el-form-item label="分享落地页">
        <el-radio v-model="form.type" label="1">公众号/h5</el-radio>
        <el-radio v-model="form.type" label="2">小程序</el-radio>
        <el-radio v-model="form.type" label="3">下载页</el-radio>
        <div class="tips">1、公众号和小程序跳到app对应的分享页面，下载页需要自己编写或者跳第三方;2、小程序仅支持分享到微信好友;</div>
      </el-form-item>

      <template v-if="form.type === 1">
        <el-form-item label="公众号/h5对应的访问路径">
          <el-input v-model="form.open_site" class="max-w460" />
          <div class="tips">如果未修改默认部署，则为https://域名/h5</div>
        </el-form-item>
      </template>

      <template v-if="form.type === 2">
        <el-form-item label="小程序原始id">
          <el-input v-model="form.gh_id" class="max-w460" />
          <div class="tips">小程序管理后台，设置->账号信息->原始ID，以gh_ 开始</div>
        </el-form-item>

        <el-form-item label="webUrl">
          <el-input v-model="form.web_url" class="max-w460" />
          <div class="tips">小程序链接失效访问的url</div>
        </el-form-item>
      </template>

      <template v-if="form.type === 3">
        <el-form-item label="下载地址">
          <el-input v-model="form.down_url" class="max-w460" />
          <div class="tips">填写app下载地址，自己开发或者第三方下载地址</div>
        </el-form-item>

        <el-form-item label="关系绑定">
          <el-radio v-model="form.bind_type" label="1">直接跳下载页，app里绑定</el-radio>
          <el-radio v-model="form.bind_type" label="2">公众号授权后跳下载页</el-radio>
          <div class="tips">公众号授权后跳下载页需填写公众号相关信息</div>
        </el-form-item>
      </template>

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
      /* form表单数据 */
      form: {
        type: 1,
        open_site: '',
        gh_id: '',
        web_url: '',
        down_url: '',
        bind_type: 1
      }
    }
  },
  created() {
    this.getData()
  },
  methods: {
    getData() {
      const self = this
      AppApi.appshareDetail({}, true)
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
      AppApi.editAppShare(params, true)
        .then(data => {
          self.$message({
            message: '恭喜你，设置成功',
            type: 'success'
          })
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
