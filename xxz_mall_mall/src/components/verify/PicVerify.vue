<template>
  <el-dialog
    title="图形验证"
    :visible.sync="dialogVisible"
    width="350px"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="dialogFormVisible(false)"
  >
    <div>
      <slide-verify
        ref="slideblock"
        :imgs="picArray"
        :slider-text="text"
        :accuracy="accuracy"
        @again="onAgain"
        @fulfilled="onFulfilled"
        @success="onSuccess"
        @fail="onFail"
        @refresh="onRefresh"
      />
    </div>
  </el-dialog>
</template>

<script>
export default {
  // eslint-disable-next-line vue/prop-name-casing,vue/require-prop-types
  props: ['is_open'],
  data() {
    return {
      text: '向右滑动->', // 设置滑块文字
      // 精确度小，可允许的误差范围小；为1时，则表示滑块要与凹槽完全重叠，才能验证成功。默认值为5
      accuracy: 5,
      dialogVisible: false,
      type: 'error',
      picArray: [
        'https://img.dfhlyl.com/2023032410071264bea6389.jpg',
        'https://img.dfhlyl.com/202303241007121dd941485.jpg',
        'https://img.dfhlyl.com/20230324100712739297403.jpg',
        'https://img.dfhlyl.com/20230324100712ce7f04007.jpg',
        'https://img.dfhlyl.com/20230324100712a7b3a5425.jpg',
        'https://img.dfhlyl.com/2023032410071238ac87635.jpg',
        'https://img.dfhlyl.com/20230324100712190980901.jpg',
        'https://img.dfhlyl.com/20230324100712243002422.jpg',
        'https://img.dfhlyl.com/20230324100712c40312754.jpg',
        'https://img.dfhlyl.com/20230324100712f0f1b9488.jpg'
      ]
    }
  },
  watch: {
    is_open: function(n, o) {
      if (n !== o) {
        this.dialogVisible = n
        this.type = 'error'
      }
    }
  },
  mounted() {
    this.onRefresh()
  },
  methods: {
    // 验证通过
    onSuccess(times) {
      this.dialogFormVisible(true)
    },
    // 验证失败
    onFail() {},
    // 滑块上的刷新
    onRefresh() {},
    // 刷新后执行的回调函数
    onFulfilled() {},
    // 检测是否人为操作
    onAgain() {
      this.msg = 'try again'
      // 刷新
      this.$refs.slideblock.reset()
    },
    // 重置刷新
    handleClick() {
      this.$refs.slideblock.reset()
    },
    /** 关闭弹窗 **/
    dialogFormVisible(flag) {
      if (flag) {
        this.dialogVisible = false
        this.$emit('closeDialog', {
          type: 'success'
        })
      } else {
        this.dialogVisible = false
        this.$emit('closeDialog', {
          type: 'error'
        })
      }
      this.$refs.slideblock.reset()
    }
  }
}
</script>
