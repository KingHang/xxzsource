<template>
  <div class="drag optional" :class="{ selected: index === selectedIndex }" @click.stop="$parent.$parent.onEditer(index)">
    <div class="diy-sharpproduct" style="background: #ffffff">
      <div class="sharpproduct-head d-b-c">
        <div class="left d-s-c">
          <div class="name dividend_name">
            项目创业分红
          </div>
        </div>
        <div class="right gray9">查看全部</div>
      </div>

      <div :class="['display__' + item.style.display]">
        <ul v-if="item.style.display === 'slide'" class="product-list dividend" :class="['column__3']" :style="getUlwidth(item)">
          <li v-for="(product, indexTemp) in item.data" :key="indexTemp" class="product-item">
            <div class="product-cover">
              <img v-img-url="product.image">
            </div>
            <div class="product-info p-0-10">
              <div v-if="item.style.show.productName === '1'" class="title">{{ product.product_name }}</div>
              <div class="price d-b-c f12">
                <span v-if="item.style.show.productPrice === '1'" style="color:#f6220c;" class="f16 fb">¥{{ product.product_price }}</span>
              </div>
            </div>
            <div class="dividend_btns">立即购买</div>
          </li>
        </ul>

        <ul v-else class="product-list dividend">
          <li v-for="(product, indexTemp) in item.data" :key="indexTemp" class="product-item">
            <div class="product-cover">
              <img v-img-url="'https://img.dfhlyl.com/images/1/2019/10/zzFK2WVFVzthddfFlhtVHydYFVfj22.jpg'">
              <div class="hover-tip">
                <span class="end">距结束 | </span>
                <span class="time">3天 01:40:51</span>
              </div>
            </div>
            <div class="product-info">
              <div class="product-left">
                <div v-if="item.style.show.productName === '1'" class="title">{{ product.product_name }}</div>
                <div class="price">
                  <span v-if="item.style.show.productPrice === '1'" style="color:#F63E36;" class="f16 fb">¥</span>
                  <span v-if="item.style.show.productPrice === '1'" style="color:#F63E36;" class="f20 fb">{{ product.product_price }}</span>
                </div>
                <div class="progress">
                  <el-progress :percentage="20" color="#56ADFF" :show-text="false" />
                </div>
              </div>
              <div class="dividend_btns">立即购买</div>
            </div>
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
export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['item', 'index', 'selectedIndex'],
  data() {
    return {
      /* 商品列表 */
      tableData: [],
      /* 分类id */
      category_id: 0
    }
  },
  created() {},
  methods: {
    /** 计算宽度 **/
    getUlwidth(item) {
      if (item.style.display === 'slide') {
        let total = 0
        if (item.params.source === 'choice') {
          total = item.data.length
        } else {
          total = item.defaultData.length
        }
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
  .sharpproduct-head {
    height: 40px;
  }

  .sharpproduct-head .name.dividend_name {
    font-size: 18px;
    font-weight: bold;
    color: #333333;
  }

  .sharpproduct-head .datetime {
    margin-left: 20px;
  }

  .sharpproduct-head .datetime>span {
    display: inline-block;
  }

  .sharpproduct-head .datetime .text {
    padding: 0 2px;
  }

  .sharpproduct-head .datetime .box {
    padding: 2px;
    background: #000000;
    color: #ffffff;
  }

  /*样式一：大图*/
  .diy-sharpproduct .display__list .product-list.dividend .product-item {
    width: 100%;
    background-color: rgba(215, 215, 215, 0.5);
    border-radius: 10px;
    margin-bottom: 10px;

    .product-cover {
      height: 120px;
      overflow: hidden;
      position: relative;
      border-radius: 10px 10px 0 0;

      img {
        width: 100%;
      }

      .hover-tip {
        position: absolute;
        top: 0;
        left: 0;
        border-radius: 10px 0 10px 0;
        background-color: rgba(0, 0, 0, 0.5);
        padding: 4px 10px;

        .end {
          color: #ffffff;
        }

        .time {
          color: #94CCFF;
        }
      }
    }

    .product-info {
      padding: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;

      .product-left {
        margin-right: 10px;

        .title {
          display: -webkit-box;
          overflow: hidden;
          -webkit-line-clamp: 1;
          -webkit-box-orient: vertical;
          font-size: 15px;
        }

        .price {
          margin-top: 5px;
        }

        .progress {
          width: 190px;
          margin-top: 5px;
        }
      }

      .dividend_btns {
        width: 100px;
        height: 40px;
        line-height: 40px;
        text-align: center;
        color: #ffffff;
        background: linear-gradient(to right, #88C6FF, #56ADFF);
        border-radius: 20px;
        font-size: 13px;
      }
    }
  }

  .diy-sharpproduct .display__list .product-list.dividend .product-item:last-child {
    margin-bottom: 0;
  }

  /*样式二：滑动*/
  .diy-sharpproduct .display__slide {
    padding: 10px;
  }

  .diy-sharpproduct .display__slide .product-list.dividend {
    display: flex;
    justify-content: flex-start;
    overflow: hidden;
  }

  .diy-sharpproduct .display__slide .product-list.dividend .product-item {
    width: 135px;
    margin: 0 10px 10px 0;
    overflow: hidden;
    border-radius: 5px 5px 0 0;
    background: linear-gradient(180deg, #94CCFF 0%, rgba(255, 255, 255, 0.19) 93%);

    .product-cover {
      width: 105px;
      height: 105px;
      overflow: hidden;
      margin: 10px 15px 0;

      img {
        width: 105px;
        height: 105px;
      }
    }

    .product-info {
      width: 135px;
      padding: 10px;

      .title {
        height: 30px;
        line-height: 30px;
        display: -webkit-box;
        overflow: hidden;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        font-size: 15px;
      }

      .price {
        display: -webkit-box;
        overflow: hidden;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
      }
    }

    .dividend_btns {
      background-color: #3CA1FF;
      width: 105px;
      height: 25px;
      line-height: 25px;
      font-size: 13px;
      text-align: center;
      color: #FFFFFF;
      border-radius: 25px;
      margin: 0 15px;
    }
  }
</style>

<style>
  .diy-sharpproduct .display__list .product-list.dividend .product-item .product-info .progress .el-progress-bar__outer {
    background-color: #E6EEF5 !important;
  }
</style>
