<template>
  <div>
    <div @click.stop="$parent.$parent.onEditer(index)">
      <div class="drag optional" :class="{selected:index === selectedIndex}">
        <div class="diy-blank" :style="{height: item.style.height +'px', background:item.style.background }" />
        <div class="btn-edit-del">
          <div class="btn-del" @click.stop="$parent.$parent.onDeleleItem(index)">删除</div>
        </div>
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
