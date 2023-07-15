<template>
  <div class="product-add">
    <!--form表单-->
    <el-form v-if="!loading" ref="form" size="small" :model="form" label-width="180px">
      <!--基础信息-->
      <Basic />

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" @click="cancelFunc">{{ $t('page.backList') }}</el-button>
        <el-button size="small" type="primary" :disabled="save_loading" @click="onSubmit">{{ $t('page.save') }}</el-button>
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
      /* 协议id */
      id: 0,
      /* 是否正在加载 */
      loading: true,
      /* 是否正在提交保存 */
      save_loading: false,
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
    /* 获取基础数据 */
    this.id = this.$route.query.id
    this.getBaseData()
  },
  methods: {
    /** 获取基础数据 **/
    getBaseData() {
      const self = this
      SettingApi.agreementDetail({ id: self.id }, true)
        .then(res => {
          Object.assign(self.form, res.data.detail)
          self.loading = false
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 提交 **/
    onSubmit() {
      const self = this
      self.$refs.form.validate(valid => {
        if (valid) {
          self.save_loading = true
          SettingApi.updateAgreement({ id: self.id, ...self.form }, true)
            .then(data => {
              self.save_loading = false
              self.$message({
                message: self.$t('msg.saveSuccess'),
                type: 'success'
              })
              self.cancelFunc()
            })
            .catch(() => {
              self.save_loading = false
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
