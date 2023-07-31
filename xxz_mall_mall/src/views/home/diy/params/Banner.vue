<template>
  <div>
    <div class="common-form">
      <span>{{ curItem.name }}</span>
    </div>
    <el-form size="small" :model="curItem" label-width="100px">
      <el-form-item label="指示点形状：">
        <el-radio-group v-model="curItem.style.btnShape">
          <el-radio :label="3">圆形</el-radio>
          <el-radio :label="6">正方形</el-radio>
          <el-radio :label="9">长方形</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="指示点颜色：">
        <div class="d-s-c">
          <el-color-picker v-model="curItem.style.btnColor" />
          <el-button type="button" style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'btnColor', '#ffffff')">重置</el-button>
        </div>
      </el-form-item>
      <el-form-item label="切换时间：">
        <el-input v-model="curItem.params.interval" />
        <p>轮播图自动切换的间隔时间，单位：毫秒</p>
      </el-form-item>
      <el-form-item label="图片：">
        <div v-for="(banner, indexTemp) in curItem.data" :key="indexTemp" class="param-img-item">
          <div class="delete-box">
            <i class="el-icon-delete-solid" @click="$parent.onEditorDeleleData(indexTemp, selectedIndex)" />
          </div>
          <div class="pic">
            <img v-img-url="banner.imgUrl" alt="" @click="$parent.onEditorSelectImage(banner, 'imgUrl')">
          </div>
          <p class="tc gray9">建议尺寸750*206</p>
          <div class="d-s-c">
            <div class="url-box flex-1">
              <span class="key-name">链接地址：</span>
              <el-input v-model="banner.name" placeholder="请选择链接地址" />
            </div>
            <div class="url-box ml10">
              <el-button type="primary" @click="changeLink(indexTemp)">选择链接</el-button>
            </div>
          </div>
        </div>
        <div class="d-c-c">
          <el-button @click="$parent.onEditorAddData">添加一个</el-button>
        </div>
      </el-form-item>
    </el-form>
    <Setlink v-if="is_linkset" :is_linkset="is_linkset" @closeDialog="closeLinkset">选择链接</Setlink>
  </div>
</template>

<script>
import Setlink from '@/components/setlink/Setlink'

export default {
  components: {
    Setlink
  },
  // eslint-disable-next-line vue/require-prop-types
  props: ['curItem', 'selectedIndex'],
  data() {
    return {
      /* 是否选择链接 */
      is_linkset: false,
      index: null
    }
  },
  methods: {
    /** 选择链接 **/
    changeLink(index) {
      this.is_linkset = true
      this.index = index
    },
    /** 获取链接并关闭弹窗 **/
    closeLinkset(e) {
      this.is_linkset = false
      this.curItem.data[this.index].linkUrl = e.url
      this.curItem.data[this.index].name = '链接到' + ' ' + e.type + ' ' + e.name
    }
  }
}
</script>
