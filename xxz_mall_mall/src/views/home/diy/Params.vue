<template>
  <div id="diy-editor" ref="diy-editor" class="diy-editor form-horizontal">
    <template v-show="diyData.items.length && form.curItem">
      <!--顶部设置-->
      <template v-if="form.curItem.type === 'page'">
        <Setpages :cur-item="form.curItem" />
      </template>

      <!--搜索框-->
      <template v-if="form.curItem.type === 'search'">
        <Search :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--图片轮播-->
      <template v-if="form.curItem.type === 'banner'">
        <Banner :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--图片-->
      <template v-if="form.curItem.type === 'imageSingle'">
        <ImageSingle :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--图片橱窗-->
      <template v-if="form.curItem.type === 'window'">
        <Window :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--视频组件-->
      <template v-if="form.curItem.type === 'video'">
        <Video :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--文章组件-->
      <template v-if="form.curItem.type === 'news'">
        <Article :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--头条快报-->
      <template v-if="form.curItem.type === 'special'">
        <Special :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--公告组-->
      <template v-if="form.curItem.type === 'notice'">
        <Notice :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--导航组-->
      <template v-if="form.curItem.type === 'navBar'">
        <NavBar :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--商品组-->
      <template v-if="form.curItem.type === 'product'">
        <Product :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--商品分类组-->
      <template v-if="form.curItem.type === 'category'">
        <Category :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--权益卡组-->
      <template v-if="form.curItem.type === 'card'">
        <Card :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--优惠券-->
      <template v-if="form.curItem.type === 'voucher'">
        <Coupon :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--门店-->
      <template v-if="form.curItem.type === 'store'">
        <Store :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--客服-->
      <template v-if="form.curItem.type === 'service'">
        <Service :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--富文本-->
      <template v-if="form.curItem.type === 'richText'">
        <RichText :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--辅助空白-->
      <template v-if="form.curItem.type === 'blank'">
        <Blank :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--辅助线-->
      <template v-if="form.curItem.type === 'guide'">
        <Guide :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--秒杀-->
      <template v-if="form.curItem.type === 'seckillProduct'">
        <Seckill :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--拼团-->
      <template v-if="form.curItem.type === 'assembleProduct'">
        <assembleProduct :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>

      <!--砍价-->
      <template v-if="form.curItem.type === 'bargainProduct'">
        <BargainProduct :cur-item="form.curItem" :selected-index="form.selectedIndex" />
      </template>
    </template>

    <!--上传图片-->
    <Upload v-if="isupload" :isupload="isupload" :config="{ total: 3 }" @returnImgs="returnImgsFunc" />

    <!--选择商品-->
    <ProductSelect :isproduct="isproduct" :exclude-ids="excludeIds" :islist="islist" @closeDialog="closeProductDialogFunc($event)">产品列表弹出层</ProductSelect>

    <!--选择门店-->
    <StoreSelect :isstore="isstore" :islist="isstorelist" />
  </div>
</template>

<script>
import { deepClone } from '@/utils/base.js'
import Setpages from './params/Setpages.vue'
import Search from './params/Search.vue'
import Banner from './params/Banner.vue'
import ImageSingle from './params/ImageSingle.vue'
import Window from './params/Window.vue'
import Video from './params/Video.vue'
import Article from './params/Article.vue'
import Special from './params/Special.vue'
import Notice from './params/Notice.vue'
import NavBar from './params/NavBar.vue'
import Product from './params/Product.vue'
import Category from './params/Category.vue'
import Card from './params/Card.vue'
import Coupon from './params/Coupon.vue'
import Store from './params/Store.vue'
import Service from './params/Service.vue'
import RichText from './params/RichText.vue'
import Blank from './params/Blank.vue'
import Guide from './params/Guide.vue'
import Seckill from './params/Seckill.vue'
import assembleProduct from './params/assembleProduct.vue'
import BargainProduct from './params/BargainProduct.vue'
import Upload from '@/components/file/Upload'
import ProductSelect from '@/components/goods/Product'
import StoreSelect from '@/components/store/StoreSelect'

export default {
  components: {
    /* 顶部设置 */
    Setpages,
    /* 搜索框 */
    Search,
    /* 图片轮播组件 */
    Banner,
    /* 图片组件 */
    ImageSingle,
    /* 图片橱窗 */
    Window,
    /* 视频 */
    Video,
    /* 文章 */
    Article,
    /* 文章 */
    Special,
    /* 公告组 */
    Notice,
    /* 导航组 */
    NavBar,
    /* 商品组 */
    Product,
    /* 商品分类组 */
    Category,
    /* 权益卡组 */
    Card,
    /* 优惠券 */
    Coupon,
    /* 门店 */
    Store,
    /* 客服 */
    Service,
    /* 富文本 */
    RichText,
    /* 辅助空白 */
    Blank,
    /* 辅助线 */
    Guide,
    /* 秒杀 */
    Seckill,
    /* 拼团 */
    assembleProduct,
    /* 砍价 */
    BargainProduct,
    /* 上传图片 */
    Upload,
    /* 商品选择 */
    ProductSelect,
    /* 门店选中 */
    StoreSelect
  },
  // eslint-disable-next-line vue/require-prop-types
  props: ['form', 'defaultData', 'diyData', 'opts'],
  data() {
    return {
      /* 是否上传图片 */
      isupload: false,
      /* 图片当前对象 */
      imgModel: null,
      /* 是否打开产品弹出层 */
      isproduct: false,
      /* 商品需要去重的 */
      excludeIds: [],
      /* 是否多选 */
      islist: false,
      /* 是否显示门店选中 */
      isstore: false,
      /* 门店是否多选 */
      isstorelist: false
    }
  },
  created() {},
  methods: {
    /**
     * 编辑器：添加data元素
     */
    onEditorAddData: function() {
      const self = this
      // 新增data数据
      const newDataItem = deepClone(self.defaultData[self.form.curItem.type].data[0])
      self.form.curItem.data.push(newDataItem)
    },
    /**
     * 编辑器：重置颜色
     * @param holder
     * @param attribute
     * @param color
     */
    onEditorResetColor: function(holder, attribute, color) {
      holder[attribute].titleBackgroundColor = color
    },
    /**
     * 编辑器：删除data元素
     * @param index
     * @param selectedIndex
     */
    onEditorDeleleData: function(index, selectedIndex) {
      const self = this
      if (self.diyData.items[selectedIndex].data.length <= 1) {
        self.$message({
          message: '至少保留一个',
          type: 'error'
        })
        return false
      }
      self.diyData.items[selectedIndex].data.splice(index, 1)
    },
    /**
     * 编辑器：选择图片
     * @param imgUrl
     * @param index
     */
    onEditorSelectImage: function(index, imgUrl) {
      this.isupload = true
      this.imgModel = {
        index: index,
        imgUrl: imgUrl
      }
    },
    /** 上传图片 **/
    returnImgsFunc(e) {
      if (e != null) {
        this.imgModel.index[this.imgModel.imgUrl] = e[0]['file_path']
      }
      this.isupload = false
    },
    /** 商品选择列表弹出层 **/
    openProduct(list, islist) {
      const arr = []
      list.forEach(item => {
        arr.push(item.product_id)
      })
      this.excludeIds = arr
      this.islist = !!(islist && typeof islist !== 'undefined')
      this.isproduct = true
    },
    /** 商品选择关闭弹窗 **/
    closeProductDialogFunc(e) {
      if (this.form.curItem == null) {
        return
      }
      this.isproduct = false
      if (e.type === 'success') {
        if (this.islist) {
          this.form.curItem.data = this.form.curItem.data.concat(e.params)
        } else {
          this.form.curItem.data.push(e.params)
        }
      }
    },
    /** 商品选择列表弹出层 **/
    openStore(list, islist) {
      const arr = []
      list.forEach(item => {
        arr.push(item.store_id)
      })
      this.excludeIds = arr
      this.isstorelist = !!(islist && typeof islist !== 'undefined')
      this.isstore = true
    }
  }
}
</script>

<style>
.param-img-item {
  position: relative;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #eeeeee;
  line-height: 20px;
}
.param-img-item .delete-box {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 20px;
  cursor: pointer;
  color: #cccccc;
}
.param-img-item .delete-box:hover {
  color: rgb(255, 51, 0);
}
.param-img-item .pic img {
  width: 200px;
  height: 100px;
  margin: 0 auto;
}
.param-img-item .icon img {
  width: 100px;
  height: 100px;
  margin: 0 auto;
}
.param-img-item .url-box {
  display: flex;
  justify-content: flex-start;
  line-height: 40px;
}
.param-img-item .url-box .key-name {
  display: block;
  width: 80px;
}
.param-img-item .url-box .el-input {
  flex: 1;
}
</style>
