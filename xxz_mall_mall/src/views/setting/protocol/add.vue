<template>
  <div class="product-add">
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" label-width="180px">
      <!--基础信息-->
      <Basic />

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" @click="cancelFunc">{{ $t('page.backList') }}</el-button>
        <el-button size="small" type="primary" :loading="loading" @click="onSubmit">{{ $t('page.submit') }}</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
import SettingApi from '@/api/setting.js'
import Basic from './part/Basic.vue'

export default {
  components: {
    /* 基础信息 */
    Basic
  },
  data() {
    return {
      /* 是否正在加载 */
      loading: false,
      /* form表单数据 */
      form: {
        /* 协议标题 */
        agreement_title: '',
        /* 关键字 */
        keyword: '',
        /* 协议内容 */
        agreement_content: ''
      }
    }
  },
  provide: function() {
    return {
      form: this.form
    }
  },
  created() {
  },
  methods: {
    /** 提交 **/
    onSubmit() {
      const self = this
      self.$refs.form.validate(valid => {
        if (valid) {
          self.loading = true
          SettingApi.createAgreement({ ...self.form }, true)
            .then(data => {
              self.loading = false
              self.$message({
                message: self.$t('msg.addSuccess'),
                type: 'success'
              })
              self.$router.push('/setting/protocol/index')
            })
            .catch(() => {
              self.loading = false
            })
        }
      })
    },
    /** 取消 **/
    cancelFunc() {
      this.$router.back(-1)
    }
  }
}
</script>

<style lang="scss" scoped>
  .product-add {
    padding-bottom: 100px;
  }
</style>
