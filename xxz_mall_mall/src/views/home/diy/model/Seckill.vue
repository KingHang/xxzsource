<template>
  <div class="drag optional" :class="{ selected: index === selectedIndex }" @click.stop="$parent.$parent.onEditer(index)">
    <div class="diy-seckill">
      <div class="diy-head d-b-c" style="color: #333333">
        <div class="left d-s-c">
          <div class="name">限时秒杀</div>
          <div class="datetime d-s-c">
            <span class="text" />
            <span class="box hour">12</span>
            <span class="text">:</span>
            <span class="box hour">30</span>
            <span class="text">:</span>
            <span class="box hour">00</span>
          </div>
        </div>
        <div class="right gray9">更多</div>
      </div>
      <div :class="['display__' + item.style.display]">
        <ul v-if="item.style.display === 'slide'" class="product-list seckill" :class="['column__3']" :style="getUlwidth(item)">
          <li v-for="(product, indexTemp) in item.data" :key="indexTemp" class="product-item">
            <div class="product-cover">
              <img v-img-url="product.image">
            </div>
            <div class="product-info p-0-10">
              <div v-if="item.style.show.productName === '1'" class="seckill-title">{{ product.product_name }}</div>
              <div class="price d-b-c f12">
                <span v-if="item.style.show.seckillPrice === '1'" style="color:#F63E36;" class="f16 fb">¥{{ product.seckill_price }}</span>
                <span v-if="item.style.show.linePrice === '1'" class="text-d-line-through gray9 f14">¥{{ product.original_price }}</span>
              </div>
            </div>
            <div class="seckill_btns">马上抢</div>
          </li>
        </ul>
        <ul v-else class="product-list seckill">
          <li v-for="(product, indexTemp) in item.data" :key="indexTemp" class="product-item">
            <div class="product-cover">
              <img v-img-url="product.image">
              <div class="hover-tip">
                <span class="end">距结束 | </span>
                <span class="time">3天 01:40:51</span>
              </div>
            </div>
            <div class="product-info">
              <div class="seckill-title">
                <div v-if="item.style.show.productName === '1'" class="title">
                  {{ product.product_name }}
                </div>
                <div v-if="item.style.show.linePrice === '1'" class="gray9 f14 text-d-line-through">
                  原价: ¥{{ product.original_price }}
                </div>
              </div>
              <div class="product-price">
                <div class="price">
                  <div class="price-top">
                    <div class="price-left">
                      <span v-if="item.style.show.seckillPrice === '1'" style="color:#F63E36;" class="f16 fb">¥</span>
                      <span v-if="item.style.show.seckillPrice === '1'" style="color:#F63E36;" class="f20 fb">{{ product.seckill_price }}</span>
                    </div>
                    <div v-if="product.original_price > 0 && product.seckill_price > 0" class="discount-num" :style="{'background-image': 'url('+discount1+')', 'background-size': 'cover'}">
                      <span class="left-text">秒杀</span>
                      <span class="right-num">{{ (product.seckill_price / product.original_price * 10).toFixed(0) }}折</span>
                    </div>
                  </div>
                  <div class="progress">
                    <el-progress :percentage="20" color="#FE3A3A" :show-text="false" />
                  </div>
                </div>
                <div class="seckill_btns">立即抢购</div>
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
import discount1 from '@/assets/img/discount1.png'

export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['item', 'index', 'selectedIndex'],
  data() {
    return {
      discount1: discount1,
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
  .diy-seckill {
    background-color: #fcecec;
    padding: 0 10px 10px;
  }

  .diy-seckill .diy-head {
    padding: 0 10px;
    height: 40px;
  }

  .diy-seckill .diy-head .name {
    font-size: 18px;
    font-weight: bold;
  }

  .diy-seckill .diy-head .datetime {
    margin-left: 20px;
  }

  .diy-seckill .diy-head .datetime>span {
    display: inline-block;
  }

  .diy-seckill .diy-head .datetime .text {
    padding: 0 2px;
    color: #F6220C;
  }

  .diy-seckill .diy-head .datetime .box {
    padding: 2px;
    border-radius: 4px;
    background: #FFFFFF;
    color: #F6220C;
  }

  /*样式一：大图*/
  .diy-seckill .display__list .product-list.seckill .product-item {
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

      .seckill-title {
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

            .discount-num {
              width: 91px;
              height: 23px;
              font-size: 13px;
              margin-left: 10px;
              display: flex;
              align-items: center;

              .left-text {
                width: 36px;
                text-align: center;
                color: #F63E36;
              }

              .right-num {
                width: 55px;
                text-align: center;
                color: #FFFFFF;
              }
            }
          }

          .progress {
            width: 170px;
            margin-top: 10px;
          }
        }

        .seckill_btns {
          width: 100px;
          height: 40px;
          line-height: 40px;
          text-align: center;
          color: #ffffff;
          background: linear-gradient(to right, #F63E36, #F63E36);
          border-radius: 20px;
          font-size: 13px;
        }
      }
    }
  }

  /*样式二：滑动*/
  .diy-seckill .display__slide {
    padding: 10px;
  }

  .diy-seckill .display__slide .product-list.seckill {
    display: flex;
    justify-content: flex-start;
    overflow: hidden;
  }

  .diy-seckill .display__slide .product-list.seckill .product-item {
    width: 135px;
    margin: 0 10px 10px 0;
    overflow: hidden;
    border-radius: 5px 5px 0 0;
    background: linear-gradient(180deg, #fcc1c7 0%, rgba(255, 255, 255, 0.19) 93%);

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

      .seckill-title {
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

    .seckill_btns {
      background-color: #F63E36;
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
  .diy-seckill .display__list .product-list.seckill .product-item .product-info .progress .el-progress-bar__outer {
    background-color: #FFF0EF !important;
  }
</style>
