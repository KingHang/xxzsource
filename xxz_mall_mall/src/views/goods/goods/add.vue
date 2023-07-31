<template>
  <div class="product-add">
    <el-tabs v-model="activeName" type="card">
      <el-tab-pane label="基础设置" name="basic" />
      <el-tab-pane label="规格库存" name="spec" />
      <el-tab-pane label="商品详情" name="content" />
      <el-tab-pane label="高级设置" name="buyset" />
    </el-tabs>

    <!--form表单-->
    <el-form ref="form" size="small" :model="form" label-width="180px">
      <!--基础信息-->
      <Basic v-show="activeName === 'basic'" />

      <!--规格设置-->
      <Spec v-show="activeName === 'spec'" />

      <!--商品详情-->
      <Content v-show="activeName === 'content'" />

      <!--高级设置-->
      <Buyset v-show="activeName === 'buyset'" />

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" type="info" @click="cancelFunc">取消</el-button>
        <el-button size="small" type="primary" :loading="loading" @click="onSubmit">保存</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
import GoodsApi from '@/api/goods.js'
import Basic from './part/Basic.vue'
import Spec from './part/Spec.vue'
import Content from './part/Content.vue'
import Buyset from './part/Buyset.vue'

export default {
  components: {
    /* 基础信息 */
    Basic,
    /* 规格信息 */
    Spec,
    /* 商品详情 */
    Content,
    /* 高级设置 */
    Buyset
  },
  data() {
    return {
      activeName: 'basic',
      /* 是否正在加载 */
      loading: false,
      /* form表单数据 */
      form: {
        model: {
          scene: 'add',
          /* 是否可以修改运费模板 */
          can_edit_delivery: true,
          /* 商品名称 */
          product_name: '',
          /* 商品编码 */
          product_no: '',
          /* 商品分类 */
          category_id: null,
          /* 商品图片 */
          image: [],
          is_picture: 0,
          contentImage: [],
          video_id: 0,
          poster_id: 0,
          /* 商品卖点 */
          selling_point: '',
          /* 规格类别,默认10单规格，20多规格 */
          spec_type: 10,
          /* 库存计算方式,默认20付款减库存，10下单减库存 */
          deduct_stock_type: 20,
          sku: {},
          /* 多规格类别 */
          spec_many: {
            /* 多规格类别 */
            spec_attr: [],
            /* 多规格表格数据 */
            spec_list: []
          },
          /* 商品详情内容 */
          content: '',
          /* 运费模板ID */
          is_delivery_free: 0,
          delivery_id: '',
          /* 商品状态 */
          product_status: 10,
          /* 商品排序，默认100 */
          product_sort: 100,
          /* 是否开启积分赠送,默认1为开启，0为关闭 */
          is_points_gift: 1,
          /* 是否允许使用积分抵扣,默认1为允许，0为不允许 */
          is_points_discount: 1,
          /* 最大积分抵扣数量 */
          max_points_discount: 0,
          /* 平台分销是否开启 */
          is_agent: 0,
          /* 是否开启单独分销,默认0为关闭，1为开启 */
          is_ind_agent: 0,
          /* 分销佣金类型,默认10为百分比，20为固定金额 */
          agent_money_type: 10,
          /* 一级佣金 */
          first_money: 0,
          /* 二级佣金 */
          second_money: 0,
          /* 三级佣金 */
          third_money: 0,
          /* 商品类型：1实物2是虚拟3是计次 */
          product_type: 1,
          /* 是否支持自提：1是0否 */
          is_selfmention: 0,
          /* 可自提门店 */
          is_on_shelf_store_one: 0,
          store_ids_one: '',
          storeList_one: [],
          /* 适用门店 */
          is_on_shelf_store_two: 0,
          store_ids_two: '',
          storeList_two: [],
          /* 是否虚拟商品 */
          is_virtual: 0,
          /* 是否计次商品 */
          is_verify: 0,
          /* 可核销次数，0不限次数 */
          verify_num: 0,
          /* 0永久有效 1指定日期有效 2购买n天后有效 3首次使用后n天有效 */
          verify_limit_type: 0,
          /* 核销有效期 */
          verify_enddate: '',
          /* n天 */
          verify_days: 0,
          /* 限购数量 */
          limit_num: 0,
          /* 虚拟商品发货方式 */
          virtual_auto: 0,
          /* 虚拟商品发货内容 */
          virtual_content: '',
          /* 检查用户等级 */
          is_alone_grade: 0,
          /* 会员折扣设置,默认1为单独设置折扣,0为默认折扣 */
          is_enable_grade: 1,
          /* 等级金额类型,默认10为百分比，20为固定金额 */
          alone_grade_type: 10,
          benefit_id: 0
        },
        /* 商品分类 */
        category: [],
        /* 运费模板 */
        delivery: [],
        /* 会员等级 */
        gradeList: [],
        /* 规格数据 */
        specData: null,
        /* 是否锁住 */
        isSpecLocked: false,
        /* 分销基础设置 */
        basicSetting: {},
        /* 分销佣金设置 */
        agentSetting: {},
        audit_setting: {}
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
    this.getBaseData()
  },
  methods: {
    /** 获取基础数据 **/
    getBaseData: function() {
      const self = this
      GoodsApi.getBaseData({}, true)
        .then(res => {
          self.loading = false
          Object.assign(self.form, res.data)
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 转JSON字符串 **/
    convertJson(list) {
      const obj = {}
      list.forEach(item => {
        obj[item.grade_id] = item.product_equity
      })
      return JSON.stringify(obj)
    },
    /** 提交 **/
    onSubmit() {
      const self = this
      const params = self.form.model
      self.$refs.form.validate(valid => {
        if (valid) {
          if (params.is_delivery_free === 0) {
            params.delivery_id = 0
          } else {
            if (params.delivery_id === '') {
              self.$message({
                message: '请选择运费模板',
                type: 'error'
              })
              return
            }
          }
          params.store_ids = self.form.model.product_type === 1 ? self.form.model.store_ids_one : self.form.model.store_ids_two
          self.loading = true
          GoodsApi.addProduct({
            params: JSON.stringify(params)
          }, true).then(data => {
            self.loading = false
            self.$message({
              message: '添加成功',
              type: 'success'
            })
            self.$router.push('/goods/goods/index')
          }).catch(() => {
            self.loading = false
          })
        } else {
          self.$message({
            message: '请检查必填项是否填写完整',
            type: 'error'
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
  .basic-setting-content {}

  .product-add {
    padding-bottom: 100px;
  }
</style>
