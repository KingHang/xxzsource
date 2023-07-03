<template>
  <div v-if="!loading" class="pb50">
    <div class="product-content">
      <!--基本信息-->
      <div class="common-form">基本信息</div>
      <div class="table-wrap">
        <el-row>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">订单号：</span>
              {{ detail.order_no }}
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">买家：</span>
              {{ detail.user.nickName }}
              <span>用户ID：({{ detail.user.user_id }})</span>
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">订单金额 (元)：</span>
              {{ detail.order_price }}
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">运费金额 (元)：</span>
              {{ detail.express_price }}
            </div>
          </el-col>
          <el-col :span="5">
            <div v-if="detail.coupon_money > 0" class="pb16">
              <span class="gray9">优惠券抵扣 (元)：</span>
              {{ detail.coupon_money }}
            </div>
          </el-col>
          <el-col :span="5">
            <div v-if="detail.points_money > 0" class="pb16">
              <span class="gray9">积分抵扣 (元)：</span>
              {{ detail.points_money }}
            </div>
          </el-col>
          <el-col :span="5">
            <div v-if="detail.deduct_money > 0" class="pb16">
              <span class="gray9">CFP抵扣 (元)：</span>
              {{ detail.deduct_money }}
            </div>
          </el-col>
          <el-col :span="5">
            <div v-if="detail.fullreduce_money > 0" class="pb16">
              <span class="gray9">满减金额 (元)：</span>
              {{ detail.fullreduce_money }}
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">实付款金额 (元)：</span>
              {{ detail.pay_price }}
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">支付方式：</span>
              {{ detail.pay_type.text }}
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">配送方式：</span>
              {{ detail.delivery_type.text }}
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">交易状态：</span>
              {{ detail.order_status.text }}
            </div>
          </el-col>
          <el-col
            v-if="detail['pay_status']['value'] === 10 && detail['order_status']['value'] === 10 && detail['order_source'] === 10"
            v-auth="'/order/order/updatePrice'"
            :span="5"
          >
            <el-button size="small" @click="editClick(detail)">修改价格</el-button>
          </el-col>
        </el-row>
        <el-row>
          <el-col>
            <span class="gray9">订单备注：</span>
            <span class="mr10">{{ detail.order_remark }}</span>
            <el-button v-auth="'/order/order/remark'" type="text" size="small" @click="remarkClick">备注</el-button>
          </el-col>
        </el-row>
      </div>

      <!--商户信息-->
      <div class="common-form mt16">商户信息</div>
      <div class="table-wrap">
        <el-row>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">商户名称：</span>
              {{ detail.supplier.name }}
            </div>
          </el-col>
        </el-row>
      </div>

      <!--商品信息-->
      <div class="common-form mt16">商品信息</div>
      <div class="table-wrap">
        <el-table size="small" :data="detail.product" border style="width: 100%">
          <el-table-column prop="product_name" label="商品" width="400">
            <template slot-scope="scope">
              <div class="product-info">
                <div class="pic"><img v-img-url="scope.row.image ? scope.row.image.file_path : ''"></div>
                <div class="info">
                  <div class="name">{{ scope.row.product_name }}</div>
                  <div v-if="scope.row.product_attr !== ''" class="gray9">{{ scope.row.product_attr }}</div>
                  <div v-if="scope.row.refund" class="orange">
                    {{ scope.row.refund.type.text }}-{{ scope.row.refund.status.text }}</div>
                  <div class="price">
                    <span :class="{'text-d-line':scope.row.is_user_grade === 1,'gray6':scope.row.is_user_grade !== 1}">
                      ￥{{ scope.row.product_price }}
                    </span>
                    <span v-if="scope.row.is_user_grade === 1" class="ml10">
                      会员折扣价：￥ {{ scope.row.grade_product_price }}
                    </span>
                  </div>
                  <div v-if="scope.row.send_type === 1" class="orange">
                    <div>已发货</div>
                    <div>物流公司：{{ scope.row.express ? scope.row.express.express_name : '' }}</div>
                    <div>物流单号：{{ scope.row.express_no }}</div>
                    <div>发货时间：{{ scope.row.delivery_time_text }}</div>
                    <div>
                      <el-button size="small" type="text" @click="getLogistics(scope.row)">查看物流</el-button>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="product_no" label="商品编码" />
          <el-table-column prop="product_weight" label="重量 (Kg)" />
          <el-table-column prop="total_num" label="购买数量">
            <template slot-scope="scope">
              <p>x {{ scope.row.total_num }}</p>
            </template>
          </el-table-column>
          <el-table-column prop="total_price" label="商品总价(元)">
            <template slot-scope="scope">
              <p>￥ {{ scope.row.total_price }}</p>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!--收货信息-->
      <div v-if="detail.delivery_type.value === 10">
        <div class="common-form mt16">收货信息</div>
        <div class="table-wrap">
          <el-row>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">收货人：</span>
                {{ addressData.name }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">收货电话：</span>
                {{ addressData.phone }}
              </div>
            </el-col>
            <el-col :span="9">
              <div class="pb16">
                <span class="gray9">收货地址：</span>
                {{ addressData.region.province }} {{ addressData.region.city }} {{ addressData.region.region }}
                {{ addressData.detail }}
              </div>
            </el-col>
            <el-col v-if="detail.delivery_status.value !== 20" :span="5">
              <div class="pb16">
                <el-button type="text" size="small" @click="changeAdd">修改地址</el-button>
              </div>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="25">
              <div class="pb16">
                <span class="gray9">备注：</span>
                {{ detail.buyer_remark }}
              </div>
            </el-col>
          </el-row>
        </div>
      </div>

      <!--自提门店信息-->
      <template v-if="detail.delivery_type.value === 20">
        <div class="common-form mt16">自提用户信息</div>
        <div v-if="detail.extract" class="table-wrap">
          <el-row>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">联系人：</span>
                {{ detail.extract.linkman }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">联系电话：</span>
                {{ detail.extract.phone }}
              </div>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="25">
              <div class="pb16">
                <span class="gray9">备注：</span>
                {{ detail.buyer_remark }}
              </div>
            </el-col>
          </el-row>
        </div>
        <div class="common-form mt16">自提信息</div>
        <div v-if="detail.extract" class="table-wrap">
          <el-row>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">门店ID：</span>
                {{ detail.extractStore.store_id }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">门店名称：</span>
                {{ detail.extractStore.store_name }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">联系人：</span>
                {{ detail.extractStore.linkman }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">联系电话：</span>
                {{ detail.extractStore.phone }}
              </div>
            </el-col>
            <el-col :span="15">
              <div class="pb16">
                <span class="gray9">门店地址：</span>
                {{ detail.extractStore.region.province }}- {{ detail.extractStore.region.city }}-
                {{ detail.extractStore.region.region }}-
                {{ detail.extractStore.address }}
              </div>
            </el-col>
          </el-row>
        </div>
      </template>

      <!--无需发货-->
      <template v-if="detail.delivery_type.value === 30">
        <div class="common-form mt16">用户信息</div>
        <div class="table-wrap">
          <el-row>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">联系手机：</span>
                {{ detail.user.mobile }}
              </div>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="25">
              <div class="pb16">
                <span class="gray9">备注：</span>
                {{ detail.buyer_remark }}
              </div>
            </el-col>
          </el-row>
        </div>
      </template>

      <!--付款信息-->
      <div v-if="detail.pay_status.value === 20" class="table-wrap">
        <div class="common-form mt16">付款信息</div>
        <div class="table-wrap">
          <el-row>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">应付款金额：</span>
                {{ detail.pay_price }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">支付方式：</span>
                {{ detail.pay_type.text }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">支付流水号：</span>
                {{ detail.transaction_id }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">付款状态：</span>
                {{ detail.pay_status.text }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">付款时间：</span>
                {{ detail.pay_time }}
              </div>
            </el-col>
            <el-col v-if="detail.order_status.value === 20 || detail.order_status.value === 21" :span="5">
              <div class="pb16">
                <span class="gray9">申请取消时间：</span>
                {{ detail.cancel_time_text }}
              </div>
            </el-col>
            <el-col v-if="detail.order_status.value === 20" :span="5">
              <div class="pb16">
                <span class="gray9">退款时间：</span>
                {{ detail.update_time }}
              </div>
            </el-col>
          </el-row>
        </div>
      </div>

      <!--发货信息-->
      <div v-if="detail.pay_status.value === 20 && detail.delivery_type.value === 10 && [20, 21].indexOf(detail.order_status.value) === -1">
        <div v-if="detail.delivery_status.value === 10">
          <!-- 去发货 -->
          <div class="common-form mt16">去发货</div>
          <el-form ref="form" size="small" :model="form" label-width="100px">
            <el-form-item label="物流公司">
              <el-select v-model="form.express_id" placeholder="请选择快递公司">
                <el-option
                  v-for="(item, index) in expressList"
                  :key="index"
                  :value="item.express_id"
                  :label="item.express_name"
                />
              </el-select>
            </el-form-item>
            <el-form-item label="物流单号">
              <el-input v-model="form.express_no" class="max-w460" />
            </el-form-item>
            <el-form-item label="发货类型">
              <el-radio-group v-model="form.send_type">
                <el-radio :label="0" :disabled="detail.send_type === 1">全部发货</el-radio>
                <el-radio :label="1" :disabled="detail.send_type === 1">部分发货</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item v-if="form.send_type === 1" label="发货商品">
              <el-checkbox-group v-model="form.order_product_ids">
                <el-checkbox v-for="(item, index) in sendProductList" :key="index" :label="item.order_product_id" class="send-checkbox">
                  <div class="send-flex">
                    <div class="send-pic"><img v-img-url="item.image ? item.image.file_path : ''"></div>
                    <div class="send-name">{{ item.product_name }}</div>
                  </div>
                </el-checkbox>
              </el-checkbox-group>
            </el-form-item>
          </el-form>
        </div>
        <div v-else>
          <div v-if="detail.send_type === 0" class="common-form mt16">发货信息</div>
          <div v-if="detail.send_type === 0" class="table-wrap">
            <el-row>
              <el-col :span="4">
                <div class="pb16">
                  <span class="gray9">物流公司：</span>
                  {{ detail.express.express_name }}
                </div>
              </el-col>
              <el-col :span="4">
                <div class="pb16">
                  <span class="gray9">物流单号：</span>
                  {{ detail.express_no }}
                </div>
              </el-col>
              <el-col :span="4">
                <div class="pb16">
                  <span class="gray9">发货状态：</span>
                  {{ detail.delivery_status.text }}
                </div>
              </el-col>
              <el-col :span="4">
                <div class="pb16">
                  <span class="gray9">发货时间：</span>
                  {{ detail.delivery_time }}
                </div>
              </el-col>
            </el-row>
          </div>
        </div>
      </div>

      <!--取消信息-->
      <div v-if="detail.order_status.value === 20 && detail.cancel_remark !== ''" class="table-wrap">
        <div class="common-form mt16">取消信息</div>
        <div class="table-wrap">
          <el-row>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">商家备注：</span>
                {{ detail.cancel_remark }}
              </div>
            </el-col>
          </el-row>
        </div>
      </div>

      <!--门店自提核销-->
      <div v-if="detail.delivery_type.value === 20 && detail.pay_status.value === 20 && detail.order_status.value !== 21 && detail.order_status.value !== 20">
        <div class="common-form mt16">门店自提核销</div>
        <div class="table-wrap">
          <template v-if="detail.extractClerk">
            <el-row>
              <el-col :span="5">
                <div class="pb16">
                  <span class="gray9">自提门店名称：</span>
                  {{ detail.extractStore.store_name }}
                </div>
              </el-col>
              <el-col :span="5">
                <div class="pb16">
                  <span class="gray9">核销员：</span>
                  {{ detail.extractClerk.real_name }}
                </div>
              </el-col>
              <el-col :span="5">
                <div class="pb16">
                  <span class="gray9">核销状态：</span>
                  <template v-if="detail.delivery_status.value === 20">
                    已核销
                  </template>
                </div>
              </el-col>
              <el-col :span="5">
                <div class="pb16">
                  <span class="gray9">核销时间：</span>
                  {{ detail.delivery_time }}
                </div>
              </el-col>
            </el-row>
          </template>
        </div>
      </div>
    </div>

    <div class="common-button-wrapper">
      <el-button size="small" type="info" @click="cancelFunc">返回上一页</el-button>
      <!--确认发货-->
      <template v-if="detail.pay_status.value === 20 && detail.delivery_type.value === 10 && [20, 21].indexOf(detail.order_status.value) === -1">
        <el-button v-if="detail.delivery_status.value === 10" size="small" type="primary" @click="onSubmit">确认发货</el-button>
      </template>
      <!--用户取消-->
      <template v-if="detail.pay_status.value === 20 && detail.order_status.value === 21">
        <el-button size="small" type="primary" @click="auditClick">确认审核</el-button>
      </template>
    </div>

    <!--订单备注-->
    <Remark v-if="open_remark" :order_id="detail.order_id" :order_remark="detail.order_remark" @close="closeRemarkCancel" />

    <!--修改收货地址-->
    <changeAddress :is-change="isChange" :address-data="addressData" @closeDialog="closeAddress" />

    <!--查看物流-->
    <Logistics :logistics-data="logisticsData" :is-logistics="isLogistics" @closeDialog="closeLogistics" />

    <!--订单审核-->
    <Audit v-if="open_audit" :order_id="detail.order_id" @close="closeAuditCancel" />

    <!--订单修改价格-->
    <Add v-if="open_edit" :open_edit="open_edit" :order="userModel" @close="closeDialogFunc($event, 'edit')" />
  </div>
</template>

<script>
import OrderApi from '@/api/order.js'
import Add from './dialog/Add.vue'
import Remark from './dialog/Remark.vue'
import Audit from './dialog/Audit.vue'
import changeAddress from '@/components/order/changeAddress.vue'
import Logistics from '@/components/logistics/viewLogistics.vue'
import { deepClone } from '@/utils/base.js'

export default {
  components: {
    Add,
    Remark,
    Audit,
    changeAddress,
    Logistics
  },
  data() {
    return {
      active: 0,
      /* 是否加载完成 */
      loading: true,
      /* 订单数据 */
      detail: {
        pay_status: [],
        pay_type: [],
        delivery_type: [],
        user: {},
        address: [],
        product: [],
        order_status: [],
        extract: [],
        extract_store: [],
        express: [],
        delivery_status: [],
        extractClerk: []
      },
      /* 是否打开添加弹窗 */
      open_add: false,
      /* 一页多少条 */
      pageSize: 20,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      /* 当前是第几页 */
      curPage: 1,
      /* 发货 */
      form: {
        express_id: null,
        express_no: '',
        /* 发货类型 */
        send_type: 0,
        /* 发货商品id */
        order_product_ids: []
      },
      /* 未发货商品列表 */
      sendProductList: [],
      forms: {
        is_cancel: 1
      },
      extract_form: {
        order: {
          extract_status: 1
        }
      },
      order: {},
      delivery_type: 0,
      /* 配送方式 */
      exStyle: [],
      /* 门店列表 */
      shopList: [],
      /* 当前编辑的对象 */
      userModel: {},
      /* 时间 */
      create_time: '',
      /* 快递公司列表 */
      expressList: [],
      shopClerkList: [],
      /* 是否打开编辑弹窗 */
      open_edit: false,
      isChange: false,
      addressData: {
        name: '',
        phone: '',
        region: {
          province: '',
          province_id: 0,
          city: '',
          city_id: 0,
          region: '',
          region_id: 0,
          detail: ''
        }
      },
      /* 订单备注 */
      open_remark: false,
      /* 是否提交确认审核 */
      isConfirmCancel: false,
      /* 查看物流 */
      isLogistics: false,
      logisticsData: {},
      /* 订单审核 */
      open_audit: false,
      open_OrderTravelers: false,
      OrderTravelers: {}
    }
  },
  created() {
    /* 获取详情 */
    this.getParams()
  },
  methods: {
    next() {
      if (this.active++ > 4) this.active = 0
    },
    /** 获取详情 **/
    getParams() {
      const self = this
      // 取到路由带过来的参数
      const params = this.$route.query.order_id
      OrderApi.orderdetail({ order_id: params }, true)
        .then(data => {
          self.detail = data.data.detail
          self.form.send_type = data.data.detail.send_type
          data.data.detail.product.forEach(item => {
            if (item.send_type === 0) {
              self.sendProductList.push(item)
            }
          })
          self.expressList = data.data.expressList
          self.shopClerkList = data.data.shopClerkList
          self.addressData = data.data.detail.address
          self.loading = false
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 发货 **/
    onSubmit() {
      const self = this
      const param = self.form
      if (param.express_id == null) {
        this.$message.error('请选择物流公司')
        return
      }
      if (param.express_no === '') {
        this.$message.error('请填写物流单号')
        return
      }
      if (param.send_type === 1 && !param.order_product_ids.length) {
        this.$message.error('部分发货请选择商品')
        return
      }
      const order_id = this.$route.query.order_id
      OrderApi.delivery({ param: param, order_id: order_id }, true)
        .then(data => {
          self.loading = false
          self.$message({
            message: '恭喜你，发货成功',
            type: 'success'
          })
          location.reload()
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 确认取消 **/
    confirmCancel() {
      const self = this
      // 是否提交确认审核
      if (self.isConfirmCancel) {
        return false
      }
      self.isConfirmCancel = true
      const order_id = this.$route.query.order_id
      const is_cancel = self.forms.is_cancel
      OrderApi.confirm({ order_id: order_id, is_cancel: is_cancel }, true)
        .then(data => {
          self.loading = false
          self.isConfirmCancel = false
          self.$message({
            message: '恭喜你，审核成功',
            type: 'success'
          })
          self.getParams()
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 打开编辑 **/
    editClick(item) {
      this.userModel = deepClone(item)
      this.open_edit = true
    },
    /** 关闭弹窗 **/
    closeDialogFunc(e, f) {
      if (f === 'edit') {
        this.open_edit = e.openDialog
        this.getParams()
      }
    },
    /** 取消 **/
    cancelFunc() {
      this.$router.back(-1)
    },
    /** 订单备注 **/
    remarkClick() {
      this.open_remark = true
    },
    /** 关闭备注 **/
    closeRemarkCancel() {
      this.open_remark = false
    },
    /** 订单审核 **/
    auditClick() {
      this.open_audit = true
    },
    /** 关闭订单审核 **/
    closeAuditCancel(e) {
      if (e.type === 'success') {
        this.getParams()
      }
      this.open_audit = false
    },
    /** 修改收货地址 **/
    changeAdd() {
      this.isChange = true
    },
    /** 关闭修改地址 **/
    closeAddress(e) {
      const self = this
      if (e === false) {
        self.isChange = false
        return false
      }
      const params = e.params
      params.order_id = self.$route.query.order_id
      OrderApi.updateAddress(params, true)
        .then(data => {
          self.$message({
            message: '修改成功',
            type: 'success'
          })
        })
        .catch(() => {
        })
      self.isChange = false
    },
    /** 查看物流 **/
    getLogistics(row) {
      const self = this
      const Params = {
        order_id: row.order_id,
        product_id: row.product_id
      }
      OrderApi.queryLogistics(Params, true)
        .then(res => {
          self.logisticsData = res.data.express.list
          self.openLogistics()
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 打开物流 **/
    openLogistics() {
      this.isLogistics = true
    },
    /** 关闭物流 **/
    closeLogistics() {
      this.isLogistics = false
    }
  }
}
</script>

<style lang="scss" scoped>
  .send-checkbox {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    margin-top: 20px;

    .send-flex {
      display: flex;
      justify-content: flex-start;
      align-items: center;

      .send-pic {
        width: 25px;

        img {
          width: 100%;
        }
      }

      .send-name {
        font-size: 13px;
        margin-left: 10px;
      }
    }
  }

  .send-checkbox:first-child {
    margin-top: 5px;
  }
</style>
