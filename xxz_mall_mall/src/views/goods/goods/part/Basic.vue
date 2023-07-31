<template>
  <div class="basic-setting-content pl16 pr16">
    <!--基本信息-->
    <div class="common-form">基本信息</div>

    <el-form-item label="商品名称：" :rules="[{ required: true, message: '请填写商品名称' }]" prop="model.product_name">
      <el-input v-model="form.model.product_name" class="max-w460" />
    </el-form-item>

    <el-form-item label="所属分类：" :rules="[{ required: true, message: '你选择商品分类' }]" prop="model.category_id">
      <el-select v-model="form.model.category_id">
        <template v-for="cat in form.category">
          <el-option :key="cat.category_id" :value="cat.category_id" :label="cat.name" />
          <template v-if="cat.child !== undefined">
            <template v-for="two in cat.child">
              <el-option :key="two.category_id" :value="two.category_id" :label="two.name" style="padding-left: 30px;" />
              <template v-if="two.child !== undefined">
                <template v-for="three in two.child">
                  <el-option :key="three.category_id" :value="three.category_id" :label="three.name" style="padding-left: 60px;" />
                </template>
              </template>
            </template>
          </template>
        </template>
      </el-select>
    </el-form-item>

    <el-form-item label="商品参数" prop="params_value">
      <el-table size="mini" border style="width: 50%;" :data="params_value">
        <el-table-column label="参数名">
          <template slot-scope="scope">
            <el-form-item label="" style="margin-bottom: 0;">{{ scope.row.params_name }}</el-form-item>
          </template>
        </el-table-column>
        <el-table-column label="参数值">
          <template slot-scope="scope">
            <el-form-item label="" style="margin-bottom: 0;">
              <el-input v-model="scope.row.params_value" class="max-w460" />
            </el-form-item>
          </template>
        </el-table-column>
      </el-table>
    </el-form-item>

    <el-form-item label="销售状态：">
      <el-radio-group v-model="form.model.product_status">
        <el-radio :label="10">立即上架</el-radio>
        <el-radio :label="20">放入仓库</el-radio>
      </el-radio-group>
    </el-form-item>

    <el-form-item label="商品图片：" :rules="[{ required: true, message: '请上传商品图片' }]" prop="model.image">
      <div class="draggable-list">
        <draggable v-model="form.model.image" class="wrapper">
          <transition-group>
            <div v-for="(item, index) in form.model.image" :key="item.file_path" class="item">
              <img v-img-url="item.file_path">
              <a href="javascript:void(0);" class="delete-btn" @click.stop="deleteImg(index)">
                <i class="el-icon-close" />
              </a>
            </div>
          </transition-group>
        </draggable>
        <div class="item img-select" @click="openProductUpload('image', 'image')">
          <i class="el-icon-plus" />
        </div>
      </div>
    </el-form-item>

    <el-form-item label="商品视频：">
      <el-row>
        <div class="draggable-list">
          <div v-if="form.model.video_id === 0" class="item img-select" @click="openProductUpload('video', 'video')">
            <i class="el-icon-plus" />
          </div>
          <div v-if="form.model.video_id !== 0">
            <video width="150" height="150" :src="form.model.video.file_path" :autoplay="false" controls>
              您的浏览器不支持 video 标签
            </video>
            <div>
              <el-button icon="el-icon-picture-outline" @click="delVideo">删除视频</el-button>
            </div>
          </div>
        </div>
      </el-row>
    </el-form-item>

    <el-form-item label="视频封面：">
      <el-row>
        <div class="draggable-list">
          <div v-if="form.model.poster_id === 0" class="item img-select" @click="openProductUpload('image', 'poster')">
            <i class="el-icon-plus" />
          </div>
          <div v-if="form.model.poster_id !== 0" class="item" @click="openProductUpload('image', 'poster')">
            <img :src="form.model.poster.file_path" width="100" height="100">
          </div>
        </div>
      </el-row>
    </el-form-item>

    <el-form-item label="商品卖点：">
      <el-input v-model="form.model.selling_point" type="textarea" class="max-w460" />
    </el-form-item>

    <!--其他设置-->
    <div class="common-form">其他设置</div>

    <el-form-item label="商品属性：">
      <el-radio-group v-model="form.model.product_type">
        <el-radio :label="1">实物商品</el-radio>
        <el-radio :label="2">虚拟商品(无需发货)</el-radio>
        <el-radio :label="3">计次商品(无需发货)</el-radio>
        <el-radio :label="4">旅游商品(无需发货)</el-radio>
      </el-radio-group>
    </el-form-item>

    <el-form-item v-if="form.model.product_type === 1" prop="model.delivery_id" label="运费模板：">
      <el-radio-group v-model="form.model.is_delivery_free" :disabled="!form.model.can_edit_delivery">
        <el-radio :label="0">包邮</el-radio>
        <el-radio :label="1">运费模板</el-radio>
      </el-radio-group>
      <el-select v-if="form.model.is_delivery_free === 1" v-model="form.model.delivery_id" :disabled="!form.model.can_edit_delivery">
        <el-option v-for="item in form.delivery" :key="item.delivery_id" :value="item.delivery_id" :label="item.name" />
      </el-select>
    </el-form-item>

    <el-form-item label="初始销量：">
      <el-input v-model="form.model.sales_initial" type="number" min="0" class="max-w460" />
    </el-form-item>

    <el-form-item label="商品排序：" :rules="[{ required: true, message: ' ' }]" prop="model.product_sort">
      <el-input v-model="form.model.product_sort" type="number" min="0" class="max-w460" />
    </el-form-item>

    <el-form-item label="限购数量：" :rules="[{ required: true, message: ' ' }]" prop="model.limit_num">
      <el-input v-model="form.model.limit_num" type="number" min="0" class="max-w460" />
      <div class="gray9">每个会员购买的最大数量，0为不限购</div>
    </el-form-item>

    <el-form-item v-if="form.model.product_type === 2" label="发货类型：">
      <el-radio-group v-model="form.model.virtual_auto">
        <el-radio :label="1">自动</el-radio>
        <el-radio :label="0">手动</el-radio>
      </el-radio-group>
    </el-form-item>

    <el-form-item v-if="form.model.product_type === 2" label="虚拟内容：" :rules="[{ required: true, message: '请填写虚拟内容' }]" prop="model.virtual_content">
      <el-input v-model="form.model.virtual_content" type="text" class="max-w460" />
      <div class="gray9">虚拟物品内容</div>
    </el-form-item>

    <el-form-item label="会员等级限制：">
      <el-select v-model="form.model.grade_ids" multiple placeholder="请选择" style="width: 460px;">
        <el-option
          v-for="item in form.gradeList"
          :key="item.grade_id"
          :label="item.name"
          :value="item.grade_id"
        />
      </el-select>
      <div class="gray9">仅设置的等级会员可购买，不设置则都可以购买</div>
    </el-form-item>

    <!--自提设置-->
    <div v-if="form.model.product_type === 1" class="common-form">自提设置</div>

    <el-form-item v-if="form.model.product_type === 1" label="是否支持自提：">
      <el-radio-group v-model="form.model.is_selfmention">
        <el-radio :label="1">支持</el-radio>
        <el-radio :label="0">不支持</el-radio>
      </el-radio-group>
    </el-form-item>

    <!--计次商品设置-->
    <div v-if="form.model.product_type === 3 || form.model.product_type === 4" class="common-form">
      <span v-if="form.model.product_type === 3">计次商品设置</span>
      <span v-else>旅游商品设置</span>
    </div>

    <el-form-item v-if="form.model.product_type === 4" label="绑定权益">
      <el-radio-group v-model="form.model.benefit_id">
        <el-radio v-for="(item,index) in form.benefit" :key="index" :label="item.benefit_id">
          {{ item.benefit_name }}
        </el-radio>
      </el-radio-group>
    </el-form-item>

    <el-form-item v-if="form.model.product_type === 3" label="包含核销次数：" prop="model.verify_num">
      <el-input v-model="form.model.verify_num" type="number" min="0" class="max-w460" />
      <div class="gray9">单个商品核销次数,不填或填写0及以下为默认不限次数</div>
    </el-form-item>

    <el-form-item v-if="form.model.product_type === 3 || form.model.product_type === 4" label="有效期：">
      <el-radio-group v-model="form.model.verify_limit_type">
        <el-form-item>
          <el-radio :label="0">
            永久有效
          </el-radio>
        </el-form-item>
        <el-form-item>
          <el-radio :label="1">
            <el-date-picker v-model="form.model.verify_enddate" value-format="yyyy-MM-dd" type="date" placeholder="核销有效时间" />前有效
          </el-radio>
        </el-form-item>
        <el-form-item>
          <el-radio :label="2">
            购买后<el-input v-model="form.model.verify_days" type="number" min="1" class="max-w460" />天内有效
          </el-radio>
        </el-form-item>
        <el-form-item>
          <el-radio :label="3">
            首次使用后<el-input v-model="form.model.verify_days" type="number" min="1" class="max-w460" />天内有效
          </el-radio>
        </el-form-item>
      </el-radio-group>
    </el-form-item>

    <el-form-item v-if="!!showStore" :label="label1">
      <el-radio-group v-model="isOnShelfStore" @change="showDescText">
        <el-radio :label="0">全部门店</el-radio>
        <el-radio :label="1">{{ label2 }}</el-radio>
      </el-radio-group>
      <div class="mg-top10 gray9">{{ descText }}</div>
      <div v-if="isOnShelfStore === 1" class="mg-top10">
        <el-button size="small" type="primary" @click="selectStore">{{ $t('page.selectStore') }}</el-button>
      </div>
      <el-table v-if="isOnShelfStore === 1 && tempStoreList.length > 0" size="mini" :data="tempStoreList" border style="width: 60%; margin-top: 20px">
        <el-table-column label="门店ID" width="100">
          <template slot-scope="scope">
            {{ scope.row.store_id }}
          </template>
        </el-table-column>

        <el-table-column label="商户名称">
          <template slot-scope="scope">
            {{ scope.row.supplier ? scope.row.supplier.name : '' }}
          </template>
        </el-table-column>

        <el-table-column label="门店名称/地址">
          <template slot-scope="scope">
            <div>{{ scope.row.store_name }}</div>
            <div>{{ scope.row.detail_address }}</div>
          </template>
        </el-table-column>

        <el-table-column label="操作" align="center" width="80">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="deleteStore(scope.$index)">{{ $t('page.del') }}</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-form-item>

    <!--商品图片组件-->
    <Upload v-if="isProductUpload" :config="config" :isupload="isProductUpload" @returnImgs="returnProductImgsFunc">上传图片</Upload>

    <!--选择门店-->
    <GetSupplierStore :is_open="open_store" :shop_supplier_id="form.model.shop_supplier_id" :store-list="form.model.storeList" @close="closeStoreDialogFunc($event)" />
  </div>
</template>

<script>
import Upload from '@/components/file/Upload'
import GetSupplierStore from '@/components/store/GetSupplierStore'
import draggable from 'vuedraggable'

export default {
  components: {
    Upload,
    GetSupplierStore,
    draggable
  },
  data() {
    return {
      isProductUpload: false,
      config: {},
      file_name: 'image',
      open_store: false,
      showStore: false,
      label1: '',
      label2: '',
      isOnShelfStore: 0,
      tempStoreList: [],
      descText: '',
      params_value: []
    }
  },
  inject: ['form'],
  watch: {
    'form.model.product_type': {
      deep: true,
      handler(newVal) {
        this.changeStoreData(newVal)
      }
    },
    'form.model.category_id': {
      deep: true,
      handler(newVal) {
        this.changeCategoryData(newVal)
      }
    },
    'form.model.is_selfmention': {
      deep: true,
      handler(newVal) {
        this.changeShowStore(newVal)
      }
    }
  },
  created() {
    this.params_value = this.form.model.category_params_value
    this.changeStoreData(this.form.model.product_type)
  },
  methods: {
    /** 是否显示店铺 **/
    changeShowStore(val) {
      this.showStore = (this.form.model.product_type === 1 && val === 1) || this.form.model.product_type === 3 || this.form.model.product_type === 4
    },
    changeCategoryData(val) {
      this.form.category.forEach((v, i) => {
        if (v.category_id === val) {
          const arr = JSON.parse(v.params)
          this.form.model.params_value = arr
          this.params_value = arr
        }
      })
    },
    /** 切换店铺数据 **/
    changeStoreData(val) {
      this.showStore = (val === 1 && this.form.model.is_selfmention === 1) || val === 3 || val === 4
      this.label1 = val === 1 ? '可自提门店：' : '适用门店：'
      this.label2 = val === 1 ? '部分门店' : '指定门店'
      this.isOnShelfStore = val === 1 ? this.form.model.is_on_shelf_store_one : this.form.model.is_on_shelf_store_two
      this.tempStoreList = val === 1 ? this.form.model.storeList_one : this.form.model.storeList_two
      this.showDescText()
    },
    /** 显示文案 **/
    showDescText() {
      if (this.form.model.product_type === 1 && this.form.model.is_selfmention === 1) {
        if (this.form.model.shop_supplier_id === 10001) {
          this.descText = this.isOnShelfStore === 1 ? '可以选择平台自营下属的部分门店支持自提' : '平台自营下属的所有门店都支持自提'
        } else {
          this.descText = this.isOnShelfStore === 1 ? '可以选择该商户下属及平台自营下属的部分门店支持自提' : '该商户及平台自营下属的所有门店都支持自提'
        }
      } else {
        this.descText = ''
      }
    },
    /** 打开上传图片 **/
    openProductUpload(file_type, file_name) {
      this.file_name = file_name
      if (file_type === 'image') {
        this.config = {
          total: 9,
          file_type: 'image'
        }
      } else {
        this.config = {
          total: 1,
          file_type: 'video'
        }
      }
      this.isProductUpload = true
    },
    /** 上传商品图片 **/
    returnProductImgsFunc(e) {
      if (e != null) {
        if (this.file_name === 'video') {
          this.form.model.video_id = e[0].file_id
          this.form.model.video = e[0]
        } else if (this.file_name === 'image') {
          const imgs = this.form.model.image.concat(e)
          this.$set(this.form.model, 'image', imgs)
        } else if (this.file_name === 'poster') {
          this.form.model.poster_id = e[0].file_id
          this.form.model.poster = e[0]
        }
      }
      this.isProductUpload = false
    },
    /** 删除商品图片 **/
    deleteImg(index) {
      this.form.model.image.splice(index, 1)
    },
    delVideo() {
      this.form.model.video_id = 0
      this.form.model.video = {}
    },
    /** 选择门店 **/
    selectStore() {
      this.open_store = true
    },
    /** 关闭获取门店弹窗 **/
    closeStoreDialogFunc(e) {
      if (e.type !== 'error' && e.params.length > 0) {
        const ids = []
        // 区分自提还是计次
        if (this.form.model.product_type === 1) {
          this.form.model.storeList_one.forEach(item => {
            ids.push(item.store_id)
          })
          e.params.forEach(item => {
            if (ids.indexOf(item.store_id) === -1) {
              this.form.model.storeList_one.push(item)
              ids.push(item.store_id)
            }
          })
          this.form.model.store_ids_one = ids.join(',')
        } else {
          this.form.model.storeList_two.forEach(item => {
            ids.push(item.store_id)
          })
          e.params.forEach(item => {
            if (ids.indexOf(item.store_id) === -1) {
              this.form.model.storeList_two.push(item)
              ids.push(item.store_id)
            }
          })
          this.form.model.store_ids_two = ids.join(',')
        }
      }
      this.open_store = false
    },
    /** 删除门店 **/
    deleteStore(index) {
      // 区分自提还是计次
      if (this.form.model.product_type === 1) {
        this.form.model.storeList_one.splice(index, 1)
        const ids = []
        this.form.model.storeList_one.forEach(item => {
          ids.push(item.store_id)
        })
        this.form.model.store_ids_one = ids.join(',')
      } else {
        this.form.model.storeList_two.splice(index, 1)
        const ids = []
        this.form.model.storeList_two.forEach(item => {
          ids.push(item.store_id)
        })
        this.form.model.store_ids_two = ids.join(',')
      }
    }
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

.mg-top10 {
  margin-top: 10px;
}
</style>
