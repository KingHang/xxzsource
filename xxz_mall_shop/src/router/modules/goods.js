/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const goodsRouter = {
  path: '/',
  component: Layout,
  redirect: '/goods/goods/index',
  name: 'Goods',
  meta: { title: 'goods', icon: 'el-icon-s-goods' },
  children: [
    {
      path: 'goods/goods',
      component: () => import('@/views/goods/goods/index'),
      name: 'GoodsManage',
      meta: { title: 'goodsManage' },
      children: [
        {
          path: 'add',
          component: () => import('@/views/goods/goods/add'),
          name: 'GoodsAdd',
          meta: { title: 'goodsAdd', activeMenu: '/goods/goods' },
          hidden: true
        },
        {
          path: 'edit',
          component: () => import('@/views/goods/goods/edit'),
          name: 'GoodsEdit',
          meta: { title: 'goodsEdit', activeMenu: '/goods/goods' },
          hidden: true
        }
      ]
    },
    {
      path: 'goods/category',
      component: () => import('@/views/goods/category/index'),
      name: 'GoodsCategory',
      meta: { title: 'goodsCategory' }
    },
    {
      path: 'goods/comment',
      component: () => import('@/views/goods/comment/index'),
      name: 'GoodsComment',
      meta: { title: 'goodsComment' },
      children: [
        {
          path: 'detail',
          component: () => import('@/views/goods/comment/detail'),
          name: 'GoodsCommentDetail',
          meta: { title: 'goodsCommentDetail', activeMenu: '/goods/comment' },
          hidden: true
        }
      ]
    }
  ]
}

export default goodsRouter
