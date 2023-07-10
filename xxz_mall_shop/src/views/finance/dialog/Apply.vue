<template>
  <el-dialog
    title="申请提现"
    :visible.sync="dialogVisible"
    width="30%"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="dialogFormVisible"
  >
    <el-form ref="order" size="small" :model="form">
      <el-form-item label="提现金额" :label-width="formLabelWidth" prop="money" :rules="[{ required: true, message: '请输入提现金额' }]">
        <el-input v-model="form.money" type="number" autocomplete="off" />
      </el-form-item>

      <el-form-item label="打款方式" :label-width="formLabelWidth" prop="pay_type" :rules="[{ required: true, message: '请输入打款方式' }]">
        <el-radio v-model="form.pay_type" :label="10">支付宝</el-radio>
        <el-radio v-model="form.pay_type" :label="20">银行卡</el-radio>
        <el-radio v-model="form.pay_type" :label="30">微信</el-radio>
      </el-form-item>
    </el-form>

    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible">取 消</el-button>
      <el-button type="primary" :loading="loading" @click="submitFunc()">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
export default {
  data() {
    return {
      order_id: 0,
      loading: false,
      /* 左边长度 */
      formLabelWidth: '100px',
      /* 是否显示 */
      dialogVisible: true,
      /* 表单 */
      form: {
        money: '',
        pay_type: 10
      }
    }
  },
  created() {},
  methods: {
    /** 确认事件 **/
    submitFunc(e) {
      const self = this
      self.$refs.order.validate(valid => {
        if (valid) {
          self.loading = true
          // FinanceApi
          //   .apply(self.form, true)
          //   .then(data => {
          //     self.loading = false
          //     self.$message({
          //       message: '修改成功',
          //       type: 'success'
          //     })
          //     self.dialogFormVisible(true)
          //   })
          //   .catch(() => {
          //     self.loading = false
          //   })
        }
      })
    },
    /** 关闭弹窗 **/
    dialogFormVisible() {
      this.$emit('close', { openDialog: false })
    }
  }
}
</script>
