/**
 * 设置cookie
 */
export const setCookie = (name, value, expiredays) => {
  const exdate = new Date()
  exdate.setDate(exdate.getDate() + expiredays)
  document.cookie = name + '=' + escape(value) + ((expiredays == null) ? '' : ';expires=' + exdate.toGMTString()) + ';path=/'
}

/**
 * 获取cookie
 */
export const getCookie = (name) => {
  const reg = new RegExp('(^| )' + name + '=([^;]*)(;|$)')
  const arr = document.cookie.match(reg)
  if (arr && arr.length > 2) {
    return unescape(arr[2])
  } else {
    return null
  }
}

/**
 * 删除cookie
 */
export const delCookie = (name) => {
  const exp = new Date()
  exp.setTime(exp.getTime() - 1)
  const cval = getCookie(name)
  if (cval != null) { document.cookie = name + '=' + cval + ';expires=' + exp.toGMTString() + ';path=/' }
}

/**
 * 设置sessionStorage
 */
export const setSessionStorage = (name, val) => {
  sessionStorage.setItem(name, JSON.stringify(val))
}

/**
 * 通过参数获取sessionStorage的数据
 */
export const getSessionStorage = (name) => {
  if (Object.prototype.hasOwnProperty.call(sessionStorage, name)) {
    return JSON.parse(sessionStorage.getItem(name))
  } else {
    return false
  }
}

/**
 * 附加sessionStorage, type==true 则是整个修改，
 */
export const addSessionStorage = (name, val) => {
  if (Object.prototype.hasOwnProperty.call(sessionStorage, name)) {
    const old = JSON.parse(sessionStorage.getItem(name))
    const obj = Object.assign(old, val)
    sessionStorage.setItem(name, JSON.stringify(obj))
  } else {
    sessionStorage.setItem(name, JSON.stringify(val))
  }
}

/**
 * 删除sessionStorage
 */
export const deleteSessionStorage = (name = null) => {
  if (name != null) {
    sessionStorage.removeItem(name)
  } else {
    sessionStorage.clear()
  }
}

/**
 * 设置localStorage
 */
export const setLocalStorage = (name, val) => {
  localStorage.setItem(name, JSON.stringify(val))
}

/**
 * 通过参数获取localStorage的数据
 */
export const getLocalStorage = (name) => {
  if (Object.prototype.hasOwnProperty.call(localStorage, name)) {
    return JSON.parse(localStorage.getItem(name))
  } else {
    return false
  }
}

/**
 * 附加localStorage, type==true 则是整个修改，
 */
export const addLocalStorage = (name, val) => {
  if (Object.prototype.hasOwnProperty.call(localStorage, name)) {
    const old = JSON.parse(localStorage.getItem(name))
    const obj = Object.assign(old, val)
    localStorage.setItem(name, JSON.stringify(obj))
  } else {
    localStorage.setItem(name, JSON.stringify(val))
  }
}

/**
 * 删除localStorage
 */
export const deleteLocalStorage = (name = null) => {
  if (name != null) {
    localStorage.removeItem(name)
  } else {
    localStorage.clear()
  }
}

/**
 * 深拷贝
 */
export const deepClone = (obj) => {
  const objClone = Array.isArray(obj) ? [] : {}
  if (obj && typeof obj === 'object') {
    for (const key in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, key)) {
        if (obj[key] && typeof obj[key] === 'object') {
          objClone[key] = deepClone(obj[key])
        } else {
          objClone[key] = obj[key]
        }
      }
    }
  }
  return objClone
}

/**
 * 深合并
 */
export const deepMerger = (obj1, obj2) => {
  for (const key in obj2) {
    if (Object.prototype.hasOwnProperty.call(obj2, key)) {
      if (obj2[key] && typeof obj2[key] === 'object') {
        obj1[key] = deepMerger(obj1[key], obj2[key])
      } else {
        obj1[key] = obj2[key]
      }
    }
  }
  return obj1
}

/**
 * 格式
 */
export const formatModel = (thisObj, sourceObj) => {
  for (const key in thisObj) {
    if (sourceObj && typeof (sourceObj[key]) !== 'undefined') {
      if (thisObj[key] && Object.prototype.toString.call(thisObj[key]) === '[object Object]') {
        formatModel(thisObj[key], sourceObj[key])
      } else {
        thisObj[key] = sourceObj[key]
      }
    }
  }
  return thisObj
}
