<template>
  <div @click.stop="$parent.$parent.onEditer(index)">
    <div class="drag optional" :class="{selected:index === selectedIndex}">
      <div class="diy-article">
        <div
          v-for="(itemTemp, indexTemp) in (item.params.source === 'choice' ? item.data : item.defaultData)"
          :key="indexTemp"
          :class="'show-type__' + itemTemp.show_type"
          class="article-item"
        >
          <!-- 小图模式 -->
          <template v-if="itemTemp.show_type === 10">
            <div class="article-item__left flex-1">
              <div class="article-item__title text-ellipsis-2">
                <span class="f18">{{ itemTemp.article_title }}</span>
              </div>
              <div class="article-item__footer">
                <span class="gray9">
                  {{ itemTemp.views_num }}次浏览
                </span>
              </div>
            </div>
            <div class="article-item__image">
              <img v-img-url="itemTemp.image" alt="">
            </div>
          </template>

          <!-- 大图模式 -->
          <template v-if="itemTemp.show_type === 20">
            <div class="article-item__title">
              <span class="f18">{{ itemTemp.article_title }}</span>
            </div>
            <div class="article-item__image">
              <img v-img-url="itemTemp.image">
            </div>
            <div class="article-item__footer">
              <span class="gray9">
                {{ itemTemp.views_num }}次浏览
              </span>
            </div>
          </template>
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
.diy-article .show-type__10{ display: flex;padding: 10px; border-bottom: 1px solid #EEEEEE;}
.diy-article .show-type__10 .article-item__image{ width: 100px; height: 100px;}
.diy-article .show-type__10 .article-item__title{ height: 40px;}
</style>
