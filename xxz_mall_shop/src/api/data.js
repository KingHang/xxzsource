import request from '@/utils/request'

const DataApi = {
  /** 用户接口 **/
  getUser(data) {
    return request({ url: '/shop/data.user/lists', method: 'post', data })
  },
  /** 地区接口 **/
  getRegion(data) {
    return request({ url: '/shop/data.region/lists', method: 'post', data })
  },
  /** 门店接口 **/
  getStore(data) {
    return request({ url: '/shop/data.store/lists', method: 'post', data })
  },
  /** 订单数据统计 **/
  getOrderTotal(data) {
    return request({ url: '/shop/statistics.sales/index', method: 'post', data })
  },
  /** 订单时间段统计 **/
  getOrderByDate(data) {
    return request({ url: '/shop/statistics.sales/order', method: 'post', data })
  },
  /** 商品时间段统计 **/
  getProductByDate(data) {
    return request({ url: '/shop/statistics.sales/product', method: 'post', data })
  },
  /** 会员数据统计 **/
  getUserTotal(data) {
    return request({ url: '/shop/statistics.user/index', method: 'post', data })
  },
  /** 成交占比 **/
  getUserScale(data) {
    return request({ url: '/shop/statistics.user/scale', method: 'post', data })
  },
  /** 新增会员 **/
  getNewUser(data) {
    return request({ url: '/shop/statistics.user/new_user', method: 'post', data })
  },
  /** 成交会员数 **/
  getPayUser(data) {
    return request({ url: '/shop/statistics.user/pay_user', method: 'post', data })
  },
  /** 会员签到数 **/
  getSignUser(data) {
    return request({ url: '/shop/statistics.user/sign_user', method: 'post', data })
  },
  /** 访问统计 **/
  getAccessTotal(data) {
    return request({ url: '/shop/statistics.access/index', method: 'post', data })
  },
  /** 访问时间段统计 **/
  getAccessByDate(data) {
    return request({ url: '/shop/statistics.access/data', method: 'post', data })
  },
  /** 获取排行榜 **/
  getSaleRankingByDate(data) {
    return request({ url: '/shop/statistics.sales/SaleRanking', method: 'post', data })
  }
}

export default DataApi
