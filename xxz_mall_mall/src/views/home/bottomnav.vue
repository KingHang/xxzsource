<template>
  <div class="d-s-s">
    <!--分类不同样式展示-->
    <div class="model-container">
      <el-table v-loading="loading" size="small" :data="menus" border style="width: 100%">
        <el-table-column prop="text" label="名称" />

        <el-table-column label="选中图标">
          <template slot-scope="scope">
            <img :src="scope.row.selectedIconPath" width="36" height="36">
          </template>
        </el-table-column>

        <el-table-column label="未选中图标">
          <template slot-scope="scope">
            <img :src="scope.row.iconPath" width="36" height="36">
          </template>
        </el-table-column>

        <el-table-column fixed="right" label="操作" width="90">
          <template slot-scope="scope">
            <el-button v-auth="'/home/bottom/Edit'" type="text" size="small" @click="editClick(scope.row)">编辑</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>

    <!--图片展示参数-->
    <div class="param-container flex-1">
      <div class="common-form"><span>其它设置</span></div>

      <el-form size="small" label-width="100px">
        <el-form-item label="选中颜色">
          <div class="d-s-c">
            <el-color-picker v-model="form.color" />
          </div>
          <p class="gray9">底部导航选中时文字颜色</p>
        </el-form-item>

        <el-form-item label="未选中颜色">
          <div class="d-s-c">
            <el-color-picker v-model="form.no_color" />
          </div>
          <p class="gray9">底部导航未选中文字颜色</p>
        </el-form-item>

        <el-form-item label="中间菜单">
          <el-radio-group v-model="form.menu_type">
            <el-radio :label="1">店铺</el-radio>
            <el-radio :label="2">订单</el-radio>
          </el-radio-group>
          <p class="gray9">底部中间菜单显示内容</p>
        </el-form-item>

        <el-form-item>
          <el-button size="small" type="primary" :loading="save_loading" @click="submit()">保存</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!--修改-->
    <Edit v-if="open_edit" :open_edit="open_edit" :editform="currentModel" @closeDialog="closeDialogFunc($event)" />
  </div>
</template>

<script>
import HomeApi from '@/api/home.js'
import Edit from './bottom/Edit.vue'

export default {
  components: {
    Edit
  },
  data() {
    return {
      loading: false,
      save_loading: false,
      /* 表单数据对象 */
      form: {
        color: '#E2231A',
        no_color: '#999999',
        menu_type: 1
      },
      menus: [],
      /* 是否打开编辑弹窗 */
      open_edit: false,
      /* 当前编辑的对象 */
      currentModel: {}
    }
  },
  created() {
    this.getData()
  },
  methods: {
    /** 获取数据 **/
    getData() {
      const self = this
      self.loading = true
      HomeApi.getbottomNav({}, true).then(res => {
        self.loading = false
        self.menus = res.data.data.menus
        self.form.color = res.data.data.color
        self.form.no_color = res.data.data.no_color
        self.form.menu_type = parseInt(res.data.data.menu_type)
      }).catch(() => {
        self.loading = false
      })
    },
    /** 打开编辑 **/
    editClick(item) {
      this.currentModel = item
      this.open_edit = true
    },
    /** 关闭弹窗 **/
    closeDialogFunc(e) {
      this.open_edit = e.openDialog
      if (e.type === 'success') {
        this.getData()
      }
    },
    /** 提交 **/
    submit() {
      const self = this
      self.save_loading = true
      const params = self.form
      params.type = 'color'
      HomeApi.postbottomNav(params, true)
        .then(data => {
          self.save_loading = false
          self.$message({
            message: '恭喜你，修改成功',
            type: 'success'
          })
          self.getData()
        })
        .catch(() => {
          self.save_loading = false
        })
    }
  }
}
</script>

<style scoped="scoped">
  .model-container {
    width: 700px;
    height: calc(100vh - 150px);
    margin-right: 30px;
  }

  .param-container {
    padding: 20px;
    height: calc(100vh - 150px);
    border: 1px solid #cccccc;
  }
</style>
