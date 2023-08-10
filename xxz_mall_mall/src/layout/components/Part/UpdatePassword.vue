<template>
  <el-dialog
    title="修改密码"
    :visible.sync="dialogVisible"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    width="30%"
    @close="dialogFormVisible"
  >
    <el-form ref="form" size="small" :model="form">
      <div style="display: block; opacity: 0; width: 0; height: 0; overflow: hidden;">
        <el-input type="text" autocomplete="off" />
        <el-input type="password" autocomplete="off" />
      </div>

      <el-form-item label="手机号" :label-width="formLabelWidth" prop="mobile" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.mobile" type="text" maxlength="11" autocomplete="off" />
      </el-form-item>

      <el-form-item label="验证码" :label-width="formLabelWidth" prop="code" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.code" type="text" maxlength="6" autocomplete="off">
          <el-button slot="append" :disabled="disabled" @click.native.prevent="getCode">{{ valiBtn }}</el-button>
        </el-input>
      </el-form-item>

      <el-form-item label="新密码" :label-width="formLabelWidth" prop="password" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.password" type="password" autocomplete="off" />
      </el-form-item>

      <el-form-item label="确认新密码" :label-width="formLabelWidth" prop="confirmPass" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.confirmPass" type="password" autocomplete="off" />
      </el-form-item>
    </el-form>

    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible">取 消</el-button>
      <el-button type="primary" :loading="loading" @click="submitFunc(form.user_id)">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
import UserApi from '@/api/user.js'

export default {
  props: [],
  data() {
    return {
      loading: false,
      /* 左边长度 */
      formLabelWidth: '100px',
      /* 是否显示 */
      dialogVisible: true,
      /* 表单 */
      form: {
        mobile: '',
        code: '',
        password: '',
        confirmPass: ''
      },
      valiBtn: '获取验证码',
      disabled: false
    }
  },
  created() {},
  methods: {
    /** 获取验证码 **/
    getCode() {
      const reg = 11 && /^((13|14|15|17|18)[0-9]{1}\d{8})$/
      if (this.form.mobile === '') {
        this.$message({
          message: '请输入手机号',
          type: 'warning'
        })
      } else if (!reg.test(this.form.mobile)) {
        this.$message({
          message: '手机号不合法',
          type: 'warning'
        })
      } else {
        UserApi.sendcode({ mobile: this.form.mobile, type: 3, isLogin: 1 }, true).then((res) => {
          if (res.code === 1) {
            this.tackBtn() // 验证码倒数60秒
            this.$message({
              message: '获取验证码成功',
              type: 'success'
            })
          } else {
            this.$message({
              message: '获取验证码失败',
              type: 'error'
            })
          }
        }).catch(() => {
        })
      }
    },
    /** 验证码倒数60秒 **/
    tackBtn() {
      let time = 60
      const timer = setInterval(() => {
        if (time === 0) {
          clearInterval(timer)
          this.valiBtn = '获取验证码'
          this.disabled = false
        } else {
          this.disabled = true
          this.valiBtn = time + '秒后重试'
          time--
        }
      }, 1000)
    },
    /** 确认事件 **/
    submitFunc(e) {
      const self = this
      const reg = 11 && /^((13|14|15|17|18)[0-9]{1}\d{8})$/
      if (self.form.mobile === '') {
        self.$message({
          message: '请输入手机号',
          type: 'warning'
        })
      } else if (!reg.test(self.form.mobile)) {
        self.$message({
          message: '手机号不合法',
          type: 'warning'
        })
      } else if (self.form.code === '') {
        self.$message({
          message: '请输入验证码',
          type: 'warning'
        })
      } else if (self.form.password === '') {
        self.$message({
          message: '请输入新密码',
          type: 'warning'
        })
      } else if (self.form.password !== self.form.confirmPass) {
        self.$message({
          message: '两次密码不相同，请重新输入',
          type: 'warning'
        })
      } else {
        const form = self.form
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true
            UserApi.EditPass(form, true).then(data => {
              self.loading = false
              if (data.code === 1) {
                self.$message({
                  message: data.msg,
                  type: 'success'
                })
                self.dialogFormVisible(true)
              } else {
                self.loading = false
              }
            }).catch(() => {
              self.loading = false
            })
          }
        })
      }
    },
    /** 关闭弹窗 **/
    dialogFormVisible() {
      this.$emit('close', false)
    }
  }
}
</script>
