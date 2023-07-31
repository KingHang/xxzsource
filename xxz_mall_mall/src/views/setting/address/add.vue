<template>
  <div class="product-add">
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" label-width="200px">
      <!--新增退货地址-->
      <div class="common-form">新增退货地址</div>

      <el-form-item label="收货人姓名" prop="name" :rules="[{required: true,message: ' '}]">
        <el-input v-model="form.name" type="text" class="max-w460" prop="name" />
      </el-form-item>

      <el-form-item label="联系电话" prop="phone" :rules="[{required: true,message: ' '}]">
        <el-input v-model="form.phone" type="text" class="max-w460" />
      </el-form-item>

      <el-form-item label="详细地址" prop="detail" :rules="[{required: true,message: ' '}]">
        <el-input v-model="form.detail" type="text" class="max-w460" />
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
      /* 切换菜单 */
      // activeIndex: '1',
      /* form表单数据 */
      form: {
        name: '',
        phone: '',
        detail: '',
        sort: 1
      }
    }
  },
  created() {},
  methods: {
    /** 提交表单 **/
    onSubmit() {
      const self = this
      const form = self.form
      self.$refs.form.validate((valid) => {
        if (valid) {
          self.loading = true
          SettingApi.addAddress(form, true)
            .then(data => {
              self.loading = false
              self.$message({
                message: '恭喜你，添加成功',
                type: 'success'
              })
              self.$router.push('/setting/address/index')
            })
            .catch(() => {
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
