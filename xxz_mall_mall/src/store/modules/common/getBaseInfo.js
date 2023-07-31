import AuthorityApi from '@/api/authority.js'

/** 获取权限 **/
const baseInfo = async function() {
  return new Promise((resolve, reject) => {
    AuthorityApi.getUserInfo({}, true)
      .then(res => {
        resolve(res.data)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export default baseInfo
