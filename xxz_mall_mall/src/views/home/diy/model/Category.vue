<template>
  <div class="drag optional" :class="{ selected: index === selectedIndex }" @click.stop="$parent.$parent.onEditer(index)">
    <div class="diy-product" :style="{ background: item.style.background }">
      <div class="diy-category">
        <ul>
          <li v-for="(cate, indexTemp) in item.data" :key="indexTemp" :class="active === indexTemp ? 'active' : ''" @click="changeCategory(indexTemp)">
            <div class="cate-name">{{ cate.name }}</div>
            <div class="cate-line">
              <img v-img-url="line">
            </div>
          </li>
        </ul>
        <div class="all-show">
          <img v-img-url="show">
        </div>
      </div>

      <div :class="['display__' + item.style.display]">
        <ul class="product-list" :class="['column__' + item.style.column]" :style="getUlwidth(item)">
          <li v-for="(product, indexTemp) in dataList" :key="indexTemp" class="product-item">
            <!-- 单列商品 -->
            <template v-if="item.style.column === 1">
              <div class="product-item-box">
                <!-- 商品图片 -->
                <div class="product-cover"><img v-img-url="product.image"></div>
                <div class="product-info">
                  <!-- 商品名称 -->
                  <div v-if="item.style.show.productName" class="product-title">
                    <span>{{ product.product_name }}</span>
                  </div>
                  <!-- 商品卖点 -->
                  <div v-if="item.style.show.sellingPoint" class="selling-point gray9">
                    <span>{{ product.selling_point }}</span>
                  </div>
                  <!-- 商品销量 -->
                  <div v-if="item.style.show.productSales" class="already-sale">
                    <span>已售{{ product.product_sales }}件</span>
                  </div>
                  <!-- 商品价格 -->
                  <div class="price d-s-c">
                    <div v-if="item.style.show.productPrice" class="orange">
                      <span>¥</span>
                      <span class="f18 fb">{{ product.product_price }}</span>
                    </div>
                    <div v-if="item.style.show.linePrice && product.line_price > 0" class="ml10 gray9 text-d-line">
                      <span>¥</span>
                      <span>{{ product.line_price }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </template>

            <!-- 两列三列 -->
            <template v-else>
              <div class="product-cover"><img v-img-url="product.image"></div>
              <div class="product-info p-0-10">
                <div v-if="item.style.show.productName" class="product-title">{{ product.product_name }}</div>
                <div class="price d-s-c f12">
                  <div v-if="item.style.show.productPrice" class="orange">
                    <span>¥</span>
                    <span class="">{{ product.product_price }}</span>
                  </div>
                  <div v-if="item.style.show.linePrice && product.line_price > 0" class="ml4 gray9 text-d-line">
                    ¥{{ product.line_price }}
                  </div>
                </div>
              </div>
            </template>
          </li>
        </ul>
      </div>
    </div>
    <div class="btn-edit-del">
      <div class="btn-del" @click.stop="$parent.$parent.onDeleleItem(index)">删除</div>
    </div>
  </div>
</template>

<script>
import GoodsApi from '@/api/goods.js'
import show from '@/assets/img/show.png'
import line from '@/assets/img/line.png'

export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['item', 'index', 'selectedIndex'],
  data() {
    return {
      show: show,
      line: line,
      active: 0,
      /* 商品列表 */
      dataList: []
    }
  },
  created() {
    this.dataList = this.item.list
  },
  methods: {
    getData(category_id) {
      const self = this
      GoodsApi.productList({
        category_id: category_id,
        page: 1,
        list_rows: self.item.params.showType === 'limit' ? self.item.params.showNum : 20
      }, true).then(res => {
        self.dataList = res.data.list.data
      }).catch(() => {})
    },
    /** 切换分类 **/
    changeCategory(index) {
      this.active = index
      // eslint-disable-next-line no-prototype-builtins
      if (this.item.data[index].hasOwnProperty('category_id')) {
        this.getData(this.item.data[index].category_id)
      } else {
        this.dataList = this.item.list
      }
    },
    /** 计算宽度 **/
    getUlwidth(item) {
      if (item.style.display === 'slide') {
        const total = item.list[0].length
        let w = 0
        if (item.style.column === 2) {
          w = total * 150
        } else {
          w = total * 100
        }
        return 'width:' + w + 'px;'
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  /*分类样式*/
  .diy-product .diy-category {
    display: flex;
    justify-content: space-between;
    padding: 10px 0 0;
  }
  .diy-product .diy-category ul {
    width: 90%;
    overflow-x: auto;
    display: flex;
  }
  .diy-product .diy-category ul li {
    min-width: 40px;
    text-align: center;
    cursor: pointer;
    margin-right: 10px;
    flex-shrink: 0;
  }
  .diy-product .diy-category ul li .cate-name {
    font-size: 13px;
    color: #1B1B1B;
  }
  .diy-product .diy-category ul li .cate-line {
    width: 39px;
    height: 27px;
    margin: 0 auto;
    display: none;
  }
  .diy-product .diy-category ul li .cate-line img {
    width: 100%;
  }
  .diy-product .diy-category ul li.active .cate-name {
    font-weight: bold;
    font-size: 14px;
  }
  .diy-product .diy-category ul li.active .cate-line {
    display: block;
  }
  .diy-product .diy-category .all-show {
    width: 24px;
    height: 24px;
    cursor: pointer;
  }
  .diy-product .diy-category .all-show img {
    width: 100%;
  }
  /*横向滑动样式*/
  .diy-product .display__slide {
    overflow-x: auto;
  }
  .diy-product .display__slide .product-list {
    display: flex;
    justify-content: flex-start;
  }
  .diy-product .display__slide .product-list.column__2 .product-item {
    width: 170px;
  }
  .diy-product .display__slide .product-list.column__3 .product-item {
    width: 117px;
  }
  .diy-product .display__slide .product-list .product-item {
    margin-right: 10px;
  }
  /*列表样式*/
  .diy-product .product-list img {
    width: 100%;
  }
  .diy-product .product-list.column__1 .product-item {
    padding: 10px;
    border-bottom: 1px solid #dddddd;
    background: #FFFFFF;
  }
  .diy-product .product-list.column__1 .product-item-box {
    display: flex;
    justify-content: flex-start;
    align-items: stretch;
  }
  .diy-product .product-list.column__1 .product-cover {
    width: 100px;
    height: 100px;
  }
  .diy-product .product-list.column__1 .product-info {
    margin-left: 10px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .diy-product .product-list .product-title {
    margin-top: 4px;
    height: 40px;
    line-height: 20px;
    display: -webkit-box;
    overflow: hidden;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }
  .diy-product .display__list .column__2,
  .diy-product .display__list .column__3 {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
  }
  .diy-product .product-list.column__2 .product-item {
    width: 170px;
    margin-bottom: 10px;
    background: #FFFFFF;
  }
  .diy-product .product-list.column__2 .product-item .product-cover {
    width: 170px;
    height: 170px;
    overflow: hidden;
  }
  .diy-product .product-list.column__3 .product-item {
    width: 117px;
    margin-bottom: 10px;
    background: #FFFFFF;
  }
  .diy-product .product-list.column__3 .product-item .product-cover {
    width: 117px;
    height: 117px;
    overflow: hidden;
  }
  .diy-product .product-list.column__3 .product-item .product-cover img {
    width: 117px;
  }
  .diy-product .product-list.column__2 .product-title,
  .diy-product .product-list.column__3 .product-title {
    height: 40px;
    overflow: hidden;
  }
</style>
