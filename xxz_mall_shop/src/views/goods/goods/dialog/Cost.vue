<template>
  <div>
    <!--基本信息-->
    <el-form-item v-if="form.product.spec_type === 10" :label-width="formLabelWidth">
      <el-table :data="form.grade_list" style="width: 100%">
        <el-table-column prop="name" label="分销商等级" />

        <el-table-column label="拿货价">
          <template slot-scope="scope">
            <el-input v-model="form.model.agent_product[0].cost_price[scope.row.grade_id]" type="number">
              <template slot="append">元</template>
            </el-input>
          </template>
        </el-table-column>
      </el-table>
    </el-form-item>

    <el-form-item v-if="form.product.spec_type === 20" :label-width="formLabelWidth">
      <el-table :data="form.model.agent_product" style="width: 100%">
        <el-table-column prop="spec_name" label="商品规格" />

        <el-table-column v-for="(item, index) in form.grade_list" :key="index" :label="item.name">
          <template slot-scope="scope">
            <el-input v-model="scope.row.cost_price[item.grade_id]" type="number" class="max-w460" />
          </template>
        </el-table-column>
      </el-table>
    </el-form-item>
  </div>
</template>

<script>
export default {
  data() {
    return {
      formLabelWidth: '120px'
    }
  },
  inject: ['form'],
  created() {},
  methods: {
  }
}
</script>

<style>
.edit_container {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  line-height: 20px;
  color: #2c3e50;
}

.ql-editor {
  height: 400px;
}

.draggable-list {
  display: flex;
  justify-content: flex-start;
  flex-wrap: wrap;
}
.draggable-list .wrapper > span {
  display: flex;
  justify-content: flex-start;
  flex-wrap: wrap;
}

.draggable-list .item {
  position: relative;
  width: 110px;
  height: 110px;
  margin-top: 10px;
  margin-right: 10px;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #dddddd;
}

.draggable-list .delete-btn {
  position: absolute;
  top: 0;
  right: 0;
  width: 16px;
  height: 16px;
  background: red;
  line-height: 16px;
  font-size: 16px;
  color: #ffffff;
  display: none;
}

.draggable-list .item:hover .delete-btn {
  display: block;
}

.draggable-list .item img {
  position: absolute;
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
  max-height: 100%;
  max-width: 100%;
}

.draggable-list .img-select {
  display: flex;
  justify-content: center;
  align-items: center;
  border: 1px dashed #dddddd;
  font-size: 30px;
}

.draggable-list .img-select i {
  color: #409eff;
}
</style>
