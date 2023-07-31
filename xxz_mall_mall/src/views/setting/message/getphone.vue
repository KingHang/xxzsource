<template>
  <div class="pt30">
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" label-width="200px">
      <el-form-item label="获取手机号">
        <el-checkbox-group v-model="form.checkedCities">
          <el-checkbox v-for="(item, index) in all_type" :key="index" :label="item.value">{{ item.name }}</el-checkbox>
        </el-checkbox-group>
      </el-form-item>

      <el-form-item label="拒绝后" prop="send_day">
        <div style="width: 500px;">
          <el-input v-model="form.send_day" placeholder="请输入" type="number">
            <template slot="append">
              天不再提醒
            </template>
          </el-input>
          <p class="gray">拒绝获取后多久再提示，设置为0则每次都要提醒</p>
        </div>
      </el-form-item>

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" type="primary" :loading="loading" @click="onSubmit">提交</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
import SettingApi from '@/api/setting.js'

export default {
  data() {
    return {
      /* 是否正在加载 */
      loading: false,
      /* 订单 */
      form: {
        checkedCities: [],
        send_day: 7
      },
      all_type: []
    }
  },
  created() {
    this.getParams()
  },
  methods: {
    /** 获取配置数据 **/
    getParams() {
      const self = this
      SettingApi.getPhoneDetail({}, true).then(res => {
        const vars = res.data.vars.values
        self.form.checkedCities = vars.area_type
        // 转成整数，兼容组件
        for (let i = 0; i < self.form.checkedCities.length; i++) {
          self.$set(self.form.checkedCities, i, parseInt(self.form.checkedCities[i]))
        }
        self.form.send_day = vars.send_day
        self.all_type = res.data.all_type
        self.loading = false
      }).catch(() => {
      })
    },
    /** 提交 **/
    onSubmit() {
      const self = this
      const params = this.form
      self.loading = true
      SettingApi.editGetPhone(params, true)
        .then(data => {
          self.loading = false
          self.$message({
            message: '恭喜你，设置成功',
            type: 'success'
          })
        })
        .catch(() => {
          self.loading = false
        })
    }
  }
}
</script>
