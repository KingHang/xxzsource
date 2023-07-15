/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const appRouter = {
  path: '/',
  component: Layout,
  redirect: '/app/app/index',
  name: 'App',
  meta: { title: 'app', icon: 'el-icon-help' },
  children: [
    {
      path: 'app/app/index',
      component: () => import('@/views/app/app/index'),
      name: 'AppSetting',
      meta: { title: 'appSetting' }
    },
    {
      path: 'app/appwx/index',
      component: () => import('@/views/app/appwx/index'),
      name: 'AppWx',
      meta: { title: 'appWx' }
    },
    {
      path: 'app/appmp/index',
      component: () => import('@/views/app/appmp/index'),
      name: 'AppMp',
      meta: { title: 'appMp' }
    },
    {
      path: 'app/appopen/event',
      component: () => import('@/views/app/appopen/event'),
      name: 'AppOpen',
      meta: { title: 'appOpen' }
    },
    {
      path: 'app/apph5/event',
      component: () => import('@/views/app/apph5/event'),
      name: 'AppH5',
      meta: { title: 'appH5' }
    }
  ]
}

export default appRouter
