<template>
  <el-dialog title="订单价格修改" :visible.sync="dialogVisible" :close-on-click-modal="false" :close-on-press-escape="false" width="30%" @close="dialogFormVisible">
    <el-form ref="order" size="small" :model="order">
      <el-form-item label="订单金额" :label-width="formLabelWidth" prop="update_price" :rules="[{required: true,message: ' '}]">
        <el-input v-model="order.update_price" type="number" autocomplete="off" />
      </el-form-item>
      <el-form-item label="运费金额" :label-width="formLabelWidth" prop="update_express_price" :rules="[{required: true,message: ' '}]">
        <el-input v-model="order.update_express_price" type="number" autocomplete="off" />
        <p>最终付款价 = 订单金额 + 运费金额</p>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible">取 消</el-button>
      <el-button :loading="loading" type="primary" @click="submitFunc()">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
import OrderApi from '@/api/order.js'

export default {
  // eslint-disable-next-line vue/prop-name-casing,vue/require-prop-types
  props: ['open_edit'],
  data() {
    return {
      order_id: 0,
      loading: false,
      /* 左边长度 */
      formLabelWidth: '100px',
      /* 是否显示 */
      dialogVisible: true,
      /* 表单 */
      order: {
        update_price: 0,
        update_express_price: 0.00
      }
    }
  },
  created() {
    this.order_id = this.$route.query.order_id
    this.getData()
  },
  methods: {
    /** 获取数据 **/
    getData() {
      const self = this
      OrderApi.orderdetail({
        order_id: this.order_id
      }).then(data => {
        self.order.update_price = data.data.detail.order_price
        self.order.update_express_price = data.data.detail.express_price
        self.loading = false
      }).catch(() => {
        self.loading = false
      })
    },
    /** 确认事件 **/
    submitFunc(e) {
      const self = this
      const order = this.order
      self.$refs.order.validate((valid) => {
        if (valid) {
          self.loading = true
          OrderApi.updatePrice({
            order_id: this.order_id,
            order: order
          }).then(data => {
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
    /** 关闭弹窗 **/
    dialogFormVisible() {
      this.$emit('close', { openDialog: false })
    }
  }
}
</script>
