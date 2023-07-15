import request from '@/utils/request'

const AuthorityApi = {
  /** 获取当前角色权限 **/
  getRoleList(data) {
    return request({ url: '/shop/auth.user/getRoleList', method: 'post', data })
  },
  /** 获取角色信息 **/
  getUserInfo(data) {
    return request({ url: '/shop/auth.user/getUserInfo', method: 'post', data })
  },
  /** 角色列表 **/
  roleList(data) {
    return request({ url: '/shop/auth.role/index', method: 'post', data })
  },
  /** 添加角色需要的数据 **/
  roleAddInfo(data) {
    return request({ url: '/shop/auth.role/add', method: 'get', params: data })
  },
  /** 添加角色 **/
  roleAdd(data) {
    return request({ url: '/shop/auth.role/add', method: 'post', data })
  },
  /** 修改角色需要的数据 **/
  roleEditInfo(data) {
    return request({ url: '/shop/auth.role/edit', method: 'get', params: data })
  },
  /** 修改角色 **/
  roleEdit(data) {
    return request({ url: '/shop/auth.role/edit', method: 'post', data })
  },
  /** 删除角色 **/
  roleDelete(data) {
    return request({ url: '/shop/auth.role/delete', method: 'post', data })
  },
  /** 管理员列表 **/
  userList(data) {
    return request({ url: '/shop/auth.user/index', method: 'post', data })
  },
  /** 添加管理员需要的数据 **/
  userAddInfo(data) {
    return request({ url: '/shop/auth.user/addInfo', method: 'post', data })
  },
  /** 添加管理员 **/
  userAdd(data) {
    return request({ url: '/shop/auth.user/add', method: 'post', data })
  },
  /** 修改管理员需要的数据 **/
  userEditInfo(data) {
    return request({ url: '/shop/auth.user/edit', method: 'get', params: data })
  },
  /** 修改管理员 **/
  userEdit(data) {
    return request({ url: '/shop/auth.user/edit', method: 'post', data })
  },
  /** 删除管理员 **/
  userDelete(data) {
    return request({ url: '/shop/auth.user/delete', method: 'post', data })
  },
  /** 登录日志 **/
  loginlog(data) {
    return request({ url: '/shop/auth.loginlog/index', method: 'post', data })
  },
  /** 操作日志 **/
  optlog(data) {
    return request({ url: '/shop/auth.optlog/index', method: 'post', data })
  }
}

export default AuthorityApi
