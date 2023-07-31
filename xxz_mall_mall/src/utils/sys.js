/* 组织类型 */
export function organTypeList() {
  return [
    {
      'key': '1',
      'val': '企业'
    },
    {
      'key': '2',
      'val': '政府/事业单位/社会组织'
    }
  ]
}

/* 状态 */
export function enabledList() {
  return [
    {
      'key': '1',
      'val': '显示'
    },
    {
      'key': '0',
      'val': '隐藏'
    }
  ]
}

/* 状态 */
export function disableList() {
  return [
    {
      'key': '1',
      'val': '已启用'
    },
    {
      'key': '0',
      'val': '已禁用'
    }
  ]
}

/* 状态 */
export function shelfList() {
  return [
    {
      'key': '1',
      'val': '上架中'
    },
    {
      'key': '0',
      'val': '已下架'
    }
  ]
}

/* 状态 */
export function shelfTempList() {
  return [
    {
      'key': '1',
      'val': '上架中'
    },
    {
      'key': '2',
      'val': '已下架'
    }
  ]
}

/* 状态 */
export function shelfTempOneList() {
  return [
    {
      'key': '1',
      'val': '已上架'
    },
    {
      'key': '0',
      'val': '未上架'
    }
  ]
}

/* 创建来源 */
export function createSourceList() {
  return [
    {
      'key': '1',
      'val': '总部创建'
    },
    {
      'key': '2',
      'val': '供应商创建'
    }
  ]
}

/* 审核状态 */
export function auditList() {
  return [
    {
      'key': '1',
      'val': '审核通过'
    },
    {
      'key': '-1',
      'val': '待审核'
    },
    {
      'key': '-2',
      'val': '审核拒绝'
    }
  ]
}

/* 审核状态 */
export function auditStatusList() {
  return [
    {
      'key': '0',
      'val': '审核中'
    },
    {
      'key': '10',
      'val': '审核通过'
    },
    {
      'key': '20',
      'val': '审核拒绝'
    }
  ]
}

/* 服务状态 */
export function serverStatusList() {
  return [
    {
      'key': '1',
      'val': '进行中'
    },
    {
      'key': '2',
      'val': '未开始'
    },
    {
      'key': '3',
      'val': '已结束'
    }
  ]
}

/* 签到状态 */
export function signupList() {
  return [
    {
      'key': '1',
      'val': '待服务'
    },
    {
      'key': '2',
      'val': '服务中'
    },
    {
      'key': '3',
      'val': '待评价'
    },
    {
      'key': '4',
      'val': '已完成'
    },
    {
      'key': '-1',
      'val': '待审核'
    },
    {
      'key': '-2',
      'val': '审核拒绝'
    }
  ]
}

/* 订单状态 */
export function orderStatusList() {
  return [
    {
      'key': '-3',
      'val': '已取消'
    },
    {
      'key': '-2',
      'val': '待服务'
    },
    {
      'key': '-1',
      'val': '待服务'
    },
    {
      'key': '2',
      'val': '服务中'
    },
    {
      'key': '3',
      'val': '待评价'
    },
    {
      'key': '1',
      'val': '已完成'
    },
    {
      'key': '4',
      'val': '已关闭'
    }
  ]
}

/* 时间类型 */
export function timeTypeList() {
  return [
    {
      'key': '1',
      'val': '下单时间'
    },
    {
      'key': '2',
      'val': '完成时间'
    },
    {
      'key': '3',
      'val': '关闭时间'
    }
  ]
}

/* 时间类型 */
export function timeFieldList() {
  return [
    {
      'key': '1',
      'val': '下单时间'
    },
    {
      'key': '2',
      'val': '付款时间'
    },
    {
      'key': '3',
      'val': '发货时间'
    },
    {
      'key': '4',
      'val': '完成时间'
    }
  ]
}

/* 服务类型 */
export function serverTypeList() {
  return [
    {
      'key': '1',
      'val': '个人服务'
    },
    {
      'key': '2',
      'val': '多人项目'
    }
  ]
}

/* 账户类型 */
export function accountTypeList() {
  return [
    {
      'key': '1',
      'val': '个人'
    },
    {
      'key': '2',
      'val': '组织'
    },
    {
      'key': '3',
      'val': '平台'
    }
  ]
}

/* 业务类型 */
export function tradeTypeList() {
  return [
    {
      'key': '6',
      'val': '个人服务'
    },
    {
      'key': '5',
      'val': '多人项目'
    },
    {
      'key': '8',
      'val': 'TIME/CFP(转入)'
    },
    {
      'key': '7',
      'val': 'CFP/TIME(转出)'
    }
  ]
}

/* 订单来源 */
export function orderComeList() {
  return [
    {
      'key': '1',
      'val': '平台'
    },
    {
      'key': '2',
      'val': '组织'
    },
    {
      'key': '3',
      'val': '移动端'
    }
  ]
}

/* 支付方式 */
export function payTypeList() {
  return [
    {
      'key': '10',
      'val': '余额支付'
    },
    {
      'key': '20',
      'val': '微信支付'
    },
    {
      'key': '30',
      'val': 'Time支付'
    }
  ]
}

/* 配送方式 */
export function deliveryTypeList() {
  return [
    {
      'key': '10',
      'val': '快递'
    },
    {
      'key': '20',
      'val': '自提'
    },
    {
      'key': '30',
      'val': '无需快递'
    }
  ]
}

/* 订单信息 */
export function orderInfoList() {
  return [
    {
      'key': 'ordersn',
      'val': '订单编号'
    },
    {
      'key': 'servertitle',
      'val': '服务名称'
    },
    {
      'key': 'need_id',
      'val': '需求方'
    },
    {
      'key': 'host_name',
      'val': '需求方负责人'
    },
    {
      'key': 'host_mobile',
      'val': '需求方手机号'
    },
    {
      'key': 'member_name',
      'val': '服务方'
    },
    {
      'key': 'member_mobile',
      'val': '服务方手机号'
    },
    {
      'key': 'remark',
      'val': '备注'
    }
  ]
}

/* 搜索订单 */
export function orderSearchList() {
  return [
    {
      'key': 'order_no',
      'val': '订单编号'
    },
    {
      'key': 'product_name',
      'val': '商品名称'
    },
    {
      'key': 'nickName',
      'val': '买家'
    },
    {
      'key': 'mobile',
      'val': '买家手机号'
    }
  ]
}

/* 搜索订单 */
export function orderFieldList() {
  return [
    {
      'key': '1',
      'val': '订单号'
    },
    {
      'key': '2',
      'val': '会员id'
    },
    {
      'key': '3',
      'val': '会员信息'
    },
    {
      'key': '4',
      'val': '收件人信息'
    },
    {
      'key': '5',
      'val': '地址信息'
    },
    {
      'key': '6',
      'val': '快递单号'
    },
    {
      'key': '7',
      'val': '商品名称'
    }
  ]
}

/* 评分等级 */
export function commentLevelList() {
  return [
    {
      'key': '1',
      'val': '一星'
    },
    {
      'key': '2',
      'val': '二星'
    },
    {
      'key': '3',
      'val': '三星'
    },
    {
      'key': '4',
      'val': '四星'
    },
    {
      'key': '5',
      'val': '五星'
    }
  ]
}

/* 评价信息 */
export function evaluationInfoList() {
  return [
    {
      'key': 'server_name',
      'val': '服务名称'
    },
    {
      'key': 'server_ide',
      'val': '服务者'
    },
    {
      'key': 'content',
      'val': '评价内容'
    }
  ]
}

/* 距离信息 */
export function distanceList() {
  return [
    {
      'key': '0.5',
      'val': '500m'
    },
    {
      'key': '1',
      'val': '1km'
    },
    {
      'key': '2',
      'val': '2km'
    },
    {
      'key': '3',
      'val': '3km'
    },
    {
      'key': '4',
      'val': '4km'
    },
    {
      'key': '5',
      'val': '5km'
    }
  ]
}

/* 数据类型 */
export function dataTypeList() {
  return [
    {
      'key': '0',
      'val': '单行文本'
    },
    {
      'key': '1',
      'val': '多行文本'
    },
    {
      'key': '2',
      'val': '下拉框'
    },
    {
      'key': '3',
      'val': '多选框'
    },
    {
      'key': '14',
      'val': '单选框'
    },
    {
      'key': '5',
      'val': '图片'
    },
    {
      'key': '6',
      'val': '身份证号码'
    },
    {
      'key': '7',
      'val': '日期'
    },
    {
      'key': '8',
      'val': '日期范围'
    },
    {
      'key': '9',
      'val': '城市'
    },
    // {
    //   'key': '10',
    //   'val': '确认文本'
    // },
    {
      'key': '11',
      'val': '时间'
    }
    // {
    //   'key': '12',
    //   'val': '时间范围'
    // },
    // {
    //   'key': '13',
    //   'val': '提示文本'
    // }
  ]
}

/* 权重类型 */
export function levelPowerList() {
  return [
    {
      'key': '0',
      'val': '1'
    },
    {
      'key': '1',
      'val': '2'
    },
    {
      'key': '2',
      'val': '3'
    },
    {
      'key': '3',
      'val': '4'
    },
    {
      'key': '4',
      'val': '5'
    },
    {
      'key': '5',
      'val': '6'
    },
    {
      'key': '6',
      'val': '7'
    },
    {
      'key': '7',
      'val': '8'
    },
    {
      'key': '8',
      'val': '9'
    },
    {
      'key': '9',
      'val': '10'
    }
  ]
}

/* 处理方式 */
export function refundTypeList() {
  return [
    {
      'key': '1',
      'val': '退款(仅退款不退货)'
    },
    {
      'key': '2',
      'val': '退货退款'
    },
    {
      'key': '3',
      'val': '换货'
    }
  ]
}

/* 退款原因 */
export function reasonList() {
  return [
    {
      'key': '1',
      'val': '不想要了'
    },
    {
      'key': '2',
      'val': '卖家缺货'
    },
    {
      'key': '3',
      'val': '拍错了/订单信息错误'
    },
    {
      'key': '4',
      'val': '其他'
    }
  ]
}

/* 通道类型 */
export function passageTypeList() {
  return [
    {
      'key': '1',
      'val': '签到'
    },
    {
      'key': '2',
      'val': '消费'
    },
    {
      'key': '3',
      'val': '人脸登记'
    },
    {
      'key': '4',
      'val': '活动核销'
    }
  ]
}

/* 设备类型 */
export function equipmentTypeList() {
  return [
    {
      'key': '1',
      'val': '大屏终端'
    },
    {
      'key': '2',
      'val': '平板'
    },
    {
      'key': '3',
      'val': '手机'
    }
  ]
}

/* 注册方式 */
export function regTypeList() {
  return [
    {
      'key': '1',
      'val': '手机号注册'
    },
    {
      'key': '2',
      'val': '身份证注册'
    }
  ]
}

/* 身份信息 */
export function usermsgList() {
  return [
    {
      'key': 'realname',
      'val': '姓名'
    },
    {
      'key': 'mobile',
      'val': '手机号'
    },
    {
      'key': 'id_card',
      'val': '身份证'
    }
  ]
}

/* 默认值 */
export function defaultValueList() {
  return [
    {
      'key': '0',
      'val': '无'
    },
    {
      'key': '1',
      'val': '自定义'
    },
    {
      'key': '2',
      'val': '姓名'
    },
    {
      'key': '3',
      'val': '电话'
    },
    {
      'key': '4',
      'val': '微信号'
    }
  ]
}

/* 默认值 */
export function defaultDateValueList() {
  return [
    {
      'key': '0',
      'val': '无'
    },
    {
      'key': '1',
      'val': '填写当天'
    },
    {
      'key': '2',
      'val': '特定日期'
    }
  ]
}

/* 报名类型 */
export function verifyComeList() {
  return [
    {
      'key': '0',
      'val': '现场报名'
    },
    {
      'key': '1',
      'val': '系统报名'
    },
    {
      'key': '2',
      'val': '自主报名'
    }
  ]
}

/* 商品属性 */
export function productTypeList() {
  return [
    {
      'key': '1',
      'val': '实物商品'
    },
    {
      'key': '2',
      'val': '虚拟商品'
    },
    {
      'key': '3',
      'val': '计次商品'
    }
  ]
}

/* 订单来源 */
export function orderSourceList() {
  return [
    {
      'key': '10',
      'val': '普通订单'
    },
    {
      'key': '70',
      'val': '服务订单'
    },
    {
      'key': '80',
      'val': '卡项订单'
    },
    {
      'key': '30',
      'val': '拼团订单'
    },
    {
      'key': '40',
      'val': '砍价订单'
    },
    {
      'key': '50',
      'val': '秒杀订单'
    },
    {
      'key': '90',
      'val': '分销订单'
    }
  ]
}

/* 获取上方的val */
export function getVal(index, list) {
  if (index === '') return ''
  let ret = ''
  list.forEach(item => {
    if (index.toString() === item.key) {
      ret = item.val
      return false
    }
  })
  return ret
}

/* 阿拉伯数字转汉字 */
export function convertNumberToWord(number) {
  const changeNum = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九']
  const unit = ['', '十', '百']
  number = parseInt(number)
  const getWan = (temp) => {
    const strArr = temp.toString().split('').reverse()
    let newNum = ''
    for (let i = 0; i < strArr.length; i++) {
      newNum = (i === 0 && strArr[i] === '0' ? '' : (i > 0 && strArr[i] === '0' && strArr[i - 1] === '0' ? '' : changeNum[strArr[i]] + (strArr[i] === '0' ? unit[0] : unit[i]))) + newNum
    }
    return newNum
  }
  const overWan = Math.floor(number / 100)
  let noWan = number % 100
  if (noWan.toString().length < 2) noWan = '0' + noWan
  const str = (overWan ? getWan(overWan) + '百' + getWan(noWan) : getWan(number))
  if (str.split('')[0] === '一') {
    return str.substring(1)
  } else {
    return overWan ? getWan(overWan) + '百' + getWan(noWan) : getWan(number)
  }
}
