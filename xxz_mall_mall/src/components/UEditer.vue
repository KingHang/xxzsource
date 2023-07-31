<template>
  <div>
    <script id="editorTemp" type="text/plain" />
    <Upload v-if="isupload" :config="{total:9}" :isupload="isupload" @returnImgs="returnImgsFunc">上传图片</Upload>
  </div>
</template>

<script>
import Upload from '@/components/file/Upload'

export default {
  name: 'Uef',
  components: { Upload },
  props: {
    // eslint-disable-next-line vue/require-default-prop
    text: String,
    // eslint-disable-next-line vue/require-default-prop
    config: Object
  },
  data() {
    return {
      editorTemp: null,
      isupload: false,
      hasCallback: false,
      callback: null,
      this_config: {
        // 不需要工具栏漂浮
        autoFloatEnabled: false
      }
    }
  },
  watch: {
  },
  created() {
    /* 富文本框的默认选择图片方式，改成自定义的选择方式 */
    window.openUpload = this.openUpload
  },
  mounted() {
    Object.assign(this.this_config, this.config)
    this.editorTemp = window.UE.getEditor('editorTemp', this.this_config)
    this.editorTemp.addListener('ready', (e) => {
      this.editorTemp.setContent(this.text)
    })
    /* 监听富文本内容变化，有变化传给调用组件的页面 */
    this.editorTemp.addListener('contentChange', (e) => {
      this.$emit('contentChange', this.editorTemp.getContent())
    })
  },
  /* 销毁*/
  destroyed() {
    this.editorTemp.destroy()
  },
  methods: {
    /** 获取富文本框内容 **/
    getUEContent() {
      return this.editorTemp.getContent()
    },
    /** 打开选择图片 **/
    openUpload: function(callback) {
      this.isupload = true
      if (callback) {
        this.hasCallback = true
        this.callback = callback
      }
    },
    /** 获取图片 **/
    returnImgsFunc(e) {
      if (e != null) {
        this.hasCallback && this.callback(e)
      }
      this.isupload = false
    }
  }
}
</script>
