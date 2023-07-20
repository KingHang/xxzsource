import request from '@/utils/request'

const HomeApi = {
  /** 获取数据 **/
  detail(data) {
    return request({ url: '/mall/page.page/detail', method: 'post', data })
  },
  /** 上架样式 **/
  toAddPage(data) {
    return request({ url: '/mall/page.page/add', method: 'get', params: data })
  },
  /** 上架样式 **/
  addPage(data) {
    return request({ url: '/mall/page.page/add', method: 'post', data })
  },
  /** 页面列表 **/
  PageList(data) {
    return request({ url: '/mall/page.page/index', method: 'post', data })
  },
  /** 删除页面 **/
  deletePage(data) {
    return request({ url: '/mall/page.page/delete', method: 'post', data })
  },
  /** 设为首页 **/
  setHome(data) {
    return request({ url: '/mall/page.page/setHome', method: 'post', data })
  },
  /** 编辑页面 **/
  editHome(data) {
    return request({ url: '/mall/page.page/home', method: 'get', params: data })
  },
  /** 编辑页面 **/
  editPage(data) {
    return request({ url: '/mall/page.page/edit', method: 'get', params: data })
  },
  /** 保存编辑页面 **/
  SavePage(data) {
    return request({ url: '/mall/page.page/edit', method: 'post', data })
  },
  /** 获取分类 **/
  getCategory(data) {
    return request({ url: '/mall/page.page/category', method: 'get', params: data })
  },
  /** 提交分类 **/
  postCategory(data) {
    return request({ url: '/mall/page.page/category', method: 'post', data })
  },
  /** 获取导航 **/
  getNav(data) {
    return request({ url: '/mall/page.page/nav', method: 'get', params: data })
  },
  /** 提交导航 **/
  postNav(data) {
    return request({ url: '/mall/page.page/nav', method: 'post', data })
  },
  /** 获取导航 **/
  getbottomNav(data) {
    return request({ url: '/mall/page.page/bottomnav', method: 'get', params: data })
  },
  /** 提交导航 **/
  postbottomNav(data) {
    return request({ url: '/mall/page.page/bottomnav', method: 'post', data })
  },
  editTabbar(data) {
    return request({ url: '/mall/page.page/bottomedit', method: 'post', data })
  },
  /** 广告列表 **/
  menuList(data) {
    return request({ url: '/mall/page.page.Mymenu/index', method: 'post', data })
  },
  /** 添加广告 **/
  addMenu(data) {
    return request({ url: '/mall/page.page.Mymenu/add', method: 'post', data })
  },
  /** 广告详情 **/
  menuDetail(data) {
    return request({ url: '/mall/page.page.Mymenu/detail', method: 'get', params: data })
  },
  /** 修改广告 **/
  editMenu(data) {
    return request({ url: '/mall/page.page.Mymenu/edit', method: 'post', data })
  },
  /** 删除广告 **/
  deleteMenu(data) {
    return request({ url: '/mall/page.page.Mymenu/delete', method: 'post', data })
  }
}

export default HomeApi
