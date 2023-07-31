<template>
  <el-dialog
    title="修改分类"
    :visible.sync="dialogVisible"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="dialogFormVisible"
  >
    <el-form ref="form" size="small" :model="form" :rules="formRules">
      <el-form-item label="所属分类" :label-width="formLabelWidth">
        <el-select v-model="form.parent_id">
          <el-option label="顶级分类" :value="0" />
          <el-option v-for="cat in editform.catList" :key="cat.category_id" :value="cat.category_id" :label="cat.name" />
        </el-select>
      </el-form-item>

      <el-form-item label="分类名称" prop="name" :label-width="formLabelWidth">
        <el-input v-model="form.name" autocomplete="off" />
      </el-form-item>

      <el-form-item label="分类图片" prop="image_id" :label-width="formLabelWidth">
        <el-row>
          <el-button type="primary" @click="openUpload">选择图片</el-button>
          <div v-if="form.image_id !== ''" class="img">
            <img :src="file_path" width="100" height="100">
          </div>
        </el-row>
      </el-form-item>

      <el-form-item label="分类排序" prop="sort" :label-width="formLabelWidth">
        <el-input v-model.number="form.sort" autocomplete="off" />
      </el-form-item>

      <el-form-item label="分类状态" prop="disabled" :label-width="formLabelWidth">
        <el-radio-group v-model="form.disabled">
          <el-radio :label="0">显示</el-radio>
          <el-radio :label="1">隐藏</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="参数名" prop="params" :label-width="formLabelWidth">
        <el-input v-model="form.params" autocomplete="off" />
      </el-form-item>
    </el-form>

    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible">取 消</el-button>
      <el-button type="primary" :loading="loading" @click="editCate">确 定</el-button>
    </div>

    <!--上传图片组件-->
    <Upload v-if="isupload" :isupload="isupload" :type="type" @returnImgs="returnImgsFunc">上传图片</Upload>
  </el-dialog>
</template>

<script>
import GoodsApi from '@/api/goods.js'
import Upload from '@/components/file/Upload'

export default {
  components: {
    Upload
  },
  props: {
    openEdit: {
      type: Boolean,
      default: true
    },
    editform: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      form: {
        category_id: 0,
        parent_id: 0,
        name: '',
        image_id: '',
        sort: '',
        disabled: 0,
        params: ''
      },
      file_path: '',
      formRules: {
        name: [{
          required: true,
          message: '请输入分类名称',
          trigger: 'blur'
        }],
        image_id: [{
          required: true,
          message: '请上传分类图片',
          trigger: 'blur'
        }],
        sort: [{
          required: true,
          message: '分类排序不能为空'
        }, {
          type: 'number',
          message: '分类排序必须为数字'
        }]
      },
      /* 左边长度 */
      formLabelWidth: '120px',
      /* 是否显示 */
      dialogVisible: false,
      loading: false,
      /* 是否上传图片 */
      isupload: false
    }
  },
  created() {
    this.dialogVisible = this.openEdit
    this.form.category_id = this.editform.model.category_id
    this.form.parent_id = this.editform.model.parent_id
    this.form.name = this.editform.model.name
    this.form.image_id = this.editform.model.image_id
    this.file_path = this.editform.model.images.file_path
    this.form.sort = this.editform.model.sort
    this.form.disabled = this.editform.model.disabled
    this.form.params = this.editform.model.params ? this.editform.model.params : ''
  },
  methods: {
    /** 修改分类 **/
    editCate() {
      const self = this
      const params = self.form
      self.$refs.form.validate((valid) => {
        if (valid) {
          self.loading = true
          GoodsApi.catEdit(params, true).then(data => {
            self.loading = false
            self.$message({
              message: '修改成功',
              type: 'success'
            })
            self.dialogFormVisible(true)
          }).catch(() => {
            self.loading = false
          })
        }
      })
    },
    getImage(category_id) {
      GoodsApi.cateImage({
        category_id: category_id
      }, true).then(data => {
        this.form.image_id = data.data.detail.image_id
        this.file_path = data.data.detail.image.file_path
      }).catch(() => {
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
    },
    /** 上传 **/
    openUpload(e) {
      this.type = e
      this.isupload = true
    },
    /** 获取图片 **/
    returnImgsFunc(e) {
      if (e != null && e.length > 0) {
        this.file_path = e[0].file_path
        this.form.image_id = e[0].file_id
      }
      this.isupload = false
    }
  }
}
</script>

<style>
  .img {
    margin-top: 10px;
  }
</style>
