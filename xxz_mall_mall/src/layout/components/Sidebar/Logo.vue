<template>
  <div class="sidebar-logo-container" :class="{'collapse':collapse}">
    <transition name="sidebarLogoFade">
      <router-link v-if="collapse" key="collapse" class="sidebar-logo-link" to="/">
        <img v-if="baseInfo.shop_logo" :src="baseInfo.shop_logo" class="sidebar-logo">
        <h1 v-else class="sidebar-title">{{ baseInfo.shop_name }}</h1>
      </router-link>
      <router-link v-else key="expand" class="sidebar-logo-link" to="/">
        <img v-if="baseInfo.shop_logo" :src="baseInfo.shop_logo" class="sidebar-logo">
        <div class="sidebar-title">{{ baseInfo.shop_name }}</div>
      </router-link>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'SidebarLogo',
  props: {
    collapse: {
      type: Boolean,
      required: true
    }
  },
  inject: ['baseInfo']
}
</script>

<style lang="scss" scoped>
.sidebarLogoFade-enter-active {
  transition: opacity 1.5s;
}

.sidebarLogoFade-enter,
.sidebarLogoFade-leave-to {
  opacity: 0;
}

.sidebar-logo-container {
  position: relative;
  width: 100%;
  height: 100px;
  text-align: center;
  overflow: hidden;

  & .sidebar-logo-link {
    height: 100%;
    width: 100%;

    & .sidebar-logo {
      display: inline-block;
      width: 36px;
      height: 36px;
      border-radius: 36px;
      margin: 7px 0;
    }

    & .sidebar-title {
      color: #3a8ee6;
      font-weight: 600;
      line-height: 40px;
      font-size: 14px;
      font-family: Avenir, Helvetica Neue, Arial, Helvetica, sans-serif;
    }
  }

  &.collapse {
    .sidebar-logo {
      margin-right: 0;
    }
  }
}
</style>
