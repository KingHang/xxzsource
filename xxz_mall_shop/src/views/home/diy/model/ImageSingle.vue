<template>
  <div @click.stop="$parent.$parent.onEditer(index)">
    <div class="drag optional" :class="{selected:index === selectedIndex}">
      <div class="diy-imageSingle" :style="{ paddingBottom: item.style.paddingTop + 'px', background: item.style.background}">
        <div v-for="(imageSingle, indexTemp) in item.data" :key="indexTemp" class="item-image" :style="{padding: item.style.paddingTop + 'px ' + item.style.paddingLeft + 'px 0'}">
          <img v-img-url="imageSingle.imgUrl">
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
.diy-imageSingle img {
  max-width: 100%;
}
</style>
