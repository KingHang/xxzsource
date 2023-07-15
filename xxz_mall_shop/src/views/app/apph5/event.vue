<template>
  <div class="common-seach-wrap">
    <!--基础设置-->
    <Pay v-if="activeName === 'pay'" />
  </div>
</template>

<script>
import bus from '@/utils/eventBus.js'
import Pay from './part/pay'

export default {
  components: {
    Pay
  },
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      form: {},
      /* 参数 */
      param: {},
      /* 当前选中 */
      activeName: 'pay',
      /* 切换数组原始数据 */
      sourceList: [
        {
          key: 'pay',
          value: '支付设置',
          path: '/app/apph5/pay'
        }
      ],
      /* 权限筛选后的数据 */
      tabList: []
    }
  },
  created() {
    this.tabList = this.authFilter()

    if (this.tabList.length > 0) {
      this.activeName = this.tabList[0].key
    }

    if (this.$route.query.type != null) {
      this.activeName = this.$route.query.type
    }

    /* 监听传插件的值 */
    bus.$on('activeValue', res => {
      this.activeName = res
    })

    /* 发送类别切换 */
    const params = {
      active: this.activeName,
      list: this.tabList,
      tab_type: 'apph5'
    }
    bus.$emit('tabData', params)
  },
  beforeDestroy() {
    // 发送类别切换
    bus.$emit('tabData', { active: null, tab_type: 'apph5', list: [] })
    bus.$off('activeValue')
  },
  methods: {
    /** 权限过滤 **/
    authFilter() {
      const list = []
      for (let i = 0; i < this.sourceList.length; i++) {
        const item = this.sourceList[i]
        if (this.$filter.isAuth(item.path)) {
          list.push(item)
        }
      }
      return list
    }
  }
}
</script>

<style>
  .operation-wrap {
    height: 124px;
    border-radius: 8px;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    padding: 30px 30px;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    overflow: hidden;
    background: #909399;
    background-size: 100% 100%;
    color: #fff;
  }
</style>
