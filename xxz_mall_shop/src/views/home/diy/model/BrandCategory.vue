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
          <li v-for="(brand, indexTemp) in dataList" :key="indexTemp" class="product-item">
            <!-- 单列商品 -->
            <template v-if="item.style.column === 1">
              <div class="brand-item">
                <div class="brand-header">
                  <div class="bg-logo">
                    <img v-img-url="brand.brand.image.file_path" class="bg-img">
                    <img v-img-url="brand.brand.image.file_path" class="brand">
                  </div>
                  <div class="right-item">
                    <div class="title text-ellipsis line-colam-2">{{ brand.brand.brand_name }}</div>
                    <div class="place">
                      <div v-if="brand.signLog" class="count">{{ brand.signLog.length }}件单品</div>
                      <div class="tag">两小时发货</div>
                    </div>
                  </div>
                </div>

                <div class="brand-content">
                  <div class="time-block">
                    <div class="time-title">距本场结束</div>
                    <div class="time">
                      <div class="time-fill">1</div>
                      <div class="time-text">天</div>
                      <div class="time-fill">12</div>
                      <div class="time-text">:</div>
                      <div class="time-fill">30</div>
                      <div class="time-text">:</div>
                      <div class="time-fill">00</div>
                    </div>
                  </div>
                  <div class="good-list">
                    <div v-for="(good, goodIndex) in brand.signLog" :key="goodIndex" class="good-item">
                      <div v-if="goodIndex < 3">
                        <img v-img-url="good.product.image[0].file_path">
                        <div class="title text-ellipsis-1">{{ good.product.product_name }}</div>
                        <div class="price">
                          <span class="now-price">￥{{ good.product.product_price }}</span>
                          <span class="before-price">￥{{ good.product.line_price }}</span>
                        </div>
                      </div>
                    </div>
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
import BrandApi from '@/api/brand.js'
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
    getData(categoty_id) {
      const self = this
      BrandApi.brandCategoryComponent({
        brand_day_id: categoty_id,
        page: 1,
        list_rows: self.item.params.showNum
      }, true).then(res => {
        self.dataList = res.data.list.data
      }).catch(() => {})
    },
    /** 切换分类 **/
    changeCategory(index) {
      this.active = index
      // eslint-disable-next-line no-prototype-builtins
      if (this.item.data[index].hasOwnProperty('category_id')) {
        this.getData(this.item.data[index].categoty_id)
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
    margin: 10px;
    background: #FFFFFF;
    border-radius: 10px;
  }
  .diy-product .product-list.column__1 .brand-item {
    border-radius: 10px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-header {
    display: flex;
    padding: 16px 12px;
    border-radius: 10px 10px 0 0;
    border-bottom: 1px solid #DEDEDE;
    background: linear-gradient(180deg, #E8E8E8 0%, rgba(255, 255, 255, 0) 100%);
  }

  .diy-product .product-list.column__1 .brand-item .brand-header .brand {
    display: flex;
    justify-content: flex-start;
    align-items: stretch;
  }

  .diy-product .product-list.column__1 .brand-item .brand-header .bg-logo {
    position: relative;
  }

  .diy-product .product-list.column__1 .brand-item .brand-header .bg-logo .bg-img {
    width: 56px;
    height: 56px;
    border-radius: 4px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-header .bg-logo .brand {
    width: 32px;
    height: 15px;
    position: absolute;
    top: 0;
    right: 0;
  }

  .diy-product .product-list.column__1 .brand-item .brand-header .right-item {
    margin-left: 14px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-header .right-item .title {
    color: #373840;
    font-weight: 500;
    font-size: 16px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-header .right-item .place {
    display: flex;
    color: #A4A4AC;
    font-size: 12px;
    align-items: center;
  }

  .diy-product .product-list.column__1 .brand-item .brand-header .right-item .count {
    margin-right: 8px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-header .right-item .tag {
    font-size: 10px;
    background: #FFE5E4;
    color: #F63E36;
    height: 15px;
    margin-right: 5px;
    padding: 0 3px;
    border-radius: 4px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content {
    border-radius: 10px;
    padding: 10px 12px 16px;
    border-radius: 0 0 10px 10px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .time-block {
    display: flex;
    align-items: center;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .time-block .time-title {
    color: #999999;
    font-size: 12px;
    margin-right: 48px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .time-block .time {
    display: flex;
    align-items: center;
    color: red;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .time-block .time .time-fill {
    font-size: 12px;
    background: #F63E36;
    color: #ffffff;
    padding: 0 5px;
    height: 18px;
    line-height: 18px;
    text-align: center;
    border-radius: 2px;
    min-width: 16px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .time-block .time .time-text {
    width: 16px;
    text-align: center;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .good-list {
    display: flex;
    margin-top: 5px;
    justify-content: space-between;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .good-list:after {
    width: 102px;
    content: '';
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .good-list .good-item {
    font-size: 12px;
    text-align: center;
    margin-right: 5px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .good-list .good-item img {
    width: 102px;
    height: 103px;
    border-radius: 4px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .good-list .good-item .title {
    width: 102px;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .good-list .good-item .price .now-price {
    font-size: 14px;
    font-weight: bold;
  }

  .diy-product .product-list.column__1 .brand-item .brand-content .good-list .good-item .price .before-price {
    font-size: 11px;
    color: #C4C4C4;
    text-decoration: line-through;
  }

  .diy-product .product-list.column__1 .brand-item .header-box img {
    width: 56px;
    height: 56px;
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
