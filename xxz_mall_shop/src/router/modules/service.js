/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const serviceRouter = {
  path: '/goods',
  component: Layout,
  redirect: '/goods/goods/index',
  name: 'Service',
  meta: { title: 'service', icon: 'el-icon-house' },
  children: [
    {
      path: 'goods',
      redirect: '/goods/goods/index',
      name: 'ServiceManage',
      meta: { title: 'serviceManage' },
      children: [
        {
          path: 'index',
          component: () => import('@/views/goods/goods/index'),
          name: 'ServiceList',
          meta: { title: 'serviceList' }
        },
        {
          path: 'index',
          component: () => import('@/views/goods/goods/index'),
          name: 'ServiceCategory',
          meta: { title: 'serviceCategory' }
        },
        {
          path: 'index',
          component: () => import('@/views/goods/goods/index'),
          name: 'ServiceTag',
          meta: { title: 'serviceTag' }
        }
      ]
    },
    {
      path: 'goods',
      redirect: '/goods/goods/index',
      name: 'ServiceCardManage',
      meta: { title: 'serviceCardManage' },
      children: [
        {
          path: 'index',
          component: () => import('@/views/goods/goods/index'),
          name: 'ServiceCardList',
          meta: { title: 'serviceCardList' }
        },
        {
          path: 'goods',
          component: () => import('@/views/goods/goods/index'),
          name: 'ServiceCardClass',
          meta: { title: 'serviceCardClass' }
        }
      ]
    },
    {
      path: 'goods',
      component: () => import('@/views/goods/goods/index'),
      name: 'ServiceSite',
      meta: { title: 'serviceSite' }
    }
  ]
}

export default serviceRouter
