<template>
  <el-dialog
    title="编辑等级"
    :visible.sync="dialogVisible"
    width="600px"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="dialogFormVisible"
  >
    <el-form ref="form" size="small" :model="form">
      <el-form-item label="标签名称" :label-width="formLabelWidth" prop="tag_name" :rules="[{required: true,message: ' '}]">
        <el-input v-model="form.tag_name" autocomplete="off" />
      </el-form-item>
    </el-form>

    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible">取 消</el-button>
      <el-button type="primary" :disabled="submit_loading" @click="editGrade">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
import TagApi from '@/api/tag.js'

export default {
  // eslint-disable-next-line vue/prop-name-casing,vue/require-prop-types
  props: ['open_edit', 'form'],
  data() {
    return {
      /* 左边长度 */
      formLabelWidth: '120px',
      /* 是否显示 */
      dialogVisible: false,
      /* 是否正在提交 */
      submit_loading: false
    }
  },
  created() {
    this.dialogVisible = this.open_edit
  },
  methods: {
    /** 修改等级 **/
    editGrade() {
      const self = this
      const params = this.form
      self.$refs.form.validate((valid) => {
        if (valid) {
          self.submit_loading = true
          TagApi.editTag(params, true)
            .then(data => {
              self.submit_loading = false
              self.$message({
                message: '恭喜你，修改成功',
                type: 'success'
              })
              self.dialogFormVisible(true)
            })
            .catch(() => {
              self.submit_loading = false
            })
        }
      })
    },
    /** 关闭弹窗 **/
    dialogFormVisible(e) {
      if (e) {
        this.$emit('closeDialog', {
          type: 'success',
          openDialog: false
        })
      } else {
        this.$emit('closeDialog', {
          type: 'error',
          openDialog: false
        })
      }
    }
  }
}
</script>
