<template>
  <div v-if="!loading" class="evaluation-detail pb50">
    <!--form表单-->
    <el-form ref="form" size="small" :model="form" label-width="120px">
      <!--添加门店-->
      <div class="common-form">商品评价详情</div>

      <el-form-item label="用户">
        <p>{{ form.user.nickName }}</p>
      </el-form-item>

      <el-form-item label="商品名称">
        <p>{{ form.product.product_name | isNull }}</p>
      </el-form-item>

      <el-form-item label="商品图片">
        <img v-if="isImg" :src="path" width="120px;" :isImg="isImg">
      </el-form-item>

      <el-form-item label="评论时间">
        <p>{{ form.create_time }}</p>
      </el-form-item>

      <el-form-item label="评价图片">
        <div class="d-s-c evaluation-imgs pb16">
          <div v-if="form.image.length > 0">
            <div v-for="(item, index) in form.image" :key="index" class="item">
              <img v-img-url="item.file_path">
            </div>
          </div>
        </div>
      </el-form-item>

      <el-form-item v-model="form.score" label="评分">
        <p v-if="form.score === 10">好评</p>
        <p v-if="form.score === 20">中评</p>
        <p v-if="form.score === 30">差评</p>
      </el-form-item>

      <el-form-item v-model="form.describe_score" label="描述评分">
        <div>{{ form.describe_score }}</div>
      </el-form-item>

      <el-form-item v-model="form.express_score" label="物流评分">
        <div>{{ form.express_score }}</div>
      </el-form-item>

      <el-form-item v-model="form.server_score" label="服务评分">
        <div>{{ form.server_score }}</div>
      </el-form-item>

      <el-form-item label="评价内容">
        <div>{{ form.content }}</div>
      </el-form-item>

      <el-form-item label="排序">
        <el-input v-model="form.sort" type="number" placeholder="请输入数字" style="width: 200px;" />
      </el-form-item>

      <el-form-item label="审核">
        <el-radio-group v-model="form.status">
          <el-radio :label="1">通过</el-radio>
          <el-radio :label="2">不通过</el-radio>
        </el-radio-group>
      </el-form-item>

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" type="info" @click="cancelFunc">取消</el-button>
        <el-button size="small" type="primary" @click="onSubmit">提交</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
import GoodsApi from '@/api/goods.js'

export default {
  data() {
    return {
      /* 是否上传图片 */
      isupload: false,
      isImg: true,
      is_comment: false,
      /* 商品图片 */
      path: '',
      /* 评论图片 */
      comment_img: '',
      /* form表单数据 */
      form: {
        user: {},
        product: {}
      },
      loading: true,
      comment_id: 0
    }
  },
  created() {
    this.comment_id = this.$route.query.comment_id
    /* 获取列表 */
    this.getComment()
  },
  methods: {
    /** 获取评论 **/
    getComment() {
      const self = this
      GoodsApi.getComment({
        comment_id: self.comment_id
      }).then(data => {
        self.loading = false
        self.form = data.data.data
        self.path = data.data.data.product.image[0].file_path
        self.comment_id = data.data.data.comment_id
      }).catch(() => {})
    },
    /** 添加文章 **/
    onSubmit() {
      const self = this
      GoodsApi.editComment({
        comment_id: self.comment_id,
        status: self.form.status,
        sort: self.form.sort
      }, true)
        .then(data => {
          if (data.code === 1) {
            self.$message({
              message: '恭喜你，操作成功',
              type: 'success'
            })
            self.$router.push('/goods/comment/index')
          } else {
            self.$message.error('错了哦，这是一条错误消息')
          }
        })
        .catch(() => {})
    },
    /** 取消 **/
    cancelFunc() {
      this.$router.back(-1)
    }
  }
}
</script>

<style>
.evaluation-detail {
  margin-bottom: 50px;
}
.evaluation-detail .el-form-item {
  border-bottom: 1px solid #eeeeee;
}
.evaluation-detail .el-form-item__label {
  color: #bbbbbb;
}
.evaluation-detail .evaluation-imgs .item {
  width: 100px;
  height: 100px;
  margin-right: 4px;
  border: 1px solid #d1d5dd;
}
.evaluation-detail .evaluation-imgs .item img {
  width: 98px;
  height: 98px;
  object-fit: contain;
}
</style>
