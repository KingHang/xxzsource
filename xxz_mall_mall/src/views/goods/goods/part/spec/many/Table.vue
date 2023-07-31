<template>
  <div class="mt16">
    <el-form-item v-if="form.model.spec_many && form.model.spec_many.spec_list.length > 0" label="规格明细：">
      <div>
        批量设置
        <el-input v-model="batchData.pv" size="small" placeholder="PV" style="width: 160px;padding-left: 4px;" />
        <el-input v-model="batchData.product_price" size="small" placeholder="销售价" style="width: 160px;padding-left: 4px;" />
        <el-input v-model="batchData.line_price" size="small" placeholder="划线价" style="width: 160px;padding-left: 4px;" />
        <el-input v-model="batchData.stock_num" size="small" placeholder="库存" style="width: 160px;padding-left: 4px;" />
        <el-input v-model="batchData.product_weight" size="small" placeholder="重量" style="width: 160px;padding-left: 4px;" />
        <el-button size="small" @click="onSubmitBatchData">应用</el-button>
      </div>

      <!--多规格表格-->
      <div>
        <el-table size="mini" :data="form.model.spec_many.spec_list" :span-method="objectSpanMethod" border style="width: 100%; margin-top: 20px">
          <el-table-column v-for="(item, index) in form.model.spec_many.spec_attr" :key="item.group_name" :label="item.group_name">
            <template slot-scope="scope">
              {{ scope.row.rows[index].spec_value }}
            </template>
          </el-table-column>

          <el-table-column label="规格图片">
            <template slot-scope="scope">
              <img v-img-url="scope.row.spec_form.image_path" alt="" width="35" height="35" @click="chooseSpecImage(scope.$index)">
            </template>
          </el-table-column>

          <el-table-column label="产品编码">
            <template slot-scope="scope">
              <el-form-item label="" style="margin-bottom: 0;">
                <el-input v-model="scope.row.spec_form.product_no" size="small" prop="product_no" />
              </el-form-item>
            </template>
          </el-table-column>

          <el-table-column label="销售价">
            <template slot-scope="scope">
              <el-form-item
                label=""
                :rules="[{ required: true, message: ' ' }]"
                :prop="'model.spec_many.spec_list.' + scope.$index + '.spec_form.product_price'"
                style="margin-bottom: 0;"
              >
                <el-input v-model="scope.row.spec_form.product_price" size="small" prop="product_price" />
              </el-form-item>
            </template>
          </el-table-column>

          <el-table-column label="划线价">
            <template slot-scope="scope">
              <el-form-item label="" style="margin-bottom: 0;">
                <el-input v-model="scope.row.spec_form.line_price" size="small" prop="line_price" />
              </el-form-item>
            </template>
          </el-table-column>

          <el-table-column label="库存">
            <template slot-scope="scope">
              <el-form-item
                label=""
                :rules="[{ required: true, message: ' ' }]"
                :prop="'model.spec_many.spec_list.' + scope.$index + '.spec_form.stock_num'"
                style="margin-bottom: 0;"
              >
                <el-input v-model="scope.row.spec_form.stock_num" size="small" prop="stock_num" />
              </el-form-item>
            </template>
          </el-table-column>

          <el-table-column label="重量(kg)">
            <template slot-scope="scope">
              <el-form-item
                label=""
                :rules="[{ required: true, message: ' ' }]"
                :prop="'model.spec_many.spec_list.' + scope.$index + '.spec_form.product_weight'"
                style="margin-bottom: 0;"
              >
                <el-input v-model="scope.row.spec_form.product_weight" size="small" prop="product_weight" />
              </el-form-item>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-form-item>

    <!--上传图片组件-->
    <Upload v-if="isupload" :isupload="isupload" @returnImgs="returnImgsFunc">上传图片</Upload>
  </div>
</template>

<script>
import Upload from '@/components/file/Upload'

export default {
  components: {
    Upload
  },
  data() {
    return {
      /* 批量设置sku属性 */
      batchData: {
        pv: '',
        product_price: '',
        line_price: '',
        stock_num: '',
        product_weight: ''
      },
      /* 图片是否打开 */
      isupload: false,
      // 上传图片选择的下标
      spec_index: -1
    }
  },
  inject: ['form'],
  created() {},
  methods: {
    /** 表格跨行 **/
    objectSpanMethod({ row, column, rowIndex, columnIndex }) {
      const spec_attr = this.form.model.spec_many.spec_attr
      // 规格组合总数 (table行数)
      let totalRow = 1
      // 比如2个规格,只有第一列有多行
      if (columnIndex < spec_attr.length - 1) {
        const startRowIndex = columnIndex + 1
        const endRowIndex = spec_attr.length - 1
        for (let i = startRowIndex; i <= endRowIndex; i++) {
          totalRow *= spec_attr[i].spec_items.length
        }
        if (rowIndex % totalRow === 0) {
          return {
            rowspan: totalRow,
            colspan: 1
          }
        } else {
          return {
            rowspan: 0,
            colspan: 0
          }
        }
      }
    },
    /** 批量设置sku属性 **/
    onSubmitBatchData() {
      const self = this
      self.form.model.spec_many.spec_list.forEach(function(value) {
        for (const key in self.batchData) {
          if (Object.prototype.hasOwnProperty.call(self.batchData, key) && self.batchData[key]) {
            self.$set(value.spec_form, key, self.batchData[key])
          }
        }
      })
    },
    /** 选择图片 **/
    chooseSpecImage: function(spec_index) {
      this.isupload = true
      this.spec_index = spec_index
    },
    /** 返回图片 **/
    returnImgsFunc(e) {
      this.isupload = false
      this.$set(this.form.model.spec_many.spec_list[this.spec_index].spec_form, 'image_id', e[0]['file_id'])
      this.$set(this.form.model.spec_many.spec_list[this.spec_index].spec_form, 'image_path', e[0]['file_path'])
    }
  }
}
</script>
