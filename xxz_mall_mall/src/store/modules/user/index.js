import { setSessionStorage, getSessionStorage, delCookie, deleteSessionStorage } from '@/utils/base'
import getRolelist from './getRolelist'
import { cearedRoute } from './cearedRoute'
import { createdAuth } from '@/utils/createdAuth'
import { constantRoutes } from '@/router/index.js'
import UserApi from '@/api/user'
import { resetRouter } from '@/router'

const user = {
  namespaced: true,
  state: {
    roles: null,
    page: null
  },
  mutations: {
    setState: (state, value) => {
      state[value.key] = value.val
    }
  },
  /* 可异步改变 */
  actions: {
    generateRoutes: function({ commit }, str) {
      return new Promise((resolve, reject) => {
        /* 判断本地缓存是否有菜单数据 */
        const rolelist = getSessionStorage('rolelist')
        let routerlist = null
        if (rolelist) {
          /* 本地缓存有数据，直接获取缓存里的数据 */
          const auth = getSessionStorage('authlist')
          if (!auth) {
            const authlist = {}
            createdAuth(auth, authlist)
            setSessionStorage('authlist', authlist)
          }
          const list = cearedRoute(rolelist)
          routerlist = list.concat(constantRoutes)
          commit('setState', {
            key: 'roles',
            val: rolelist
          })
          resolve(routerlist)
        } else {
          /* 本地缓存没有数据，去掉菜单接口 */
          getRolelist().then(res => {
            const list = cearedRoute(res)
            routerlist = list.concat(constantRoutes)
            setSessionStorage('rolelist', res)
            const authlist = {}
            createdAuth(res, authlist)
            setSessionStorage('authlist', authlist)
            commit('setState', {
              key: 'roles',
              val: rolelist
            })
            resolve(routerlist)
          }).catch(error => {
            reject(error)
          })
        }
      })
    },

    // user logout
    logout({ commit, dispatch }) {
      return new Promise((resolve, reject) => {
        UserApi.loginOut({}).then(() => {
          delCookie('isLogin')
          delCookie('baseInfo')
          deleteSessionStorage('rolelist')
          deleteSessionStorage('authlist')
          commit('setState', {
            key: 'roles',
            val: null
          })

          resetRouter()

          // reset visited views and cached views
          // to fixed https://github.com/PanJiaChen/vue-element-admin/issues/2485
          dispatch('tagsView/delAllViews', null, { root: true })

          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    // refresh
    resetToken({ commit }) {
      return new Promise(resolve => {
        delCookie('isLogin')
        delCookie('baseInfo')
        deleteSessionStorage('rolelist')
        deleteSessionStorage('authlist')
        commit('setState', {
          key: 'roles',
          val: null
        })
        resolve()
      })
    }
  }
}

export default user
