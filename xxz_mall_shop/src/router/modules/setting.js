/** When your routing table is too long, you can split it into small modules**/

import Layout from '@/layout'

const settingRouter = {
  path: '/',
  component: Layout,
  redirect: '/setting/store/index',
  name: 'Setting',
  meta: { title: 'setting', icon: 'el-icon-setting' },
  children: [
    {
      path: 'setting/store/index',
      component: () => import('@/views/setting/store/index'),
      name: 'SettingStore',
      meta: { title: 'settingStore' }
    },
    {
      path: 'setting/trade/index',
      component: () => import('@/views/setting/trade/index'),
      name: 'SettingTrade',
      meta: { title: 'settingTrade' }
    },
    {
      path: 'setting/delivery/index',
      component: () => import('@/views/setting/delivery/index'),
      name: 'SettingDelivery',
      meta: { title: 'settingDelivery' }
    },
    {
      path: 'setting/express/index',
      component: () => import('@/views/setting/express/index'),
      name: 'SettingExpress',
      meta: { title: 'settingExpress' }
    },
    {
      path: 'setting/message/index',
      component: () => import('@/views/setting/message/index'),
      name: 'SettingMessage',
      meta: { title: 'settingMessage' }
    },
    {
      path: 'setting/sms/index',
      component: () => import('@/views/setting/sms/index'),
      name: 'SettingSms',
      meta: { title: 'settingSms' }
    },
    {
      path: 'setting/protocol/index',
      component: () => import('@/views/setting/protocol/index'),
      name: 'SettingProtocol',
      meta: { title: 'settingProtocol' }
    },
    {
      path: 'setting/storage/index',
      component: () => import('@/views/setting/storage/index'),
      name: 'SettingStorage',
      meta: { title: 'settingStorage' }
    },
    {
      path: 'setting/clear/index',
      component: () => import('@/views/setting/clear/index'),
      name: 'SettingClear',
      meta: { title: 'settingClear' }
    },
    {
      path: 'setting/printer/index',
      component: () => import('@/views/setting/printer/index'),
      name: 'SettingPrinter',
      meta: { title: 'settingPrinter' }
    },
    {
      path: 'setting/printing/index',
      component: () => import('@/views/setting/printing/index'),
      name: 'SettingPrinting',
      meta: { title: 'settingPrinting' }
    },
    {
      path: 'setting/address/index',
      component: () => import('@/views/setting/address/index'),
      name: 'SettingAddress',
      meta: { title: 'settingAddress' }
    }
  ]
}

export default settingRouter
