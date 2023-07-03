import request from '@/utils/request'

const OrderApi = {
  /** 订单列表 **/
  orderlist(data) {
    return request({ url: '/shop/order.order/index', method: 'post', data })
  },
  /** 订单详情 **/
  orderdetail(data) {
    return request({ url: '/shop/order.order/detail', method: 'post', data })
  },
  /** 售后管理 **/
  orderrefund(data) {
    return request({ url: '/shop/order.refund/index', method: 'post', data })
  },
  /** 去发货 **/
  delivery(data) {
    return request({ url: '/shop/order.order/delivery', method: 'post', data })
  },
  /** 确认审核 **/
  confirm(data) {
    return request({ url: '/shop/order.Operate/confirmCancel', method: 'post', data })
  },
  /** 售后详情 **/
  refundDetail(data) {
    return request({ url: '/shop/order.refund/detail', method: 'get', params: data })
  },
  /** 售后审核 **/
  Approval(data) {
    return request({ url: '/shop/order.refund/audit', method: 'get', params: data })
  },
  /** 确认收货并退款 **/
  receipt(data) {
    return request({ url: '/shop/order.refund/receipt', method: 'post', data })
  },
  /** 核销 **/
  extract(data) {
    return request({ url: '/shop/order.operate/extract', method: 'post', data })
  },
  /** 修改价格 **/
  updatePrice(data) {
    return request({ url: '/shop/order.order/updatePrice', method: 'post', data })
  },
  /** 平台售后管理 **/
  orderplaterefund(data) {
    return request({ url: '/shop/order.platerefund/index', method: 'post', data })
  },
  /** 平台售后详情 **/
  refundplateDetail(data) {
    return request({ url: '/shop/order.platerefund/detail', method: 'get', params: data })
  },
  /** 平台售后审核 **/
  plateApproval(data) {
    return request({ url: '/shop/order.platerefund/audit', method: 'get', params: data })
  },
  /** 查询物流 */
  queryLogistics(data) {
    return request({ url: '/shop/order.order/express', method: 'post', data })
  },
  /** 修改地址 **/
  updateAddress(data) {
    return request({ url: '/shop/order.order/updateAddress', method: 'post', data })
  },
  /** 订单备注 **/
  orderRemark(data) {
    return request({ url: '/shop/order.order/remark', method: 'post', data })
  }
}

export default OrderApi
