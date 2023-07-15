import request from '@/utils/request'

const AppApi = {
  /** 小程序 **/
  appwxDetail(data) {
    return request({ url: '/shop/appsetting.appwx/index', method: 'get', params: data })
  },
  /** 小程序 **/
  editAppWx(data) {
    return request({ url: '/shop/appsetting.appwx/index', method: 'post', data })
  },
  /** 公众号 **/
  appmpDetail(data) {
    return request({ url: '/shop/appsetting.appmp/index', method: 'get', params: data })
  },
  /** 公众号 **/
  editAppMp(data) {
    return request({ url: '/shop/appsetting.appmp/index', method: 'post', data })
  },
  /** app **/
  appDetail(data) {
    return request({ url: '/shop/appsetting.app/index', method: 'get', params: data })
  },
  /** app **/
  editApp(data) {
    return request({ url: '/shop/appsetting.app/index', method: 'post', data })
  },
  /** app开放平台 **/
  appopenDetail(data) {
    return request({ url: '/shop/appsetting.appopen/index', method: 'get', params: data })
  },
  /** app开放平台 **/
  editAppOpen(data) {
    return request({ url: '/shop/appsetting.appopen/index', method: 'post', data })
  },
  /** app分享 **/
  appshareDetail(data) {
    return request({ url: '/shop/appsetting.appshare/index', method: 'get', params: data })
  },
  /** app分享 **/
  editAppShare(data) {
    return request({ url: '/shop/appsetting.appshare/index', method: 'post', data })
  },
  /** app升级-列表 **/
  appUpdateList(data) {
    return request({ url: '/shop/appsetting.appupdate/index', method: 'post', data })
  },
  /** app升级-新增 **/
  addAppUpdate(data) {
    return request({ url: '/shop/appsetting.appupdate/add', method: 'get', params: data })
  },
  /** app升级-修改 **/
  editAppUpdate(data) {
    return request({ url: '/shop/appsetting.appupdate/edit', method: 'post', data })
  },
  /** app升级-删除 **/
  delAppUpdate(data) {
    return request({ url: '/shop/appsetting.appupdate/delete', method: 'post', data })
  },
  /** h5支付宝支付 **/
  h5AlipayDetail(data) {
    return request({ url: '/shop/appsetting.apph5/pay', method: 'get', params: data })
  },
  /** app支付宝支付 **/
  editH5Alipay(data) {
    return request({ url: '/shop/appsetting.apph5/pay', method: 'post', data })
  }
}

export default AppApi