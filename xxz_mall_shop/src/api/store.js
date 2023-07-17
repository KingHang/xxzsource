import request from '@/utils/request'

const StoreApi = {
  /** 添加门店 **/
  addShop(data) {
    return request({ url: '/shop/store.store/add', method: 'post', data })
  },
  /** 门店详情 **/
  shopDetail(data) {
    return request({ url: '/shop/store.store/edit', method: 'get', params: data })
  },
  /** 门店编辑 **/
  editShop(data) {
    return request({ url: '/shop/store.store/edit', method: 'post', data })
  },
  /** 删除门店 **/
  deleteShop(data) {
    return request({ url: '/shop/store.store/delete', method: 'post', data })
  },
  /** 门店列表 **/
  shoplist(data) {
    return request({ url: '/shop/store.store/index', method: 'post', data })
  },
  /** 门店选择列表 **/
  storeLists(data) {
    return request({ url: '/shop/data.store/lists', method: 'post', data })
  },
  /** 店员列表 **/
  clerklist(data) {
    return request({ url: '/shop/store.clerk/index', method: 'post', data })
  },
  /** 店员添加 **/
  addClerk(data) {
    return request({ url: '/shop/store.clerk/add', method: 'post', data })
  },
  /** 店员详情 **/
  clerkDetail(data) {
    return request({ url: '/shop/store.clerk/edit', method: 'get', params: data })
  },
  /** 店员编辑 **/
  editClerk(data) {
    return request({ url: '/shop/store.clerk/edit', method: 'post', data })
  },
  /** 删除店员 **/
  deleteClerk(data) {
    return request({ url: '/shop/store.clerk/delete', method: 'post', data })
  },
  /** 弹出层查询 **/
  search(data) {
    return request({ url: '/shop/store.clerk/search', method: 'post', data })
  },
  /** 核销订单列表 **/
  orderList(data) {
    return request({ url: '/shop/store.order/index', method: 'post', data })
  }
}

export default StoreApi
