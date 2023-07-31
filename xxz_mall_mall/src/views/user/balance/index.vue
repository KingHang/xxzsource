<template>
  <div class="common-seach-wrap">
    <el-tabs v-model="activeName">
      <el-tab-pane label="余额明细" name="log" />
      <el-tab-pane label="充值设置" name="settings" />
      <el-tab-pane label="充值套餐" name="plan" />
      <el-tab-pane label="充值记录" name="record" />
    </el-tabs>

    <!--余额明细-->
    <Log v-if="activeName === 'log'" />

    <!--余额设置-->
    <Setting v-if="activeName === 'settings'" />

    <!--充值套餐-->
    <Plan v-if="activeName === 'plan'" />

    <!--充值记录-->
    <Record v-if="activeName === 'record'" />
  </div>
</template>

<script>
import Setting from './part/Setting'
import Log from './part/Log'
import Plan from './part/Plan'
import Record from './part/Record'

export default {
  components: {
    Setting,
    Log,
    Plan,
    Record
  },
  data() {
    return {
      form: {
        status: 0
      },
      activeName: 'log'
    }
  },
  methods: {}
}
</script>
