import request from '@/utils/request'

const GoodsApi = {
  /** 分类管理 **/
  catList(data) {
    return request({ url: '/mall/goods.category/index', method: 'post', data })
  },
  /** 显示分类管理 **/
  showCatList(data) {
    return request({ url: '/mall/goods.category/show', method: 'post', data })
  },
  /** 分类添加 **/
  catAdd(data) {
    return request({ url: '/mall/goods.category/add', method: 'post', data })
  },
  /** 分类删除 **/
  catDel(data) {
    return request({ url: '/mall/goods.category/delete', method: 'post', data })
  },
  /** 分类修改 **/
  catEdit(data) {
    return request({ url: '/mall/goods.category/edit', method: 'post', data })
  },
  /** 产品列表 **/
  productList(data) {
    return request({ url: '/mall/goods.goods/index', method: 'post', data })
  },
  /** 产品选择列表 **/
  chooseLists(data) {
    return request({ url: '/mall/data.goods/lists', method: 'post', data })
  },
  /** 规格选择列表 **/
  chooseSpec(data) {
    return request({ url: '/mall/data.goods/spec', method: 'post', data })
  },
  /** 新增产品 **/
  addProduct(data) {
    return request({ url: '/mall/goods.goods/add', method: 'post', data })
  },
  /** 产品基础数据 **/
  getBaseData(data) {
    return request({ url: '/mall/goods.goods/add', method: 'get', params: data })
  },
  /** 删除产品 **/
  delProduct(data) {
    return request({ url: '/mall/goods.goods/delete', method: 'post', data })
  },
  /** 产品基础数据 **/
  getEditData(data) {
    return request({ url: '/mall/goods.goods/edit', method: 'get', params: data })
  },
  /** 新增产品 **/
  editProduct(data) {
    return request({ url: '/mall/goods.goods/edit', method: 'post', data })
  },
  /** 审核产品 **/
  auditProduct(data) {
    return request({ url: '/mall/goods.goods/audit', method: 'post', data })
  },
  /** 产品上下架 **/
  handleProduct(data) {
    return request({ url: '/mall/goods.goods/handle', method: 'post', data })
  },
  /** 新增规格组 **/
  addSpec(data) {
    return request({ url: '/mall/goods.spec/addSpec', method: 'post', data })
  },
  /** 新增规格值 **/
  addSpecValue(data) {
    return request({ url: '/mall/goods.spec/addSpecValue', method: 'post', data })
  },
  /** 商品列表不带分页 **/
  getList(data) {
    return request({ url: '/mall/data.goods/lists', method: 'post', data })
  },
  /** 商品列表不带分页 **/
  getActiveProductList(data) {
    return request({ url: '/mall/plus.fans.goods/lists', method: 'post', data })
  },
  /** 商品评论列表 **/
  getCommentList(data) {
    return request({ url: '/mall/goods.comment/index', method: 'post', data })
  },
  /** 获取评论详情 **/
  getComment(data) {
    return request({ url: '/mall/goods.comment/detail', method: 'post', data })
  },
  /** 获取评论详情 **/
  editComment(data) {
    return request({ url: '/mall/goods.comment/edit', method: 'post', data })
  },
  /** 删除评论 **/
  delComment(data) {
    return request({ url: '/mall/goods.comment/delete', method: 'post', data })
  },
  /** 推广图片 **/
  getPromoteImage(data) {
    return request({ url: '/api/goods.goods/getImage', method: 'post', data })
  },
  /** 推广图片 **/
  cateImage(data) {
    return request({ url: '/mall/goods.category/image', method: 'post', data })
  }
}

export default GoodsApi
