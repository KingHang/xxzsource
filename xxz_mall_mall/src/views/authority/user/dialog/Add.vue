<template>
  <el-dialog
    title="会员等级"
    :visible.sync="dialogVisible"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="dialogFormVisible"
  >
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" :rules="formRules" :label-width="formLabelWidth">
      <el-form-item label="用户名" prop="user_name">
        <el-input v-model="form.user_name" placeholder="请输入用户名" />
      </el-form-item>

      <el-form-item label="所属角色" prop="role_id">
        <el-select v-model="form.role_id" :multiple="true">
          <el-option v-for="item in roleList" :key="item.role_id" :label="item.role_name_h1" :value="item.role_id" />
        </el-select>
      </el-form-item>

      <el-form-item label="登录密码" prop="password">
        <el-input v-model="form.password" placeholder="请输入登录密码" type="password" />
      </el-form-item>

      <el-form-item label="确认密码" prop="confirm_password">
        <el-input v-model="form.confirm_password" placeholder="请输入确认密码" type="password" />
      </el-form-item>

      <el-form-item label="姓名" prop="real_name">
        <el-input v-model="form.real_name" />
      </el-form-item>
    </el-form>

    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogVisible = false">取 消</el-button>
      <el-button type="primary" :loading="loading" @click="onSubmit">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
import AuthorityApi from '@/api/authority.js'

export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['open', 'roleList'],
  data() {
    return {
      /* 左边长度 */
      formLabelWidth: '120px',
      /* 是否显示 */
      loading: false,
      /* 是否显示 */
      dialogVisible: false,
      /* form表单对象 */
      form: {
        user_name: '',
        access_id: []
      },
      /* form验证 */
      formRules: {
        user_name: [
          {
            required: true,
            message: ' ',
            trigger: 'blur'
          }
        ],
        role_id: [
          {
            required: true,
            message: ' ',
            trigger: 'blur'
          }
        ],
        password: [
          {
            required: true,
            message: ' ',
            trigger: 'blur'
          }
        ],
        confirm_password: [
          {
            required: true,
            message: ' ',
            trigger: 'blur'
          }
        ],
        real_name: [
          {
            required: true,
            message: ' ',
            trigger: 'blur'
          }
        ]
      }
    }
  },
  watch: {
    open: function(n, o) {
      if (n !== o) {
        this.dialogVisible = this.open
      }
    }
  },
  created() {},
  methods: {
    /** 添加 **/
    onSubmit() {
      const self = this
      self.loading = true
      const params = self.form
      AuthorityApi.userAdd(params, true)
        .then(data => {
          self.loading = false
          self.$message({
            message: '恭喜你，添加成功',
            type: 'success'
          })
          self.dialogFormVisible(true)
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 关闭弹窗 **/
    dialogFormVisible(e) {
      if (e) {
        this.$emit('close', {
          type: 'success',
          openDialog: false
        })
      } else {
        this.$emit('close', {
          type: 'error',
          openDialog: false
        })
      }
    }
  }
}
</script>
