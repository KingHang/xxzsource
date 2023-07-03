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
  }
}

export default DataApi
