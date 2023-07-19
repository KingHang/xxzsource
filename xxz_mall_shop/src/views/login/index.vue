<template>
  <div v-if="!loading" class="all-login" :style="'background-image:url(' + bgimg_url + ');background-repeat: no-repeat;background-size: 100%;'">
    <div>
      <el-container class="login-container">
        <el-row class="h1" type="flex">
          <el-col>
            <img
              :src="shop_logo"
              alt=""
              style="display: inline-block; width: 50px; height: 50px;border-radius: 50%;"
            >
          </el-col>
          <el-col style="margin-left: 5px"> {{ shop_name }} </el-col>
        </el-row>

        <el-aside class="login-container-left">
          <el-row type="flex" class="row-bg">
            <el-col><div class="col-1">他们都在{{ shop_name }}</div></el-col>
          </el-row>
          <el-row type="flex" class="row-bag">
            <el-col :offset="6">
              <div class="col-2">为企业提供数字化转型新思路 ></div>
            </el-col>
          </el-row>
          <el-row type="flex" style="margin-top: 70px">
            <el-col style="margin: 0 70px 0 70px">
              <div class="Third-party" />
              <div class="Third-party" />
            </el-col>
          </el-row>
          <el-row type="flex" class="Thirdparty">
            <el-col style="margin: 0 30px 0 30px">
              <div class="Third-party" />
              <div class="Third-party" />
              <div class="Third-party" />
            </el-col>
          </el-row>
          <el-row type="flex" class="Thirdparty">
            <el-col style="margin: 0 70px 0 70px">
              <div class="Third-party" />
              <div class="Third-party" />
            </el-col>
          </el-row>
        </el-aside>

        <el-main class="login-container-right" style="overflow: hidden">
          <el-row class="loginindex">
            <el-col class="loginindex-1">
              <div>
                <img
                  :src="shop_logo"
                  alt=""
                  style="width: 60px; height: 60px; margin-left: 150px;border-radius: 50%;"
                >
              </div>
            </el-col>
            <el-col class="loginindex-2"> 登录 </el-col>
          </el-row>
          <el-row class="login">
            <el-col
              v-for="(item, index) in loginlist"
              :key="index"
              :span="5"
              :class="[item.undline ? 'logins' : 'islogin']"
              @click.native="switchColor(item, index)"
            >
              <div>
                <div style="text-align: center;">{{ item.username }}</div>
                <div class="unline" />
              </div>
            </el-col>
          </el-row>
          <el-form
            v-if="!flag"
            ref="ruleForm"
            :model="ruleForm"
            :rules="rules"
            label-position="left"
            label-width="0px"
            class="demo-ruleForm"
          >
            <!-- <h3 class="title" style="margin-bottom: 20px">{{ shop_name }}</h3> -->
            <!--用户名-->
            <el-form-item prop="account">
              <el-input
                v-model="ruleForm.account"
                type="text"
                auto-complete="off"
                placeholder="请输入您的账号"
              />
            </el-form-item>
            <!--密码-->
            <el-form-item prop="checkPass">
              <el-input
                v-model="ruleForm.checkPass"
                type="password"
                auto-complete="off"
                placeholder="请输入您的密码"
                show-password
              />
            </el-form-item>
            <!--登录-->
            <el-form-item>
              <el-row>
                <el-checkbox v-model="checked">自动登录</el-checkbox>
              </el-row>
              <el-row>
                <el-col>
                  <el-button type="primary" style="width: 50%" :loading="logining" @click.native.prevent="SubmitFunc">登录</el-button>
                  <span class="or">或</span>
                  <!--<router-link to="/register" />-->
                  <el-link class="regedit" @click.native.prevent="regedit">免费注册</el-link>
                </el-col>
                <el-link class="nopassword" :underline="false" @click.native.prevent="restpwd">忘记密码？</el-link>
              </el-row>
            </el-form-item>
          </el-form>
          <!-- 手机号登录 -->
          <el-form
            v-if="flag"
            ref="ruleForm"
            :model="ruleForm"
            :rules="rules"
            label-position="left"
            label-width="0px"
            class="demo-ruleForm"
          >
            <!-- <h3 class="title" style="margin-bottom: 20px">{{ shop_name }}</h3> -->
            <!--用户名-->
            <el-form-item prop="phone">
              <el-select v-model="data1" style="width: 30%">
                <el-option
                  v-for="item in options"
                  :key="item.value"
                  :label="item.text"
                  :value="item.value"
                />
              </el-select>
              <el-input
                v-model="ruleForm.phone"
                type="text"
                maxlength="11"
                placeholder="请输入手机号"
                style="width: 65%"
              />
            </el-form-item>
            <el-form-item prop="code">
              <div class="verify-wrapper">
                <el-input
                  v-model="ruleForm.code"
                  type="text"
                  maxlength="6"
                  placeholder="请输入短信验证码"
                  style="width: 65%"
                />
                <el-button
                  style="
                    width: 30%;
                    border: 0;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
                  "
                  class="btn-orange"
                  :disabled="disabled"
                  @click.native.prevent="getCode"
                >{{ valiBtn }}</el-button>
              </div>
            </el-form-item>
            <!--登录-->
            <el-form-item>
              <el-row>
                <el-checkbox v-model="checked">自动登录</el-checkbox>
              </el-row>
              <el-row>
                <el-col>
                  <el-button type="primary" style="width: 50%" :loading="logining" @click.native.prevent="submitLogin">登录</el-button>
                  <span class="or">或</span>
                  <el-link class="regedit" @click.native.prevent="regedit">免费注册</el-link>
                </el-col>
                <el-link class="nopassword" :underline="false" @click.native.prevent="restpwd">忘记密码？</el-link>
              </el-row>
            </el-form-item>
          </el-form>
        </el-main>
      </el-container>
    </div>
  </div>
</template>

<script>
import bgimg from '@/assets/img/login_bg.png'
import IndexApi from '@/api/index.js'
import UserApi from '@/api/user.js'
import { setCookie } from '@/utils/base.js'

export default {
  data() {
    return {
      data1: 1,
      options: [{ value: 1, text: '中国+86' }],
      /* 密码登录or验证码登录 */
      loginlist: [
        { username: '密码登录', undline: 'true' },
        { username: '验证码登录', undeline: 'false' }
      ],
      select: '',
      input3: '',
      checked: true,
      flag: 0,
      /* 是否正在加载*/
      loading: true,
      /* 商城名称*/
      shop_name: '小玄猪',
      /* 商城logo*/
      shop_logo: 'https://img.pighack.com/20220123155756e81819541.png',
      /* 背景图片*/
      bgimg_url: bgimg,
      /* 是否正在提交*/
      logining: false,
      /* 表单对象*/
      ruleForm: {
        /* 用户名*/
        account: '',
        /* 密码*/
        checkPass: '',
        // 手机号
        phone: '',
        // 验证码
        code: ''
      },
      dialogVisible: false, // 对话框显示和隐藏
      valiBtn: '获取验证码',
      disabled: false,
      autoLogin: false,
      /* 验证规则*/
      rules: {
        /* 用户名*/
        account: [
          { required: true, message: '请输入您的账号', trigger: 'blur' }
        ],
        /* 密码*/
        checkPass: [
          { required: true, message: '请输入您的密码', trigger: 'blur' }
        ],
        phone: [
          {
            required: true,
            trigger: 'blur',
            message: '请输入手机号'
          }
        ]
        // code: [{ required: true, message: "请输入验证码", trigger: "blur" }],
      },
      /* 基础配置*/
      baseData: {}
    }
  },
  created() {
    this.getData()
  },
  methods: {
    /** 忘记密码页面 **/
    restpwd() {
      this.$router.push({ path: '/resetpassword' })
    },
    /** 注册 **/
    regedit() {
      const _this = this
      _this.$router.push({ path: '/register' })
    },
    /** 获取验证码 **/
    getCode() {
      const reg = 11 && /^((13|14|15|17|18)[0-9]{1}\d{8})$/
      if (this.ruleForm.phone === '') {
        this.$message({
          message: '请输入手机号',
          type: 'warning',
          center: 'true'
        })
      } else if (!reg.test(this.ruleForm.phone)) {
        this.$message({
          message: '手机号不合法',
          type: 'warning',
          center: 'true'
        })
      } else {
        this.$refs.ruleForm.validate((valid) => {
          if (valid) {
            this.logining = true
            const Params = {
              mobile: this.ruleForm.phone,
              type: 1
            }
            /* 调用登录接口*/
            UserApi.sendcode(Params, true)
              .then((res) => {
                console.log(res)
                this.logining = false
                if (res.code === 1) {
                  /* 保存个人信息*/
                  // setCookie("userinfo", res.data);
                  /* 设置一个登录状态*/
                  // setCookie("isLogin", true);
                  this.tackBtn() // 验证码倒数60秒
                  /* 跳转到首页*/
                  // this.$router.push({ path: "/" });
                } else {
                  this.$message({
                    message: '获取失败',
                    type: 'error'
                  })
                }
              })
              .catch(() => {
                // 接口调用方法统一处理
                this.logining = false
              })
          }
        })
      }
    },
    /** 倒计时 **/
    tackBtn() {
      // 验证码倒数60秒
      let time = 60
      const timer = setInterval(() => {
        if (time === 0) {
          clearInterval(timer)
          this.valiBtn = '获取验证码'
          this.disabled = false
        } else {
          this.disabled = true
          this.valiBtn = time + '秒后重试'
          time--
        }
      }, 1000)
    },
    /** 手机号登录 **/
    submitLogin() {
      const reg = 11 && /^((13|14|15|17|18)[0-9]{1}\d{8})$/
      if (this.ruleForm.phone === '') {
        this.$message({
          message: '请输入手机号',
          type: 'warning',
          center: 'true'
        })
      } else if (!reg.test(this.ruleForm.phone)) {
        this.$message({
          message: '手机号不合法',
          type: 'warning',
          center: 'true'
        })
      } else if (this.ruleForm.code === '') {
        this.$message({
          message: '请输入验证码',
          type: 'warning',
          center: 'true'
        })
      } else {
        if (this.flag === 1) {
          this.$refs.ruleForm.validate((valid) => {
            if (valid) {
              this.logining = true
              const Params = {
                mobile: this.ruleForm.phone,
                code: this.ruleForm.code
              }
              /* 调用登录接口*/
              UserApi.smslogin(Params, true)
                .then((res) => {
                  this.logining = false
                  if (res.code === 1) {
                    /* 保存个人信息*/
                    setCookie('userinfo', res.data.mobile)
                    /* 设置一个登录状态*/
                    setCookie('isLogin', true)
                    /* 跳转区分*/
                    if (res.data.isTrade === 0) {
                      /* 跳转到选择行业*/
                      this.$router.push({ path: '/industry' })
                    } else {
                      /* 跳转到首页*/
                      this.$router.push({ path: '/' })
                    }
                  } else {
                    this.$message({
                      message: '登录失败',
                      type: 'error'
                    })
                  }
                })
                .catch(() => {
                  // 接口调用方法统一处理
                  this.logining = false
                })
            }
          })
        }
      }
    },
    /** 动态绑定切换 **/
    switchColor(item, index) {
      this.flag = index
      for (let i = 0; i < this.loginlist.length; i++) {
        if (index === i) {
          this.loginlist[index].undline = true
        } else {
          this.loginlist[i].undline = false
        }
      }
    },
    /** 获取基础配置 **/
    getData() {
      const self = this
      IndexApi.base(true)
        .then((res) => {
          self.loading = false
          self.shop_name = res.data.settings.shop_name
          if (res.data.settings.shop_logo && res.data.settings.shop_logo !== '') {
            self.shop_logo = res.data.settings.shop_logo
          }
          if (res.data.settings.shop_bg_img !== '') {
            self.bgimg_url = res.data.settings.shop_bg_img
          }
        })
        .catch(() => {
          self.loading = false
        })
    },
    /** 登录方法 **/
    SubmitFunc(ev) {
      if (this.flag === 0) {
        this.$refs.ruleForm.validate((valid) => {
          this.logining = true
          const Params = {
            username: this.ruleForm.account,
            password: this.ruleForm.checkPass
          }
          /* 调用登录接口 */
          UserApi.login(Params, true)
            .then((res) => {
              this.logining = false
              if (res.code === 1) {
                /* 保存个人信息 */
                setCookie('userinfo', res.data.username)
                /* 设置一个登录状态 */
                setCookie('isLogin', true)
                /* 跳转区分 */
                if (res.data.isTrade === 0) {
                  /* 跳转到选择行业 */
                  this.$router.push({ path: '/industry' })
                } else {
                  /* 跳转到首页 */
                  this.$router.push({ path: '/' })
                }
              } else {
                this.$message({
                  message: '登录失败',
                  type: 'error'
                })
              }
            })
            .catch(() => {
              // 接口调用方法统一处理
              this.logining = false
            })
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  .all-login {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    // border: 1px red solid;
  }
  .Third-party {
    margin: 10px 10px;
    display: inline-block;
    width: 55px;
    height: 55px;
    background: #ffffff;
    box-shadow: 3px 3px 0 rgba(55, 110, 240, 0.11);
    opacity: 1;
    border-radius: 4px 41px 41px 41px;
  }
  .loginindex {
    .loginindex-1 {
      margin-top: 50px;
    }
  }
  .loginindex-2 {
    margin-top: 14px;
    // margin-bottom: 30px;
    text-align: center;
    font-size: 20px;
    font-family: Microsoft YaHei;
    font-weight: bold;
    line-height: 23px;
    color: rgba(0, 0, 0, 0.87);
    opacity: 1;
  }
  .nopassword {
    width: 70px;
    height: 19px;
    margin-top: 5px;
    font-size: 14px;
    font-family: Microsoft YaHei;
    font-weight: 400;
    line-height: 19px;
    color: #376ef0;
    opacity: 1;
  }
  .or {
    position: relative;
    top: 8px;
    margin-left: 40px;
    font-size: 15px;
    font-family: Microsoft YaHei;
    font-weight: 400;
    line-height: 24px;
    color: rgba(0, 0, 0, 0.32);
    opacity: 1;
  }
  .regedit {
    position: relative;
    top: 8px;
    margin-left: 46px;
    font-size: 15px;
    font-family: Microsoft YaHei;
    font-weight: 400;
    line-height: 21px;
    color: #376ef0;
    opacity: 1;
    border-bottom: 1px #376ef0 solid;
  }
  .islogin {
    //   width: 100px;
    // height: 27px;
    font-size: 15px;
    font-family: Microsoft YaHei;
    font-weight: 400;
    line-height: 20px;
    color: rgba(0, 0, 0, 0.32);
    opacity: 1;
  }
  .login {
    // margin: 15px 0;
    margin-top: 40px;
    margin-bottom: 10px;
    font-size: 15px;
    font-family: Microsoft YaHei;
    font-weight: 400;
    line-height: 20px;
    color: rgba(0, 0, 0, 0.87);
    opacity: 1;
  }
  .logins {
    display: flex;
    // flex-flow: column;
    justify-content: center;
    align-items: center;
    .unline {
      margin-top: 8px;
      width: 75px;
      height: 2px;
      // opacity: 1;
      background: linear-gradient(270deg, #376ef0 0%, #6895ff 100%);
      border-radius: 2px;
    }
    // .undlines {
    //   margin-top: 8px;
    //   align-items: center;
    //   justify-content: center;
    //   width: 80px;
    //   height: 2px;
    //   background: linear-gradient(270deg, #376ef0 0%, #6895ff 100%);
    //   opacity: 1;
    //   border-radius: 2px;
    //   align-items: center;
    // }
  }
  .row-bg {
    margin-top: 112px;
    font-family: Microsoft YaHei;
    text-align: center;
    .col-1 {
      font-size: 20px;
      font-weight: 600;
      line-height: 40px;
      color: #000000;
      letter-spacing: 1px;
      opacity: 1;
    }
    .col-2 {
      font-size: 18px;
      font-weight: 400;
      line-height: 50px;
      color: rgba(0, 0, 0, 0.32);
      letter-spacing: 5px;
      opacity: 1;
    }
  }
  .login-bg {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-size: 100%;
    width: 1440px;
    height: 1204px;
    background: #fafbfb center;
    opacity: 1;
  }
  .login-container {
    width: 700px;
    height: 518px;
    box-shadow: 0 0 8px 0 rgba(0, 0, 0, 0.1), 0 1px 0 0 rgba(0, 0, 0, 0.04);
    -webkit-border-radius: 5px;
    border-radius: 16px;
    -moz-border-radius: 5px;
    background-clip: padding-box;
    // margin: 190px auto;
    background-color: #ffffff;
    .h1 {
      align-items: center;
      position: absolute;
      top: 46px;
      left: 46px;
      font-size: 16px;
      font-family: Microsoft YaHei;
      font-weight: 600;
      line-height: 20px;
      color: rgba(0, 0, 0, 0.87);
      opacity: 1;
    }
  }
  .login-container-left {
    width: 330px;
    height: 518px;
    background: linear-gradient(320deg, #e6eef8 0%, #b7d1ef 100%);
    opacity: 1;
    border-radius: 16px 0 0 16px;
    padding: 0 0;
    margin-bottom: 0;
    line-height: normal;
    font-size: 12px;
  }
  .login-container-right {
    width: 433px;
    height: 518px;
    .title {
      text-align: center;
      color: #505458;
    }
  }
  @media screen and (max-width: 1440px) {
    body {
      background: #000;
    }
  }
  @media screen and (max-device-width: 1440px) {
    body {
      background: red;
    }
  }
</style>
