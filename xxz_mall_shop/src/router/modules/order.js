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
      path: 'order/order',
      component: () => import('@/views/order/order/index'),
      name: 'OrderManage',
      meta: { title: 'orderManage' },
      children: [
        {
          path: 'detail',
          component: () => import('@/views/order/order/detail'),
          name: 'OrderDetail',
          meta: { title: 'orderDetail', activeMenu: '/order/order' },
          hidden: true
        }
      ]
    },
    {
      path: 'order/refund',
      component: () => import('@/views/order/refund/index'),
      name: 'OrderRefund',
      meta: { title: 'orderRefund' },
      children: [
        {
          path: 'detail',
          component: () => import('@/views/order/refund/detail'),
          name: 'OrderRefundDetail',
          meta: { title: 'orderRefundDetail', activeMenu: '/order/refund' },
          hidden: true
        }
      ]
    },
    {
      path: 'order/platerefund',
      component: () => import('@/views/order/platerefund/index'),
      name: 'PlateRefund',
      meta: { title: 'plateRefund' },
      children: [
        {
          path: 'detail',
          component: () => import('@/views/order/platerefund/detail'),
          name: 'PlateRefundDetail',
          meta: { title: 'plateRefundDetail', activeMenu: '/order/platerefund' },
          hidden: true
        }
      ]
    }
  ]
}

export default orderRouter
