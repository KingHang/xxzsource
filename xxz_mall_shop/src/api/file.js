import request from '@/utils/request'

const FileApi = {
  /** 文件类别列表 **/
  fileCategory(data) {
    return request({ url: '/mall/file.file/category', method: 'post', data })
  },
  /** 文件列表 **/
  fileList(data) {
    return request({ url: '/mall/file.file/lists', method: 'post', data })
  },
  /** 删除多文件 **/
  deleteFiles(data) {
    return request({ url: '/mall/file.file/deleteFiles', method: 'post', data })
  },
  /** 添加文件分类 **/
  addCategory(data) {
    return request({ url: '/mall/file.file/addGroup', method: 'post', data })
  },
  /** 修改文件分类 **/
  editCategory(data) {
    return request({ url: '/mall/file.file/editGroup', method: 'post', data })
  },
  /** 删除文件分类 **/
  deleteCategory(data) {
    return request({ url: '/mall/file.file/deleteGroup', method: 'post', data })
  },
  /** 上传 **/
  uploadFile(data) {
    return request({ url: '/mall/file.upload/image', method: 'post', data })
  },
  /** 移动文件 **/
  moveFile(data) {
    return request({ url: '/mall/file.upload/moveFiles', method: 'post', data })
  }
}

export default FileApi
