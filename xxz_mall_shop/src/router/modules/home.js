/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const homeRouter = {
  path: '/',
  component: Layout,
  redirect: '/home/home',
  name: 'Home',
  meta: { title: 'home', icon: 'el-icon-s-home' },
  children: [
    {
      path: 'home/home',
      component: () => import('@/views/home/home'),
      name: 'HomeEdit',
      meta: { title: 'homeEdit' }
    },
    {
      path: 'home/index',
      component: () => import('@/views/home/index'),
      name: 'HomePage',
      meta: { title: 'homePage' }
    },
    {
      path: 'home/category',
      component: () => import('@/views/home/category'),
      name: 'HomeCategory',
      meta: { title: 'homeCategory' }
    },
    {
      path: 'home/nav',
      component: () => import('@/views/home/nav'),
      name: 'HomeNav',
      meta: { title: 'homeNav' }
    },
    {
      path: 'home/bottomnav',
      component: () => import('@/views/home/bottomnav'),
      name: 'HomeMenu',
      meta: { title: 'homeMenu' }
    },
    {
      path: 'home/mymenu/index',
      component: () => import('@/views/home/mymenu/index'),
      name: 'HomeMyMenu',
      meta: { title: 'homeMyMenu' }
    }
  ]
}

export default homeRouter
