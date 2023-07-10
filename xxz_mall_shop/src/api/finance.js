import request from '@/utils/request'

const FinanceApi = {
  /** 首页概况 **/
  index(data) {
    return request({ url: '/shop/cash.cash/index', method: 'post', data })
  },
  /** 店铺结算 **/
  settled(data) {
    return request({ url: '/shop/cash.settled/index', method: 'post', data })
  },
  getSettledByDate(data) {
    return request({ url: '/shop/cash.settled/data', method: 'post', data })
  },
  /** 订单结算 **/
  order(data) {
    return request({ url: '/shop/cash.order/index', method: 'post', data })
  },
  /** 订单结算详情 **/
  orderDetail(data) {
    return request({ url: '/shop/cash.order/detail', method: 'post', data })
  }
}

export default FinanceApi
