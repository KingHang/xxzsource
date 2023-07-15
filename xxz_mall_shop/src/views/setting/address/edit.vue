<template>
  <div class="product-add">
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" label-width="200px">
      <!--修改物流公司-->
      <div class="common-form">修改物流公司</div>

      <el-form-item label="收货人姓名 " prop="name" :rules="[{required: true,message: ' '}]">
        <el-input v-model="form.name" class="max-w460" />
      </el-form-item>

      <el-form-item label="联系电话" prop="phone" :rules="[{required: true,message: ' '}]">
        <el-input v-model="form.phone" class="max-w460" />
      </el-form-item>

      <el-form-item label="详细地址" prop="detail" :rules="[{required: true,message: ' '}]">
        <el-input v-model="form.detail" class="max-w460" />
      </el-form-item>

      <el-form-item label="排序">
        <el-input v-model="form.sort" type="number" class="max-w460" />
        <div class="tips">数字越小越靠前</div>
      </el-form-item>

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" type="primary" :loading="loading" @click="onSubmit()">提交</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
import SettingApi from '@/api/setting.js'

export default {
  data() {
    return {
      loading: false,
      /* form表单数据 */
      form: {
        address_id: '',
        name: '',
        phone: '',
        sort: 1,
        detail: ''
      }
    }
  },
  created() {
    this.getData()
  },
  methods: {
    getData() {
      const self = this
      // 取到路由带过来的参数
      const params = this.$route.query.address_id
      SettingApi.addressDetail({
        address_id: params
      }, true).then(data => {
        const detail = data.data.detail
        // 将数据放在当前组件的数据内
        self.form.address_id = detail.address_id
        self.form.name = detail.name
        self.form.phone = detail.phone
        self.form.detail = detail.detail
        self.form.sort = detail.sort
      }).catch(() => {
      })
    },
    /** 提交表单 **/
    onSubmit() {
      const self = this
      const form = self.form
      self.$refs.form.validate((valid) => {
        if (valid) {
          self.loading = true
          SettingApi.editAddress(form, true)
            .then(data => {
              self.loading = false
              self.$message({
                message: '恭喜你，修改成功',
                type: 'success'
              })
              self.$router.push('/setting/address/index')
            }).catch(() => {
              self.loading = false
            })
        }
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
