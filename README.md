## 平台简介
🔥热门推荐中🔥小玄猪商城小程序、多端应用商城，多商户商城易于二次开发
小玄猪商城基于php、thinkphp6、vue、element-ui、、vue-element-admin、uniapp开发的商城小程序。包含小程序商城、H5商城、公众号商城、PC商城、App，拼团、砍价、秒杀、优惠券、活动报名、客户管理、推广素材、品牌、分类商品参数、知识付费、抽奖、积分、会员等级、小程序直播、页面DIY，前后端分离全部100%开源。


如果这个项目让你有所收获，记得右上角 点  ⭐️  Star，👀 Watching 关注哦，
这对我是非常不错的鼓励与支持。



## 软件技术栈

后端：php+thinkphp6.0

前端：vue-element-admin

小程序端：uniapp

部署环境建议：Linux + Nginx + PHP7.1～7.3 + MySQL5.6，或者直接用宝塔集成环境。

 **技术特点** 

标准API接口，前后端分离(分工协助 开发效率高)

统一权限(前后端一致的权限管理)

前端移动端使用UNIAPP框架编写，多端一体（支持微信小程序、微信公众号、QQ小程序、百度小程序、支付宝小程序、抖音/头条小程序、安卓、IOS）集成多个主流平台，易于维护

thinkphp6(上手简单，极易开发)

vue-element-admin (github最受欢迎前端开源管理后台框架，方便快速开发)

代码全部开源，方便企业扩展自身业务需求


 **部分PC后台截图** 

![输入图片说明](screenshot/1%E5%95%86%E5%9F%8E%E8%A1%8C%E4%B8%9A.jpg)
![输入图片说明](screenshot/3%E9%A1%B5%E9%9D%A2%E7%BC%96%E8%BE%91%E5%99%A8'.jpg)
![输入图片说明](screenshot/pc1.jpg)
![输入图片说明](screenshot/pc2.jpg)
![输入图片说明](screenshot/pc3.jpg)
 **部分小程序截图** 

![输入图片说明](screenshot/%E7%A7%BB%E5%8A%A8%E7%AB%AF%E6%8B%BC%E5%9B%BE1.jpg)`Ω`
![输入图片说明](screenshot/%E7%A7%BB%E5%8A%A8%E7%AB%AF%E6%8B%BC%E5%9B%BE2.jpg)

## 小玄猪商城开发交流群

入群前记得帮忙点一下右上角 ⭐️ ，这对我是非常不错的鼓励与支持

![输入图片说明](screenshot/%E4%BC%81%E4%B8%9A%E5%BE%AE%E4%BF%A1%E7%BE%A4%E4%BA%8C%E7%BB%B4%E7%A0%81.png)

## 开源说明

1.本商城开源版的源码全部公开、允许用于个人学习、毕业设计、教学案例、公益事业，也允许商业使用;

2.如果商用必须保留版权信息，商用免费，但仅限自运营，请自觉遵守;

3.不允许对程序代码以任何形式、任何目的的再发行或出售，否则将追究侵权者法律责任;

4.本版本源码全部开源;包括前端，后端，无任何加密;

5.商用请仔细审查代码和漏洞，不得用于任一国家许可范围之外的商业应用，产生的一切任何后果责任自负;

6.我们的团队水平有限，也是在探索中学习、改进。开源，是为了让认可我们的用户能自由的使用、学习软件的内部架构，让更多的人有机会阅读并发现Bug、对软件项目提出改进意见。


## 项目定制

如果你不想自己开发，有项目想要外包，可以扫码联系以下微信，并注明定制开发

![输入图片说明](screenshot/%E5%86%AF%E4%BD%B3%202.png)

我们的团队包含专业的项目经理、架构师、前端工程师、后端工程师、测试工程师、运维工程师，可以提供全流程的定制服务。

项目可以是商城、SCRM 系统、OA 系统、物流系统、ERP 系统、CMS 系统、HIS 系统、支付系统、IM 聊天、微信公众号、微信小程序等等。

## 系统演示

官网介绍
https://www.pighack.com
部署教程
https://docs.pighack.com/web/#/5/546

## 特别鸣谢

thinkphp:https://www.thinkphp.cn

vue-element-admin:https://gitee.com/panjiachen/vue-element-admin

vue:https://cn.vuejs.org/

easywechat:https://www.easywechat.com/

## mall目录结构

```bash
├── build                       构建相关
├── dist                        构建完成后的文件目录
├── mock                        项目mock模拟数据
├── node_modules                依赖类库
├── plop-templates              模板文件
├── public                      静态资源
│   ├── favicon.ico             favicon图标
│   └── index.html              html模板
├── src                         开发目录
│   ├── api                     所有请求
│   ├── assets                  主题、字体、图片等静态资源
│   ├── components              全局公用组件
│   ├── directive               全局指令
│   ├── filters                 全局filter
│   ├── icons                   项目所有svg、icons
│   ├── lang                    国际化language
│   ├── layout                  全局layout
│   ├── router                  路由
│   ├── store                   全局store管理
│   ├── styles                  全局样式
│   ├── utils                   全局公用方法
│   ├── vendor                  公用vendor
│   ├── views                   views所有页面
│   │   ├── app                 应用
│   │   ├── authority           权限
│   │   ├── dashboard           主页
│   │   ├── data                统计
│   │   ├── error-page          错误页
│   │   ├── finance             财务
│   │   ├── goods               商品
│   │   ├── home                店铺
│   │   ├── login               登录
│   │   ├── order               订单
│   │   ├── profile             个人信息
│   │   ├── redirect            跳转
│   │   ├── setting             设置
│   │   └── user                会员
│   ├── App.vue                 入口页面
│   ├── main.js                 入口文件、加载组件、初始化等
│   ├── permission.js           权限管理
│   └── settings.js             配置文件
├── tests                       测试目录
├── .editorconfig               editor编辑器配置
├── .env.development            开发环境变量配置
├── .env.production             生产环境变量配置
├── .env.staging                测试环境变量配置
├── .eslintignore               eslint忽略文件
├── .eslintrc.js                eslint配置项
├── .gitignore                  git忽略文件
├── .travis.yml                 自动化CI配置
├── babel.config.js             babel-loader配置
├── jest.config.js              测试配置
├── jsconfig.json               js配置
├── LICENSE                     许可
├── package.json                package.json
├── package-lock.json           package-lock.json
├── plopfile.js                 自动创建项目文件
├── postcss.config.js           postcss配置
├── README.md                   使用手册
└── vue.config.js               vue-cli配置
```

## server目录结构

```bash
├── app                         应用目录
│   ├── api                     前端接口目录
│   │   ├── controller          控制器目录
│   │   ├── event               事件类目录
│   │   ├── model               数据模型目录
│   │   ├── service             逻辑处理层目录
│   │   └── common.php          公共函数文件
│   ├── common                  公共目录
│   │   ├── enum                枚举类目录
│   │   ├── event               事件类目录
│   │   ├── exception           异常类目录
│   │   ├── library             工具类目录
│   │   ├── model               数据模型目录
│   │   └── service             逻辑处理层目录
│   ├── gateway                 Workerman目录
│   │   └── Events.php          事件触发
│   ├── job                     任务目录
│   │   ├── command             命令类目录
│   │   ├── controller          控制器目录
│   │   ├── event               事件类目录
│   │   ├── model               数据模型目录
│   │   └── service             逻辑处理层目录
│   ├── mall                    后台目录
│   │   ├── controller          控制器目录
│   │   ├── model               数据模型目录
│   │   ├── service             逻辑处理层目录
│   │   ├── validate            验证类目录
│   │   └── common.php          公共函数文件
│   ├── middleware              中间件目录
│   │   └── Visit.php           记录访问信息
│   ├── swoole                  Swoole目录
│   ├── BaseController.php      控制器基础类
│   ├── common.php              公共函数文件
│   ├── event.php               事件定义文件
│   ├── ExceptionHandle.php     应用异常处理类
│   ├── middleware.php          全局中间件定义文件
│   ├── provider.php            容器Provider定义文件
│   ├── Request.php             应用请求对象类
│   └── XxzController.php       控制器中间类
├── config                      配置目录
│   ├── annotation.php          注解配置
│   ├── app.php                 应用配置
│   ├── cache.php               缓存配置
│   ├── console.php             控制台配置
│   ├── cookie.php              Cookie设置
│   ├── database.php            数据库配置
│   ├── filesystem.php          文件磁盘配置
│   ├── gateway_worker.php      Workerman中gateway指令配置
│   ├── lang.php                多语言配置
│   ├── log.php                 日志配置
│   ├── middleware.php          中间件配置
│   ├── route.php               URL和路由配置
│   ├── session.php             Session配置
│   ├── swoole.php              Swoole配置
│   ├── trace.php               Trace配置
│   ├── view.php                视图配置
│   ├── worker.php              Workerman配置
│   └── worker_server.php       Workerman中server指令配置
├── db                          mysql存放目录
├── extend                      扩展类库目录
├── public                      WEB目录（对外访问目录）
│   ├── favicon.ico             favicon图标
│   ├── index.php               入口文件
│   ├── robots.txt              Robots配置文件
│   └── router.php              快速测试文件
├── route                       路由定义目录
│   └── app.php                 路由定义文件
├── vendor                      Composer类库目录
├── .env                        配置文件
├── .gitignore                  git忽略文件
├── .travis.yml                 自动化CI配置
├── composer.json               composer定义文件
├── composer.lock               composer锁定文件
├── LICENSE.txt                 授权说明文件
├── README.md                   使用手册
├── think                       命令行入口文件
└── version.json                版本号
```
