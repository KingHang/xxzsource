<template>
  <div v-if="typeList != null">
    <div class="common-form">组件库</div>
    <div class="min-group">
      <div v-for="(group,key) in typeList" :key="key">
        <div class="hd">{{ key | typename }}</div>
        <div class="bd">
          <div v-for="(item, index) in group.children" :key="index" class="item" @click="$parent.onAddItem(item.type)">
            <p class="p-icon icon iconfont icon-tuichu" />
            <p class="p-txt">{{ item.name }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  filters: {
    /* 组名转换成中文 */
    typename: function(type) {
      let name = ''
      if (type === 'media') {
        name = '媒体组件'
      } else if (type === 'shop') {
        name = '商城组件'
      } else if (type === 'tools') {
        name = '工具组件'
      }
      return name
    }
  },
  props: {
    // eslint-disable-next-line vue/require-default-prop
    defaultData: Object
  },
  data() {
    return {
      /* 类别列表 */
      typeList: null
    }
  },
  created() {
    this.init()
  },
  methods: {
    /** 初始化数据 **/
    init() {
      const tempList = {}
      for (const key in this.defaultData) {
        const item = this.defaultData[key]
        // eslint-disable-next-line no-prototype-builtins
        if (!tempList.hasOwnProperty(item.group)) {
          tempList[item.group] = {}
          tempList[item.group].children = []
        }
        tempList[item.group].children.push(item)
      }
      this.typeList = tempList
    }
  }
}
</script>
