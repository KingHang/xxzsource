<template>
  <el-dialog title="修改收货地址" :visible.sync="dialogVisible" :close-on-click-modal="false" :close-on-press-escape="false" width="900px">
    <el-input v-model="addressData.name" class="mb16" size="small" placeholder="请输入收货人" />

    <el-input v-model="addressData.phone" class="mb16" size="small" placeholder="请输入收货电话" />

    <el-select v-model="addressData.province_id" class="mb16" placeholder="省" @change="initCity">
      <el-option v-for="(item,index) in areaList" :key="index" :label="item.name" :value="item.id" />
    </el-select>

    <el-select v-if="!loading && addressData.province_id !== ''" v-model="addressData.city_id" placeholder="市" @change="initRegion">
      <el-option v-for="(item1,index1) in areaList[addressData.province_id]['city']" :key="index1" :label="item1.name" :value="item1.id" />
    </el-select>

    <el-select v-if="!loading && addressData.city_id !== ''" v-model="addressData.region_id" placeholder="区">
      <el-option v-for="(item2,index2) in areaList[addressData.province_id]['city'][addressData.city_id]['region']" :key="index2" :label="item2.name" :value="item2.id" />
    </el-select>

    <el-input v-model="addressData.detail" class="mb16" size="small" placeholder="请输入详细地址" />

    <div slot="footer" class="dialog-footer">
      <el-button size="small" @click="dialogFormVisible(false)">取 消</el-button>
      <el-button size="small" type="primary" @click="dialogFormVisible(true)">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
import DataApi from '@/api/data.js'

export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['isChange', 'addressData'],
  data() {
    return {
      /* 是否显示 */
      dialogVisible: false,
      loading: true,
      /* 结果类别 */
      type: 'error',
      /* 传出去的参数 */
      params: null,
      reverse: false,
      order_id: 0,
      activities: [],
      /* 省市区 */
      areaList: [],
      address: {
        name: '',
        phone: '',
        region: {
          province: '',
          province_id: '',
          city: '',
          city_id: '',
          region: '',
          region_id: '',
          detail: ''
        }
      }
    }
  },
  watch: {
    isChange: function(n, o) {
      if (n !== o) {
        this.dialogVisible = n
        this.type = 'error'
      }
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getData() {
      const self = this
      DataApi.getRegion({}, true)
        .then(res => {
          self.areaList = res.data.regionData
          self.loading = false
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 初始化城市id **/
    initCity() {
      this.addressData.city_id = ''
    },
    /** 初始化区id **/
    initRegion() {
      this.addressData.region_id = ''
    },
    /** 关闭弹窗 **/
    dialogFormVisible(flag) {
      if (flag) {
        this.$emit('closeDialog', {
          type: this.type,
          openDialog: false,
          params: this.addressData
        })
      } else {
        this.$emit('closeDialog', false)
      }
    }
  }
}
</script>
