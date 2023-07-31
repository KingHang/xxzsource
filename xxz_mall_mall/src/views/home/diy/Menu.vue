<template>
  <div v-if="typeList != null">
    <div class="diy-new-menu-title">组件库</div>

    <!--菜单-->
    <div class="diy-new-menu-boxer">
      <el-collapse v-model="activeNames">
        <el-collapse-item v-for="(group, key) in typeList" :key="key" :title="key | typename" :name="key">
          <div class="diy-new-box">
            <div v-for="(item, index) in group.children" :key="index" class="diy-new-box-item" @click="$parent.onAddItem(item.type)">
              <div class="diy-new-box-thumb">
                <img :src="item.type | thumbFilter" alt="">
              </div>
              <div class="diy-new-box-name">{{ item.name }}</div>
            </div>
          </div>
        </el-collapse-item>
      </el-collapse>
    </div>
  </div>
</template>

<script>
import searchPng from '@/assets/img/diy/search.png'
import productPng from '@/assets/img/diy/product.png'
import categoryPng from '@/assets/img/diy/category.png'
import couponPng from '@/assets/img/diy/coupon.png'
import assembleProductPng from '@/assets/img/diy/assembleProduct.png'
import bargainProductPng from '@/assets/img/diy/bargainProduct.png'
import seckillProductPng from '@/assets/img/diy/seckillProduct.png'
import formInfoPng from '@/assets/img/diy/formInfo.png'
import cardPng from '@/assets/img/diy/card.png'
import bannerPng from '@/assets/img/diy/banner.png'
import imageSinglePng from '@/assets/img/diy/imageSingle.png'
import navBarPng from '@/assets/img/diy/navBar.png'
import videoPng from '@/assets/img/diy/video.png'
import articlePng from '@/assets/img/diy/article.png'
import specialPng from '@/assets/img/diy/special.png'
import noticePng from '@/assets/img/diy/notice.png'
import windowPng from '@/assets/img/diy/window.png'
import blankPng from '@/assets/img/diy/blank.png'
import guidePng from '@/assets/img/diy/guide.png'
import richTextPng from '@/assets/img/diy/richText.png'
import servicePng from '@/assets/img/diy/service.png'

export default {
  filters: {
    /* 组件thumb */
    thumbFilter(type) {
      switch (type) {
        case 'search':
          return searchPng
        case 'product':
          return productPng
        case 'category':
          return categoryPng
        case 'voucher':
          return couponPng
        case 'assembleProduct':
          return assembleProductPng
        case 'bargainProduct':
          return bargainProductPng
        case 'seckillProduct':
          return seckillProductPng
        case 'formInfo':
          return formInfoPng
        case 'card':
          return cardPng
        case 'banner':
          return bannerPng
        case 'imageSingle':
          return imageSinglePng
        case 'navBar':
          return navBarPng
        case 'video':
          return videoPng
        case 'news':
          return articlePng
        case 'special':
          return specialPng
        case 'notice':
          return noticePng
        case 'window':
          return windowPng
        case 'blank':
          return blankPng
        case 'guide':
          return guidePng
        case 'richText':
          return richTextPng
        case 'service':
          return servicePng
        default:
          return ''
      }
    },
    /* 组名转换成中文 */
    typename(type) {
      let name = ''
      if (type === 'media') {
        name = '媒体组件'
      } else if (type === 'mall') {
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
      activeNames: ['media', 'mall', 'tools'],
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

<style>
  .diy-new-menu-title {
    font-size: 14px;
    font-family: Microsoft YaHei-Bold, Microsoft YaHei;
    font-weight: bold;
    color: #4D4D4D;
    line-height: 16px;
    margin-bottom: 20px;
  }

  .diy-new-menu-boxer .el-collapse {
    border-top: none;
    border-bottom: none;
  }

  .diy-new-menu-boxer .el-collapse-item__header {
    font-size: 12px;
    font-family: Microsoft YaHei-Regular, Microsoft YaHei;
    font-weight: 400;
    color: #4D4D4D;
    line-height: 14px;
    border-bottom: 1px solid #E0E0E0;
  }

  .diy-new-menu-boxer .el-icon-arrow-right {
    color: #000000;
  }

  .diy-new-menu-boxer .el-collapse-item__wrap {
    border-bottom: none;
  }

  .diy-new-menu-boxer .diy-new-box {
    display: flex;
    flex-wrap: wrap;
    padding: 10px 0 0;
  }

  .diy-new-menu-boxer .diy-new-box .diy-new-box-item {
    width: 90px;
    height: 110px;
    padding: 24px 0;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    cursor: pointer;
  }

  .diy-new-menu-boxer .diy-new-box .diy-new-box-item:nth-child(3n+2) {
    margin: 0 10px;
  }

  .diy-new-menu-boxer .diy-new-box .diy-new-box-item:hover {
    background: #F2F6FA;
    border-radius: 4px;
  }

  .diy-new-menu-boxer .diy-new-box .diy-new-box-item .diy-new-box-thumb {
    width: 36px;
    height: 36px;
  }

  .diy-new-menu-boxer .diy-new-box .diy-new-box-item .diy-new-box-thumb img {
    width: 100%;
  }

  .diy-new-menu-boxer .diy-new-box .diy-new-box-item .diy-new-box-name {
    font-size: 12px;
    font-family: Microsoft YaHei-Regular, Microsoft YaHei;
    font-weight: 400;
    color: #808080;
    line-height: 14px;
    margin-top: 10px;
    width: 100%;
    text-align: center;
  }
</style>
