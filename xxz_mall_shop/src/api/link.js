import request from '@/utils/request'

const LinkApi = {
  /** 获取数据列表 **/
  getList(data) {
    return request({ url: '/mall/link.link/index', method: 'post', data })
  },
  /** 获取数据列表 **/
  getPageList(data) {
    return request({ url: '/mall/link.link/getPageList', method: 'post', data })
  }
}

export default LinkApi
