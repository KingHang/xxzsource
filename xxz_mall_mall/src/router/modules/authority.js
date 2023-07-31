/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const authorityRouter = {
  path: '/',
  component: Layout,
  redirect: '/authority/user/index',
  name: 'Authority',
  meta: { title: 'authority', icon: 'el-icon-lock' },
  children: [
    {
      path: 'authority/user/index',
      component: () => import('@/views/authority/user/index'),
      name: 'AuthorityUser',
      meta: { title: 'authorityUser' }
    },
    {
      path: 'authority/role/index',
      component: () => import('@/views/authority/role/index'),
      name: 'AuthorityRole',
      meta: { title: 'authorityRole' }
    },
    {
      path: 'authority/loginlog/index',
      component: () => import('@/views/authority/loginlog/index'),
      name: 'AuthorityLoginLog',
      meta: { title: 'authorityLoginLog' }
    },
    {
      path: 'authority/optlog/index',
      component: () => import('@/views/authority/optlog/index'),
      name: 'AuthorityOptLog',
      meta: { title: 'authorityOptLog' }
    }
  ]
}

export default authorityRouter
