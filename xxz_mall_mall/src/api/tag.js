import request from '@/utils/request'

const TagApi = {
  /** 等级列表 **/
  tagList(data) {
    return request({ url: '/mall/user.tag/index', method: 'post', data })
  },
  /** 添加等级 **/
  addTag(data) {
    return request({ url: '/mall/user.tag/add', method: 'post', data })
  },
  /** 修改等级 **/
  editTag(data) {
    return request({ url: '/mall/user.tag/edit', method: 'post', data })
  },
  /** 删除等级 **/
  deleteTag(data) {
    return request({ url: '/mall/user.tag/delete', method: 'post', data })
  }
}

export default TagApi
