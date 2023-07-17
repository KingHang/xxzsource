<template>
  <div class="drag optional" :class="{ selected: index === selectedIndex }" @click.stop="$parent.$parent.onEditer(index)">
    <div class="diy-wxlive" :style="{ background: item.style.background }">
      <div class="wxlive-head d-b-c">
        <div class="left d-s-c">
          <div class="name">
            热门直播
          </div>
        </div>
        <div class="right">更多</div>
      </div>
      <ul class="wxlive-list d-s-c f-w" :style="getUlwidth(item)">
        <li v-for="(live, indexTemp) in item.data" :key="indexTemp" class="item">
          <div class="box">
            <div class="pic">
              <img v-img-url="live.image">
            </div>
            <div>{{ live.name }}</div>
          </div>
        </li>
      </ul>
    </div>
    <div class="btn-edit-del"><div class="btn-del" @click.stop="$parent.$parent.onDeleleItem(index)">删除</div></div>
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

<style>
.diy-wxlive{}
.diy-wxlive .wxlive-head {
  height: 40px;
}
.diy-wxlive .wxlive-head .name {
  font-size: 18px;
  font-weight: bold;
}
.diy-wxlive .wxlive-list{}
.diy-wxlive .wxlive-list .item{ width: 50%;}
.diy-wxlive .wxlive-list .item .box{ padding: 10px;}
.diy-wxlive .wxlive-list .item img{width: 100%;}
</style>
