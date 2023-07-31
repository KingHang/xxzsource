<template>
  <div @click.stop="$parent.$parent.onEditer(index)">
    <div class="drag optional" :class="{selected: index === selectedIndex}">
      <div
        class="diy-richText"
        :style="{background: item.style.background, padding: item.style.paddingTop + 'px ' + item.style.paddingLeft + 'px'}"
        v-html="item.params.content"
      />
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
.diy-richText video{ width: 100%;}
</style>
