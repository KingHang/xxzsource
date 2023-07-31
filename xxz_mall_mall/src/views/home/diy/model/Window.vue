<template>
  <div @click.stop="$parent.$parent.onEditer(index)">
    <div class="drag optional" :class="{selected:index === selectedIndex}">
      <div class="diy-window" :style="{ background: item.style.background, padding: item.style.paddingTop + 'px ' + item.style.paddingLeft + 'px' }">
        <ul v-if="item.style.layout > -1" :class="'column__' + item.style.layout" class="data-list">
          <li v-for="(window, indexTemp) in item.data" :key="indexTemp" :style="{ padding: item.style.paddingTop + 'px ' + item.style.paddingLeft + 'px' }">
            <div class="item-image">
              <img v-img-url="window.imgUrl">
            </div>
          </li>
        </ul>
        <div v-else class="display" :style="{ padding:item.style.paddingTop + 'px ' + item.style.paddingLeft + 'px' }">
          <div class="percent-w50">
            <div class="img-box-wrap-1">
              <div class="img-box">
                <img v-img-url="item.data[0].imgUrl">
              </div>
            </div>
          </div>
          <div class="percent-w50 d-s-c d-c">
            <div v-if="item.data.length >= 2" class="img-box-wrap-2">
              <div class="img-box">
                <img v-img-url="item.data[1].imgUrl">
              </div>
            </div>
            <div class="d-s-c img-box-wrap-3">
              <div v-if="item.data.length >= 3" class="img-box-wrap-4">
                <div class="img-box">
                  <img v-img-url="item.data[2].imgUrl">
                </div>
              </div>
              <div v-if="item.data.length >= 4" class="img-box-wrap-4">
                <div class="img-box">
                  <img v-img-url="item.data[3].imgUrl">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="btn-edit-del">
        <div class="btn-del" @click.stop="$parent.$parent.onDeleleItem(index)">删除</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['item', 'index', 'selectedIndex'],
  data() {
    return {
    }
  },
  methods: {
  }
}
</script>

<style>
.diy-window .data-list{ display: flex; flex-wrap: wrap;}
.diy-window .data-list.column__2 li{width: 50%; }
.diy-window .data-list.column__3 li{width: 33.333333333333%; }
.diy-window .data-list.column__4 li{width: 25%; }
.diy-window .display{ display: flex;}
.diy-window .display .img-box-wrap-1{ position: relative; padding-bottom: 100%;}
.diy-window .display .img-box-wrap-2{position: relative; width: 100%; height: 50%;}
.diy-window .display .img-box{ position: absolute; top: 0; right: 0; bottom: 0; left: 0;}
.diy-window .display .img-box img{ height: 100%;}
.diy-window .display .img-box-wrap-3{ width: 100%; height: 50%;}
.diy-window .display .img-box-wrap-4{ position: relative; width: 50%; height: 100%;}
</style>
