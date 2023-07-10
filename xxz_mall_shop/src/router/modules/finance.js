/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const financeRouter = {
  path: '/',
  component: Layout,
  redirect: '/finance/finance/index',
  name: 'Finance',
  meta: { title: 'finance', icon: 'el-icon-s-finance' },
  children: [
    {
      path: 'finance/finance/index',
      component: () => import('@/views/finance/finance/index'),
      name: 'FinanceInfo',
      meta: { title: 'financeInfo' }
    },
    {
      path: 'finance/settled/index',
      component: () => import('@/views/finance/settled/index'),
      name: 'FinanceSettled',
      meta: { title: 'financeSettled' }
    },
    {
      path: 'finance/order/index',
      component: () => import('@/views/finance/order/index'),
      name: 'FinanceOrder',
      meta: { title: 'financeOrder' }
    }
  ]
}

export default financeRouter
