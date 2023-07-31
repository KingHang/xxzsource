<template>
  <div class="spec-many-type">
    <!--规格属性-->
    <div class="spec-wrap">
      <div v-for="(attr, index) in form.model.spec_many.spec_attr" :key="attr.group_name" class="mb16 min-spc">
        <div class="spec-hd">
          <div class="input-box">{{ attr.group_name }}</div>
          <a href="javascript:void(0);" @click="onDeleteGroup(index)">
            <i class="el-icon-delete" />
          </a>
        </div>
        <div class="spec-bd">
          <div v-for="(items, i) in attr.spec_items" :key="items.spec_value" class="item">
            <el-tag closable @close="onDeleteValue(index, i)">{{ items.spec_value }}</el-tag>
          </div>
          <div class="item">
            <el-input v-model="attr.tempValue" size="small" style="width: 160px;" />
          </div>
          <div class="item">
            <el-button size="small" :loading="attr.loading" @click="onSubmitAddValue(attr)">添加</el-button>
          </div>
        </div>
      </div>

      <!--添加规格-->
      <div v-if="!form.isSpecLocked">
        <el-button v-show="showAddGroupBtn" size="small" class="el-icon-circle-plus" @click="onToggleAddGroupForm">添加规格</el-button>
      </div>

      <!--规格列表-->
      <div v-show="!showAddGroupBtn" class="add-spec mb16">
        <div class="from-box">
          <div class="item">
            <span class="key">规格名：</span>
            <el-input v-model="addGroupFrom.specName" size="small" placeholder="请输入规格名称" />
          </div>
          <div class="item">
            <span class="key">规格值：</span>
            <el-input v-model="addGroupFrom.specValue" size="small" placeholder="请输入规格值" />
          </div>
          <el-button type="primary" size="small" :loading="groupLoading" plain @click="onSubmitAddGroup">确定</el-button>
          <el-button size="small" @click="onToggleAddGroupForm">取消</el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import GoodsApi from '@/api/goods.js'

export default {
  data() {
    return {
      /* 示添加规格组按钮 */
      showAddGroupBtn: true,
      /* 显示添加规格组表单 */
      showAddGroupForm: false,
      /* 新增规格组值 */
      addGroupFrom: {
        specName: '',
        specValue: ''
      },
      groupLoading: false
    }
  },
  inject: ['form'],
  created() {
    /* 获取列表 */
    if (this.form.model.spec_many && this.form.model.spec_many.spec_list.length > 0) {
      this.buildSkulist()
    }
  },
  methods: {
    /** 显示/隐藏添加规则组 **/
    onToggleAddGroupForm() {
      this.showAddGroupBtn = !this.showAddGroupBtn
      this.showAddGroupForm = !this.showAddGroupForm
    },
    /** 表单提交：新增规格组 **/
    onSubmitAddGroup() {
      const self = this
      if (self.addGroupFrom.specName === '' || self.addGroupFrom.specValue === '') {
        self.$message('请填写规则名或规则值')
        return false
      }
      // 添加到数据库
      self.groupLoading = true
      const Params = {
        spec_name: self.addGroupFrom.specName,
        spec_value: self.addGroupFrom.specValue
      }
      GoodsApi.addSpec(Params, true)
        .then(res => {
          self.groupLoading = false
          // 记录规格数据
          self.form.model.spec_many.spec_attr.push({
            group_id: res.data['spec_id'],
            group_name: self.addGroupFrom.specName,
            spec_items: [
              {
                item_id: res.data['spec_value_id'],
                spec_value: self.addGroupFrom.specValue
              }
            ],
            tempValue: '',
            loading: false
          })
          // 清空输入内容
          self.addGroupFrom.specName = ''
          self.addGroupFrom.specValue = ''
          // 隐藏添加规则组
          self.onToggleAddGroupForm()
          // 构建规格组合列表
          self.buildSkulist()
        })
        .catch(() => {
          self.groupLoading = false
        })
    },
    /** 新增规格值 **/
    onSubmitAddValue(specAttr) {
      const self = this
      if (!Object.prototype.hasOwnProperty.call(specAttr, 'tempValue') || specAttr.tempValue === '') {
        self.$message('规格值不能为空')
        return false
      }
      // 添加到数据库
      specAttr.loading = true
      const Params = {
        spec_id: specAttr.group_id,
        spec_value: specAttr.tempValue
      }
      GoodsApi.addSpecValue(Params, true)
        .then(data => {
          specAttr.loading = false
          // 记录规格数据
          specAttr.spec_items.push({
            item_id: data.data['spec_value_id'],
            spec_value: specAttr.tempValue
          })
          // 清空输入内容
          specAttr.tempValue = ''
          // 构建规格组合列表
          self.buildSkulist()
        })
        .catch(() => {
          specAttr.loading = false
        })
    },
    /** 构建规格组合列表 **/
    buildSkulist() {
      const self = this
      const spec_attr = self.form.model.spec_many.spec_attr
      const specArr = []
      for (let i = 0; i < spec_attr.length; i++) {
        specArr.push(spec_attr[i].spec_items)
      }
      const specListTemp = self.calcDescartes(specArr)
      const specList = []
      for (let i = 0; i < specListTemp.length; i++) {
        let rows = []
        const specSkuIdAttr = []
        if (!Array.isArray(specListTemp[i])) {
          rows.push(specListTemp[i])
        } else {
          rows = specListTemp[i]
        }
        for (let j = 0; j < rows.length; j++) {
          specSkuIdAttr.push(rows[j].item_id)
        }
        specList.push({
          goods_sku_id: 0,
          spec_sku_id: specSkuIdAttr.join('_'),
          rows: rows,
          spec_form: {}
        })
      }

      // 合并旧sku数据
      if (self.form.model.spec_many.spec_list.length > 0 && specList.length > 0) {
        for (let i = 0; i < specList.length; i++) {
          const overlap = self.form.model.spec_many.spec_list.filter(function(val) {
            return val.spec_sku_id === specList[i].spec_sku_id
          })
          if (overlap.length > 0) {
            specList[i].spec_form = overlap[0].spec_form
            specList[i].goods_sku_id = overlap[0].goods_sku_id
          }
        }
      }

      self.form.model.spec_many.spec_list = specList
    },
    /** 规格组合 **/
    calcDescartes(array) {
      if (array.length < 2) return array[0] || []
      return [].reduce.call(array, function(col, set) {
        const res = []
        col.forEach(function(c) {
          set.forEach(function(s) {
            const t = [].concat(Array.isArray(c) ? c : [c])
            t.push(s)
            res.push(t)
          })
        })
        return res
      })
    },
    /** 删除规格组事件 **/
    onDeleteGroup(index) {
      const self = this
      self.$confirm('删除后不可恢复，确认删除该记录吗?', '提示', {
        type: 'warning'
      }).then(() => {
        // 删除指定规则组
        self.form.model.spec_many.spec_attr.splice(index, 1)
        // 构建规格组合列表
        self.buildSkulist()
      })
    },
    /** 删除规格值值事件 **/
    onDeleteValue: function(index, itemIndex) {
      const self = this
      if (self.form.isSpecLocked) {
        self.$message({
          message: '本商品正在参加活动，不能删除规格！',
          type: 'warning'
        })
        return
      }
      self.$confirm('删除后不可恢复，确认删除该记录吗?', '提示', {
        type: 'warning'
      }).then(() => {
        // 删除指定规则组
        self.form.model.spec_many.spec_attr[index].spec_items.splice(itemIndex, 1)
        // 构建规格组合列表
        self.buildSkulist()
      })
    }
  }
}
</script>

<style scoped="scoped">
.spec-many-type {
  margin-left: 180px;
  margin-top: 16px;
  padding: 20px;
  border: 1px solid #e5ecf4;
  background: #f6f9fc;
}
.spec-wrap .spec-hd {
  padding: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #fff;
  font-weight: bold;
}
.spec-wrap .spec-hd .el-icon-delete-solid {
  font-size: 16px;
  color: #999999;
}
.spec-wrap .min-spc {
  border: 1px solid #dfecf8;
}
.spec-wrap .spec-bd {
  padding: 5px;
  display: flex;
  justify-content: flex-start;
  flex-wrap: wrap;
  border-top: 1px solid #dfecf8;
  background: #ffffff;
}
.spec-wrap .spec-bd .el-tag {
  color: #333333;
}
.spec-wrap .spec-bd .item {
  position: relative;
  padding: 5px;
}
.spec-wrap .spec-bd .item input {
  padding-right: 30px;
}
.spec-wrap .spec-hd a,
.spec-wrap .spec-hd .svg-icon,
.spec-wrap .spec-bd .item .svg-icon {
  display: block;
  width: 16px;
  height: 16px;
}
.spec-wrap .spec-bd .item a {
  position: absolute;
  top: 6px;
  right: 5px;
  width: 30px;
  height: 30px;
  display: flex;
  justify-content: center;
  align-items: center;
}
.add-spec .from-box {
  display: flex;
  justify-content: flex-start;
}
.add-spec .item {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  width: 200px;
  margin-right: 20px;
}
.add-spec .item .key {
  display: block;
  white-space: nowrap;
}
</style>
