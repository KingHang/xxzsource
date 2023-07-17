<template>
  <div class="drag optional" :class="{ selected: index === selectedIndex }" @click.stop="$parent.$parent.onEditer(index)">
    <div class="diy-sharpproduct" :style="{ background: item.style.background }">
      <div class="sharpproduct-head d-b-c">
        <div class="left d-s-c card-name">超值权益卡</div>
        <div class="right gray9">查看全部</div>
      </div>

      <div :class="['display__' + item.style.display]">
        <ul class="product-list card">
          <li v-for="(product, indexTemp) in item.data" :key="indexTemp" class="product-item">
            <div class="product-cover">
              <img v-img-url="product.image">
              <div class="card-product-left">
                <div v-if="item.style.show.cardName === 1" class="card-product-name">{{ product.card_name }}</div>
                <div v-if="item.style.show.validPeriod === 1" class="card-valid-period">{{ product.valid_period }}</div>
              </div>
              <div v-if="item.style.show.price === 1" class="card-product-price">¥{{ product.price }}</div>
              <div class="card-btns">立即购买</div>
            </div>

            <div class="product-info">
              <div class="benefit-box">
                <div v-for="(ben, fit) in product.benefit" :key="fit" class="benefit-item">
                  <img v-img-url="ben.image">
                  <div class="benefit-info">
                    <div class="benefit-header">
                      <span>{{ ben.benefit_name }}</span>
                      <span class="mg-left8">{{ ben.number }}次</span>
                    </div>
                    <div class="benefit-desc">{{ ben.desc }}</div>
                  </div>
                </div>
              </div>
              <div class="progress">
                <el-progress :percentage="50" color="#0AB9D8" :show-text="false" />
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
  props: ['item', 'index', 'selectedIndex']
}
</script>

<style lang="scss" scoped>
  .diy-sharpproduct {
    padding: 0 10px 10px;

    .sharpproduct-head {
      height: 40px;

      .card-name {
        font-size: 18px;
        font-weight: bold;
        color: #333333;
      }
    }

    /*大图*/
    .display__list .product-list.card .product-item {
      width: 100%;
      background-color: #ffffff;
      border-radius: 10px;
      margin-bottom: 10px;

      .product-cover {
        height: 140px;
        overflow: hidden;
        position: relative;
        border-radius: 10px 10px 0 0;
        color: #ffffff;

        img {
          width: 100%;
        }

        .card-product-left {
          position: absolute;
          left: 24px;
          top: 24px;

          .card-product-name {
            font-size: 20px;
            font-weight: bold;
          }

          .card-valid-period {
            font-size: 12px;
            margin-top: 8px;
          }
        }

        .card-product-price {
          position: absolute;
          right: 24px;
          top: 26px;
          font-size: 20px;
          font-weight: bold;
        }

        .card-btns {
          position: absolute;
          right: 24px;
          bottom: 24px;
          width: 96px;
          height: 40px;
          line-height: 36px;
          border-radius: 20px;
          border: solid 1px #ffffff;
          font-size: 14px;
          text-align: center;
        }
      }

      .product-info {
        padding: 10px;

        .benefit-box {
          display: flex;
          justify-content: space-between;
          align-items: center;
          flex-wrap: wrap;
          gap: 10px;

          .benefit-item {
            width: 45%;
            display: flex;
            align-items: center;

            img {
              width: 34px;
              height: 34px;
            }

            .benefit-info {
              margin-left: 10px;

              .benefit-header {
                font-size: 12px;
                font-weight: bold;
                color: #1B1B1B;

                .mg-left8 {
                  margin-left: 8px;
                }
              }

              .benefit-desc {
                font-size: 12px;
                color: #999999;
              }
            }
          }
        }

        .progress {
          width: 28px;
          margin: 10px auto 0;
        }
      }
    }
  }
</style>
