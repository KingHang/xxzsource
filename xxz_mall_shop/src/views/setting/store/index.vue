<template>
  <div class="product-add">
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" label-width="150px">
      <!--添加门店-->
      <div class="common-form">商城设置</div>

      <el-form-item label="商城名称" :rules="[{required: true, message: ' '}]" prop="name">
        <el-input v-model="form.name" placeholder="商城名称" class="max-w460" />
      </el-form-item>

      <el-form-item label="商城logo" :rules="[{required: true, message: ' '}]" prop="logo">
        <div class="draggable-list">
          <div v-if="form.logo !== ''" class="item">
            <img :src="form.logo" alt="">
            <a href="javascript:void(0);" class="delete-btn" @click.stop="deleteImg('shop_logo')">
              <i class="el-icon-close" />
            </a>
          </div>
          <div v-else class="item img-select" @click="chooseImg('shop_logo')">
            <i class="el-icon-plus" />
          </div>
        </div>
      </el-form-item>

      <el-form-item label="配送方式">
        <el-checkbox-group v-model="form.checkedCities">
          <el-checkbox v-for="(item,index) in all_type" :key="index" :label="item.value">{{ item.name }}</el-checkbox>
        </el-checkbox-group>
        <div class="tips">注：配送方式至少选择一个</div>
      </el-form-item>

      <el-form-item label="客服微信二维码" :rules="[{required: true, message: ' '}]" prop="kefu">
        <div class="draggable-list">
          <div v-if="form.kefu !== ''" class="item">
            <img :src="form.kefu" alt="">
            <a href="javascript:void(0);" class="delete-btn" @click.stop="deleteImg('shop_kefu')">
              <i class="el-icon-close" />
            </a>
          </div>
          <div v-else class="item img-select" @click="chooseImg('shop_kefu')">
            <i class="el-icon-plus" />
          </div>
        </div>
      </el-form-item>

      <div class="common-form">日志记录</div>

      <el-form-item label="是否记录查询日志" prop="customer">
        <el-checkbox v-model="form.is_get_log">是否记录查询日志</el-checkbox>
        <div class="tips">如果记录，日志量会有点大</div>
      </el-form-item>

      <div class="common-form">物流查询api</div>

      <el-form-item label="快递100 Customer" :rules="[{required: true,message: ' '}]" prop="customer">
        <el-input v-model="form.customer" placeholder="" class="max-w460" />
        <div class="tips">用于查询物流信息,<el-link :underline="false" href="https://www.kuaidi100.com/openapi/" target="_blank" type="primary">快递100申请</el-link></div>
      </el-form-item>

      <el-form-item label="快递100 Key" :rules="[{required: true,message: ' '}]" prop="key">
        <el-input v-model="form.key" placeholder="" class="max-w460" />
      </el-form-item>

      <div class="common-form">商户入驻设置</div>

      <el-form-item label="是否开启短信验证">
        <div>
          <el-radio v-model="form.sms_open" label="1">开启</el-radio>
          <el-radio v-model="form.sms_open" label="0">关闭</el-radio>
        </div>
      </el-form-item>

      <div class="common-form">平台运营设置</div>

      <el-form-item label="抽成百分比(%)" :rules="[{required: true,message: ' '}]" prop="commission_rate">
        <el-input v-model="form.commission_rate" placeholder="抽查比例" class="max-w460" type="number" @keyup.native="renumber($event)" />
      </el-form-item>

      <el-form-item label="商品新增是否审核">
        <div>
          <el-radio v-model="form.add_audit" label="1">是</el-radio>
          <el-radio v-model="form.add_audit" label="0">否</el-radio>
        </div>
      </el-form-item>

      <el-form-item label="商品修改是否审核">
        <div>
          <el-radio v-model="form.edit_audit" label="1">是</el-radio>
          <el-radio v-model="form.edit_audit" label="0">否</el-radio>
        </div>
      </el-form-item>

      <div class="common-form">商户图片背景</div>

      <el-form-item label="商户入住背景" :rules="[{required: true,message: '请输入商户入住图片'}]">
        <el-button @click="chooseImg('image')">选择图片</el-button>
        <img v-img-url="form.supplier_image" class="mt10" width="200">
      </el-form-item>

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button type="primary" :loading="loading" @click="onSubmit">提交</el-button>
      </div>
    </el-form>

    <!--上传图片-->
    <Upload v-if="isupload" :isupload="isupload" :type="type" :config="{ total: 3 }" @returnImgs="returnImgsFunc" />
  </div>
</template>

<script>
import SettingApi from '@/api/setting.js'
import Upload from '@/components/file/Upload'
import { formatModel } from '@/utils/base.js'

export default {
  components: {
    Upload
  },
  data() {
    return {
      /* 是否正在加载 */
      loading: false,
      /* form表单数据 */
      form: {
        name: '',
        logo: '',
        kefu: '',
        customer: '',
        key: '',
        supplier_cash: '',
        operate_type: '',
        commission_rate: '',
        supplier_image: '',
        sms_open: '',
        supplier_logo: '',
        checkedCities: [],
        edit_audit: 1,
        add_audit: 1,
        is_get_log: 0
      },
      all_type: [],
      type: [],
      /* 是否打开图片选择 */
      isupload: false
    }
  },
  created() {
    this.getParams()
  },
  methods: {
    /** 获取配置数据 **/
    getParams() {
      const self = this
      SettingApi.storeDetail({}, true).then(res => {
        const vars = res.data.vars.values
        self.form = formatModel(self.form, vars)
        self.form.checkedCities = res.data.vars.values.delivery_type
        // 转成整数，兼容组件
        for (let i = 0; i < self.form.checkedCities.length; i++) {
          self.$set(self.form.checkedCities, i, parseInt(self.form.checkedCities[i]))
        }
        self.type = vars.delivery_type
        self.form.customer = vars.kuaidi100.customer
        self.form.key = vars.kuaidi100.key
        self.all_type = res.data.all_type
        self.loading = false
      }).catch(() => {
      })
    },
    /** 提交 **/
    onSubmit() {
      const self = this
      const params = this.form
      if (params.checkedCities.length < 1) {
        self.$message({
          message: '配送方式至少选择一种！',
          type: 'warning'
        })
        return
      }
      self.$refs.form.validate((valid) => {
        if (valid) {
          self.loading = true
          SettingApi.editStore(params, true)
            .then(data => {
              self.loading = false
              self.$message({
                message: '恭喜你，商城设置成功',
                type: 'success'
              })
              self.$router.push('/setting/store/index')
            })
            .catch(() => {
              self.loading = false
            })
        }
      })
    },
    renumber(e) {
      const keynum = window.event ? e.keyCode : e.which
      // eslint-disable-next-line no-unused-vars
      const keycar = String.fromCharCode(keynum)
      if (keynum === 189 || keynum === 190 || keynum === 110 || keynum === 109) {
        this.$message.warning('禁止输入小数和负数')
        e.target.value = ''
      }
    },
    /** 选择图片 **/
    chooseImg(e) {
      this.type = e
      this.isupload = true
    },
    /** 关闭选择图片 **/
    returnImgsFunc(e) {
      this.isupload = false
      if (e != null && e.length > 0) {
        if (this.type === 'logo') {
          this.form.supplier_logo = e[0].file_path
        } else if (this.type === 'image') {
          this.form.supplier_image = e[0].file_path
        } else if (this.type === 'shop_logo') {
          this.form.logo = e[0].file_path
        } else if (this.type === 'shop_kefu') {
          this.form.kefu = e[0].file_path
        }
      }
    },
    /** 删除图片 **/
    deleteImg(type) {
      switch (type) {
        case 'logo':
          this.$set(this.form, 'supplier_logo', '')
          break
        case 'image':
          this.$set(this.form, 'supplier_image', '')
          break
        case 'shop_logo':
          this.$set(this.form, 'logo', '')
          break
        case 'shop_kefu':
          this.$set(this.form, 'kefu', '')
          break
      }
    }
  }
}
</script>

<style>
  .tips {
    color: #ccc;
  }
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
  }
  input[type="number"] {
    -moz-appearance: textfield;
  }
  .draggable-list {
    display: flex;
    justify-content: flex-start;
    flex-wrap: wrap;
  }
  .draggable-list .wrapper > span {
    display: flex;
    justify-content: flex-start;
    flex-wrap: wrap;
  }
  .draggable-list .item {
    position: relative;
    width: 110px;
    height: 110px;
    margin-top: 10px;
    margin-right: 10px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #dddddd;
    cursor: pointer;
  }
  .draggable-list .delete-btn {
    position: absolute;
    top: 0;
    right: 0;
    width: 16px;
    height: 16px;
    background: red;
    line-height: 16px;
    font-size: 16px;
    color: #ffffff;
    display: none;
  }
  .draggable-list .item:hover .delete-btn {
    display: block;
  }
  .draggable-list .item img {
    position: absolute;
    top: 50%;
    left: 50%;
    -webkit-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    max-height: 100%;
    max-width: 100%;
  }
  .draggable-list .img-select {
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px dashed #dddddd;
    font-size: 30px;
  }
  .draggable-list .img-select i {
    color: #409eff;
  }
</style>
