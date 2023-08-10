<template>
  <el-dialog
    title="版本升级"
    :visible.sync="dialogVisible"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="dialogFormVisible"
  >
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" :label-width="formLabelWidth">
      <el-form-item label="版本号" prop="version">
        <el-input v-model="form.version" placeholder="请输入版本号" />
      </el-form-item>

      <el-form-item label="热更新包下载地址" prop="wgt_url">
        <el-input v-model="form.wgt_url" />
      </el-form-item>

      <el-form-item label="安卓整包升级地址" prop="pkg_url_android">
        <el-input v-model="form.pkg_url_android" />
      </el-form-item>

      <el-form-item label="ios整包升级地址" prop="pkg_url_ios">
        <el-input v-model="form.pkg_url_ios" />
      </el-form-item>
    </el-form>

    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogVisible = false">取 消</el-button>
      <el-button type="primary" :loading="loading" @click="onSubmit">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
import AppApi from '@/api/app.js'

export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['open'],
  data() {
    return {
      /* 左边长度 */
      formLabelWidth: '140px',
      /* 是否显示 */
      loading: false,
      /* 是否显示 */
      dialogVisible: false,
      /* form表单对象 */
      form: {
        version: '',
        wgt_url: '',
        pkg_url_android: '',
        pkg_url_ios: ''
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
      AppApi.addAppUpdate(params, true)
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
