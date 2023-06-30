<template>
  <el-dialog
    title="添加分类"
    :visible.sync="dialogVisible"
    width="30%"
    :before-close="handleClose"
    :append-to-body="true"
  >
    <el-form ref="form" size="small" :model="form" label-width="100px" class="demo-ruleForm">
      <el-form-item label="分类名称" prop="categoryname" :rules="[{ required: true, message: '名字不能为空'}]">
        <el-input v-model="form.categoryname" type="age" autocomplete="off" />
      </el-form-item>

      <el-form-item>
        <el-button size="small" @click="handleClose">取消</el-button>
        <el-button size="small" type="primary" @click="submitForm('form')">提交</el-button>
      </el-form-item>
    </el-form>
  </el-dialog>
</template>

<script>
import FileApi from '@/api/file.js'

export default {
  /* eslint-disable vue/require-prop-types */
  props: ['category', 'fileType'],
  data() {
    return {
      /* 是否显示 */
      dialogVisible: true,
      /* from表单模型 */
      form: {
        categoryname: ''
      },
      /* 分类名称 */
      categoryName: ''
    }
  },
  created() {
    if (this.category != null) {
      this.form.categoryname = this.category.group_name
      this.form.group_id = this.category.group_id
    }
  },
  methods: {
    /** 添加图片类别 **/
    addCategory(categoryname) {
      const self = this
      FileApi.addCategory({
        group_name: categoryname,
        group_type: self.fileType
      }).then(data => {
        self.$message({
          message: '添加成功',
          type: 'success'
        })
        self.handleClose({ status: 'success' })
      }).catch(() => {
        self.$message.error('添加失败')
      })
    },
    /** 修改图片类别 **/
    editCategory(model) {
      const self = this
      const param = {
        group_name: model.categoryname,
        group_id: model.group_id
      }
      FileApi.editCategory(param).then(data => {
        self.$message({
          message: '修改成功',
          type: 'success'
        })
        self.handleClose({ status: 'success' })
      }).catch(() => {
        self.$message.error('修改失败')
      })
    },
    /** 提交 **/
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if (this.category && this.category.group_id != null) {
            this.editCategory(this.form)
          } else {
            this.addCategory(this.form.categoryname)
          }
        } else {
          return false
        }
      })
    },
    /** 关闭弹窗 **/
    handleClose(param) {
      this.dialogVisible = false
      this.$emit('closeCategory', param)
    }
  }
}
</script>
