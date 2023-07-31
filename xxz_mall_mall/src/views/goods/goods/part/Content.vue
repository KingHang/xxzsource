<template>
  <div class="basic-setting-content pl16 pr16">
    <!--商品详情-->
    <div class="common-form">商品详情</div>
    <el-form-item label="详情类型：">
      <el-radio-group v-model="form.model.is_picture">
        <el-radio :label="0">图文</el-radio>
        <el-radio :label="1">纯图</el-radio>
      </el-radio-group>
    </el-form-item>

    <!--内容-->
    <el-form-item v-show="form.model.is_picture === 0" label="详情内容：">
      <div class="edit_container">
        <Uediter ref="ue" :text="form.model.content" :config="ueditor.config" @contentChange="contentChangeFunc" />
      </div>
    </el-form-item>

    <el-form-item v-show="form.model.is_picture === 1" label="详情图片：">
      <div class="draggable-list">
        <draggable v-model="form.model.contentImage" class="wrapper">
          <transition-group>
            <div v-for="(item, index) in form.model.contentImage" :key="item.file_path" class="item">
              <img v-img-url="item.file_path">
              <a href="javascript:void(0);" class="delete-btn" @click.stop="deleteImg(index)">
                <i class="el-icon-close" />
              </a>
            </div>
          </transition-group>
        </draggable>
        <div class="item img-select" @click="openUpload">
          <i class="el-icon-plus" />
        </div>
      </div>
    </el-form-item>

    <!--商品图片组件-->
    <Upload v-if="is_upload" :config="config" :isupload="is_upload" @returnImgs="returnImgsFunc">上传图片</Upload>
  </div>
</template>

<script>
import Uediter from '@/components/UE.vue'
import Upload from '@/components/file/Upload'
import draggable from 'vuedraggable'

export default {
  components: {
    /* 编辑器 */
    Uediter,
    Upload,
    draggable
  },
  data() {
    return {
      /* 富文本框配置 */
      ueditor: {
        text: '',
        config: {
          initialFrameWidth: '90%',
          initialFrameHeight: 800
        }
      },
      is_upload: false,
      config: {
        total: 9,
        file_type: 'image'
      }
    }
  },
  created() {
    // this.ueditor.text = this.form.model.content
  },
  inject: ['form'],
  methods: {
    /** 获取富文本框内容 **/
    getContent() {
      // return this.$refs.ue.getUEContent()
    },
    /** 获取富文本内容 **/
    contentChangeFunc(e) {
      this.form.model.content = e
    },
    openUpload() {
      this.is_upload = true
    },
    /** 上传商品图片 **/
    returnImgsFunc(e) {
      if (e != null) {
        const imgs = this.form.model.contentImage.concat(e)
        this.$set(this.form.model, 'contentImage', imgs)
      }
      this.is_upload = false
    },
    /** 删除商品图片 **/
    deleteImg(index) {
      this.form.model.contentImage.splice(index, 1)
    }
  }
}
</script>
