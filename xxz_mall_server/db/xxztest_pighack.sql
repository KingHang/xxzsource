/*
 Navicat Premium Data Transfer

 Source Server         : 139.224.164.56
 Source Server Type    : MySQL
 Source Server Version : 50562
 Source Host           : 139.224.164.56:3306
 Source Schema         : xxztest_pighack

 Target Server Type    : MySQL
 Target Server Version : 50562
 File Encoding         : 65001

 Date: 17/07/2023 08:54:56
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for xxzmall_ad
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_ad`;
CREATE TABLE `xxzmall_ad`  (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '广告名称',
  `image_id` int(11) NOT NULL COMMENT '图片id',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序(越小越靠前)',
  `link_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '跳转链接',
  `name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '链接名称',
  `category_id` int(11) NOT NULL COMMENT 'banner类型id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `shop_supplier_id` int(11) NOT NULL COMMENT '商户id',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1显示0隐藏',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`ad_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'banner图' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_ad_category
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_ad_category`;
CREATE TABLE `xxzmall_ad_category`  (
  `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '类型id',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`category_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'banner类型' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_admin_user`;
CREATE TABLE `xxzmall_admin_user`  (
  `admin_user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '登录密码',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`admin_user_id`) USING BTREE,
  INDEX `user_name`(`user_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10002 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '超管用户记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_app
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_app`;
CREATE TABLE `xxzmall_app`  (
  `app_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '小程序id',
  `app_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '应用名称',
  `trade_id` int(11) NOT NULL DEFAULT 0 COMMENT '行业id',
  `logo` int(11) NULL DEFAULT 0 COMMENT 'logo',
  `passport_open` tinyint(3) NULL DEFAULT 0 COMMENT '通行证是否开发0,不开放1,开放',
  `passport_type` tinyint(3) NULL DEFAULT 10 COMMENT '通行证类型10,微信开放平台,20手机号30,账号密码',
  `is_recycle` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否回收',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态1=》启用0禁用',
  PRIMARY KEY (`app_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10036 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信小程序记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_app_update
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_app_update`;
CREATE TABLE `xxzmall_app_update`  (
  `update_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `app_id` int(11) UNSIGNED NOT NULL COMMENT 'appid',
  `version` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '版本号',
  `wgt_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '热更新包下载地址',
  `pkg_url_android` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '安卓整包升级地址',
  `pkg_url_ios` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'ios整包升级地址',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`update_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'app升级记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_app_wx
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_app_wx`;
CREATE TABLE `xxzmall_app_wx`  (
  `app_id` int(11) UNSIGNED NOT NULL COMMENT 'appid',
  `wxapp_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '小程序AppID',
  `wxapp_secret` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '小程序AppSecret',
  `mchid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信商户号id',
  `apikey` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信支付密钥',
  `cert_pem` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '证书文件cert',
  `key_pem` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '证书文件key',
  `is_recycle` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否回收',
  `fwxapp_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分销小程序AppID',
  `fwxapp_secret` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分销小程序AppSecret',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`app_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信小程序记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_app_wx_live
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_app_wx_live`;
CREATE TABLE `xxzmall_app_wx_live`  (
  `live_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '直播间名称',
  `roomid` int(11) UNSIGNED NOT NULL COMMENT '直播间id',
  `cover_img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '直播间背景图链接',
  `share_img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '直播间分享图链接',
  `live_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 102 COMMENT '直播间状态。101：直播中，102：未开始，103已结束，104禁播，105：暂停，106：异常，107：已过期',
  `anchor_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '主播名',
  `start_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '直播间开始时间',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '直播计划结束时间',
  `is_top` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '置顶状态(0未置顶 1已置顶)',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '软删除(0未删除 1已删除)',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`live_id`) USING BTREE,
  INDEX `roomid`(`roomid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信小程序直播记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_balance_order
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_balance_order`;
CREATE TABLE `xxzmall_balance_order`  (
  `order_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `order_no` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单号',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '充值方式(10自定义金额 20套餐充值)',
  `plan_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '充值套餐id',
  `pay_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '用户支付金额',
  `give_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '赠送金额',
  `real_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '实际到账金额',
  `pay_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '支付状态(10待支付 20已支付)',
  `pay_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '付款时间',
  `transaction_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信支付交易号',
  `snapshot` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '套餐快照json格式',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序appid',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`order_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 96 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '充值订单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_balance_plan
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_balance_plan`;
CREATE TABLE `xxzmall_balance_plan`  (
  `plan_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `plan_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '套餐名称',
  `money` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '充值金额',
  `give_money` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送金额',
  `real_money` int(11) NULL DEFAULT 0 COMMENT '到账金额',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序(数字越小越靠前)',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序商城id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`plan_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '充值余额套餐表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_category
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_category`;
CREATE TABLE `xxzmall_category`  (
  `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '产品分类id',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级分类id',
  `image_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类图片id',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序方式(数字越小越靠前)',
  `disabled` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否禁用：0-启用，1-禁用',
  `hlb_primary_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '汇乐宝临时保存的商品分类id，无用的字段',
  `params` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '参数名称逗号隔开',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`category_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 42 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '产品分类表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_center_menu
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_center_menu`;
CREATE TABLE `xxzmall_center_menu`  (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单名称',
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片url',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序(越小越靠前)',
  `link_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '跳转链接',
  `name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '链接名称',
  `sys_tag` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标签',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1显示0隐藏',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`menu_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'banner图' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_comment
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_comment`;
CREATE TABLE `xxzmall_comment`  (
  `comment_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '评价id',
  `score` tinyint(3) NULL DEFAULT 10 COMMENT '评分 (10好评 20中评 30差评)',
  `express_score` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT '物流服务评分总分5分',
  `server_score` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT '服务态度评分总分5分',
  `describe_score` tinyint(3) NULL DEFAULT 5 COMMENT '描述评分',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '评价内容',
  `is_picture` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为图片评价',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评价排序',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评价状态(0=待审核 1=审核通过2=审核不通过)',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品id',
  `order_goods_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单商品id',
  `purveyor_id` int(11) NULL DEFAULT 0 COMMENT '供应商id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `is_delete` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '软删除',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`comment_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 39 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单评价记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_comment_image
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_comment_image`;
CREATE TABLE `xxzmall_comment_image`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `comment_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评价id',
  `image_id` int(11) NOT NULL DEFAULT 0 COMMENT '图片id(关联文件记录表)',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单评价图片记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_delivery
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_delivery`;
CREATE TABLE `xxzmall_delivery`  (
  `delivery_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '模板id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模板名称',
  `method` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '计费方式(10按件数 20按重量)',
  `shop_supplier_id` int(11) NULL DEFAULT 0 COMMENT '供应商id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序d',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序方式(数字越小越靠前)',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`delivery_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10023 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '配送模板主表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_delivery_rule
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_delivery_rule`;
CREATE TABLE `xxzmall_delivery_rule`  (
  `rule_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '规则id',
  `delivery_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '配送模板id',
  `region` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '可配送区域(城市id集)',
  `first` double UNSIGNED NOT NULL DEFAULT 0 COMMENT '首件(个)/首重(Kg)',
  `first_fee` decimal(10, 2) UNSIGNED NOT NULL COMMENT '运费(元)',
  `additional` double UNSIGNED NOT NULL DEFAULT 0 COMMENT '续件/续重',
  `additional_fee` decimal(10, 2) UNSIGNED NOT NULL COMMENT '续费(元)',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`rule_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 23 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '配送模板区域及运费表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_elder_caller
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_elder_caller`;
CREATE TABLE `xxzmall_elder_caller`  (
  `caller_id` int(11) NOT NULL AUTO_INCREMENT,
  `caller_type` tinyint(1) NULL DEFAULT 1 COMMENT '通话类型',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '开始时间',
  `initiative_m` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '主号',
  `passive_m` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '被叫号',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类型',
  `caller_context` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '通话内容',
  `caller_duration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '通话时长记录多少秒',
  `callStatus` tinyint(1) NULL DEFAULT 1 COMMENT '1成功2失败',
  PRIMARY KEY (`caller_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_goods
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_goods`;
CREATE TABLE `xxzmall_goods`  (
  `goods_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '产品id',
  `product_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '产品名称',
  `product_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '产品一口价',
  `line_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '产品划线价',
  `product_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '产品编码',
  `product_stock` int(10) NULL DEFAULT 0 COMMENT '产品总库存',
  `video_id` int(11) NULL DEFAULT 0 COMMENT '视频id',
  `poster_id` int(11) NULL DEFAULT 0 COMMENT '视频封面id',
  `selling_point` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品卖点',
  `category_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品分类id',
  `spec_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品规格(10单规格 20多规格)',
  `deduct_stock_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 20 COMMENT '库存计算方式(10下单减库存 20付款减库存)',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '产品详情',
  `sales_initial` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '初始销量',
  `sales_actual` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实际销量',
  `product_sort` int(11) UNSIGNED NOT NULL DEFAULT 100 COMMENT '产品排序(数字越小越靠前)',
  `delivery_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '配送模板id',
  `is_points_gift` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否开启积分赠送(1开启 0关闭)',
  `is_points_discount` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否允许使用积分抵扣(1允许 0不允许)',
  `max_points_discount` int(11) NULL DEFAULT 0 COMMENT '最大积分抵扣数量',
  `is_enable_grade` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否开启会员折扣(1开启 0关闭)',
  `is_alone_grade` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员折扣设置(0默认等级折扣 1单独设置折扣)',
  `alone_grade_equity` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '单独设置折扣的配置',
  `alone_grade_type` tinyint(3) NULL DEFAULT 10 COMMENT '折扣金额类型(10百分比 20固定金额)',
  `is_agent` tinyint(3) NULL DEFAULT 0 COMMENT '是否参加分销0否1是',
  `is_ind_agent` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启单独分销(0关闭 1开启)',
  `agent_money_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '分销佣金类型(10百分比 20固定金额)',
  `first_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '分销佣金(一级)',
  `second_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '分销佣金(二级)',
  `third_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '分销佣金(三级)',
  `product_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '产品状态(10销售中 20仓库中 30回收站)',
  `audit_status` tinyint(3) NULL DEFAULT 0 COMMENT '审核状态0待审核10审核通过20审核未通过30强制下架40草稿',
  `audit_remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '审核备注',
  `view_times` int(11) NULL DEFAULT 0 COMMENT '访问次数',
  `purveyor_id` int(11) NULL DEFAULT 0 COMMENT '供应商id',
  `supplier_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '供应商价格',
  `is_virtual` tinyint(3) NULL DEFAULT 0 COMMENT '是否虚拟，0否1是',
  `is_verify` tinyint(3) NULL DEFAULT 0 COMMENT '是否是计次商品，0否1是',
  `verify_num` int(11) NULL DEFAULT 0 COMMENT '可核销次数，0不限次数',
  `verify_limit_type` tinyint(3) NULL DEFAULT 0 COMMENT '0永久有效 1指定日期有效 2购买n天后有效 3首次使用后n天有效',
  `verify_days` int(11) NULL DEFAULT 0 COMMENT 'n天',
  `verify_enddate` int(11) NULL DEFAULT 0 COMMENT '核销有效期',
  `store_ids` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '支持购买的门店id，逗号分隔，为空代表支持所有门店',
  `limit_num` int(11) NULL DEFAULT 0 COMMENT '限购数量0为不限',
  `grade_ids` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '可购买会员等级id，逗号隔开',
  `virtual_auto` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否自动发货1自动0手动',
  `virtual_content` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '虚拟物品内容',
  `is_picture` tinyint(3) NULL DEFAULT 0 COMMENT '详情是否纯图0否1是',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `hlb_primary_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '汇乐宝临时保存的商品分类id，无用的字段',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `product_type` int(1) NOT NULL DEFAULT 1 COMMENT '1实物2是虚拟3是计次',
  `is_selfmention` int(1) NOT NULL DEFAULT 0 COMMENT '1是0否',
  `is_deduct` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否允许抵扣：0-允许，1-不允许',
  `deduct_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '抵扣比例：0-默认比例，1-自定义比例',
  `customize_deduct` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '自定义抵扣：0-抵扣金额，1-抵扣比例',
  `deduct_money_setting` decimal(10, 2) UNSIGNED NOT NULL COMMENT '抵扣金额',
  `deduct_discount_setting` decimal(10, 2) UNSIGNED NOT NULL COMMENT '抵扣比例',
  `band_coupon` int(11) NULL DEFAULT NULL COMMENT '绑定的优惠券id',
  `is_gift` tinyint(1) NULL DEFAULT 0 COMMENT '是否赠品',
  `is_open_gift` tinyint(1) NULL DEFAULT 0 COMMENT '是否开启赠品',
  `category_params_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '参数名和值',
  `benefit_id` int(11) NOT NULL DEFAULT 0 COMMENT '权益id',
  PRIMARY KEY (`goods_id`) USING BTREE,
  INDEX `category_id`(`category_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1013 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_goods_image
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_goods_image`;
CREATE TABLE `xxzmall_goods_image`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品id',
  `image_id` int(11) NOT NULL COMMENT '图片id(关联文件记录表)',
  `image_type` tinyint(3) NULL DEFAULT 0 COMMENT '图片类型0商品主图，1详情图',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1240 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品图片记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_goods_sku
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_goods_sku`;
CREATE TABLE `xxzmall_goods_sku`  (
  `goods_sku_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '产品规格id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品id',
  `spec_sku_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '产品sku记录索引 (由规格id组成)',
  `image_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '规格图片id',
  `product_no` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '产品编码',
  `product_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '产品价格',
  `line_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '产品划线价',
  `low_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '产品底价',
  `stock_num` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前库存数量',
  `product_sales` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品销量(废弃)',
  `product_weight` double UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品重量(Kg)',
  `supplier_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '供应商价格',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`goods_sku_id`) USING BTREE,
  UNIQUE INDEX `sku_idx`(`goods_id`, `spec_sku_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1711 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品规格表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_goods_spec_rel
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_goods_spec_rel`;
CREATE TABLE `xxzmall_goods_spec_rel`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品id',
  `spec_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '规格组id',
  `spec_value_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '规格值id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1049 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品与规格值关系记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_home
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_home`;
CREATE TABLE `xxzmall_home`  (
  `home_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '页面id',
  `page_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '页面类型(10首页 20自定义页)',
  `page_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '页面名称',
  `page_data` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '页面数据',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否设置首页1是',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'appid',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '软删除',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`home_id`) USING BTREE,
  INDEX `app_id`(`app_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10038 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'diy页面表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_home_category
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_home_category`;
CREATE TABLE `xxzmall_home_category`  (
  `app_id` int(11) UNSIGNED NOT NULL COMMENT 'appid',
  `category_style` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '分类页样式(10一级分类[大图] 11一级分类[小图] 20二级分类)',
  `share_title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分享标题',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`app_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'app分类页模板' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order`;
CREATE TABLE `xxzmall_order`  (
  `order_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `order_no` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单号',
  `total_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '商品总金额(不含优惠折扣)',
  `order_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '订单金额(含优惠折扣)',
  `order_grade_money` decimal(10, 2) NOT NULL COMMENT '会员折扣',
  `coupon_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '优惠券id',
  `coupon_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '优惠券抵扣金额',
  `coupon_id_sys` int(11) NULL DEFAULT 0 COMMENT '系统优惠券',
  `coupon_money_sys` decimal(10, 2) NULL DEFAULT NULL COMMENT '平台优惠券抵扣',
  `points_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '积分抵扣金额',
  `points_num` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分抵扣数量',
  `deduct_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '通证抵扣金额',
  `deduct_num` decimal(10, 5) UNSIGNED NOT NULL COMMENT '通证抵扣数量',
  `pay_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '实际付款金额(包含运费)',
  `update_price` decimal(10, 2) NOT NULL COMMENT '后台修改的订单金额（差价）',
  `fullreduce_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '满减金额',
  `fullreduce_remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '满减备注',
  `buyer_remark` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '买家留言',
  `pay_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 20 COMMENT '支付方式(10余额支付 20微信支付)',
  `pay_source` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '支付来源,mp,wx',
  `pay_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '付款状态(10未付款 20已付款)',
  `pay_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '付款时间',
  `pay_end_time` int(11) NULL DEFAULT 0 COMMENT '支付截止时间',
  `delivery_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '配送方式(10快递配送 20上门自提 30无需物流)',
  `extract_store_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '自提门店id',
  `extract_clerk_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '核销店员id',
  `express_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '运费金额',
  `express_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '物流公司id',
  `express_company` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '物流公司',
  `express_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '物流单号',
  `delivery_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '发货状态(10未发货 20已发货)',
  `delivery_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发货时间',
  `receipt_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '收货状态(10未收货 20已收货)',
  `receipt_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收货时间',
  `order_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '订单状态10=>进行中，20=>已经取消，30=>已完成，40=>退换货',
  `points_bonus` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送的积分数量',
  `growth_value_bonus` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送的成长值',
  `is_settled` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单是否已结算(0未结算 1已结算)',
  `transaction_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信支付交易号',
  `is_comment` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已评价(0否 1是)',
  `order_source` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '订单来源(10普通 20积分 30拼团 40砍价 50秒杀 60礼包购 70;服务，80：卡项,90品牌日,110创业分红订单)',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `is_refund` tinyint(3) NULL DEFAULT 0 COMMENT '拼团等活动失败退款',
  `assemble_status` tinyint(3) NULL DEFAULT 0 COMMENT '拼团状态 10拼单中 20拼单成功 30拼单失败',
  `activity_id` int(11) NULL DEFAULT 0 COMMENT '活动id，对应拼团，秒杀，砍价活动id',
  `is_agent` tinyint(3) NULL DEFAULT 0 COMMENT '是否可以分销0否1是',
  `shop_supplier_id` int(11) NULL DEFAULT 0 COMMENT '供应商id',
  `supplier_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '供应商结算金额,支付金额-平台结算金额',
  `sys_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '平台结算金额',
  `coupon_id_agent` int(11) NULL DEFAULT 0 COMMENT '分销商优惠券',
  `coupon_money_agent` decimal(10, 2) NULL DEFAULT NULL COMMENT '分销商优惠券抵扣',
  `agent_id` int(11) NOT NULL DEFAULT 0 COMMENT '分销商id',
  `pay_method` tinyint(1) NOT NULL DEFAULT 1 COMMENT '付款方式1用户2分销商',
  `total_pv` decimal(10, 2) NULL DEFAULT NULL COMMENT '分销金额',
  `room_id` int(11) NULL DEFAULT 0 COMMENT '直播间id',
  `cancel_remark` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商家取消订单备注',
  `virtual_auto` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否自动发货1自动0手动',
  `virtual_content` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '虚拟物品内容',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `verify_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '核销码',
  `share_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享用户id',
  `send_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '发货类型：0-全部发货，1-部分发货',
  `team_id` int(11) NOT NULL DEFAULT 0 COMMENT '团长id',
  `is_bonus` int(1) NULL DEFAULT 0 COMMENT '是否可以分红',
  `order_remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单备注',
  `cancel_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请取消时间',
  `benefit_card_id` int(11) NOT NULL DEFAULT 0 COMMENT '旅游卡id（旅游卡兑换旅游商品）',
  `benefit_card_money` decimal(10, 2) NOT NULL COMMENT '权益卡抵扣金额',
  PRIMARY KEY (`order_id`) USING BTREE,
  UNIQUE INDEX `order_no`(`order_no`) USING BTREE,
  INDEX `pay_status`(`pay_status`) USING BTREE,
  INDEX `order_status`(`order_status`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 590 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order_address
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order_address`;
CREATE TABLE `xxzmall_order_address`  (
  `order_address_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '地址id',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '联系电话',
  `province_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在省份id',
  `city_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在城市id',
  `region_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在区id',
  `detail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`order_address_id`) USING BTREE,
  INDEX `order_id`(`order_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 444 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单收货地址记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order_extract
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order_extract`;
CREATE TABLE `xxzmall_order_extract`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单id',
  `linkman` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '联系电话',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `order_id`(`order_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 36 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '自提订单联系方式记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order_goods`;
CREATE TABLE `xxzmall_order_goods`  (
  `order_goods_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品id',
  `product_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `category_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类id',
  `image_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品封面图id',
  `deduct_stock_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 20 COMMENT '库存计算方式(10下单减库存 20付款减库存)',
  `spec_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '规格类型(10单规格 20多规格)',
  `spec_sku_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品sku标识',
  `goods_sku_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品规格id',
  `product_attr` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品规格信息',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品详情',
  `product_no` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品编码',
  `product_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '商品价格(单价)',
  `line_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '商品划线价',
  `pv` decimal(10, 2) NULL DEFAULT NULL COMMENT '商品pv',
  `product_weight` double UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品重量(Kg)',
  `is_user_grade` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否存在会员等级折扣',
  `grade_ratio` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员折扣比例(0-10)',
  `grade_product_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '会员折扣的商品单价',
  `grade_total_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '会员折扣的总额差',
  `coupon_money_sys` decimal(10, 2) NULL DEFAULT NULL COMMENT '平台优惠券抵扣',
  `coupon_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '优惠券折扣金额',
  `points_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '积分金额',
  `points_num` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分抵扣数量',
  `deduct_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '通证抵扣金额',
  `deduct_num` decimal(10, 5) UNSIGNED NOT NULL COMMENT '通证抵扣数量',
  `points_bonus` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送的积分数量',
  `growth_value_bonus` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送的成长值',
  `is_gift` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否赠送通证：0-否，1-是',
  `gift_amount` decimal(10, 5) UNSIGNED NOT NULL COMMENT '赠送通证数量',
  `total_num` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '购买数量',
  `total_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '商品总价(数量×单价)',
  `total_pay_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '实际付款价(折扣和优惠后)',
  `total_pv` decimal(10, 2) NULL DEFAULT NULL COMMENT '总pv',
  `supplier_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '供应商金额',
  `sys_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '平台结算金额',
  `fullreduce_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '满减金额',
  `is_agent` tinyint(3) NULL DEFAULT 0 COMMENT '是否开启分销0否1是',
  `is_ind_agent` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启单独分销(0关闭 1开启)',
  `agent_money_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '分销佣金类型(10百分比 20固定金额)',
  `first_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '分销佣金(一级)',
  `second_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '分销佣金(二级)',
  `third_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '分销佣金(三级)',
  `is_comment` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已评价(0否 1是)',
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `goods_source_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源商品记录id',
  `sku_source_id` int(11) NULL DEFAULT 0 COMMENT '来源商品sku id',
  `bill_source_id` int(11) NULL DEFAULT 0 COMMENT '拼团等的拼团订单id',
  `virtual_content` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '虚拟物品内容',
  `coupon_money_agent` decimal(10, 2) NULL DEFAULT NULL COMMENT '分销商优惠券抵扣',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `order_source` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品来源（0：product,1:server,2:卡项）',
  `card_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联卡项id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `server_min` int(11) NOT NULL DEFAULT 0 COMMENT '服务时长/每分钟',
  `product_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1实物2是虚拟3是计次',
  `verify_num` int(11) NOT NULL DEFAULT 0 COMMENT '可核销次数，0不限次数',
  `already_verify` int(11) NOT NULL DEFAULT 0 COMMENT '已核销次数',
  `verify_limit_type` tinyint(3) NOT NULL DEFAULT 0 COMMENT '0永久有效 1指定日期有效 2购买n天后有效 3首次使用后n天有效',
  `verify_days` int(11) NOT NULL DEFAULT 0 COMMENT 'n天',
  `verify_enddate` int(11) NOT NULL DEFAULT 0 COMMENT '核销有效期',
  `store_ids` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支持购买的门店id，逗号分隔，为空代表支持所有门店',
  `verify_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '核销码',
  `send_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '发货类型：0-全部发货，1-部分发货',
  `express_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '物流公司id',
  `express_company` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '物流公司',
  `express_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '物流单号',
  `delivery_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发货时间',
  `is_gift_product` tinyint(1) NULL DEFAULT 0 COMMENT '是否为赠品',
  `gift_stages` smallint(5) NOT NULL DEFAULT 0 COMMENT '赠送通证期数（0和1为不分期发放）',
  `already_stages` smallint(5) NOT NULL DEFAULT 0 COMMENT '已发放期数',
  `benefit_card_id` int(11) NOT NULL DEFAULT 0 COMMENT '旅游卡id（旅游卡兑换旅游商品）',
  `benefit_id` int(11) NOT NULL DEFAULT 0 COMMENT 'l旅游商品绑定权益id',
  `card_order_product_id` int(11) NOT NULL DEFAULT 0 COMMENT '旅游商品抵扣的权益卡订单id',
  `benefit_card_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '权益抵扣金额',
  PRIMARY KEY (`order_goods_id`) USING BTREE,
  INDEX `order_id`(`order_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 609 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单商品记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order_refund
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order_refund`;
CREATE TABLE `xxzmall_order_refund`  (
  `order_refund_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '售后单id',
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单id',
  `order_goods_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单商品id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '售后类型(10退货退款 20换货 30退款)',
  `apply_desc` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户申请原因(说明)',
  `is_agree` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商家审核状态(0待审核 10已同意 20已拒绝)',
  `refuse_desc` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商家拒绝原因(说明)',
  `refund_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '实际退款金额',
  `is_user_send` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户是否发货(0未发货 1已发货)',
  `send_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户发货时间',
  `express_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户发货物流公司id',
  `express_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户发货物流单号',
  `is_receipt` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商家收货状态(0未收货 1已收货)',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '售后单状态(0进行中 10已拒绝 20已完成 30已取消)',
  `shop_supplier_id` int(11) NULL DEFAULT 0 COMMENT '供应商id',
  `plate_status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '10申请平台介入20同意30拒绝',
  `plate_desc` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '平台备注',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`order_refund_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 67 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '售后单记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order_refund_address
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order_refund_address`;
CREATE TABLE `xxzmall_order_refund_address`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_refund_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '售后单id',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '联系电话',
  `detail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '售后单退货地址记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order_refund_image
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order_refund_image`;
CREATE TABLE `xxzmall_order_refund_image`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_refund_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '售后单id',
  `image_id` int(11) NOT NULL DEFAULT 0 COMMENT '图片id(关联文件记录表)',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '售后单图片记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order_refund_log
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order_refund_log`;
CREATE TABLE `xxzmall_order_refund_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_refund_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '售后单id',
  `status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '处理状态：0-待审核，1-已审核，2-已寄回，3-退款中，4-已完成，5-已发出，6-已收货',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '操作说明',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '售后单处理进度表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order_settled
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order_settled`;
CREATE TABLE `xxzmall_order_settled`  (
  `settled_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NULL DEFAULT 0 COMMENT '订单号',
  `shop_supplier_id` int(11) NULL DEFAULT 0 COMMENT '供应商id',
  `order_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '订单金额，不包括运费',
  `pay_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '支付金额',
  `express_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '运费',
  `supplier_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '店铺金额',
  `real_supplier_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '供应商实际结算金额',
  `sys_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '平台抽成',
  `real_sys_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '平台实际结算金额',
  `agent_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '分销佣金',
  `settled_agent_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '已结算分销佣金',
  `refund_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '退款金额',
  `refund_supplier_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '供应商退款金额',
  `refund_sys_money` decimal(10, 2) NULL DEFAULT NULL COMMENT '平台退款结算金额',
  `app_id` int(11) NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`settled_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 102 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单结算表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order_trade
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order_trade`;
CREATE TABLE `xxzmall_order_trade`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `out_trade_no` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '外部交易号',
  `order_id` int(11) NULL DEFAULT 0 COMMENT '订单号',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `app_id` int(11) NULL DEFAULT 0,
  `pay_status` tinyint(3) NULL DEFAULT 10 COMMENT '支付状态10,未支付,20已支付',
  `pay_time` int(11) NULL DEFAULT 0 COMMENT '支付时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '外部交易号跟内部订单对应关系表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_order_travelers
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_order_travelers`;
CREATE TABLE `xxzmall_order_travelers`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `order_goods_id` int(11) NOT NULL DEFAULT 0 COMMENT '订单商品表id',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `id_card` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '身份证',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0：待核销，1：已核销，2：无效',
  `verify_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '核销码',
  `app_id` int(11) NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `card_order_product_id` int(11) NOT NULL DEFAULT 0 COMMENT '旅游商品抵扣的权益卡订单id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 119 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单出行人' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_plus_category
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_plus_category`;
CREATE TABLE `xxzmall_plus_category`  (
  `plus_category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '插件分类id',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '插件分类名称',
  `image_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类图片id',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序方式(数字越小越靠前)',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`plus_category_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '插件分类表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_plus_wx_collection
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_plus_wx_collection`;
CREATE TABLE `xxzmall_plus_wx_collection`  (
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0=》关，1=》开',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品收藏记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_printer
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_printer`;
CREATE TABLE `xxzmall_printer`  (
  `printer_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '打印机id',
  `printer_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '打印机名称',
  `printer_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '打印机类型',
  `printer_config` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '打印机配置',
  `print_times` smallint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '打印联数(次数)',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序 (数字越小越靠前)',
  `is_delete` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除0=显示1=隐藏',
  `shop_supplier_id` int(11) NOT NULL COMMENT '商户id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`printer_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '小票打印机记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_region
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_region`;
CREATE TABLE `xxzmall_region`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(11) NULL DEFAULT NULL COMMENT '父id',
  `shortname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '简称',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `merger_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '全称',
  `level` tinyint(4) UNSIGNED NULL DEFAULT 0 COMMENT '层级 1 2 3 省市区县',
  `pinyin` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '拼音',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '长途区号',
  `zip_code` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮编',
  `first` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '首字母',
  `lng` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '经度',
  `lat` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '纬度',
  `sort` int(11) NULL DEFAULT 100 COMMENT '排序',
  `is_delete` tinyint(3) NULL DEFAULT 0 COMMENT '是否删除0否1是',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `name,level`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4018 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_return_address
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_return_address`;
CREATE TABLE `xxzmall_return_address`  (
  `address_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '退货地址id',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '联系电话',
  `detail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序 (数字越小越靠前)',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除0=显示1=隐藏',
  `purveyor_id` int(11) NOT NULL COMMENT '商城id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`address_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '退货地址记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_screen_statistics
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_screen_statistics`;
CREATE TABLE `xxzmall_screen_statistics`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '大屏地区统计表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for xxzmall_settings
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_settings`;
CREATE TABLE `xxzmall_settings`  (
  `key` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设置项标示',
  `describe` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '设置项描述',
  `values` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设置内容（json格式）',
  `shop_supplier_id` int(11) NOT NULL DEFAULT 0 COMMENT '商户id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  UNIQUE INDEX `unique_key`(`app_id`, `key`, `shop_supplier_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商城设置记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_shop_access
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_shop_access`;
CREATE TABLE `xxzmall_shop_access`  (
  `access_id` int(11) UNSIGNED NOT NULL COMMENT '主键id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '权限名称',
  `path` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '路由地址',
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级id',
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序(数字越小越靠前)',
  `icon` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '菜单图标',
  `redirect_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '重定向名称',
  `is_route` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是路由 0=不是1=是',
  `is_menu` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是菜单 0不是 1是',
  `alias` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '别名(废弃)',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示1=显示0=不显示',
  `plus_category_id` int(11) NULL DEFAULT 0 COMMENT '插件分类id',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '描述',
  `app_id` int(10) UNSIGNED NULL DEFAULT 10001 COMMENT 'app_id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`access_id`) USING BTREE,
  UNIQUE INDEX `idx_path`(`path`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商家用户权限表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_shop_fullreduce
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_shop_fullreduce`;
CREATE TABLE `xxzmall_shop_fullreduce`  (
  `fullreduce_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `active_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '活动名称',
  `full_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '满类型1，满额2，满件数',
  `full_value` int(11) NULL DEFAULT 0 COMMENT '满值',
  `reduce_type` tinyint(3) NULL DEFAULT 1 COMMENT '减类型，1，减金额 2，打折',
  `reduce_value` int(11) NULL DEFAULT 0 COMMENT '减值',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=显示1=伪删除',
  `purveyor_id` int(11) NOT NULL DEFAULT 0 COMMENT '商户id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`fullreduce_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10035 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '满减设置表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_shop_login_log
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_shop_login_log`;
CREATE TABLE `xxzmall_shop_login_log`  (
  `login_log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `ip` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '登录ip',
  `result` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '登录结果',
  `app_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '签到时间',
  PRIMARY KEY (`login_log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2838 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员登录记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_shop_opt_log
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_shop_opt_log`;
CREATE TABLE `xxzmall_shop_opt_log`  (
  `opt_log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `shop_user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '标题',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '访问url',
  `request_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '请求类型',
  `browser` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '浏览器',
  `agent` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '浏览器信息',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '操作内容',
  `ip` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '登录ip',
  `app_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '签到时间',
  PRIMARY KEY (`opt_log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 125792 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员操作记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_shop_role
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_shop_role`;
CREATE TABLE `xxzmall_shop_role`  (
  `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `role_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序(数字越小越靠前)',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商家用户角色表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_shop_role_access
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_shop_role_access`;
CREATE TABLE `xxzmall_shop_role_access`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `role_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色id',
  `access_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '权限id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `role_id`(`role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6514 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商家用户角色权限关系表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_shop_user
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_shop_user`;
CREATE TABLE `xxzmall_shop_user`  (
  `shop_user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '登录密码',
  `real_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `is_super` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为超级管理员0不是,1是',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=显示1=伪删除',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`shop_user_id`) USING BTREE,
  INDEX `user_name`(`user_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10049 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商家用户记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_shop_user_role
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_shop_user_role`;
CREATE TABLE `xxzmall_shop_user_role`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `shop_user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '超管用户id',
  `role_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admin_user_id`(`shop_user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 55 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商家用户角色记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_sms
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_sms`;
CREATE TABLE `xxzmall_sms`  (
  `sms_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `mobile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `code` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '验证码',
  `sence` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '场景,login，apply',
  `app_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '签到时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`sms_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 754 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户注册短信表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_spec
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_spec`;
CREATE TABLE `xxzmall_spec`  (
  `spec_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '规格组id',
  `spec_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规格组名称',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`spec_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 47 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品规格组记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_spec_value
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_spec_value`;
CREATE TABLE `xxzmall_spec_value`  (
  `spec_value_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '规格值id',
  `spec_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规格值',
  `spec_id` int(11) NOT NULL COMMENT '规格组id',
  `app_id` int(11) NOT NULL COMMENT '应用id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`spec_value_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 289 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品规格值记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_table
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_table`;
CREATE TABLE `xxzmall_table`  (
  `table_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'json格式',
  `sort` tinyint(3) NULL DEFAULT 100 COMMENT '排序',
  `total_count` int(11) NULL DEFAULT 0 COMMENT '数量',
  `is_delete` tinyint(3) NULL DEFAULT 0 COMMENT '是否删除0否1是',
  `app_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`table_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '万能表单' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_table_record
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_table_record`;
CREATE TABLE `xxzmall_table_record`  (
  `table_record_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `table_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '名称',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'json格式',
  `user_id` int(11) NULL DEFAULT 0 COMMENT '用户id',
  `is_delete` tinyint(3) NULL DEFAULT 0 COMMENT '是否删除0否1是',
  `app_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`table_record_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '万能表单记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_tag
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_tag`;
CREATE TABLE `xxzmall_tag`  (
  `tag_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `tag_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '标签名称',
  `app_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`tag_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户tag表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_trade
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_trade`;
CREATE TABLE `xxzmall_trade`  (
  `trade_id` int(11) NOT NULL AUTO_INCREMENT,
  `trade_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '行业名称',
  `thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标签',
  `sort` int(11) NOT NULL COMMENT '行业排序',
  `create_time` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`trade_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 23 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '行业表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_trade_access
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_trade_access`;
CREATE TABLE `xxzmall_trade_access`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `trade_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '行业id',
  `access_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '权限id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '类型0shop/1supplier',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `role_id`(`trade_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24917 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商家所选行业权限关系表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_upload_file
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_upload_file`;
CREATE TABLE `xxzmall_upload_file`  (
  `file_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文件id',
  `storage` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '存储方式',
  `group_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件分组id',
  `file_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '存储域名',
  `save_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '保存路径',
  `file_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件路径',
  `file_size` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小(字节)',
  `file_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件类型',
  `real_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '文件真实名',
  `extension` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件扩展名',
  `is_user` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为c端用户上传',
  `is_recycle` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收',
  `purveyor_id` int(11) NULL DEFAULT 0 COMMENT '供应商id',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '软删除',
  `app_id` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`file_id`) USING BTREE,
  UNIQUE INDEX `path_idx`(`file_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13098 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '文件库记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_upload_group
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_upload_group`;
CREATE TABLE `xxzmall_upload_group`  (
  `group_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `group_type` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件类型',
  `group_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类排序(数字越小越靠前)',
  `purveyor_id` int(11) NULL DEFAULT 0 COMMENT '供应商id',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`group_id`) USING BTREE,
  INDEX `type_index`(`group_type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 40 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '文件库分组记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user`;
CREATE TABLE `xxzmall_user`  (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `open_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信openid(唯一标示)',
  `mpopen_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '微信公众号openid',
  `appopen_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'openappid',
  `union_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '微信开放平台id',
  `reg_source` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '注册来源',
  `nickName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '微信昵称',
  `weixin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '微信号',
  `age` int(11) NULL DEFAULT NULL COMMENT '年龄',
  `id_card` char(18) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '证件号',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `avatarUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信头像',
  `gender` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别0=女1=男2=未知',
  `country` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '国家',
  `province` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '城市',
  `area` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '区域',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `realname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `birthday` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '生日',
  `address_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '默认收货地址',
  `balance` decimal(10, 2) UNSIGNED NOT NULL COMMENT '用户可用余额',
  `bonus` decimal(10, 2) UNSIGNED NOT NULL COMMENT '用户赠金',
  `points` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户可用积分',
  `growth_value` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户成长值',
  `pay_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '用户总支付的金额',
  `expend_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '实际消费的金额(不含退款)',
  `grade_id` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT '会员等级id',
  `referee_id` int(11) NOT NULL DEFAULT 0 COMMENT '推荐人id',
  `total_points` int(11) NULL DEFAULT 0 COMMENT '累计积分',
  `total_invite` int(11) NULL DEFAULT 0 COMMENT '总邀请人数',
  `user_type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '供应商状态1普通用户2供应商,10:游客',
  `gift_money` int(11) NULL DEFAULT 0 COMMENT '虚拟币，刷礼物',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `face_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '人脸认证时间',
  `face_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否人脸识别注册(0：否，1：是)',
  `face_token` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '人脸token',
  `face_pic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '人脸图片',
  `equipment_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '设备id',
  `telephone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '固定电话',
  `shopopen_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商城小程序open_id',
  PRIMARY KEY (`user_id`) USING BTREE,
  INDEX `openid`(`open_id`) USING BTREE,
  INDEX `face_status`(`face_status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 148103 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_address
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_address`;
CREATE TABLE `xxzmall_user_address`  (
  `address_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '联系电话',
  `province_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在省份id',
  `city_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在城市id',
  `region_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在区id',
  `province` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '所在省份',
  `city` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '所在城市',
  `region` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '所在区',
  `district` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '新市辖区(该字段用于记录region表中没有的市辖区)',
  `detail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `door` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '门牌号',
  `lng` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '经度',
  `lat` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '纬度',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `gender` tinyint(1) NULL DEFAULT 1 COMMENT '1男2女',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '1删除',
  `is_default` tinyint(1) NULL DEFAULT 0 COMMENT '1默认',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `tag` tinyint(1) NULL DEFAULT NULL COMMENT '1家2公司',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`address_id`) USING BTREE,
  INDEX `user_id_delete`(`user_id`, `is_delete`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `is_delete`(`is_delete`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 707 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户收货地址表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_balance_log
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_balance_log`;
CREATE TABLE `xxzmall_user_balance_log`  (
  `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `scene` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '余额变动场景(10用户充值 20用户消费 30管理员操作 40订单退款)',
  `money` decimal(10, 2) NOT NULL COMMENT '变动金额',
  `describe` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '描述/说明',
  `remark` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '管理员备注',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序商城id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 802 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户余额变动明细表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_favorite
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_favorite`;
CREATE TABLE `xxzmall_user_favorite`  (
  `favorite_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `pid` int(11) NOT NULL COMMENT '商品/店铺id',
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '10店铺20商品',
  `purveyor_id` int(11) NULL DEFAULT 0 COMMENT '供应商id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`favorite_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 236 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '我的收藏关注' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_grade
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_grade`;
CREATE TABLE `xxzmall_user_grade`  (
  `grade_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '等级ID',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '等级名称',
  `open_money` tinyint(3) NULL DEFAULT 0 COMMENT '是否开放0，否1是',
  `upgrade_money` int(11) NOT NULL DEFAULT 0 COMMENT '升级条件',
  `open_points` tinyint(3) NULL DEFAULT 0 COMMENT '积分是否开放0否1是',
  `upgrade_points` int(11) NULL DEFAULT 0 COMMENT '累计积分升级',
  `open_grow` tinyint(3) NULL DEFAULT 0 COMMENT '成长值是否开放0否1是',
  `upgrade_grow` int(11) NULL DEFAULT 0 COMMENT '成长值升级',
  `open_invite` tinyint(3) NULL DEFAULT 0 COMMENT '邀请是否开放0否1是',
  `upgrade_invite` int(11) NULL DEFAULT 0 COMMENT '邀请人数升级',
  `equity` int(11) NOT NULL DEFAULT 100 COMMENT '等级权益,百分比',
  `is_default` tinyint(3) NULL DEFAULT 0 COMMENT '是否默认，1是，0否',
  `remark` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '备注',
  `weight` tinyint(3) NULL DEFAULT 100 COMMENT '权重',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`grade_id`) USING BTREE,
  INDEX `app_id`(`app_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户会员等级表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_grade_log
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_grade_log`;
CREATE TABLE `xxzmall_user_grade_log`  (
  `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `old_grade_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '变更前的等级id',
  `new_grade_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '变更后的等级id',
  `change_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '变更类型(10后台管理员设置 20自动升级)',
  `remark` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '管理员备注',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户会员等级变更记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_growth_log
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_growth_log`;
CREATE TABLE `xxzmall_user_growth_log`  (
  `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `value` int(11) NOT NULL DEFAULT 0 COMMENT '变动数量',
  `describe` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '描述/说明',
  `remark` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '管理员备注',
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '来源：0-下单，1-签到，2-注册，3-文章阅读，4-文章转发，5-投稿成功，6-补签，7-邀请注册，8-活动报名签到发放奖励',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序商城id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 150479 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户成长值明细表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_login_log
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_login_log`;
CREATE TABLE `xxzmall_user_login_log`  (
  `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员id',
  `successions` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '连续登陆天数',
  `maxsuccessions` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最大连续登陆天数',
  `prevtime` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上次登录时间',
  `loginip` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录IP',
  `token` char(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '登录令牌',
  `createtime` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  PRIMARY KEY (`log_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '会员登录日志' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_push
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_push`;
CREATE TABLE `xxzmall_user_push`  (
  `push_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员id',
  `push_number` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '推送次数',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '推送标识（0：未推送，1：已推送，2：推送失败）',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '编辑时间',
  PRIMARY KEY (`push_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '会员推送表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_push_log
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_push_log`;
CREATE TABLE `xxzmall_user_push_log`  (
  `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `push_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '推送id',
  `realname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '推送名称',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '推送手机号',
  `push_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '推送返回编码',
  `push_msg` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '推送返回信息',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '推送时间',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '推送日志' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_tag
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_tag`;
CREATE TABLE `xxzmall_user_tag`  (
  `user_tag_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `tag_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标签id',
  `app_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '小程序id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '签到时间',
  PRIMARY KEY (`user_tag_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户标签表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_user_visit
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_user_visit`;
CREATE TABLE `xxzmall_user_visit`  (
  `visit_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `purveyor_id` int(11) NOT NULL COMMENT '供应商id',
  `goods_id` int(11) NOT NULL DEFAULT 0 COMMENT '商品id',
  `content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '访问内容',
  `visitcode` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '访客id',
  `app_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '程序id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`visit_id`) USING BTREE,
  INDEX `idx_visitcode`(`visitcode`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20373 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户访问记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xxzmall_verify_goods_log
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_verify_goods_log`;
CREATE TABLE `xxzmall_verify_goods_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_goods_id` int(11) NOT NULL COMMENT '核销单id',
  `verify_num` int(11) NOT NULL DEFAULT 0 COMMENT '核销次数',
  `verify_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0扫码1后台核销',
  `verify_date` int(11) NOT NULL DEFAULT 0 COMMENT '核销日期',
  `app_id` int(11) NOT NULL DEFAULT 0 COMMENT '应用id',
  `store_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '门店id',
  `clerk_id` int(11) NOT NULL DEFAULT 0 COMMENT '核销员id',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:计次商品,1:旅游商品',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 44 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for xxzmall_version
-- ----------------------------
DROP TABLE IF EXISTS `xxzmall_version`;
CREATE TABLE `xxzmall_version`  (
  `version` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '当前版本'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统信息表' ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
