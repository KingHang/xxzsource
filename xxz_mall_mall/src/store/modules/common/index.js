import getBaseInfoList from './getBaseInfo'
import { setCookie, getCookie } from '@/utils/base'

const common = {
  namespaced: true,
  state: {
    is_show: false,
    page: null,
    base_info: null
  },
  mutations: {
    setState(state, value) {
      state[value.key] = value.val
    }
  },
  /* 可异步改变 */
  actions: {
    getBaseInfo({ commit }) {
      return new Promise((resolve, reject) => {
        const _data = getCookie('baseInfo')
        if (_data != null) {
          commit('setState', { key: 'baseInfo', val: JSON.parse(_data) })
          resolve(JSON.parse(_data))
        } else {
          getBaseInfoList().then(res => {
            commit('setState', { key: 'baseInfo', val: res })
            setCookie('baseInfo', JSON.stringify(res))
            resolve(res)
          }).catch(error => {
            reject(error)
          })
        }
      })
    }
  }
}

export default common
