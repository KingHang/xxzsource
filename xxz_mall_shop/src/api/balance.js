import request from '@/utils/request'

const BalanceApi = {
  /** 积分设置 **/
  setSettings(data) {
    return request({ url: '/shop/user.balance/setting', method: 'post', data })
  },
  /** 获取设置 **/
  getSettings(data) {
    return request({ url: '/shop/user.balance/setting', method: 'get', params: data })
  },
  /** 获取积分列表 **/
  getBalanceLog(data) {
    return request({ url: '/shop/user.balance/log', method: 'get', params: data })
  },
  /** 充值套餐 **/
  listPlan(data) {
    return request({ url: '/shop/user.plan/index', method: 'post', data })
  },
  /** 添加套餐 **/
  addPlan(data) {
    return request({ url: '/shop/user.plan/add', method: 'get', params: data })
  },
  /** 修改套餐 **/
  editPlan(data) {
    return request({ url: '/shop/user.plan/edit', method: 'post', data })
  },
  /** 删除套餐 **/
  deletePlan(data) {
    return request({ url: '/shop/user.plan/delete', method: 'get', params: data })
  },
  /** 充值记录 **/
  log(data) {
    return request({ url: '/shop/user.plan/log', method: 'post', data })
  }
}

export default BalanceApi
