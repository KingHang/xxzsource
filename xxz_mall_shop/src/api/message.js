import request from '@/utils/request'

const MessageApi = {
  /** 会员模板列表 **/
  messageList(data) {
    return request({ url: '/shop/setting.message/index', method: 'post', data })
  },
  /** 会员模板字段列表 **/
  fieldList(data) {
    return request({ url: '/shop/setting.message/field', method: 'post', data })
  },
  /** 会员模板字段设置保存 **/
  saveSettings(data) {
    return request({ url: '/shop/setting.message/saveSettings', method: 'post', data })
  },
  /** 会员模板设置状态修改 **/
  updateSettingsStatus(data) {
    return request({ url: '/shop/setting.message/updateSettingsStatus', method: 'post', data })
  }
}

export default MessageApi
