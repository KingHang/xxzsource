<template>
  <div class="drag optional" :class="{ selected: index === selectedIndex }" @click.stop="$parent.$parent.onEditer(index)">
    <div class="diy-sharpproduct" style="background: #ffffff">
      <div class="sharpproduct-head d-b-c">
        <div class="left d-s-c">
          <div class="name assemble_name">
            每日必拼
          </div>
        </div>
        <div class="right gray9">更多</div>
      </div>
      <div :class="['display__' + item.style.display]">
        <ul v-if="item.style.display === 'slide'" class="product-list assemble" :class="['column__3']" :style="getUlwidth(item)">
          <li v-for="(product, indexTemp) in item.data" :key="indexTemp" class="product-item">
            <div class="group-num">2人团</div>
            <div class="product-cover">
              <img v-img-url="product.image">
            </div>
            <div class="product-info p-0-10">
              <div v-if="item.style.show.productName === '1'" class="assemble-title">{{ product.product_name }}</div>
              <div class="price d-b-c f12">
                <span v-if="item.style.show.assemblePrice === '1'" style="color:#f6220c;" class="f16 fb">¥{{ product.assemble_price }}</span>
                <span v-if="item.style.show.linePrice === '1'" class="text-d-line-through gray9 f14">¥{{ product.line_price }}</span>
              </div>
            </div>
            <div class="assemble_btns">去开团</div>
          </li>
        </ul>
        <ul v-else class="product-list assemble">
          <li v-for="(product, indexTemp) in item.data" :key="indexTemp" class="product-item">
            <div class="product-cover">
              <img v-img-url="'http://img.pighack.com/2022031211404702a078124.png'">
              <div class="hover-tip">
                <span class="end">距结束 | </span>
                <span class="time">3天 01:40:51</span>
              </div>
            </div>
            <div class="product-info">
              <div class="assemble-title">
                <div v-if="item.style.show.productName === '1'" class="title">{{ product.product_name }}</div>
                <div v-if="item.style.show.linePrice === '1'" class="gray9 f14">
                  <span>原价: </span>
                  <span class="text-d-line-through">¥{{ product.line_price }}</span>
                </div>
              </div>
              <div class="product-price">
                <div class="price">
                  <div class="price-top">
                    <div class="price-left">
                      <span v-if="item.style.show.assemblePrice === '1'" style="color:#F63E36;" class="f16 fb">¥</span>
                      <span v-if="item.style.show.assemblePrice === '1'" style="color:#F63E36;" class="f20 fb">{{ product.assemble_price }}</span>
                    </div>
                    <div class="group-num">
                      <span class="num">2人团</span>
                      <span class="cost">立省5元</span>
                    </div>
                  </div>
                  <div class="progress">
                    <el-progress :percentage="20" color="#FF7307" :show-text="false" />
                  </div>
                </div>
                <div class="assemble_btns">立即开团</div>
              </div>
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
        if (item.style.column === '1') {
          w = total * 300
        } else if (item.style.column === '2') {
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

  .sharpproduct-head .name.assemble_name {
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

  .diy-sharpproduct {
    padding: 0 10px 10px;
  }

  /*样式一：大图*/
  .diy-sharpproduct .display__list .product-list.assemble .product-item {
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
          color: #FFDE07;
        }
      }
    }

    .product-info {
      padding: 10px;
      border-top: none;

      .assemble-title {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;

        .title {
          display: -webkit-box;
          overflow: hidden;
          -webkit-line-clamp: 1;
          -webkit-box-orient: vertical;
          font-size: 15px;
        }
      }

      .product-price {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 10px 0 0;

        .price {
          .price-top {
            display: flex;
            align-items: center;

            .group-num {
              border: solid 1px #FF7307;
              border-radius: 4px;
              font-size: 14px;
              margin-left: 10px;

              .num {
                display: inline-block;
                width: 46px;
                text-align: center;
                height: 100%;
                color: #F63E36;
                background-color: #FFF1E6;
                border-radius: 4px 0 0 4px;
              }

              .cost {
                color: #F63E36;
                padding: 0 2px;
              }
            }
          }

          .progress {
            width: 190px;
            margin-top: 10px;
          }
        }

        .assemble_btns {
          width: 100px;
          height: 40px;
          line-height: 40px;
          text-align: center;
          color: #ffffff;
          background: linear-gradient(to right, #F3A123, #FF7307);
          border-radius: 20px;
          font-size: 13px;
        }
      }
    }
  }

  /*样式二：滑动*/
  .diy-sharpproduct .display__slide {
    padding: 10px;
  }

  .diy-sharpproduct .display__slide .product-list.assemble {
    display: flex;
    justify-content: flex-start;
    overflow: hidden;
  }

  .diy-sharpproduct .display__slide .product-list.assemble .product-item {
    width: 135px;
    margin: 0 10px 10px 0;
    overflow: hidden;
    border-radius: 5px 5px 0 0;
    background: linear-gradient(180deg, #FFECEC 0%, rgba(255, 255, 255, 0.19) 93%);

    .group-num {
      width: 50px;
      background-color: #6f6f71;
      border-radius: 5px 0 5px 0;
      color: #ffffff;
      text-align: center;
    }

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
      border-top: none;

      .assemble-title {
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

    .assemble_btns {
      background-color: #FF7307;
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
  .diy-sharpproduct .display__list .product-list.assemble .product-item .product-info .progress .el-progress-bar__outer {
    background-color: #f5d2bb !important;
  }
</style>
