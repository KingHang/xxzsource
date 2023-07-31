<template>
  <div
    v-if="diyData.page"
    class="phone-top"
    :style="'background:' + diyData.page.style.titleBackgroundColor"
    @click.stop="$parent.onEditer(-1)"
  >
    <div class="status-bar">
      <span class="icon iconfont icon-wifi" :style="'color:' + diyData.page.style.titleTextColor" />
      <span class="time" :style="'color:' + diyData.page.style.titleTextColor">19:00</span>
      <span class="icon iconfont icon-xinhao" :style="'color:' + diyData.page.style.titleTextColor" />
      <span class="ml4 icon iconfont icon-iconset0250" :style="'color:' + diyData.page.style.titleTextColor" />
    </div>
    <div class="navigation d-s-c" :style="'color:' + diyData.page.style.titleTextColor">
      <img :src="diyData.page.style.toplogo" alt="">
      <div v-if="diyData.page.params && diyData.page.params.name" class="phone-top-name">{{ diyData.page.params.name }}</div>
      <div v-else class="phone-top-search-box">搜索商品</div>
      <div class="flex-1" />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    // eslint-disable-next-line vue/require-default-prop
    diyData: Object
  },
  data() {
    return {}
  }
}
</script>

<style>
  .phone-top-name {
    width: 80%;
    height: 30px;
    line-height: 30px;
  }
  .phone-top-search-box {
    width: 80%;
    height: 30px;
    line-height: 30px;
    background-color: #FFFFFF;
    border-radius: 3px;
    margin-left: 10px;
    color: #999999;
  }
  .navigation>img{
    width: 30px;
    height: 30px;
  }
</style>
