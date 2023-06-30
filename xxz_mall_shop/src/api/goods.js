import request from '@/utils/request'

const GoodsApi = {
  /** 分类管理 **/
  catList(data) {
    return request({ url: '/shop/product.category/index', method: 'post', data })
  },
  /** 显示分类管理 **/
  showCatList(data) {
    return request({ url: '/shop/product.category/show', method: 'post', data })
  },
  /** 分类添加 **/
  catAdd(data) {
    return request({ url: '/shop/product.category/add', method: 'post', data })
  },
  /** 分类删除 **/
  catDel(data) {
    return request({ url: '/shop/product.category/delete', method: 'post', data })
  },
  /** 分类修改 **/
  catEdit(data) {
    return request({ url: '/shop/product.category/edit', method: 'post', data })
  },
  /** 产品列表 **/
  productList(data) {
    return request({ url: '/shop/product.product/index', method: 'post', data })
  },
  /** 产品选择列表 **/
  chooseLists(data) {
    return request({ url: '/shop/data.product/lists', method: 'post', data })
  },
  /** 规格选择列表 **/
  chooseSpec(data) {
    return request({ url: '/shop/data.product/spec', method: 'post', data })
  },
  /** 新增产品 **/
  addProduct(data) {
    return request({ url: '/shop/product.product/add', method: 'post', data })
  },
  /** 产品基础数据 **/
  getBaseData(data) {
    return request({ url: '/shop/product.product/add', method: 'get', params: data })
  },
  /** 删除产品 **/
  delProduct(data) {
    return request({ url: '/shop/product.product/delete', method: 'post', data })
  },
  /** 产品基础数据 **/
  getEditData(data) {
    return request({ url: '/shop/product.product/edit', method: 'get', params: data })
  },
  /** 新增产品 **/
  editProduct(data) {
    return request({ url: '/shop/product.product/edit', method: 'post', data })
  },
  /** 审核产品 **/
  auditProduct(data) {
    return request({ url: '/shop/product.product/audit', method: 'post', data })
  },
  /** 产品上下架 **/
  handleProduct(data) {
    return request({ url: '/shop/product.product/handle', method: 'post', data })
  },
  /** 新增规格组 **/
  addSpec(data) {
    return request({ url: '/shop/product.spec/addSpec', method: 'post', data })
  },
  /** 新增规格值 **/
  addSpecValue(data) {
    return request({ url: '/shop/product.spec/addSpecValue', method: 'post', data })
  },
  /** 商品列表不带分页 **/
  getList(data) {
    return request({ url: '/shop/data.product/lists', method: 'post', data })
  },
  /** 商品列表不带分页 **/
  getActiveProductList(data) {
    return request({ url: '/shop/plus.fans.product/lists', method: 'post', data })
  },
  /** 商品评论列表 **/
  getCommentList(data) {
    return request({ url: '/shop/product.comment/index', method: 'post', data })
  },
  /** 获取评论详情 **/
  getComment(data) {
    return request({ url: '/shop/product.comment/detail', method: 'post', data })
  },
  /** 获取评论详情 **/
  editComment(data) {
    return request({ url: '/shop/product.comment/edit', method: 'post', data })
  },
  /** 删除评论 **/
  delComment(data) {
    return request({ url: '/shop/product.comment/delete', method: 'post', data })
  },
  /** 推广图片 **/
  getPromoteImage(data) {
    return request({ url: '/api/product.product/getImage', method: 'post', data })
  },
  /** 推广图片 **/
  cateImage(data) {
    return request({ url: '/shop/product.category/image', method: 'post', data })
  }
}

export default GoodsApi
