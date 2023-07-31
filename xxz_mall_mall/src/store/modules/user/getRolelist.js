import AuthorityApi from '@/api/authority.js'

/** 获取权限 **/
const getlist = function() {
  return new Promise((resolve, reject) => {
    AuthorityApi.getRoleList({}, true)
      .then(res => {
        resolve(res.data.menus)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export default getlist
