<template>
  <div v-if="!loading" :class="{'has-logo':showLogo}">
    <div class="left-menu-box">
      <!-- 一级菜单 -->
      <div class="first-menu" :class="{'collapse': isCollapse}">
        <logo v-if="showLogo" :collapse="isCollapse" />
        <el-scrollbar wrap-class="scrollbar-wrapper">
          <div
            v-for="(item, index) in firstMenu"
            :key="index"
            :class="index === active_menu ? 'menu-item menu-active-item' : 'menu-item'"
            @click="choseMenu(item)"
          >
            <span :class="'icon iconfont menu-item-icon ' + item.icon" />
            <span v-if="!isCollapse">{{ item.name }}</span>
          </div>
        </el-scrollbar>
      </div>

      <!-- 二级菜单 -->
      <div v-if="!isCollapse" class="sub-menu-box">
        <div class="fixed-sub-menu">{{ menu_name }}</div>
        <el-scrollbar wrap-class="scrollbar-wrapper">
          <el-menu
            :default-active="activeMenu"
            :collapse="isCollapse"
            :background-color="variables.subMenuBg"
            :text-color="variables.menuText"
            :unique-opened="true"
            :active-text-color="variables.subMenuActiveText"
            :collapse-transition="false"
            mode="vertical"
          >
            <sidebar-item v-for="route in subMenu" :key="route.path" :item="route" :base-path="route.path" />
          </el-menu>
        </el-scrollbar>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Logo from './Logo'
import SidebarItem from './SidebarItem'
import variables from '@/styles/variables.scss'
import { getSessionStorage, setSessionStorage } from '@/utils/base'
import bus from '@/utils/eventBus.js'

export default {
  components: { SidebarItem, Logo },
  data() {
    return {
      loading: true,
      /* 传到顶部的标题 */
      menu_name: '首页',
      /* 选中的菜单 */
      active_menu: null,
      /* 菜单数据 */
      menuList: [],
      /* 插件传的值 */
      activeValue: 0,
      tab_type: '',
      tab_name: '',
      firstMenu: [],
      subMenu: [],
      /* 插件菜单 */
      pluginMenu: []
    }
  },
  computed: {
    ...mapGetters([
      'sidebar'
    ]),
    activeMenu() {
      const route = this.$route
      const { meta, path } = route
      // if set path, the sidebar will highlight the path you set
      if (meta.activeMenu) {
        return meta.activeMenu
      }
      return path
    },
    showLogo() {
      return this.$store.state.settings.sidebarLogo
    },
    variables() {
      return variables
    },
    isCollapse() {
      return !this.sidebar.opened
    }
  },
  watch: {
    /* 监听路由 */
    $route(to, from) {
      this.selectMenu(to)
    }
  },
  created() {
    this.menuList = getSessionStorage('rolelist')
    this.initRoutes()
    this.selectMenu(this.$route)

    /* 监听插件的传值 */
    bus.$on('tabData', res => {
      this.tabList = res.list
      this.activeValue = res.active
      this.tab_type = res.tab_type
      this.tab_name = res.tab_name
      if (!this.tabList.length) {
        const childTabInfo = JSON.parse(getSessionStorage('childTabInfo'))
        this.tabList = childTabInfo.tabList
        this.activeValue = childTabInfo.activeValue
        this.tab_type = childTabInfo.tab_type
        this.tab_name = childTabInfo.tab_name
      } else {
        setSessionStorage('childTabInfo', JSON.stringify({ tabList: this.tabList, activeValue: this.activeValue, tab_type: this.tab_type, tab_name: this.tab_name }))
      }
      // this.getPluginMenu()
    })

    const hasTrader = this.$route.path.indexOf('plugin/') && this.$route.path.indexOf('plugin/plugin') < 0

    if (hasTrader && !this.tabList.length) {
      const childTabInfo = JSON.parse(getSessionStorage('childTabInfo'))
      this.tabList = childTabInfo.tabList
      this.activeValue = childTabInfo.activeValue
      this.tab_type = childTabInfo.tab_type
      this.tab_name = childTabInfo.tab_name
      // this.getPluginMenu()
    }
  },
  beforeDestroy() {
    bus.$off('tabData')
  },
  methods: {
    initRoutes() {
      this.loading = true
      this.menuList.forEach((item) => {
        if (item.is_menu === 1 && item.is_route === 1 && item.parent_id === 0) {
          this.firstMenu.push(item)
        }
      })
      this.subMenu = this.firstMenu[0].children
      this.loading = false
    },
    /** 获取插件菜单 **/
    getPluginMenu() {
      for (let i = 0; i < this.pluginMenu.length; i++) {
        if (this.tab_name === this.pluginMenu[i].name) {
          this.subMenu = this.pluginMenu[i].children
        }
      }
    },
    /** 点击菜单跳转 **/
    choseMenu(item) {
      if (item != null) {
        this.$router.push(item.path)
      } else {
        this.$router.push('/')
      }
    },
    /** 判断字符串第一个是否斜杠 **/
    slantingBar(str) {
      str = str.toLowerCase()
      if (str.length > 0) {
        if (str.substr(0, 1) === '/') {
          return str
        } else {
          return '/' + str
        }
      } else {
        return str
      }
    },
    /** 菜单 **/
    selectMenu(to) {
      this.menu_name = '首页'
      const menupath = to.path.toLowerCase()
      let active = null
      for (let i = 0; i < this.menuList.length; i++) {
        const item = this.menuList[i]
        /* 判断主菜单选择 */
        if (menupath === this.slantingBar(item['path']) || menupath === this.slantingBar(item['redirect_name'])) {
          this.menu_name = item['name']
          active = i
          break
        } else {
          /** 判断子菜单选择 **/
          if (item['children']) {
            const childs = item['children']
            for (let c = 0; c < childs.length; c++) {
              const child = childs[c]
              /* 保存插件菜单 */
              if (child['path'] === '/plugin/plugin/index') {
                this.pluginMenu = child.children
              }
              if (menupath === this.slantingBar(child['path'])) {
                active = i
                this.menu_name = child['name']
                break
              } else {
                if (child['children']) {
                  const name = this.hasChild(menupath, child['children'])
                  if (name != null) {
                    active = i
                    this.menu_name = name
                    break
                  }
                }
              }
            }
          }
        }
      }
      this.active_menu = active
      this.subMenu = this.menuList[active]['children']
      this.$emit('selectMenu', active)
    },
    /** 判断子菜单有没有 **/
    hasChild(path, list) {
      let name = null
      for (let i = 0, length = list.length; i < length; i++) {
        const item = list[i]
        if (path === this.slantingBar(item['path'])) {
          name = item['name']
          break
        } else {
          if (item['children'] != null && item['children'].length > 0) {
            name = this.hasChild(path, item['children'])
            if (name != null) {
              break
            }
          }
        }
      }
      return name
    }
  }
}
</script>

<style lang="scss" scoped>
  .left-menu-box {
    display: flex;
    height: 100%;

    .first-menu {
      width: 90px;

      .menu-item {
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 14px;
        color: #BDBDBD;
        cursor: pointer;
        font-weight: 400;
        margin: 15px 6px;

        .menu-item-icon {
          font-size: 18px;
          margin-right: 8px;
        }
      }

      .menu-item:hover {
        color: #FFFFFF;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 2px;
      }

      .menu-active-item, .menu-active-item:hover {
        color: #FFFFFF;
        background: #339EFB;
        border-radius: 2px;
      }
    }

    .sub-menu-box {
      width: 140px;
      background: #ffffff;
      border-right: 1px solid #F1F1F1;

      .fixed-sub-menu {
        height: 51px;
        line-height: 51px;
        border-bottom: 1px solid #F1F1F1;
        font-size: 14px;
        color: #333333;
        padding-left: 20px;
        cursor: pointer;
      }
    }

    .collapse {
      .menu-item {
        .menu-item-icon {
          margin-right: 0;
        }
      }
    }
  }
</style>
