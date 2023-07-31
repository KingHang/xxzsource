import Vue from 'vue'
import Vuex from 'vuex'
import getters from './getters'
import common from './modules/common/index'
import user from './modules/user/index'
import app from './modules/app'
import errorLog from './modules/errorLog'
import permission from './modules/permission'
import settings from './modules/settings'
import tagsView from './modules/tagsView'

Vue.use(Vuex)

// https://webpack.js.org/guides/dependency-management/#requirecontext
// const modulesFiles = require.context('./modules', true, /\.js$/)

// you do not need `import app from './modules/app'`
// it will auto require all vuex module from modules file
// const modules = modulesFiles.keys().reduce((modules, modulePath) => {
//   // set './app.js' => 'app'
//   const moduleName = modulePath.replace(/^\.\/(.*)\.\w+$/, '$1')
//   const value = modulesFiles(modulePath)
//   modules[moduleName] = value.default
//   return modules
// }, {})

const store = new Vuex.Store({
  modules: {
    common: common,
    user: user,
    app: app,
    errorLog: errorLog,
    permission: permission,
    settings: settings,
    tagsView: tagsView
  },
  getters
})

export default store
