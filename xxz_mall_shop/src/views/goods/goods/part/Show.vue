<template>
  <el-dialog
    :title="title"
    :visible.sync="dialogVisible"
    width="300px"
    center
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="dialogFormVisible"
  >
    <div class="show-center">
      <el-avatar shape="square" :size="180" :src="image" />
    </div>

    <div slot="footer" class="dialog-footer">
      <el-button size="small" @click="dialogVisible = false">{{ $t('page.cancel') }}</el-button>
    </div>
  </el-dialog>
</template>

<script>
export default {
  props: {
    scene: {
      type: String,
      required: true
    },
    image: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      dialogVisible: true
    }
  },
  computed: {
    title() {
      return this.scene === 'qr' ? '签到码' : '推广'
    }
  },
  created() {
  },
  methods: {
    /** 关闭弹窗 **/
    dialogFormVisible(e) {
      if (e) {
        this.$emit('close', {
          type: 'success',
          openDialog: false
        })
      } else {
        this.$emit('close', {
          type: 'error',
          openDialog: false
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  .show-center {
    text-align: center;
  }
</style>
