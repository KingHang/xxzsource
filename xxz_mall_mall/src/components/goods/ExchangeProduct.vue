<template>
  <!--商品信息-->
  <div class="common-level-rail pl16 pr16">
    <el-table
      size="small"
      :data="orderproduct"
      style="width: 100%"
    >
      <el-table-column prop="product_name" label="商品名称">
        <template slot-scope="scope">
          <div class="table-user">
            <el-avatar shape="square" :size="50" :src="scope.row.file" />
            <div class="user-right">
              {{ scope.row.product_name }}
            </div>
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="product_no" label="商品编码">
        <template slot-scope="scope">
          {{ scope.row.product_no }}
        </template>
      </el-table-column>

      <el-table-column prop="total_price" label="兑换单价">
        <template slot-scope="scope">
          <div>{{ scope.row.total_time }} {{ token_name }}</div>
          <div>￥{{ scope.row.total_price }}</div>
        </template>
      </el-table-column>

      <el-table-column prop="total_num" label="数量">
        <template slot-scope="scope">
          {{ scope.row.total_num }}
        </template>
      </el-table-column>

      <el-table-column prop="product_price" label="原价">
        <template slot-scope="scope">
          {{ scope.row.product_price }}
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
export default {
  // eslint-disable-next-line vue/require-prop-types,vue/prop-name-casing
  props: ['orderproduct', 'token_name'],
  created() {
  },
  methods: {
  }
}
</script>

<style>
  .table-user {
    display: flex;
    align-items: center;
  }

  .table-user .user-right {
    margin-left: 10px;
  }
</style>
