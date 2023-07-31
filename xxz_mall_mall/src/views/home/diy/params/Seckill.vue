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

      <!--组件样式-->
      <div class="p-10-0 mb16 f14 border-b">
        <span class="gray6">组件样式</span>
      </div>

      <!-- 商品排序 -->
      <el-form-item label="列表样式：">
        <el-radio-group v-model="curItem.style.display">
          <el-radio :label="'list'">大图显示</el-radio>
          <el-radio :label="'slide'">横向滑动</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="显示内容：">
        <el-checkbox v-model="curItem.style.show.productName">商品名称</el-checkbox>
        <el-checkbox v-model="curItem.style.show.seckillPrice">秒杀价格</el-checkbox>
        <el-checkbox v-model="curItem.style.show.linePrice">划线价格</el-checkbox>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['curItem', 'selectedIndex'],
  data() {
    return {
    }
  },
  created() {
  },
  methods: {
  }
}
</script>
