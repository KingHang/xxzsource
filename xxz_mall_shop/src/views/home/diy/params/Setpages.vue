<template>
  <div>
    <div class="common-form">
      <span>{{ curItem.name }}</span>
    </div>

    <el-form size="small" :model="curItem" label-width="100px">
      <!--页面标题-->
      <el-form-item label="标题类型：">
        <el-radio-group v-model="logo_type" @change="logechange">
          <el-radio :label="'image'">图片</el-radio>
        </el-radio-group>
      </el-form-item>
      <!--图片-->

      <el-form-item label="图片：">
        <div class="diy-setpages-cover">
          <img
            v-if="curItem.style.toplogo"
            v-img-url="curItem.style.toplogo"
            alt=""
            :style="'background-color:'+curItem.style.titleBackgroundColor+' ;'"
            @click="$parent.onEditorSelectImage(curItem.style, 'toplogo')"
          >
          <div>建议尺寸60×60</div>
        </div>
      </el-form-item>

      <!--页面名称-->
      <el-form-item label="页面名称：">
        <el-input v-model="curItem.params.name" />
        <p class="gray">页面名称仅用于后台查找</p>
      </el-form-item>

      <!--分享标题-->
      <el-form-item label="分享标题：">
        <el-input v-model="curItem.params.share_title" />
        <p class="gray">小程序端转发时显示的标题</p>
      </el-form-item>

      <!-- 背景颜色 -->
      <el-form-item label="背景颜色：">
        <div class="d-s-c">
          <el-color-picker v-model="curItem.style.titleBackgroundColor" />
          <el-button type="button" style="margin-left: 10px;" @click.stop="$parent.onEditorResetColor(curItem.style, 'titleBackgroundColor', '#ffffff')">重置</el-button>
        </div>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['curItem', 'selectedIndex', 'opts'],
  data() {
    return {
      logo_type: 'image'
    }
  },
  created() {
    if (this.curItem.style.toplogo === 'text') {
      this.logo_type = 'image'
    } else {
      this.logo_type = 'image'
    }
  },
  methods: {
    logechange(e) {
      if (e === 'text') {
        this.curItem.style.toplogo = 'text'
      } else {
        this.curItem.style.toplogo = ''
      }
    }
  }
}
</script>

<style>
  .diy-setpages-cover>img{
    width: 60px;
    height: 60px;
  }
</style>
