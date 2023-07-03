<template>
  <el-dialog
    title="订单审核"
    :visible.sync="dialogVisible"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    width="40%"
    center
    @close="dialogFormVisible"
  >
    <p class="red pb16">当前买家已付款并申请取消订单，请审核是否同意，如同意则自动退回付款金额（微信支付原路退款）并关闭订单。</p>
    <el-form ref="form" size="small" :model="form" label-width="80px" label-position="left">
      <el-form-item label="审核状态">
        <el-radio v-model="form.is_cancel" :label="1">同意</el-radio>
        <el-radio v-model="form.is_cancel" :label="0">拒绝</el-radio>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button size="small" @click="dialogVisible = false">{{ $t('page.cancel') }}</el-button>
      <el-button :loading="loading" size="small" type="primary" @click="submitClick">{{ $t('page.sure') }}</el-button>
    </div>
  </el-dialog>
</template>

<script>
import OrderApi from '@/api/order.js'

export default {
  // eslint-disable-next-line vue/prop-name-casing,vue/require-prop-types
  props: ['order_id'],
  data() {
    return {
      /* 是否显示 */
      dialogVisible: true,
      loading: false,
      /* 表单数据 */
      form: {
        is_cancel: 1
      },
      /* 是否提交确认审核 */
      isConfirmCancel: false
    }
  },
  created() {
  },
  methods: {
    /** 提交 **/
    submitClick() {
      const self = this
      // 是否提交确认审核
      if (self.isConfirmCancel) {
        return false
      }
      self.isConfirmCancel = true
      self.loading = true
      OrderApi.confirm({ order_id: self.order_id, ...self.form }, true)
        .then(data => {
          self.isConfirmCancel = false
          self.loading = false
          self.$message({
            message: '恭喜你，审核成功',
            type: 'success'
          })
          self.dialogFormVisible(true)
        })
        .catch(() => {
          self.isConfirmCancel = false
          self.loading = false
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
