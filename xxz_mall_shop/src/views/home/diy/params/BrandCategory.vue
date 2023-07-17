<template>
  <div>
    <div class="common-form">
      <span>{{ curItem.name }}</span>
    </div>
    <el-form size="small" :model="curItem" label-width="100px">
      <el-form-item label="分类来源：">
        <el-radio-group v-model="curItem.params.source">
          <el-radio :label="'all'">全部分类</el-radio>
          <el-radio :label="'choice'">手动选择</el-radio>
        </el-radio-group>
      </el-form-item>

      <!-- 手动选择 -->
      <template v-if="curItem.params.source === 'choice'">
        <el-form-item label="分类列表：">
          <div class="choice-category-list">
            <draggable v-model="curItem.data" :options="{draggable:'.item',animation:500}">
              <transition-group class="d-s-c f-w">
                <div v-for="(cate, index) in curItem.data" :key="cate.brand_day_id" class="item">
                  <div class="delete-box"><i class="el-icon-error" @click="deleteCategory(index)" /></div>
                  <div>{{ cate.brand_day_name }}</div>
                </div>
              </transition-group>
            </draggable>
          </div>
          <div>
            <el-select v-model="currCategory" multiple placeholder="请选择分类" style="width: 460px" @change="changeCategory">
              <el-option
                v-for="item in categoryList"
                :key="item.brand_day_id"
                :label="item.brand_day_name"
                :value="item.brand_day_id"
              />
            </el-select>
          </div>
        </el-form-item>
      </template>

      <!-- 商品排序 -->
      <el-form-item label="商品排序：">
        <el-radio-group v-model="curItem.params.productSort">
          <el-radio :label="'all'">综合</el-radio>
          <el-radio :label="'sales'">销量</el-radio>
          <el-radio :label="'price'">价格</el-radio>
        </el-radio-group>
      </el-form-item>

      <!-- 商品显示 -->
      <el-form-item label="商品显示：">
        <el-radio-group v-model="curItem.params.showType">
          <el-radio :label="'all'">全部</el-radio>
          <el-radio :label="'limit'">自定义数量</el-radio>
        </el-radio-group>
      </el-form-item>

      <!-- 显示数量 -->
      <el-form-item v-if="curItem.params.showType === 'limit'" label="显示数量：">
        <el-input v-model="curItem.params.showNum" class="w-auto" />
      </el-form-item>

      <!--组件样式-->
      <div class="p-10-0 mb16 f14 border-b"><span class="gray6">组件样式</span></div>

      <!-- 背景颜色 -->
      <el-form-item label="背景颜色：">
        <div class="d-s-c">
          <el-color-picker v-model="curItem.style.background" />
          <el-button type="button" style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'btnColor', '#ffffff')">重置</el-button>
        </div>
      </el-form-item>

      <!-- 商品分布 -->
      <el-form-item label="商品分布：">
        <el-radio-group v-model="curItem.style.display">
          <el-radio :label="'list'">列表平铺</el-radio>
          <!-- <el-radio :label="'slide'" :disabled="curItem.style.column == 1">横向滑动</el-radio>-->
        </el-radio-group>
      </el-form-item>

      <!-- 分列数量 -->
      <el-form-item label="分列数量：">
        <el-radio-group v-model="curItem.style.column">
          <el-radio label="1" :disabled="curItem.style.display === 'slide'">单列</el-radio>
          <!--  <el-radio label="2">两列</el-radio>-->
          <!--  <el-radio label="3">三列</el-radio>-->
        </el-radio-group>
      </el-form-item>

      <!-- 显示内容 -->
      <el-form-item label="显示内容：">
        <el-checkbox v-model="productNameShow" @change="checked => check(checked, 'productName')">商品名称</el-checkbox>
        <el-checkbox v-model="productPriceShow" @change="checked => check(checked, 'productPrice')">商品价格</el-checkbox>
        <el-checkbox v-model="linePriceShow" @change="checked => check(checked, 'linePrice')">划线价格</el-checkbox>
        <!--  <el-checkbox v-model="sellingPointShow" @change="checked => check(checked, 'sellingPoint')" v-show="curItem.style.column == 1">商品卖点</el-checkbox>-->
        <!--  <el-checkbox v-model="productSalesShow" @change="checked => check(checked, 'productSales')" v-show="curItem.style.column == 1">商品销量</el-checkbox>-->
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import BrandApi from '@/api/brand.js'
import draggable from 'vuedraggable'

export default {
  components: {
    draggable
  },
  // eslint-disable-next-line vue/require-prop-types
  props: ['curItem', 'selectedIndex', 'opts'],
  data() {
    return {
      /* 是否正在加载 */
      loading: true,
      /* 商品类别 */
      categoryList: [],
      /* 当前选中的 */
      currCategory: [],
      productNameShow: false,
      productPriceShow: false,
      linePriceShow: false,
      sellingPointShow: false,
      productSalesShow: false
    }
  },
  watch: {
    selectedIndex: function(n, o) {
      this.currCategory = this.currCategoryAuto(this.categoryList)
    }
  },
  created() {
    /* 获取列表 */
    this.getData()
    this.productNameShow = this.curItem.style.show.productName === 1
    this.productPriceShow = this.curItem.style.show.productPrice === 1
    this.linePriceShow = this.curItem.style.show.linePrice === 1
    this.sellingPointShow = this.curItem.style.show.sellingPoint === 1
    this.productSalesShow = this.curItem.style.show.productSales === 1
  },
  methods: {
    /** 获取分类 **/
    getData() {
      const self = this
      BrandApi.brandDayList({ page_id: self.page_id }, true)
        .then(res => {
          self.categoryList = res.data.list.data
          self.currCategory = self.currCategoryAuto(res.data.list.data)
          self.loading = false
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 选择分类 **/
    currCategoryAuto(list) {
      const arr = []
      for (let i = 0; i < list.length; i++) {
        const item = list[i]
        for (let j = 0; j < this.curItem.data.length; j++) {
          // eslint-disable-next-line no-prototype-builtins
          if (this.curItem.data[j].hasOwnProperty('category_id') && item.brand_day_id === this.curItem.data[j].category_id) {
            arr.push(item.brand_day_id)
          }
        }
      }
      return arr
    },
    /** 检查 **/
    check(checked, name) {
      this.curItem.style.show[name] = checked ? 1 : 0
    },
    /** 选择类别 **/
    changeCategory() {
      this.curItem.data = this.resetCategory()
      for (let i = 0; i < this.currCategory.length; i++) {
        for (let j = 0; j < this.categoryList.length; j++) {
          if (this.currCategory[i] === this.categoryList[j].brand_day_id) {
            this.curItem.data.push({ category_id: this.categoryList[j].brand_day_id, name: this.categoryList[j].brand_day_name })
          }
        }
      }
    },
    /** 清理数据 **/
    resetCategory() {
      const arr = []
      for (let i = 0; i < this.curItem.data.length; i++) {
        // eslint-disable-next-line no-prototype-builtins
        if (!this.curItem.data[i].hasOwnProperty('category_id')) {
          arr.push(this.curItem.data[i])
        }
      }
      return arr
    },
    /** 删除分类 **/
    deleteCategory(index) {
      const search = this.currCategory.indexOf(this.curItem.data[index].category_id)
      this.currCategory.splice(search, 1)
      this.$parent.onEditorDeleleData(index, this.selectedIndex)
    }
  }
}
</script>

<style lang="scss" scoped>
  .choice-category-list {
    display: flex;
    justify-content: flex-start;
    flex-wrap: wrap;
  }

  .choice-category-list .item {
    position: relative;
    margin: 0 20px 20px 0;
    border: 1px solid #dddddd;
    padding: 0 20px;
  }

  .choice-category-list .item .delete-box {
    position: absolute;
    width: 20px;
    height: 20px;
    top: -10px;
    right: -10px;
    font-size: 20px;
    cursor: pointer;
    color: #999999;
  }

  .choice-category-list .item .delete-box:hover {
    color: rgb(255, 51, 0);
  }
</style>
