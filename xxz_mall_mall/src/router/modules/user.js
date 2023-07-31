/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const userRouter = {
  path: '/',
  component: Layout,
  redirect: '/user/user/index',
  name: 'User',
  meta: { title: 'user', icon: 'el-icon-user-solid' },
  children: [
    {
      path: 'user/user/index',
      component: () => import('@/views/user/user/index'),
      name: 'UserManage',
      meta: { title: 'userManage' }
    },
    {
      path: 'user/grade/index',
      component: () => import('@/views/user/grade/index'),
      name: 'UserGrade',
      meta: { title: 'userGrade' }
    },
    {
      path: 'user/tag/index',
      component: () => import('@/views/user/tag/index'),
      name: 'UserTag',
      meta: { title: 'userTag' }
    },
    {
      path: 'user/points/index',
      component: () => import('@/views/user/points/index'),
      name: 'UserPoints',
      meta: { title: 'userPoints' }
    },
    {
      path: 'user/balance/index',
      component: () => import('@/views/user/balance/index'),
      name: 'UserBalance',
      meta: { title: 'userBalance' }
    }
  ]
}

export default userRouter
