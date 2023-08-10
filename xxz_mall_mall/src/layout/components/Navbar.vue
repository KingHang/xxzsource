<template>
  <div class="navbar">
    <hamburger id="hamburger-container" :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" />

    <breadcrumb id="breadcrumb-container" class="breadcrumb-container" />

    <div class="right-menu">
      <template v-if="device!=='mobile'">
        <search id="header-search" class="right-menu-item" />

        <error-log class="errLog-container right-menu-item hover-effect" />

        <screenfull id="screenfull" class="right-menu-item hover-effect" />

        <el-tooltip :content="$t('navbar.size')" effect="dark" placement="bottom">
          <size-select id="size-select" class="right-menu-item hover-effect" />
        </el-tooltip>

        <div class="right-menu-version">当前版本：{{ baseInfo.version }}</div>

        <div class="right-menu-username">{{ baseInfo.user.user_name }}</div>
      </template>

      <el-dropdown class="avatar-container right-menu-item hover-effect" trigger="click">
        <div class="avatar-wrapper">
          <img :src="baseInfo.shop_logo" class="user-avatar">
          <i class="el-icon-caret-bottom" />
        </div>
        <el-dropdown-menu slot="dropdown">
          <router-link to="/">
            <el-dropdown-item>
              {{ $t('navbar.dashboard') }}
            </el-dropdown-item>
          </router-link>
          <el-dropdown-item @click.native="passwordFunc">
            <span style="display:block;">{{ $t('navbar.updatePassword') }}</span>
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>

      <div class="login-out" @click="login_out">
        <svg class="icon" aria-hidden="true">
          <use xlink:href="#icon-tuichudenglu" />
        </svg>
      </div>
    </div>

    <!--修改密码-->
    <UpdatePassword v-if="is_password" @close="closeFunc" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Breadcrumb from '@/components/Breadcrumb'
import Hamburger from '@/components/Hamburger'
import ErrorLog from '@/components/ErrorLog'
import Screenfull from '@/components/Screenfull'
import SizeSelect from '@/components/SizeSelect'
import Search from '@/components/HeaderSearch'
import UpdatePassword from './Part/UpdatePassword.vue'

export default {
  components: {
    Breadcrumb,
    Hamburger,
    ErrorLog,
    Screenfull,
    SizeSelect,
    Search,
    UpdatePassword
  },
  data() {
    return {
      is_password: false
    }
  },
  inject: ['baseInfo'],
  computed: {
    ...mapGetters([
      'name',
      'sidebar',
      'avatar',
      'device'
    ])
  },
  methods: {
    /** 修改密码 **/
    passwordFunc() {
      this.is_password = true
    },
    /** 关闭修改密码 **/
    closeFunc() {
      this.is_password = false
    },
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar')
    },
    /** 退出登录 **/
    login_out() {
      this.$confirm('此操作将退出登录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.logout()
      }).catch(() => {
      })
    },
    async logout() {
      await this.$store.dispatch('user/logout')
      this.$router.push(`/login?redirect=${this.$route.fullPath}`)
    }
  }
}
</script>

<style lang="scss" scoped>
.navbar {
  height: 50px;
  overflow: hidden;
  position: relative;
  background: #fff;
  box-shadow: 0 1px 4px rgba(0,21,41,.08);

  .hamburger-container {
    line-height: 46px;
    height: 100%;
    float: left;
    cursor: pointer;
    transition: background .3s;
    -webkit-tap-highlight-color:transparent;

    &:hover {
      background: rgba(0, 0, 0, .025)
    }
  }

  .breadcrumb-container {
    float: left;
  }

  .errLog-container {
    display: inline-block;
    vertical-align: top;
  }

  .right-menu {
    float: right;
    height: 100%;
    line-height: 50px;

    &:focus {
      outline: none;
    }

    .right-menu-item {
      display: inline-block;
      padding: 0 8px;
      height: 100%;
      font-size: 18px;
      color: #5a5e66;
      vertical-align: text-bottom;

      &.hover-effect {
        cursor: pointer;
        transition: background .3s;

        &:hover {
          background: rgba(0, 0, 0, .025)
        }
      }
    }

    .right-menu-version {
      display: inline-block;
      padding: 0 15px;
      height: auto;
      line-height: 36px;
      font-size: 14px;
      color: #828282;
      background: #F3F3F3;
      border-radius: 24px;
      vertical-align: top;
      margin: 7px 10px;
    }

    .right-menu-username {
      display: inline-block;
      vertical-align: text-bottom;
      margin: 0 10px;
      font-size: 16px;
    }

    .avatar-container {
      margin-right: 30px;

      .avatar-wrapper {
        margin-top: 5px;
        position: relative;

        .user-avatar {
          cursor: pointer;
          width: 40px;
          height: 40px;
          border-radius: 40px;
        }

        .el-icon-caret-bottom {
          cursor: pointer;
          position: absolute;
          right: -20px;
          top: 25px;
          font-size: 12px;
        }
      }
    }

    .login-out {
      display: inline-block;
      cursor: pointer;
      height: 100%;
      vertical-align: text-bottom;
      border-left: solid 1px #F1F1F1;
      width: 60px;
      text-align: center;

      .icon {
        width: 30px;
        height: 30px;
        fill: #EB5757;
        margin-top: 10px;
      }
    }
  }
}
</style>
