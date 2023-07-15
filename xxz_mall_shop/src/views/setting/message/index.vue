<template>
  <div>
    <el-tabs v-model="activeName">
      <el-tab-pane label="会员通知" name="user" />
      <el-tab-pane label="服务者通知" name="servant" />
      <el-tab-pane label="平台通知" name="shop" />
      <el-tab-pane label="商户通知" name="supplier" />
      <el-tab-pane label="获取手机号" name="mobile" />
    </el-tabs>

    <!--会员通知-->
    <Message v-if="activeName === 'user'" :message_to="10" />

    <!--服务者通知-->
    <Message v-if="activeName === 'servant'" :message_to="40" />

    <!--平台通知-->
    <Message v-if="activeName ==='shop'" :message_to="20" />

    <!--商户通知-->
    <Message v-if="activeName === 'supplier'" :message_to="30" />

    <!--获取手机号-->
    <GetPhone v-if="activeName === 'mobile'" />
  </div>
</template>

<script>
import Message from './Message'
import GetPhone from './getphone.vue'

export default {
  components: {
    /* 编辑组件 */
    Message,
    /* 获取手机 */
    GetPhone
  },
  data() {
    return {
      /* 参数 */
      message_to: 10,
      /* 当前选中 */
      activeName: 'user'
    }
  },
  created() {
    if (this.$route.query.type != null) {
      this.activeName = this.$route.query.type
    }
  },
  methods: {
  }
}
</script>
