import request from '@/utils/request'

const FinanceApi = {
  /** 首页概况 **/
  index(data) {
    return request({ url: '/mall/cash.cash/index', method: 'post', data })
  },
  /** 店铺结算 **/
  settled(data) {
    return request({ url: '/mall/cash.settled/index', method: 'post', data })
  },
  getSettledByDate(data) {
    return request({ url: '/mall/cash.settled/data', method: 'post', data })
  },
  /** 订单结算 **/
  order(data) {
    return request({ url: '/mall/cash.order/index', method: 'post', data })
  },
  /** 订单结算详情 **/
  orderDetail(data) {
    return request({ url: '/mall/cash.order/detail', method: 'post', data })
  }
}

export default FinanceApi
