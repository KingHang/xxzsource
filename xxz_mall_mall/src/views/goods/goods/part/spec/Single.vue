<template>
  <div>
    <el-form-item label="产品编码：" :rules="[{ required: true, message: '请填写产品编码' }]" prop="model.sku.product_no">
      <el-input v-model="form.model.sku.product_no" class="max-w460" />
    </el-form-item>

    <el-form-item label="产品价格：" width="80" :rules="[{ required: true, message: '请填写产品价格' }]" prop="model.sku.product_price">
      <el-input v-model="form.model.sku.product_price" type="number" class="max-w460" />
    </el-form-item>

    <el-form-item label="产品划线价：" :rules="[{ required: true, message: '请填写产品划线价' }]" prop="model.sku.line_price">
      <el-input v-model="form.model.sku.line_price" type="number" class="max-w460" />
    </el-form-item>

    <el-form-item label="库存数量：" :rules="[{ required: true, message: '请填写库存数量' }]" prop="model.sku.stock_num">
      <el-input v-model="form.model.sku.stock_num" type="number" class="max-w460" />
    </el-form-item>

    <el-form-item label="商品重量(Kg)：" :rules="[{ required: true, message: '请填写商品重量' }]" prop="model.sku.product_weight">
      <el-input v-model="form.model.sku.product_weight" type="number" class="max-w460" />
    </el-form-item>
  </div>
</template>

<script>
export default {
  inject: ['form']
}
</script>
