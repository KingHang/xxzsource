import request from '@/utils/request'

const IndexApi = {
  /** 基础配置 **/
  base(data) {
    return request({ url: '/shop/index/base', method: 'post', data })
  },
  /** 商城首页 **/
  getCount(data) {
    return request({ url: '/shop/Index/index', method: 'post', data })
  },
  /** 行业列表 **/
  getTradeList(data) {
    return request({ url: '/shop/index/trade', method: 'post', data })
  }
}

export default IndexApi
