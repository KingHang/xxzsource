<template>
  <el-dialog
    title="添加等级"
    :visible.sync="dialogVisible"
    width="600px"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="dialogFormVisible"
  >
    <el-form ref="form" size="small" :model="form">
      <el-form-item label="等级名称" :label-width="formLabelWidth" prop="name" :rules="[{required: true,message: '请输入等级名称'}]">
        <el-input v-model="form.name" placeholder="请输入等级名称" />
      </el-form-item>

      <el-form-item label="等级权重" :label-width="formLabelWidth" prop="weight" :rules="[{required: true,message: '请输入等级权重'}]">
        <el-input v-model="form.weight" type="number" placeholder="请输入等级权重" />
        <div class="gray9">权重越大，等级越高</div>
      </el-form-item>

      <el-form-item label="等级折扣" :label-width="formLabelWidth" prop="equity" :rules="[{required: true,message: '请输入等级折扣'}]">
        <el-input v-model="form.equity" type="number" :precision="1" :step="1" :min="0" :max="100" placeholder="请输入等级折扣">
          <template slot="append">%</template>
        </el-input>
      </el-form-item>

      <el-form-item label="升级条件" :label-width="formLabelWidth">
        <div class="gray9">满足以下勾选的其中一个条件，会员自动升级到该等级</div>
        <div class="d-s-c mt16">
          <el-checkbox v-model="form.open_money">累计消费满</el-checkbox>
          <el-input v-model="form.upgrade_money" type="number" :disabled="form.open_money === 0" style="width: 160px; margin-left: 10px;" />
          <span class="ml10">元</span>
        </div>
        <div class="d-s-c mt16">
          <el-checkbox v-model="form.open_points">累计积分满</el-checkbox>
          <el-input v-model="form.upgrade_points" type="number" :disabled="form.open_points === 0" style="width: 160px;margin-left: 10px;" />
          <span class="ml10">个</span>
        </div>
        <div class="d-s-c mt16">
          <el-checkbox v-model="form.open_grow">成长值满</el-checkbox>
          <el-input v-model="form.upgrade_grow" type="number" :disabled="form.open_grow === 0" style="width: 160px;margin-left: 10px;" />
          <span class="ml10">个</span>
        </div>
        <div class="d-s-c mt16">
          <el-checkbox v-model="form.open_invite">推荐人数满</el-checkbox>
          <el-input v-model="form.upgrade_invite" type="number" :disabled="form.open_invite === 0" style="width: 160px;margin-left: 10px;" />
          <span class="ml10">人</span>
        </div>
      </el-form-item>
    </el-form>

    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible">取 消</el-button>
      <el-button type="primary" :disabled="submit_loading" @click="addGrade()">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
import UserApi from '@/api/user.js'

export default {
  // eslint-disable-next-line vue/prop-name-casing,vue/require-prop-types
  props: ['open_add'],
  data() {
    return {
      form: {
        /* 昵称 */
        name: '',
        /* 等级折扣 */
        equity: '',
        /* 是否打开累计消费满 */
        open_money: 0,
        /* 累计消费满 */
        upgrade_money: 0,
        /* 是否打开累计积分满 */
        open_points: 0,
        /* 累计积分满 */
        upgrade_points: 0,
        /* 是否打开成长值满 */
        open_grow: 0,
        /* 累计积分满 */
        upgrade_grow: 0,
        /* 是否推荐人数满 */
        open_invite: 0,
        /* 推荐人数满 */
        upgrade_invite: 0,
        /* 等级权重 */
        weight: 100
      },
      /* 左边长度 */
      formLabelWidth: '80px',
      /* 是否显示 */
      dialogVisible: false,
      /* 是否正在提交 */
      submit_loading: false
    }
  },
  created() {
    this.dialogVisible = this.open_add
  },
  methods: {
    /** 添加等级 **/
    addGrade() {
      const self = this
      const params = this.form
      self.$refs.form.validate((valid) => {
        if (valid) {
          self.submit_loading = true
          params.open_money = params.open_money === true ? 1 : 0
          params.open_points = params.open_points === true ? 1 : 0
          params.open_grow = params.open_grow === true ? 1 : 0
          params.open_invite = params.open_invite === true ? 1 : 0
          UserApi.addgrade(params, true).then(data => {
            self.submit_loading = false
            self.$message({
              message: data.msg,
              type: 'success'
            })
            self.dialogFormVisible(true)
          }).catch(() => {
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
