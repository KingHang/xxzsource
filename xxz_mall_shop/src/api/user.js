import request from '@/utils/request'

const UserApi = {
  /** 验证码登录 **/
  smslogin(data) {
    return request({ url: '/mall/passport/smsLogin', method: 'post', data })
  },
  /** 获取验证码 **/
  sendcode(data) {
    return request({ url: '/mall/passport/sendCode', method: 'post', data })
  },
  /** 短信注册 **/
  smsregister(data) {
    return request({ url: '/mall/passport/smsRegister', method: 'post', data })
  },
  /** 密码重置 **/
  resetpassword(data) {
    return request({ url: '/mall/passport/resetPassword', method: 'post', data })
  },
  /** 用户登录 **/
  login(data) {
    return request({ url: '/mall/passport/login', method: 'post', data })
  },
  saasLogin(data) {
    return request({ url: '/mall/passport/saasLogin', method: 'post', data })
  },
  getInfo(token) {
    return request({ url: '/mall/passport/info', method: 'get', params: { token }})
  },
  /** 退出登录 **/
  loginOut(data) {
    return request({ url: '/mall/passport/logout', method: 'post', data })
  },
  /** 选择行业 **/
  bindTrade(data) {
    return request({ url: '/mall/passport/bindTrade', method: 'post', data })
  },
  /** 添加用户 **/
  adduser(data) {
    return request({ url: '/mall/user.user/add', method: 'post', data })
  },
  /** 修改用户 **/
  edituser(data) {
    return request({ url: '/mall/user.user/edit', method: 'post', data })
  },
  /** 充值 **/
  userRecharge(data) {
    return request({ url: '/mall/user.user/recharge', method: 'post', data })
  },
  /** 删除用户 **/
  deleteuser(data) {
    return request({ url: '/mall/user.user/delete', method: 'post', data })
  },
  /** 用户列表 **/
  userlist(data) {
    return request({ url: '/mall/user.user/index', method: 'post', data })
  },
  /** 等级列表 **/
  gradelist(data) {
    return request({ url: '/mall/user.grade/index', method: 'post', data })
  },
  /** 添加等级 **/
  addgrade(data) {
    return request({ url: '/mall/user.grade/add', method: 'post', data })
  },
  /** 修改等级 **/
  editGrade(data) {
    return request({ url: '/mall/user.grade/edit', method: 'post', data })
  },
  /** 删除等级 **/
  deletegrade(data) {
    return request({ url: '/mall/user.grade/delete', method: 'post', data })
  },
  /** 用户余额 **/
  BalanceLog(data) {
    return request({ url: '/mall/user.Balance/log', method: 'post', data })
  },
  /** 充值记录 **/
  RechargeOrder(data) {
    return request({ url: '/mall/user.Recharge/order', method: 'post', data })
  },
  /** 修改密码 **/
  EditPass(data) {
    return request({ url: '/mall/passport/editPass', method: 'post', data })
  },
  /** 用户标签 **/
  toEditTag(data) {
    return request({ url: '/mall/user.user/tag', method: 'get', params: data })
  },
  /** 用户标签 **/
  editTag(data) {
    return request({ url: '/mall/user.user/tag', method: 'post', data })
  }
}

export default UserApi
