import request from '@/utils/request'

const SettingApi = {
  /** 商城设置模板变量 **/
  storeDetail(data) {
    return request({ url: '/mall/setting.store/index', method: 'get', params: data })
  },
  /** 保存商城设置 **/
  editStore(data) {
    return request({ url: '/mall/setting.store/index', method: 'post', data })
  },
  /** 交易设置模板变量 **/
  tradeDetail(data) {
    return request({ url: '/mall/setting.trade/index', method: 'get', params: data })
  },
  /** 保存交易设置 **/
  editTrade(data) {
    return request({ url: '/mall/setting.trade/index', method: 'post', data })
  },
  /** 短信通知模板变量 **/
  smsDetail(data) {
    return request({ url: '/mall/setting.sms/index', method: 'get', params: data })
  },
  /** 保存短信通知 **/
  editSms(data) {
    return request({ url: '/mall/setting.sms/index', method: 'post', data })
  },
  /** 发送短信 **/
  sendSms(data) {
    return request({ url: '/mall/setting.sms/smsTest', method: 'post', data })
  },
  /** 上传设置模板变量 **/
  storageDetail(data) {
    return request({ url: '/mall/setting.storage/index', method: 'get', params: data })
  },
  /** 保存上传设置 **/
  editStorage(data) {
    return request({ url: '/mall/setting.storage/index', method: 'post', data })
  },
  /** 打印设置模板变量 **/
  printingDetail(data) {
    return request({ url: '/mall/setting.printing/index', method: 'get', params: data })
  },
  /** 保存打印设置 **/
  editPrinting(data) {
    return request({ url: '/mall/setting.printing/index', method: 'post', data })
  },
  /** 打印设置模板变量 **/
  clearDetail(data) {
    return request({ url: '/mall/setting.clear/index', method: 'get', params: data })
  },
  /** 保存缓存 **/
  editCache(data) {
    return request({ url: '/mall/setting.clear/index', method: 'post', data })
  },
  /** 物流公司列表 **/
  expressList(data) {
    return request({ url: '/mall/setting.express/index', method: 'post', data })
  },
  /** 添加物流公司 **/
  addExpress(data) {
    return request({ url: '/mall/setting.express/add', method: 'post', data })
  },
  /** 物流公司详情 **/
  expressDetail(data) {
    return request({ url: '/mall/setting.express/edit', method: 'get', params: data })
  },
  /** 修改物流公司 **/
  editExpress(data) {
    return request({ url: '/mall/setting.express/edit', method: 'post', data })
  },
  /** 删除物流公司 **/
  deleteExpress(data) {
    return request({ url: '/mall/setting.express/delete', method: 'post', data })
  },
  /** 退货地址列表 **/
  addressList(data) {
    return request({ url: '/mall/setting.address/index', method: 'post', data })
  },
  /** 添加退货地址 **/
  addAddress(data) {
    return request({ url: '/mall/setting.address/add', method: 'post', data })
  },
  /** 退货地址详情 **/
  addressDetail(data) {
    return request({ url: '/mall/setting.address/edit', method: 'get', params: data })
  },
  /** 修改退货地址 **/
  editAddress(data) {
    return request({ url: '/mall/setting.address/edit', method: 'post', data })
  },
  /** 删除退货地址 **/
  deleteAddress(data) {
    return request({ url: '/mall/setting.address/delete', method: 'post', data })
  },
  /** 打印机列表 **/
  printerList(data) {
    return request({ url: '/mall/setting.printer/index', method: 'post', data })
  },
  /** 打印机类型 **/
  printerType(data) {
    return request({ url: '/mall/setting.printer/add', method: 'get', params: data })
  },
  /** 添加打印机 **/
  addPrinter(data) {
    return request({ url: '/mall/setting.printer/add', method: 'post', data })
  },
  /** 打印机详情 **/
  printerDetail(data) {
    return request({ url: '/mall/setting.printer/edit', method: 'get', params: data })
  },
  /** 修改打印机 **/
  editPrinter(data) {
    return request({ url: '/mall/setting.printer/edit', method: 'post', data })
  },
  /** 删除打印机 **/
  deletePrinter(data) {
    return request({ url: '/mall/setting.printer/delete', method: 'post', data })
  },
  /** 运费模板列表 **/
  deliveryList(data) {
    return request({ url: '/mall/setting.delivery/index', method: 'post', data })
  },
  /** 配送区域 **/
  deliveryArea(data) {
    return request({ url: '/mall/setting.delivery/area', method: 'post', data })
  },
  /** 添加运费模板 **/
  addDelivery(data) {
    return request({ url: '/mall/setting.delivery/add', method: 'post', data })
  },
  /** 运费模板详情 **/
  deliveryDetail(data) {
    return request({ url: '/mall/setting.delivery/edit', method: 'get', params: data })
  },
  /** 修改运费模板 **/
  editDelivery(data) {
    return request({ url: '/mall/setting.delivery/edit', method: 'post', data })
  },
  /** 删除运费模板 **/
  deleteDelivery(data) {
    return request({ url: '/mall/setting.delivery/delete', method: 'post', data })
  },
  /** 物流公司编码表 **/
  getCompany(data) {
    return request({ url: '/mall/setting.express/companyList', method: 'post', data })
  },
  /** 获取客服设置 **/
  getMpService(data) {
    return request({ url: '/mall/setting.MpService/index', method: 'get', params: data })
  },
  /** 修改客服设置 **/
  setMpService(data) {
    return request({ url: '/mall/setting.MpService/index', method: 'post', data })
  },
  /** 获取手机号设置 **/
  getPhoneDetail(data) {
    return request({ url: '/mall/setting.message/getphone', method: 'get', params: data })
  },
  /** 保存获取手机号设置 **/
  editGetPhone(data) {
    return request({ url: '/mall/setting.message/getphone', method: 'post', data })
  },
  /** 满额包邮变量 **/
  fullfreeDetail(data) {
    return request({ url: '/mall/plus.fullfree/index', method: 'get', params: data })
  },
  /** 满额包邮设置 **/
  editFullfree(data) {
    return request({ url: '/mall/plus.fullfree/index', method: 'post', data })
  },
  /** 客服设置模板变量 **/
  serviceDetail(data) {
    return request({ url: '/mall/setting.service/index', method: 'get', params: data })
  },
  /** 保存客服设置 **/
  editService(data) {
    return request({ url: '/mall/setting.service/index', method: 'post', data })
  },
  /** 协议列表 **/
  agreementList(data) {
    return request({ url: '/mall/setting.agreement/index', method: 'post', data })
  },
  /** 协议详情 **/
  agreementDetail(data) {
    return request({ url: '/mall/setting.agreement/detail', method: 'get', params: data })
  },
  /** 新增协议 **/
  createAgreement(data) {
    return request({ url: '/mall/setting.agreement/create', method: 'post', data })
  },
  /** 修改协议 **/
  updateAgreement(data) {
    return request({ url: '/mall/setting.agreement/edit', method: 'post', data })
  },
  /** 删除协议 **/
  deleteAgreement(data) {
    return request({ url: '/mall/setting.agreement/delete', method: 'get', params: data })
  }
}

export default SettingApi
