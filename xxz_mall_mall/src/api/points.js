import request from '@/utils/request'

const PointsApi = {
  /** 积分设置 **/
  setPoints(data) {
    return request({ url: '/mall/user.points/setting', method: 'post', data })
  },
  /** 获取设置 **/
  getPoints(data) {
    return request({ url: '/mall/user.points/setting', method: 'get', params: data })
  },
  /** 获取积分列表 **/
  GetUserList(data) {
    return request({ url: '/mall/user.points/log', method: 'get', params: data })
  },
  /** 成长值设置 **/
  setGrow(data) {
    return request({ url: '/mall/user.points/grow', method: 'post', data })
  },
  /** 获取成长值设置 **/
  getGrow(data) {
    return request({ url: '/mall/user.points/grow', method: 'get', params: data })
  },
  /** 获取成长值列表 **/
  getGrowList(data) {
    return request({ url: '/mall/user.points/growLog', method: 'get', params: data })
  }
}

export default PointsApi
