/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const goodsRouter = {
  path: '/goods',
  component: Layout,
  redirect: '/goods/goods/index',
  name: 'Goods',
  meta: { title: 'goods', icon: 'el-icon-goods' },
  children: [
    {
      path: 'goods',
      component: () => import('@/views/goods/goods/index'),
      name: 'GoodsManage',
      meta: { title: 'goodsManage' }
    },
    {
      path: 'category',
      component: () => import('@/views/goods/category/index'),
      name: 'GoodsCategory',
      meta: { title: 'goodsCategory' }
    },
    {
      path: 'comment',
      component: () => import('@/views/goods/comment/index'),
      name: 'GoodsComment',
      meta: { title: 'goodsComment' }
    }
  ]
}

export default goodsRouter
