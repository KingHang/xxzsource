/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const dataRouter = {
  path: '/',
  component: Layout,
  redirect: '/data/sales/index',
  name: 'Data',
  meta: { title: 'data', icon: 'el-icon-s-marketing' },
  children: [
    {
      path: 'data/sales/index',
      component: () => import('@/views/data/sales/index'),
      name: 'DataSales',
      meta: { title: 'dataSales' }
    },
    {
      path: 'data/user/index',
      component: () => import('@/views/data/user/index'),
      name: 'DataUser',
      meta: { title: 'dataUser' }
    },
    {
      path: 'data/purveyor/index',
      component: () => import('@/views/data/purveyor/index'),
      name: 'DataPurveyor',
      meta: { title: 'dataPurveyor' }
    },
    {
      path: 'data/access/index',
      component: () => import('@/views/data/access/index'),
      name: 'DataAccess',
      meta: { title: 'dataAccess' }
    }
  ]
}

export default dataRouter
