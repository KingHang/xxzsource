import Vue from 'vue'
import { getSessionStorage, setSessionStorage } from '@/utils/base'
import { createdAuth } from '@/utils/createdAuth'

const defaultImg = require('@/assets/img/default.png')

/** 指令测试 **/
Vue.directive('demo', {
  bind: function(el, binding, vnode) {
    const s = JSON.stringify
    el.innerHTML =
      'name: ' + s(binding.name) + '<br>' +
      'value: ' + s(binding.value) + '<br>' +
      'expression: ' + s(binding.expression) + '<br>' +
      'argument: ' + s(binding.arg) + '<br>' +
      'modifiers: ' + s(binding.modifiers) + '<br>' +
      'vnode keys: ' + Object.keys(vnode).join(', ')
  }
})

/** 权限 **/
Vue.directive('auth', function(el, binding) {
  let auth = getSessionStorage('authlist')
  if (!auth) {
    const authlist = {}
    createdAuth(auth, authlist)
    setSessionStorage('authlist', authlist)
    auth = authlist
  }
  const value = binding.value.toLowerCase()
  if (auth[value] !== true) {
    el.style.display = 'none'
  }
})

/** 默认图片 **/
Vue.directive('img-url', async function(el, binding) {
  let imgURL = ''

  if (binding.value instanceof Object) {
    const jsonStr = binding.expression.split(',')[0].replace(/\'/g, '')
    imgURL = checkChild(binding.value, jsonStr)
  } else {
    imgURL = binding.value
  }

  if (imgURL && typeof (imgURL) !== 'undefined') {
    const exist = await imageIsExist(imgURL)
    if (exist) {
      el.setAttribute('src', imgURL)
    } else {
      el.setAttribute('src', defaultImg)
    }
  } else {
    el.setAttribute('src', defaultImg)
  }
})

/** 检查子对象是否为NUll **/
const checkChild = function(obj, str) {
  let _img = null
  const arr = str.match(/\./g)
  if (!arr) {
    _img = obj[str]
  } else {
    const _i = str.indexOf('.')
    const key = str.substring(0, _i)
    const newobj = obj[key]
    if (newobj instanceof Object) {
      const newstr = str.substring(_i + 1)
      _img = checkChild(newobj, newstr)
    }
  }
  return _img
}

/** 检测图片是否存在 **/
const imageIsExist = function(url) {
  return new Promise((resolve) => {
    let img = new Image()
    img.onload = function() {
      if (this.complete === true) {
        resolve(true)
        img = null
      }
    }
    img.onerror = function() {
      resolve(false)
      img = null
    }
    img.src = url
  })
}
