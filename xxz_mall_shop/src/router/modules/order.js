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
      path: 'order/refund/index',
      component: () => import('@/views/order/refund/index'),
      name: 'OrderRefund',
      meta: { title: 'orderRefund' }
    },
    {
      path: 'order/platerefund/index',
      component: () => import('@/views/order/platerefund/index'),
      name: 'PlateRefund',
      meta: { title: 'plateRefund' }
    }
  ]
}

export default orderRouter
