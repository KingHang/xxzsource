<template>
  <div>
    <div class="drag optional" :class="{selected: index === selectedIndex}" @click.stop="$parent.$parent.onEditer(index)">
      <div class="diy-guide" :style="{padding: item.style.paddingTop +'px '+'0', background:item.style.background }">
        <p class="line" :style="{borderTopWidth: item.style.lineHeight +'px',borderTopColor:item.style.lineColor,borderTopStyle: item.style.lineStyle }" />
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
