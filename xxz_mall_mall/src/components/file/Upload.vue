<template>
  <div class="upload-wrap">
    <el-dialog
      title="文件管理"
      :visible.sync="dialogVisible"
      :close-on-click-modal="false"
      custom-class="upload-dialog"
      :close-on-press-escape="false"
      :width="dialogWidth"
      :append-to-body="true"
      @close="cancelFunc"
    >
      <div class="upload-wrap-inline mb16 clearfix">
        <div class="leval-item">
          <el-button size="small" icon="el-icon-plus" @click="addCategory">添加分类</el-button>

          <el-popover
            placement="bottom"
            width="200"
            trigger="click"
            :value="true"
          >
            <ul class="move-type">
              <li v-for="(item,index) in typeList" :key="index" @click="moveTypeFunc(item.group_id)">
                {{ item.group_name }}
              </li>
            </ul>
            <el-button slot="reference" size="small" icon="el-icon-caret-bottom">移动至</el-button>
          </el-popover>

          <el-button size="small" type="danger" icon="el-icon-delete" @click="deleteFileFunc(false)">批量删除</el-button>
        </div>

        <div class="leval-item upload-btn">
          <el-upload
            ref="upload"
            class="avatar-uploader"
            multiple
            action="string"
            :accept="accept"
            :before-upload="onBeforeUploadImage"
            :http-request="UploadImage"
            :show-file-list="false"
            :on-change="fileChange"
          >
            <el-button size="small" icon="el-icon-upload" type="primary">点击上传</el-button>
          </el-upload>
        </div>
      </div>

      <!--我的资源库-->
      <div class="fileContainer">
        <div class="file-type">
          <ul>
            <li
              v-for="(item,index) in typeList"
              :key="index"
              :class="activeType === item.group_id?'item active':'item'"
              @click="selectTypeFunc(item.group_id)"
            >
              {{ item.group_name }}
              <div v-if="item.group_id!=null" class="operation" @click.stop>
                <p @click="editCategoryFunc(item)">
                  <i class="el-icon-edit" />
                </p>
                <p @click="deleteCategoryFunc(item.group_id)">
                  <i class="el-icon-delete" />
                </p>
              </div>
            </li>
          </ul>
        </div>

        <div class="file-content">
          <ul class="fileContainerUI clearfix">
            <li v-for="(item, index) in fileList.data" :key="index" class="file" @click="selectFileFunc(item,index)">
              <div v-if="item.selected === true" class="selectedIcon">
                <i class="el-icon-check" />
              </div>
              <img v-if="this_config.file_type === 'image'" :scr="item.file_path" :style="'background-image:url('+item.file_path+')'" alt="">
              <video v-if="this_config.file_type === 'video'" height="100" width="100" :src="item.file_path" :autoplay="false">
                您的浏览器不支持 video 标签
              </video>
              <img v-if="this_config.file_type === 'audio'" :scr="audio_image" :style="'background-image:url('+audio_image+')'" alt="">
              <p class="desc">{{ item.real_name }}</p>
              <div class="bottom-shade" @click.stop>
                <a href="javascript:void(0)" @click="deleteFileFunc(item)">
                  <i class="el-icon-delete" />
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!--分页-->
      <div class="pagination-wrap">
        <el-pagination
          :current-page="curPage"
          :page-sizes="[12, 24, 36, 42, 48]"
          :page-size="pageSize"
          layout="total, sizes, prev, pager, next, jumper"
          :total="totalDataNumber"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>

      <div slot="footer" class="dialog-footer">
        <el-button size="small" @click="cancelFunc">取 消</el-button>
        <el-button size="small" type="primary" @click="confirmFunc">确 定</el-button>
      </div>
    </el-dialog>

    <!--图片类别-->
    <AddCategory v-if="isShowCategory" :category="category" :file-type="this_config.file_type" @closeCategory="closeCategoryFunc" />
  </div>
</template>

<script>
import FileApi from '@/api/file.js'
import AddCategory from './part/AddCategory'

export default {
  components: {
    AddCategory
  },
  /* eslint-disable vue/require-prop-types */
  props: ['config'],
  data() {
    return {
      /* 宽度 */
      dialogWidth: '910px',
      /* 类别 */
      activeType: null,
      /* 图片类别 */
      typeList: [],
      /* 图标路径 */
      imageUrl: null,
      /* 弹窗显示 */
      dialogVisible: true,
      /* 文件列表 */
      fileList: [],
      /* 分类添加loading */
      addLoading: false,
      /* 默认值 */
      this_config: {
        /* 上传个数 */
        total: 1,
        file_type: 'image'
      },
      /* 记录选中的个数 */
      record: 0,
      /* 是否加载完成 */
      loading: true,
      /* 列表数据 */
      tableData: [],
      /* 一页多少条 */
      pageSize: 36,
      /* 一共多少条数据 */
      totalDataNumber: 0,
      /* 当前是第几页 */
      curPage: 1,
      /* 是否显示图片类别编辑框 */
      isShowCategory: false,
      /* 当前类别model */
      category: null,
      /* 选中图片 */
      fileIds: [],
      accept: '',
      audio_image: 'http://img.pighack.com/20220104131655aa2cf7234.png'
    }
  },
  created() {
    /* 覆盖默认值 */
    if (Object.prototype.toString.call(this.config).toLowerCase() === '[object object]') {
      for (const key in this.config) {
        this.this_config[key] = this.config[key]
      }
      if (this.this_config['file_type'] === 'image') {
        this.accept = 'image/jpeg,image/png,image/jpg'
      } else {
        this.accept = 'video/mp4,audio/mp3'
      }
    }
    this.getFileCategory()
    this.getData()
  },
  methods: {
    /** 获取图片类别 **/
    getFileCategory() {
      const self = this
      FileApi.fileCategory({
        type: self.this_config.file_type
      }, true)
        .then(data => {
          const type = [{
            group_id: null,
            group_name: '全部'
          }]
          self.typeList = type.concat(data.data.group_list)
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 添加图片类别 **/
    addCategory() {
      this.isShowCategory = true
    },
    /** 修改类别 **/
    editCategoryFunc(item) {
      this.isShowCategory = true
      this.category = item
    },
    /** 关闭类别层 **/
    closeCategoryFunc(e) {
      if (e != null) {
        this.getFileCategory()
      }
      this.isShowCategory = false
    },
    /** 删除类别提示 **/
    deleteCategoryFunc(e) {
      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.deleteCategory(e)
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        })
      })
    },
    /** 删除类别 **/
    deleteCategory(e) {
      const self = this
      FileApi.deleteCategory({
        group_id: e
      }).then(data => {
        self.$message({
          message: '删除成功',
          type: 'success'
        })
        self.getFileCategory()
      }).catch(() => {
        self.$message.error('删除失败')
      })
    },
    /** 选择类别 **/
    selectTypeFunc(id) {
      this.activeType = id
      this.curPage = 1
      this.getData()
    },
    /** 选择第几页 **/
    handleCurrentChange(val) {
      this.curPage = val
      this.getData()
    },
    /** 每页多少条 **/
    handleSizeChange(val) {
      this.curPage = 1
      this.pageSize = val
      this.getData()
    },
    /** 获取图片列表数据 **/
    getData() {
      const self = this
      const param = {
        page: self.curPage,
        pageSize: self.pageSize,
        group_id: self.activeType,
        type: self.this_config.file_type
      }
      FileApi.fileList(param, true).then(data => {
        self.loading = false
        self.fileList = data.data.file_list
        self.totalDataNumber = self.fileList.total
      }).catch(() => {
        self.loading = false
      })
    },
    /** 图片移动到某个类别 **/
    moveTypeFunc(e) {
      const self = this
      const fileIds = []
      self.fileList.data.forEach(item => {
        if (item.selected) {
          fileIds.push(item.file_id)
        }
      })
      if (fileIds.length === 0) {
        self.$message({
          message: '请选择文件',
          type: 'warning'
        })
        return
      }
      self.$confirm('确定移动选中的文件吗？, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        FileApi.moveFile({
          group_id: e,
          fileIds: fileIds
        }, true).then(data => {
          self.$message({
            message: '移动成功',
            type: 'success'
          })
          self.getFileCategory()
          self.getData()
        }).catch(() => {
        })
      })
    },
    /** 选择上传图片 **/
    fileChange(e) {
      console.log(e)
    },
    /** 选择图片之前 **/
    onBeforeUploadImage(file) {
      return true
    },
    /** 上传图片 **/
    UploadImage(param) {
      const self = this
      const loading = this.$loading({
        lock: true,
        text: '图片上传中,请等待',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)'
      })
      const formData = new FormData()
      formData.append('iFile', param.file)
      formData.append('group_id', self.activeType)
      formData.append('file_type', self.this_config.file_type)
      FileApi.uploadFile(formData)
        .then(response => {
          loading.close()
          self.getData()
          param.onSuccess() // 上传成功的图片会显示绿色的对勾
          // 但是我们上传成功了图片， fileList 里面的值却没有改变，还好有on-change指令可以使用
        })
        .catch(response => {
          loading.close()
          self.$message({
            message: '本次上传图片失败',
            type: 'warning'
          })
          param.onError()
        })
    },
    /** 选择图片 **/
    selectFileFunc(item, index) {
      if (item.selected) {
        item.selected = false
        this.record--
      } else {
        if (this.record >= this.this_config.total) {
          this.$message({
            message: '本次最多只能上传 ' + this.this_config.total + ' 个文件',
            type: 'warning'
          })
          return
        } else {
          item.selected = true
          this.record++
        }
      }
      this.$set(this.fileList.data, index, item)
    },
    /** 删除图片 **/
    deleteFileFunc(e) {
      const self = this
      self.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const loading = self.$loading({
          lock: true,
          text: '正在删除',
          spinner: 'el-icon-loading',
          background: 'rgba(0, 0, 0, 0.7)'
        })
        const temp_list = []
        if (e) {
          temp_list.push(e.file_id)
        } else {
          for (let i = 0; i < self.fileList.data.length; i++) {
            const item = self.fileList.data[i]
            if (item.selected) {
              temp_list.push(item.file_id)
            }
          }
        }
        FileApi.deleteFiles({
          fileIds: temp_list
        }, true).then(data => {
          loading.close()
          self.$message({
            message: '删除成功',
            type: 'success'
          })
          self.getData()
        }).catch(() => {
          loading.close()
          self.$message({
            message: '删除失败',
            type: 'warning'
          })
        })
      })
    },
    /** 选择图片确认 **/
    confirmFunc() {
      const list = []
      let leng = 0
      for (let i = 0; i < this.fileList.data.length; i++) {
        const item = this.fileList.data[i]
        if (item.selected) {
          const obj = {
            file_id: item.file_id,
            file_path: item.file_path,
            real_name: item.real_name
          }
          list.push(obj)
          leng++
        }
        if (leng > this.this_config.total) {
          break
        }
      }
      this.$emit('returnImgs', list)
    },
    /** 取消 **/
    cancelFunc() {
      this.$emit('returnImgs', null)
    }
  }
}
</script>

<style lang="scss">
  .upload-dialog .el-dialog__body {
    padding-top: 0;
    padding-bottom: 0;
  }

  .upload-wrap-inline .leval-item {
    display: inline-block;
  }

  .upload-wrap-inline .upload-btn {
    float: right;
  }

  .fileContainer {
    position: relative;
    padding-left: 120px;
  }

  .fileContainer .file-type {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    overflow-y: auto;
    width: 120px;
  }

  .fileContainer .file-type li {
    padding: 10px 0;
    cursor: pointer;
    text-align: center;
    padding-right: 20px;
    min-height: 20px;
    position: relative;
  }

  .fileContainer .file-type li.active {
    background: #b4b4b4;
    color: #FFFFFF
  }

  .fileContainer .file-type li .operation {
    display: none;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 20px;
  }

  .fileContainer .file-type li:hover .operation {
    display: block;
  }

  .fileContainer .file-content {
    height: 300px;
    text-align: center;
    overflow: hidden;
    padding: 10px;
    margin: 0;
    overflow-y: auto;
    border: 1px solid #c6c6c6;
  }

  .fileContainer li.file {
    float: left;
    margin: 10px 9px 0px;
    position: relative;
    width: 100px;
  }

  .fileContainer li.file img {
    display: inline-block;
    width: 100px;
    height: 100px;
    cursor: pointer;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    background-color: #fff;
  }

  .fileContainer li.file p.desc {
    font-size: 12px;
    height: 15px;
    line-height: 15px;
    overflow: hidden;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    word-wrap: break-word;
  }

  .fileContainer li.file:hover .bottom-shade {
    display: block;
  }

  .fileContainer .bottom-shade {
    position: absolute;
    bottom: 16px;
    left: 0;
    background: rgba(0, 0, 0, 0.7);
    height: 26px;
    line-height: 26px;
    width: 100%;
    display: none;
    transform: all 0.2s ease-out 0s;
    color: #FFFFFF;
  }
  .fileContainer .bottom-shade a{ color:#FFFFFF;}

  .fileContainer .selectedIcon {
    width: 20px;
    height: 20px;
    position: absolute;
    top: 0;
    left: 0;
    background: #ff9900;
    z-index: 1;
    color: #FFFFFF;
    cursor: pointer;
  }

  // :focus {
  //   outline: -webkit-focus-ring-color auto 1px;
  // }

  .upload-dialog .pagination-wrap {
    margin-top: 16px;
    text-align: right;
  }

  .move-type{ max-height: 200px; overflow-y: auto;}
  .move-type li{ padding: 0 10px; height:30px; line-height: 30px; cursor:pointer;}
  .move-type li:hover{ background: #c6e2ff;}
</style>
