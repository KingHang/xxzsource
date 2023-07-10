<template>
  <div class="common-seach-wrap">
    <el-tabs v-model="activeName">
      <el-tab-pane label="积分设置" name="first" />
      <el-tab-pane label="积分明细" name="second" />
      <el-tab-pane label="成长值设置" name="third" />
      <el-tab-pane label="成长值明细" name="fourth" />
    </el-tabs>

    <!--积分设置-->
    <Setting v-if="activeName === 'first'" />

    <!--积分明细-->
    <Log v-if="activeName === 'second'" />

    <!--成长值设置-->
    <Grow v-if="activeName === 'third'" />

    <!--成长值明细-->
    <GrowLog v-if="activeName === 'fourth'" />
  </div>
</template>

<script>
import Setting from './part/Setting'
import Log from './part/Log'
import Grow from './part/Grow'
import GrowLog from './part/GrowLog'

export default {
  components: {
    Setting,
    Log,
    Grow,
    GrowLog
  },
  data() {
    return {
      form: {
        status: 0
      },
      activeName: 'first'
    }
  },
  methods: {}
}
</script>
