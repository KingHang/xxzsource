<template>
  <div>
    <div class="common-form">
      <span>{{ curItem.name }}</span>
    </div>
    <el-form size="small" :model="curItem" label-width="100px">
      <el-form-item label="商品来源：">
        <el-radio-group v-model="curItem.params.source">
          <el-radio :label="'auto'">自动获取</el-radio>
          <el-radio :label="'choice'">手动选择</el-radio>
        </el-radio-group>
      </el-form-item>

      <!-- 手动选择 -->
      <template v-if="curItem.params.source === 'choice'">
        <el-form-item label="权益卡列表：">
          <div class="choice-category-list">
            <draggable v-model="curItem.data" :options="{draggable:'.item',animation:500}">
              <transition-group class="d-s-c f-w">
                <div v-for="(cate, index) in curItem.data" :key="cate.card_id" class="item">
                  <div class="delete-box"><i class="el-icon-error" @click="deleteCard(index)" /></div>
                  <div>{{ cate.card_name }}</div>
                </div>
              </transition-group>
            </draggable>
          </div>
          <div>
            <el-select v-model="currCard" multiple placeholder="请选择权益卡" style="width: 100%" @change="changeCard">
              <el-option
                v-for="(item,index) in cardList"
                :key="index"
                :label="item.card_name"
                :value="item.card_id"
              />
            </el-select>
          </div>
        </el-form-item>
      </template>

      <!-- 商品排序 -->
      <el-form-item label="商品排序：">
        <el-radio-group v-model="curItem.params.cardSort">
          <el-radio :label="'all'">综合</el-radio>
          <el-radio :label="'sales'">销量</el-radio>
          <el-radio :label="'price'">价格</el-radio>
        </el-radio-group>
      </el-form-item>

      <!-- 显示数量 -->
      <el-form-item label="显示数量：">
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
        </el-radio-group>
      </el-form-item>

      <!-- 分列数量 -->
      <el-form-item label="分列数量：">
        <el-radio-group v-model="curItem.style.column">
          <el-radio label="1">单列</el-radio>
        </el-radio-group>
      </el-form-item>

      <!-- 显示内容 -->
      <el-form-item label="显示内容：">
        <el-checkbox v-model="cardNameShow" @change="checked => check(checked, 'cardName')">权益卡名称</el-checkbox>
        <el-checkbox v-model="priceShow" @change="checked => check(checked, 'price')">权益卡价格</el-checkbox>
        <el-checkbox v-model="validPeriodShow" @change="checked => check(checked, 'validPeriod')">有效期</el-checkbox>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import BenefitApi from '@/api/benefit.js'
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
      /* 权益卡列表 */
      cardList: [],
      /* 当前选中的 */
      currCard: [],
      /* 是否显示 */
      cardNameShow: false,
      priceShow: false,
      validPeriodShow: false
    }
  },
  watch: {
    selectedIndex: function(n, o) {
      this.currCard = this.currCardAuto(this.cardList)
    }
  },
  created() {
    /* 获取列表 */
    this.getData()
    this.cardNameShow = this.curItem.style.show.cardName === 1
    this.priceShow = this.curItem.style.show.price === 1
    this.validPeriodShow = this.curItem.style.show.validPeriod === 1
  },
  methods: {
    /** 获取权益卡 **/
    getData() {
      const self = this
      BenefitApi.CardList({ status: 1, type: 'all' }, true)
        .then(res => {
          console.log(res.data.list)
          self.cardList = res.data.list
          self.currCard = self.currCardAuto(res.data.list)
          self.loading = false
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 选择权益卡 **/
    currCardAuto(list) {
      const arr = []
      for (let i = 0; i < list.length; i++) {
        const item = list[i]
        for (let j = 0; j < this.curItem.data.length; j++) {
          // eslint-disable-next-line no-prototype-builtins
          if (this.curItem.data[j].hasOwnProperty('card_id') && item.card_id === this.curItem.data[j].card_id) {
            arr.push(item.card_id)
          }
        }
      }
      return arr
    },
    /** 检查 **/
    check(checked, name) {
      this.curItem.style.show[name] = checked ? 1 : 0
    },
    /** 选择权益卡 **/
    changeCard() {
      this.curItem.data = this.resetCard()
      for (let i = 0; i < this.currCard.length; i++) {
        for (let j = 0; j < this.cardList.length; j++) {
          if (this.currCard[i] === this.cardList[j].card_id) {
            const temp = []
            this.cardList[j].Relevance.forEach(res => {
              temp.push({
                benefit_id: res.benefit.benefit_id,
                benefit_name: res.benefit.benefit_name,
                number: res.number,
                desc: res.benefit.remarks,
                image: res.benefit.file ? res.benefit.file.file_path : ''
              })
            })
            this.curItem.data.push({
              card_id: this.cardList[j].card_id,
              card_name: this.cardList[j].card_name,
              image: this.cardList[j].file ? this.cardList[j].file.file_path : '',
              price: this.cardList[j].retail_price,
              valid_period: this.cardList[j].valid_period.text,
              benefit: temp
            })
          }
        }
      }
    },
    /** 清理数据 **/
    resetCard() {
      const arr = []
      for (let i = 0; i < this.curItem.data.length; i++) {
        // eslint-disable-next-line no-prototype-builtins
        if (!this.curItem.data[i].hasOwnProperty('card_id')) {
          arr.push(this.curItem.data[i])
        }
      }
      return arr
    },
    /** 删除权益卡 **/
    deleteCard(index) {
      const search = this.currCard.indexOf(this.curItem.data[index].card_id)
      this.currCard.splice(search, 1)
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
