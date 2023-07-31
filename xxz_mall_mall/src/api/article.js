import request from '@/utils/request'

const ArticleApi = {
  /** 文章列表 **/
  articlelist(data) {
    return request({ url: '/mall/plugin.news.news/index', method: 'post', data })
  },
  /** 获取文章分类 **/
  getCategory(data) {
    return request({ url: '/mall/plugin.news.category/index', method: 'post', data })
  },
  /** 添加文章 **/
  toAddArticle(data) {
    return request({ url: '/mall/plugin.news.news/add', method: 'get', params: data })
  },
  /** 添加文章 **/
  addArticle(data) {
    return request({ url: '/mall/plugin.news.news/add', method: 'post', data })
  },
  /** 文章详情 **/
  toEditArticle(data) {
    return request({ url: '/mall/plugin.news.news/edit', method: 'get', params: data })
  },
  /** 编辑文章 **/
  editArticle(data) {
    return request({ url: '/mall/plugin.news.news/edit', method: 'post', data })
  },
  /** 删除文章 **/
  deleteArticle(data) {
    return request({ url: '/mall/plugin.news.news/delete', method: 'post', data })
  },
  /** 添加分类 **/
  addCategory(data) {
    return request({ url: '/mall/plugin.news.category/add', method: 'post', data })
  },
  /** 编辑分类 **/
  editCategory(data) {
    return request({ url: '/mall/plugin.news.category/edit', method: 'post', data })
  },
  /** 删除分类 **/
  deleteCategory(data) {
    return request({ url: '/mall/plugin.news.category/delete', method: 'post', data })
  }
}

export default ArticleApi
