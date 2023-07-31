<template>
  <div v-if="!loading" :class="{'has-logo':showLogo}">
    <div class="left-menu-box">
      <!-- 一级菜单 -->
      <div class="first-menu" :class="{'collapse': isCollapse}">
        <logo v-if="showLogo" :collapse="isCollapse" />
        <el-scrollbar wrap-class="scrollbar-wrapper">
          <div v-for="(item, index) in firstMenu" :key="index" :class="item.meta.title === firstMenuName ? 'menu-item menu-active-item' : 'menu-item'" @click="changeSubMenu(index)">
            <i :class="item.meta && item.meta.icon" />
            <span v-if="!isCollapse">{{ generateTitle(item.meta.title) }}</span>
          </div>
        </el-scrollbar>
      </div>

      <!-- 二级菜单 -->
      <div v-if="!isCollapse" class="sub-menu-box">
        <div class="fixed-sub-menu">{{ generateTitle(firstMenuName) }}</div>
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
import { generateTitle } from '@/utils/i18n'

export default {
  components: { SidebarItem, Logo },
  data() {
    return {
      loading: true,
      firstMenu: [],
      subMenu: [],
      firstMenuName: ''
    }
  },
  computed: {
    ...mapGetters([
      'permission_routes',
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
  created() {
    this.initRoutes()
  },
  methods: {
    initRoutes() {
      this.loading = true
      this.permission_routes.forEach((item) => {
        if (!item.hidden && item.meta) {
          this.firstMenu.push(item)
        }
      })
      this.subMenu = this.firstMenu[0].children
      this.firstMenuName = this.firstMenu[0].meta.title
      this.loading = false
    },

    changeSubMenu(index) {
      this.subMenu = this.firstMenu[index].children
      this.firstMenuName = this.firstMenu[index].meta.title
    },

    generateTitle
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

        i {
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
        i {
          margin-right: 0;
        }
      }
    }
  }
</style>
