<template>
  <div>
    <div class="common-form">
      <span>{{ curItem.name }}</span>
    </div>
    <el-form size="small" :model="curItem" label-width="100px">
      <!--显示数量-->
      <el-form-item label="显示数量：">
        <el-input v-model="curItem.params.showNum" class="w-auto" />
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
export default {
  components: {},
  // eslint-disable-next-line vue/require-prop-types
  props: ['curItem', 'selectedIndex'],
  data() {
    return {}
  },
  created() {
    console.log(this.curItem)
  },
  methods: {}
}
</script>
