<template>
  <div class="drag optional" :class="{selected: index === selectedIndex}" @click.stop="$parent.$parent.onEditer(index)">
    <div class="diy-banner">
      <div class="img-list">
        <template v-if="index <= 1">
          <img v-for="(banner, indexTemp) in item.data" :key="indexTemp" v-img-url="banner.imgUrl">
        </template>
      </div>

      <div class="dots center" :class="item.style.btnShape">
        <span v-for="(banner, indexTemp) in item.data" :key="indexTemp" :style="{background:item.style.btnColor}" />
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
    }
  },
  methods: {
  }
}
</script>

<style>
.diy-banner{ height: 180px; overflow: hidden;}
.diy-banner img{width: 100%; height: 180px; object-fit: fill;}
</style>
