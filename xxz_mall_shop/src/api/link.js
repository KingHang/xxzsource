import request from '@/utils/request'

const LinkApi = {
  /** 获取数据列表 **/
  getList(data) {
    return request({ url: '/shop/link.link/index', method: 'post', data })
  },
  /** 获取数据列表 **/
  getPageList(data) {
    return request({ url: '/shop/link.link/getPageList', method: 'post', data })
  }
}

export default LinkApi
