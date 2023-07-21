/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const orderRouter = {
  path: '/',
  component: Layout,
  redirect: '/order/order/index',
  name: 'Order',
  meta: { title: 'order', icon: 'el-icon-s-order' },
  children: [
    {
      path: 'order/order/index',
      component: () => import('@/views/order/order/index'),
      name: 'OrderManage',
      meta: { title: 'orderManage' }
    },
    {
      path: 'order/order/detail',
      component: () => import('@/views/order/order/detail'),
      name: 'OrderDetail',
      meta: { title: 'orderDetail', activeMenu: '/order/order/index' },
      hidden: true
    },
    {
      path: 'order/refund/index',
      component: () => import('@/views/order/refund/index'),
      name: 'OrderRefund',
      meta: { title: 'orderRefund' }
    },
    {
      path: 'order/refund/detail',
      component: () => import('@/views/order/refund/detail'),
      name: 'OrderRefundDetail',
      meta: { title: 'orderRefundDetail', activeMenu: '/order/refund/index' },
      hidden: true
    },
    {
      path: 'order/platerefund/index',
      component: () => import('@/views/order/platerefund/index'),
      name: 'PlateRefund',
      meta: { title: 'plateRefund' }
    },
    {
      path: 'order/platerefund/detail',
      component: () => import('@/views/order/platerefund/detail'),
      name: 'PlateRefundDetail',
      meta: { title: 'plateRefundDetail', activeMenu: '/order/platerefund/index' },
      hidden: true
    }
  ]
}

export default orderRouter
