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
        <el-checkbox v-model="productName" @change="checked=>check(checked, 'productName')">商品名称</el-checkbox>
        <el-checkbox v-model="peoples" @change="checked=>check(checked, 'peoples')">正在砍价</el-checkbox>
        <el-checkbox v-model="originalPrice" @change="checked=>check(checked, 'originalPrice')">商品原价</el-checkbox>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['curItem', 'selectedIndex', 'opts'],
  data() {
    return {
      /* 商品名称 */
      productName: false,
      /* 正在砍价 */
      peoples: false,
      /* 砍价底价 */
      floorPrice: false,
      /* 商品价格 */
      originalPrice: false
    }
  },
  created() {
    /* 获取列表 */
    // this.getData()
    this.productName = this.curItem.style.show.productName === '1'
    this.peoples = this.curItem.style.show.peoples === '1'
    this.floorPrice = this.curItem.style.show.floorPrice === '1'
    this.originalPrice = this.curItem.style.show.originalPrice === '1'
  },
  methods: {
    /** 内容是否选择 **/
    check(checked, name) {
      this.curItem.style.show[name] = checked ? '1' : '0'
    }
  }
}
</script>
