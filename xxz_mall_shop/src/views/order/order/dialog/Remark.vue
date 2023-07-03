<template>
  <el-dialog title="订单备注" :visible.sync="dialogVisible" :close-on-click-modal="false" :close-on-press-escape="false" @close="dialogFormVisible">
    <el-form ref="form" size="small" :model="form" label-width="80px">
      <el-form-item label="备注" :rules="[{ required: true, message: '请填写订单备注' }]" prop="order_remark">
        <el-input v-model="form.order_remark" type="textarea" placeholder="请填写订单备注" rows="6" maxlength="255" show-word-limit />
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button size="small" @click="dialogVisible = false">{{ $t('page.cancel') }}</el-button>
      <el-button :loading="loading" size="small" type="primary" @click="submitClick">{{ $t('page.save') }}</el-button>
    </div>
  </el-dialog>
</template>

<script>
import OrderApi from '@/api/order.js'

export default {
  // eslint-disable-next-line vue/prop-name-casing,vue/require-prop-types
  props: ['order_id', 'order_remark'],
  data() {
    return {
      /* 是否显示 */
      dialogVisible: true,
      loading: false,
      /* 表单数据 */
      form: {
        order_remark: ''
      }
    }
  },
  created() {
    this.form.order_remark = this.order_remark
  },
  methods: {
    /** 提交 **/
    submitClick() {
      const self = this
      self.$refs.form.validate(valid => {
        if (valid) {
          self.loading = true
          OrderApi.orderRemark({ order_id: self.order_id, ...self.form }, true)
            .then(data => {
              self.loading = false
              self.$message({
                message: self.$t('msg.success'),
                type: 'success',
                onClose: () => {
                  location.reload()
                }
              })
            })
            .catch(() => {
              self.loading = false
            })
        }
      })
    },
    /** 关闭弹窗 **/
    dialogFormVisible(e) {
      this.$emit('close', {
        type: e ? 'success' : 'error',
        openDialog: false
      })
    }
  }
}
</script>
