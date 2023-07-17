<template>
  <el-dialog
    title="超链接设置"
    :visible.sync="dialogVisible"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="dialogFormVisible"
  >
    <!--内容-->
    <el-tabs v-model="activeName" type="border-card">
      <!--页面-->
      <el-tab-pane label="页面" name="pages">
        <Pages v-if="activeName === 'pages'" @changeData="activeDataFunc" />
      </el-tab-pane>

      <el-tab-pane label="营销" name="market">
        <Marketing v-if="activeName === 'market'" @changeData="activeDataFunc" />
      </el-tab-pane>

      <el-tab-pane label="产品" name="product">
        <Product v-if="activeName === 'product'" @changeData="activeDataFunc" />
      </el-tab-pane>

      <el-tab-pane label="文章" name="Article">
        <Article v-if="activeName === 'Article'" @changeData="activeDataFunc" />
      </el-tab-pane>

      <!--  <el-tab-pane label="小程序" name="SmallProgram">
                <SmallProgram v-if="activeName === 'SmallProgram'" @changeData="activeDataFunc" />
            </el-tab-pane>
            <el-tab-pane label="H5" name="H5">
                <H5 v-if="activeName === 'H5'" @changeData="activeDataFunc" />
            </el-tab-pane>-->

      <el-tab-pane label="自定义" name="diypage">
        <DiyPage v-if="activeName === 'diypage'" @changeData="activeDataFunc" />
      </el-tab-pane>

      <el-tab-pane label="我的菜单" name="menu">
        <Menu v-if="activeName === 'menu'" @changeData="activeDataFunc" />
      </el-tab-pane>

      <el-tab-pane label="小程序" name="applets">
        <Applets v-if="activeName === 'applets'" :current-data="currentData" @changeData="activeDataFunc" />
      </el-tab-pane>
    </el-tabs>

    <div slot="footer" class="dialog-footer d-b-c">
      <div class="flex-1">
        <div v-if="activeData != null" class="d-s-s d-c tl">
          <p v-if="activeData.type !== '小程序'" class="text-ellipsis setlink-set-link">
            <span>当前链接：</span>
            <span class="gray9">{{ activeData.type }}</span>
            <span class="p-0-10 gray">/</span>
            <span class="blue">{{ activeData.name }}</span>
          </p>
          <p v-else>
            <span>当前小程序：</span>
            <span class="blue">{{ activeData.envVersion === 'release' ? '正式版' : '体验版' }} appid {{ activeData.name }}</span>
          </p>
          <p class="text-ellipsis gray" style="font-size: 10px;">{{ activeData.url }}</p>
        </div>
        <div v-else class="tl">
          暂无
        </div>
      </div>
      <div class="setlink-footer-btn">
        <el-button size="small" @click="dialogFormVisible(false)">取 消</el-button>
        <el-button size="small" type="primary" @click="dialogFormVisible(true)">确 定</el-button>
      </div>
    </div>
  </el-dialog>
</template>

<script>
import Pages from './part/Pages.vue'
import Marketing from './part/Marketing.vue'
import Product from './part/Product.vue'
import DiyPage from './part/DiyPage.vue'
import Menu from './part/Menu.vue'
import Article from './part/Article.vue'
import Applets from './part/Applets.vue'

export default {
  components: {
    Menu,
    Pages,
    Marketing,
    Product,
    Article,
    Applets,
    DiyPage
  },
  // eslint-disable-next-line vue/require-prop-types,vue/prop-name-casing
  props: ['is_linkset', 'currentData'],
  data() {
    return {
      /* 是否显示 */
      dialogVisible: true,
      /* 选中的链接 */
      activeData: null,
      activeName: 'pages'
    }
  },
  created() {
    this.dialogVisible = this.is_linkset
  },
  methods: {
    /** 关闭弹窗 **/
    dialogFormVisible(e) {
      if (e) {
        this.$emit('closeDialog', this.activeData)
      } else {
        this.$emit('closeDialog', null)
      }
    },
    /** 页面返回值 **/
    activeDataFunc(e) {
      this.activeData = e
    }
  }
}
</script>

<style>
.marketing-box .el-tabs__item {font-size: 12px;}
.setlink-footer-btn{ width: 160px;}
.setlink-set-link{ width: 500px;}
</style>
