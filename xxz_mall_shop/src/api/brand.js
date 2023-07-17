import request from '@/utils/request'

const BrandApi = {
  /** 品牌列表 **/
  brandList(data) {
    return request({ url: '/shop/plus.brand.active/index', method: 'post', data })
  },
  /** 品牌详情 **/
  brandDetail(data) {
    return request({ url: '/shop/plus.brand.active/detail', method: 'get', params: data })
  },
  /** 品牌操作 **/
  operateBrand(data) {
    return request({ url: '/shop/plus.brand.active/operate', method: 'post', data })
  },
  /** 创建品牌 **/
  addBrand(data) {
    return request({ url: '/shop/plus.brand.active/add', method: 'post', data })
  },
  getBaseData(data) {
    return request({ url: '/shop/plus.brand.active/add', method: 'get', params: data })
  },
  /** 编辑品牌 **/
  editBrand(data) {
    return request({ url: '/shop/plus.brand.active/edit', method: 'post', data })
  },
  getEditData(data) {
    return request({ url: '/shop/plus.brand.active/edit', method: 'get', params: data })
  },
  /** 品牌审核 **/
  brandAudit(data) {
    return request({ url: '/shop/plus.brand.active/audit', method: 'post', data })
  },
  /** 品牌日列表 **/
  brandDayList(data) {
    return request({ url: '/shop/plus.brand.day/index', method: 'post', data })
  },
  /** 品牌日详情 **/
  brandDayDetail(data) {
    return request({ url: '/shop/plus.brand.day/detail', method: 'get', params: data })
  },
  /** 创建品牌日 **/
  addBrandDay(data) {
    return request({ url: '/shop/plus.brand.day/add', method: 'post', data })
  },
  /** 编辑品牌日 **/
  editBrandDay(data) {
    return request({ url: '/shop/plus.brand.day/edit', method: 'post', data })
  },
  /** 删除品牌日 **/
  deleteBrandDay(data) {
    return request({ url: '/shop/plus.brand.day/delete', method: 'post', data })
  },
  /** 品牌日品牌列表 **/
  brandDayBrandList(data) {
    return request({ url: '/shop/plus.brand.day/brand', method: 'post', data })
  },
  /** 删除品牌日品牌 **/
  deleteBrandDayBrand(data) {
    return request({ url: '/shop/plus.brand.day/deleteBrand', method: 'post', data })
  },
  /** 添加报名 **/
  addDaySign(data) {
    return request({ url: '/shop/plus.brand.day/addsign', method: 'post', data })
  },
  /** 编辑报名 **/
  editDaySign(data) {
    return request({ url: '/shop/plus.brand.day/editsign', method: 'post', data })
  },
  /** 报名记录详情 **/
  brandDaySignInfo(data) {
    return request({ url: '/shop/plus.brand.day/info', method: 'get', params: data })
  },
  /** 报名记录审核 **/
  brandDaySignAudit(data) {
    return request({ url: '/shop/plus.brand.day/audit', method: 'post', data })
  },
  /** 品牌日品牌商品列表 **/
  brandDayProductList(data) {
    return request({ url: '/shop/plus.brand.day/productList', method: 'post', data })
  },
  brandCategoryComponent(data) {
    return request({ url: '/shop/plus.brand.BrandDaySign/index', method: 'post', data })
  },
  getSetting(data) {
    return request({ url: '/shop/plus.brand.setting/index', method: 'get', params: data })
  },
  editSetting(data) {
    return request({ url: '/shop/plus.brand.setting/index', method: 'post', data })
  },
  getBrandList(data) {
    return request({ url: '/shop/plus.material.material/brand_list/index', method: 'get', params: data })
  }
}

export default BrandApi
