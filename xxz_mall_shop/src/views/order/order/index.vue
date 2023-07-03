<template>
  <div class="user">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form ref="searchForm" size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-row>
          <el-form-item label="时间筛选：" prop="time_type">
            <el-select v-model="searchForm.time_type">
              <el-option label="不按时间" value="" />
              <el-option v-for="(item, index) in timeFieldList" :key="index" :label="item.val" :value="item.key" />
            </el-select>
          </el-form-item>
          <el-form-item prop="create_time">
            <div class="block">
              <span class="demonstration" />
              <el-date-picker
                v-model="searchForm.create_time"
                size="small"
                type="daterange"
                value-format="yyyy-MM-dd"
                range-separator="至"
                start-placeholder="开始日期"
                end-placeholder="结束日期"
              />
            </div>
          </el-form-item>
          <el-form-item label="订单类型：" prop="order_source">
            <el-select v-model="searchForm.order_source" size="small" placeholder="请选择">
              <el-option label="全部" value="" />
              <el-option v-for="(item, index) in orderSourceList" :key="index" :label="item.val" :value="item.key" />
            </el-select>
          </el-form-item>
          <el-form-item label="配送方式：" prop="style_id">
            <el-select v-model="searchForm.style_id" size="small" placeholder="请选择">
              <el-option label="全部" value="" />
              <el-option v-for="(item, index) in exStyle" :key="index" :label="item.name" :value="item.value" />
            </el-select>
          </el-form-item>
        </el-row>
        <el-row>
          <el-form-item label="订单信息：" prop="fields">
            <el-select v-model="searchForm.fields">
              <el-option v-for="(item, index) in orderFieldList" :key="index" :label="item.val" :value="item.key" />
            </el-select>
          </el-form-item>
          <el-form-item prop="search">
            <el-input v-model="searchForm.search" size="small" class="w350" placeholder="请输入关键词" />
          </el-form-item>
          <el-form-item>
            <el-button size="small" type="primary" @click="resetForm('searchForm')">{{ $t('page.reset') }}</el-button>
          </el-form-item>
          <el-form-item>
            <el-button size="small" type="primary" icon="el-icon-search" @click="onSubmit">查询</el-button>
          </el-form-item>
          <el-form-item>
            <el-button size="small" type="success" @click="onExport">导出</el-button>
          </el-form-item>
        </el-row>
      </el-form>
    </div>

    <!--订单统计-->
    <div class="p-20-0">
      <span>订单数：</span>
      <span class="red fb">{{ orderStatistics.orderNum }}</span>
      <span class="ml20">订单金额：</span>
      <span class="red fb">￥{{ orderStatistics.totalOrderAmount }}</span>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-tabs v-model="activeName" @tab-click="handleClick">
          <el-tab-pane label="全部订单" name="all" />
          <el-tab-pane :label="'未付款'" name="payment">
            <span slot="label">未付款 <el-tag size="mini">{{ order_count.payment }}</el-tag></span>
          </el-tab-pane>
          <el-tab-pane :label="'待发货'" name="delivery">
            <span slot="label">待发货 <el-tag size="mini">{{ order_count.delivery }}</el-tag></span>
          </el-tab-pane>
          <el-tab-pane :label="'待收货'" name="received">
            <span slot="label">待收货 <el-tag size="mini">{{ order_count.received }}</el-tag></span>
          </el-tab-pane>
          <el-tab-pane :label="'待取消'" name="cancel">
            <span slot="label">待取消 <el-tag size="mini">{{ order_count.cancel }}</el-tag></span>
          </el-tab-pane>
          <el-tab-pane label="待评价" name="comment" />
          <el-tab-pane label="已完成" name="complete" />
        </el-tabs>

        <el-table v-loading="loading" size="small" :data="tableData.data" :span-method="arraySpanMethod" border style="width: 100%">
          <el-table-column prop="order_no" label="订单信息" width="400">
            <template slot-scope="scope">
              <div v-if="scope.row.is_top_row" class="order-code">
                <span class="state-text" :class="scope.row.order_source_style">{{ scope.row.order_source_text }}</span>
                <span class="c_main">订单号：{{ scope.row.order_no }}</span>
                <span class="pl16">下单时间：{{ scope.row.create_time }}</span>
                <!--是否取消申请中-->
                <span v-if="scope.row.order_status === 21" class="pl16">
                  <el-tag effect="dark" size="mini">取消申请中</el-tag>
                </span>
                <!--订单备注-->
                <span v-if="scope.row.order_remark" class="pl16" style="color: #FF8C00">订单备注：{{ scope.row.order_remark }}</span>
              </div>
              <template v-else>
                <div v-for="(item, index) in scope.row.product" :key="index" class="product-info">
                  <div class="pic"><img v-img-url="item.image ? item.image.file_path : ''" alt=""></div>
                  <div class="info">
                    <div class="name gray3 product-name">
                      <span>{{ item.product_name }}</span>
                    </div>
                    <div v-if="item.product_attr" class="gray9">{{ item.product_attr }}</div>
                    <div v-if="item.refund" class="orange">{{ item.refund.type.text }}-{{ item.refund.status.text }}</div>
                  </div>
                  <div class="d-c-c d-c">
                    <div class="orange">￥ {{ item.product_price }}</div>
                    <div class="gray3">x{{ item.total_num }}</div>
                  </div>
                </div>
              </template>
            </template>
          </el-table-column>
          <el-table-column prop="pay_price" label="实付款">
            <template v-if="!scope.row.is_top_row" slot-scope="scope">
              <div class="orange">{{ scope.row.pay_price }}</div>
              <p class="gray9">(含运费：{{ scope.row.express_price }})</p>
            </template>
          </el-table-column>
          <el-table-column prop="" label="买家">
            <template v-if="!scope.row.is_top_row" slot-scope="scope">
              <div>{{ scope.row.user ? scope.row.user.nickName : '' }}</div>
              <div class="gray9">ID：({{ scope.row.user ? scope.row.user.user_id : '' }})</div>
            </template>
          </el-table-column>
          <el-table-column prop="supplier.name" label="商户名称" />
          <el-table-column prop="state_text" label="交易状态">
            <template v-if="!scope.row.is_top_row" slot-scope="scope">
              <div v-if="scope.row.state_text === '已关闭'">
                <span v-if="scope.row.pay_status && scope.row.pay_status.value === 20" style="color: #ff0000">{{ scope.row.state_text }}(退款)</span>
                <span v-else style="color: #409EFF">{{ scope.row.state_text }}(取消)</span>
              </div>
              <div v-else>{{ scope.row.state_text }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="pay_type.text" label="支付方式">
            <template v-if="!scope.row.is_top_row" slot-scope="scope">
              <span class="gray9">{{ scope.row.pay_type.text }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="delivery_type.text" label="配送方式">
            <template v-if="!scope.row.is_top_row" slot-scope="scope">
              <span class="green">{{ scope.row.delivery_type.text }}</span>
              <span v-if="scope.row.delivery_type.value === 30">手机号:{{ scope.row.user ? scope.row.user.mobile : '' }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="is_comment" label="评价" width="60">
            <template v-if="!scope.row.is_top_row" slot-scope="scope">
              <span v-if="scope.row.is_comment === 0">未评价</span>
              <span v-else>已评价</span>
            </template>
          </el-table-column>
          <el-table-column fixed="right" label="操作" width="200">
            <template v-if="!scope.row.is_top_row" slot-scope="scope">
              <el-button
                v-if="scope.row.pay_status.value === 20 && scope.row.delivery_type.value === 10 && scope.row.delivery_status.value === 10 && scope.row.order_status.value !== 20 && scope.row.order_status.value !== 21"
                type="text"
                size="small"
                @click="addClick(scope.row)"
              >
                去发货
              </el-button>
              <el-button v-auth="'/order/order/detail'" type="text" size="small" @click="addClick(scope.row)">订单详情</el-button>
              <el-button
                v-if="scope.row.delivery_type.value === 10 && scope.row.delivery_status.value === 20 && scope.row.send_type === 0"
                type="text"
                size="small"
                @click="getLogistics(scope.row)"
              >
                查看物流
              </el-button>
              <el-button v-auth="'/order/order/remark'" type="text" size="small" @click="remarkClick(scope.row)">备注</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!--分页-->
      <div class="pagination">
        <el-pagination
          background
          :current-page="curPage"
          :page-size="pageSize"
          layout="total, prev, pager, next, jumper"
          :total="totalDataNumber"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </div>

    <!--订单备注-->
    <Remark v-if="open_remark" :order_id="order_id" :order_remark="order_remark" @close="closeRemarkCancel" />

    <!--查看物流-->
    <Logistics :logistics-data="logisticsData" :is-logistics="isLogistics" @closeDialog="closeLogistics" />
  </div>
</template>

<script>
import OrderApi from '@/api/order.js'
import Remark from './dialog/Remark.vue'
import Logistics from '@/components/logistics/viewLogistics.vue'
import qs from 'qs'
import { timeFieldList, orderSourceList, orderFieldList } from '@/utils/sys'

export default {
  components: {
    Remark,
    Logistics
  },
  data() {
    return {
      /* 切换菜单 */
      activeName: 'all',
      /* 是否加载完成 */
      loading: true,
      /* 列表数据 */
      tableData: [],
      /* 一页多少条 */
      pageSize: 20,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      /* 当前是第几页 */
      curPage: 1,
      /* 横向表单数据模型 */
      searchForm: {
        time_type: '',
        create_time: '',
        order_source: '',
        style_id: '',
        fields: '1',
        search: ''
      },
      /* 时间搜索 */
      timeFieldList: timeFieldList(),
      /* 订单来源 */
      orderSourceList: orderSourceList(),
      /* 订单搜索 */
      orderFieldList: orderFieldList(),
      /* 配送方式 */
      exStyle: [],
      /* 门店列表 */
      shopList: [],
      /* 时间 */
      create_time: '',
      /* 统计 */
      order_count: {
        payment: 0,
        delivery: 0,
        received: 0
      },
      /* 订单统计 */
      orderStatistics: {},
      isLogistics: false,
      logisticsData: {},
      /* 当前订单id */
      order_id: '',
      /* 订单备注 */
      open_remark: false,
      order_remark: ''
    }
  },
  created() {
    /* 获取列表 */
    this.getData()
  },
  methods: {
    /** 跨多列 **/
    arraySpanMethod(row) {
      if (row.rowIndex % 2 === 0) {
        if (row.columnIndex === 0) {
          return [1, 8]
        }
      }
    },
    /** 选择第几页 **/
    handleCurrentChange(val) {
      const self = this
      self.curPage = val
      self.getData()
    },
    /** 每页多少条 **/
    handleSizeChange(val) {
      this.curPage = 1
      this.pageSize = val
      this.getData()
    },
    /** 切换菜单 **/
    handleClick(tab, event) {
      const self = this
      self.curPage = 1
      self.getData()
    },
    /** 获取列表 **/
    getData() {
      const self = this
      const Params = this.searchForm
      Params.dataType = self.activeName
      Params.page = self.curPage
      Params.list_rows = self.pageSize
      Params.by = this.$route.query.by ? this.$route.query.by : ''
      Params.user_id = this.$route.query.user_id ? this.$route.query.user_id : ''
      self.loading = true
      OrderApi.orderlist(Params, true)
        .then(res => {
          const list = []
          for (let i = 0; i < res.data.list.data.length; i++) {
            const item = res.data.list.data[i]
            let order_source_style = ''
            switch (item.order_source) {
              case 90:
                order_source_style = { 'state-text-orange': true }
                break
              case 80:
                order_source_style = { 'state-text-blue': true }
                break
              case 70:
                order_source_style = { 'state-text-purple': true }
                break
              case 60:
              case 50:
              case 40:
              case 30:
                order_source_style = { 'state-text-red': true }
                break
            }
            const topitem = {
              order_no: item.order_no,
              create_time: item.create_time,
              order_source: item.order_source,
              order_source_text: item.order_source_text,
              order_source_style: order_source_style,
              is_top_row: true,
              order_status: item.order_status.value,
              order_remark: item.order_remark
            }
            list.push(topitem)
            list.push(item)
          }
          self.tableData.data = list
          self.totalDataNumber = res.data.list.total
          self.exStyle = res.data.ex_style
          self.order_count = res.data.order_count.order_count
          self.orderStatistics = res.data.orderStatistics
          self.loading = false
        })
        .catch(() => {})
    },
    /** 打开添加 **/
    addClick(row) {
      const self = this
      const params = row.order_id
      self.$router.push({
        path: '/order/order/Detail',
        query: {
          order_id: params
        }
      })
    },
    /** 获取物流信息 **/
    getLogistics(row) {
      const self = this
      const Params = {
        order_id: row.order_id
      }
      self.loading = true
      OrderApi.queryLogistics(Params, true)
        .then(res => {
          self.logisticsData = res.data.express.list
          self.loading = false
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
    },
    /** 订单备注 **/
    remarkClick(row) {
      this.order_id = row.order_id
      this.order_remark = row.order_remark
      this.open_remark = true
    },
    /** 关闭备注 **/
    closeRemarkCancel() {
      this.open_remark = false
    },
    /** 搜索查询 **/
    onSubmit() {
      this.curPage = 1
      this.tableData = []
      this.getData()
    },
    /** 重置 **/
    resetForm(form) {
      this.$refs[form].resetFields()
      this.searchForm.create_time = ''
      this.getData()
    },
    /** 导出 **/
    onExport() {
      const baseUrl = window.location.protocol + '//' + window.location.host
      window.location.href = baseUrl + '/index.php/shop/order.operate/export?' + qs.stringify(this.searchForm)
    }
  }
}
</script>

<style lang="scss">
  .product-info {
    padding: 10px 0;
    border-top: 1px solid #eeeeee;
  }

  .order-code .state-text {
    padding: 2px 4px;
    border-radius: 4px;
    background: #808080;
    color: #ffffff;
  }

  .order-code .state-text-red {
    background: red;
  }

  .order-code .state-text-purple {
    background: #AF00EC;
  }

  .order-code .state-text-blue {
    background: #1360FC;
  }

  .order-code .state-text-orange {
    background: #FF8C00;
  }

  .table-wrap .product-info:first-of-type {
    border-top: none;
  }

  .table-wrap .el-table__body tbody .el-table__row:nth-child(odd) {
    background: #f5f7fa;
  }

  .w350 {
    width: 350px;
  }
</style>
