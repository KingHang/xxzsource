<template>
  <div v-if="!loading" class="diy-editor-container">
    <!--顶部-->
    <div class="diy-editor-header">
      <div class="diy-new-header-left">
        <div class="diy-header-left-quit" @click="quitEdit">
          <img :src="quitPng" alt="">
        </div>
        <div class="diy-header-left-text" @click="quitEdit">退出编辑器</div>
        <div class="diy-header-left-line" />
        <div class="diy-header-left-text1">首页编辑</div>
      </div>
      <div class="diy-new-header-right">
        <el-button size="small" type="primary" :loading="loading" @click="submitData">保存</el-button>
      </div>
    </div>

    <!--中间-->
    <div class="diy-editor-main">
      <!--类别选择-->
      <div class="diy-new-menu">
        <Menu v-if="!loading" :default-data="defaultData" />
      </div>

      <!--手机diy容器-->
      <div class="diy-new-phone">
        <NewModel v-if="!loading" ref="model" :form="form" :default-data="defaultData" :diy-data="diyData" />
      </div>

      <!--参数设置-->
      <div class="diy-new-info">
        <Params v-if="!loading" :form="form" :default-data="defaultData" :diy-data="diyData" />
      </div>
    </div>
  </div>
</template>

<script>
import { deepClone } from '@/utils/base.js'
import HomeApi from '@/api/home.js'
import Menu from './diy/Menu'
import NewModel from './diy/NewModel'
import Params from './diy/Params'
import quitPng from '@/assets/img/diy/quit.png'

export default {
  components: {
    /* 组件类别 */
    Menu,
    /* 组件信息 */
    NewModel,
    /* 参数信息 */
    Params
  },
  data() {
    return {
      /* 是否正在加载 */
      loading: true,
      /* 默认数据 */
      defaultData: {},
      /* 组件数据列表 */
      diyData: {
        items: []
      },
      opts: {},
      /* 表单对象 */
      form: {
        umeditor: {},
        /* 当前选中 */
        curItem: {},
        /* 当前选中的元素（下标） */
        selectedIndex: -1
      },
      /* 图片 */
      quitPng: quitPng
    }
  },
  created() {
    /* 获取列表 */
    this.getData()
  },
  methods: {
    /** 获取列表 **/
    getData() {
      const self = this
      HomeApi.editHome({}, true).then(res => {
        self.defaultData = res.data.defaultData
        self.diyData = res.data.jsonData
        self.form.curItem = self.diyData.page
        self.opts = res.data.opts
        self.loading = false
      }).catch(() => {
        self.loading = false
      })
    },
    /** 新增Diy组件 **/
    onAddItem(key) {
      // 复制默认diy组件数据
      const item = deepClone(this.defaultData[key])
      let cur_index = 0
      if (this.form.selectedIndex < 0) {
        cur_index = 0
        this.diyData.items.unshift(item)
      } else {
        cur_index = this.form.selectedIndex + 1
        this.diyData.items.splice(cur_index, 0, item)
      }
      // 编辑当前选中的元素
      this.$refs.model.onEditer(cur_index)
    },
    /** 保存 **/
    submitData() {
      const self = this
      self.loading = true
      const params = self.diyData
      const page_id = self.page_id
      HomeApi.SavePage({
        params: JSON.stringify(params),
        page_id: page_id
      }, true).then(data => {
        self.$message({
          message: '恭喜你，修改成功',
          type: 'success'
        })
        self.getData()
        self.form.selectedIndex = -1
        self.loading = false
      }).catch(() => {
        self.loading = false
      })
    },
    /** 退出编辑器模式 **/
    quitEdit() {
      this.$router.push('/home/home')
    }
  }
}
</script>

<style lang="scss" scoped>
  .diy-editor-container {
    height: 100%;
    background: #FFFFFF;
    overflow-y: auto;

    /*顶部*/
    .diy-editor-header {
      width: 100%;
      height: 56px;
      background: #393649;
      padding: 0 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;

      .diy-new-header-left {
        display: flex;
        align-items: center;

        .diy-header-left-quit {
          width: 20px;
          height: 20px;
          cursor: pointer;

          img {
            width: 100%;
          }
        }

        .diy-header-left-text {
          font-size: 14px;
          font-family: Microsoft YaHei-Regular, Microsoft YaHei;
          font-weight: 400;
          color: #FFFFFF;
          line-height: 16px;
          margin-left: 10px;
          cursor: pointer;
        }

        .diy-header-left-line {
          width: 1px;
          height: 21px;
          background-color: rgba(255,255,255,0.1);
          margin: 0 20px;
        }

        .diy-header-left-text1 {
          font-size: 16px;
          font-family: Microsoft YaHei-Bold, Microsoft YaHei;
          font-weight: bold;
          color: #FFFFFF;
          line-height: 19px;
        }
      }
    }

    /*中间*/
    .diy-editor-main {
      display: flex;
      justify-content: space-between;

      .diy-new-menu {
        width: 330px;
        padding: 20px;
        flex-shrink: 0;
      }

      .diy-new-phone {
        width: 100%;
        background: #F6F8FB;
        padding: 50px 0;
        display: flex;
        justify-content: center;
      }

      .diy-new-info {
        width: 540px;
        padding: 20px;
        flex-shrink: 0;
      }
    }
  }
</style>
