import request from '@/utils/request'

const BenefitApi = {
  /** 权益列表 **/
  benefitList(data) {
    return request({ url: '/mall/plugin.benefit.benefit/index', method: 'post', data })
  },
  /** 权益新增 **/
  benefitAdd(data) {
    return request({ url: '/mall/plugin.benefit.benefit/add', method: 'post', data })
  },
  /** 权益编辑 **/
  benefitEdit(data) {
    return request({ url: '/mall/plugin.benefit.benefit/edit', method: 'post', data })
  },
  /** 获取权益信息权益 **/
  benefitDetail(data) {
    return request({ url: '/mall/plugin.benefit.benefit/edit', method: 'get', params: data })
  },
  /** 权益操作 **/
  setOperate(data) {
    return request({ url: '/mall/plugin.benefit.benefit/operate', method: 'post', data })
  },
  /** 权益列表 **/
  CardList(data) {
    return request({ url: '/mall/plugin.benefit.benefitCard/index', method: 'post', data })
  },
  /** 权益新增 **/
  CardAdd(data) {
    return request({ url: '/mall/plugin.benefit.benefitCard/add', method: 'post', data })
  },
  /** 权益编辑 **/
  CardEdit(data) {
    return request({ url: '/mall/plugin.benefit.benefitCard/edit', method: 'post', data })
  },
  /** 获取权益信息权益 **/
  CardDetail(data) {
    return request({ url: '/mall/plugin.benefit.benefitCard/edit', method: 'get', params: data })
  },
  /** 权益操作 **/
  setCardOperate(data) {
    return request({ url: '/mall/plugin.benefit.benefitCard/operate', method: 'post', data })
  },
  /** 使用记录 **/
  getLogList(data) {
    return request({ url: '/mall/plugin.benefit.benefitCard/log', method: 'post', data })
  },
  /** 权益卡设置 **/
  CardSettingDetail(data) {
    return request({ url: '/mall/plugin.benefit.benefitCard/setting', method: 'get', params: data })
  },
  /** 权益卡设置 **/
  editCardSetting(data) {
    return request({ url: '/mall/plugin.benefit.benefitCard/setting', method: 'post', data })
  }
}

export default BenefitApi
