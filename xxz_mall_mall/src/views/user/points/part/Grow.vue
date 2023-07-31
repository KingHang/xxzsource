<template>
  <div v-if="!loading" class="pb50">
    <el-form ref="form" size="small" :model="form" label-width="200px">
      <div class="open-bg">
        <span>是否启用成长值</span>
        <el-switch v-model="form.is_grow" :active-value="1" :inactive-value="0" class="mg-left10" />
      </div>

      <div class="common-form">成长值设置</div>

      <el-form-item label="成长值名称" prop="growth_name" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.growth_name" autocomplete="off" class="max-w460" />
        <div class="lh18 mt10 gray9">
          <p>注：修改成长值名称后，在买家端的所有页面里，看到的都是自定义的名称</p>
          <p>成长值不清零，以此作为会员等级依据</p>
        </div>
      </el-form-item>

      <el-form-item label="成长值说明" prop="describe" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.describe" type="textarea" rows="5" autocomplete="off" placeholder="请输入成长值说明" />
      </el-form-item>

      <div class="common-form">成长值赠送</div>

      <el-form-item label="是否开启购物送成长值" prop="is_shopping_gift">
        <el-radio-group v-model="form.is_shopping_gift">
          <el-radio :label="1">开启</el-radio>
          <el-radio :label="0">关闭</el-radio>
        </el-radio-group>
        <div class="lh18 mt10 gray9">
          <p>注：如开启则订单完成后赠送用户成长值</p>
          <p>成长值赠送规则：1.订单确认收货已完成；2.已完成订单超出后台设置的申请售后期限</p>
        </div>
      </el-form-item>

      <el-form-item label="成长值赠送比例" prop="gift_ratio" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.gift_ratio" placeholder="请输入内容" class="max-w460">
          <template slot="append">%</template>
        </el-input>
        <div class="lh18 mt10 gray9">
          <p>注：赠送比例请填写数字0~100；订单的运费不参与成长值赠送</p>
          <p>例：订单付款金额(100.00元) * 成长值赠送比例(100%) = 实际赠送的成长值(100成长值)</p>
        </div>
      </el-form-item>

      <el-form-item label="注册会员赠送成长值" prop="is_register">
        <el-radio-group v-model="form.is_register">
          <div class="radio-style">
            <el-radio :label="1">
              <span class="mg-right10 lineHeight32">注册成功后每人赠送成长值</span>
              <el-input v-model="form.register_grow" type="number" autocomplete="off" min="0" class="w200">
                <template slot="append">个</template>
              </el-input>
            </el-radio>
          </div>
          <div class="radio-style">
            <el-radio :label="0">关闭</el-radio>
          </div>
        </el-radio-group>
        <div class="lh18 mt10 gray9">
          <p>新用户绑定手机号且成功注册后赠送，每位用户限获得1次</p>
        </div>
      </el-form-item>

      <el-form-item label="邀请会员赠送成长值" prop="is_invite">
        <el-radio-group v-model="form.is_invite">
          <div class="radio-style">
            <el-radio :label="1">
              <span class="mg-right10 lineHeight32">邀请注册成功后每人赠送成长值</span>
              <el-input v-model="form.invite_grow" type="number" autocomplete="off" min="0" class="w200">
                <template slot="append">个</template>
              </el-input>
            </el-radio>
          </div>
          <div class="radio-style">
            <el-radio :label="0">关闭</el-radio>
          </div>
        </el-radio-group>
        <div class="lh18 mt10 gray9">
          <p>邀请新用户绑定手机号且成功注册后赠送，每邀请一位新用户限获得1次</p>
        </div>
      </el-form-item>

      <el-form-item label="签到赠送成长值" prop="is_sign">
        <el-radio-group v-model="form.is_sign">
          <el-radio :label="1">开启</el-radio>
          <el-radio :label="0">关闭</el-radio>
        </el-radio-group>
        <div class="lh18 mt10 gray9">
          <p>开启后可在促销插件-签到有礼-签到设置中编辑奖励<el-link type="primary" class="mg-left10" @click="goPage(1)">前往编辑</el-link></p>
        </div>
      </el-form-item>

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button type="primary" size="small" :loading="saveLoading" @click="onSubmit">提交</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
import PointsApi from '@/api/points.js'

export default {
  data() {
    return {
      /* 加载数据 */
      loading: true,
      /* 保存数据 */
      saveLoading: false,
      form: {
        is_grow: 1,
        growth_name: '',
        describe: '',
        is_shopping_gift: 1,
        gift_ratio: 10,
        is_register: 1,
        register_grow: 0,
        is_invite: 1,
        invite_grow: 0,
        is_sign: 1
      }
    }
  },
  created() {
    /* 获取数据 */
    this.getData()
  },
  methods: {
    /** 获取数据 **/
    getData() {
      const self = this
      const Params = {}
      PointsApi.getGrow(Params, true).then(data => {
        self.form = data.data.values
        self.form.is_grow = parseInt(data.data.values.is_grow)
        self.form.is_shopping_gift = parseInt(data.data.values.is_shopping_gift)
        self.form.is_register = parseInt(data.data.values.is_register)
        self.form.is_invite = parseInt(data.data.values.is_invite)
        self.form.is_sign = parseInt(data.data.values.is_sign)
        self.loading = false
      }).catch(() => {
        self.loading = false
      })
    },
    /** 保存 **/
    onSubmit() {
      const self = this
      const form = self.form
      self.$refs.form.validate((valid) => {
        if (valid) {
          self.saveLoading = true
          PointsApi.setGrow(form, true)
            .then(data => {
              self.saveLoading = false
              if (data.code === 1) {
                self.$message({
                  message: '恭喜你，保存成功',
                  type: 'success'
                })
              }
            })
            .catch(() => {
              self.saveLoading = false
            })
        }
      })
    },
    /** 跳转页面 **/
    goPage(type) {
      switch (type) {
        case 1:
          this.$router.push('/plugin/clockin/index')
          break
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  .el-form {
    background: #ffffff !important;
  }
  .open-bg {
    margin: 20px 0;
    padding: 20px;
    background: #e2f0ff;
  }
  .radio-style {
    padding: 0 0 10px;
  }
  .lineHeight32 {
    line-height: 32px;
  }
  .mg-right10 {
    margin-right: 10px;
  }
  .mg-left10 {
    margin-left: 10px;
  }
  .w200 {
    width: 200px;
  }
</style>
