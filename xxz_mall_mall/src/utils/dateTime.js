export function formatDate(date, fmt) {
  if (/(y+)/.test(fmt)) {
    fmt = fmt.replace(RegExp.$1, (date.getFullYear() + '').substr(4 - RegExp.$1.length))
  }
  const o = {
    'M+': date.getMonth() + 1,
    'd+': date.getDate(),
    'h+': date.getHours(),
    'm+': date.getMinutes(),
    's+': date.getSeconds()
  }
  for (const k in o) {
    if (new RegExp(`(${k})`).test(fmt)) {
      const str = o[k] + ''
      fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? str : padLeftZero(str))
    }
  }
  return fmt
}

/** 设置时间 **/
export function searchTime(type) {
  const dateline = new Date()
  let time_start = ''
  let time_end = ''
  switch (type) {
    case 2:
      /* 昨天时间范围 */
      dateline.setDate(dateline.getDate() - 1)
      time_start = currentTime(dateline, 1)
      time_end = currentTime(dateline, 2)
      break
    case 3:
      /* 近7天时间范围 */
      dateline.setDate(dateline.getDate() - 6)
      time_start = currentTime(dateline, 1)
      time_end = currentTime(new Date(), 2)
      break
    case 4:
      /* 近30天时间范围 */
      dateline.setDate(dateline.getDate() - 29)
      time_start = currentTime(dateline, 1)
      time_end = currentTime(new Date(), 2)
      break
    default:
      /* 当天时间范围 */
      time_start = currentTime(dateline, 1)
      time_end = currentTime(dateline, 2)
      break
  }
  return { time_start: time_start, time_end: time_end }
}

export function searchMonth(type) {
  const dateline = new Date()
  let time_start = ''
  let time_end = ''
  switch (type) {
    case 1:
      /* 上月范围 */
      dateline.setDate(dateline.getMonth() - 29)
      time_start = currentMonth(dateline, 1)
      time_end = currentMonth(dateline, 2)
      break
    case 2:
      /* 近三个月范围 */
      dateline.setDate(dateline.getDate() - 89)
      time_start = currentMonth(dateline, 1)
      time_end = currentMonth(new Date(), 2)
      break
    case 3:
      /* 近六个月范围 */
      dateline.setDate(dateline.getDate() - 179)
      time_start = currentMonth(dateline, 1)
      time_end = currentMonth(new Date(), 2)
      break
    default:
      /* 当天时间范围 */
      time_end = currentMonth(dateline, 1)
      time_start = currentMonth(dateline, 2)
      break
  }
  return { time_start: time_start, time_end: time_end }
}

/** 获取当前时间 **/
function currentTime(dateline, type) {
  const year = dateline.getFullYear()
  const month = fillZero(dateline.getMonth() + 1)
  const day = fillZero(dateline.getDate())
  if (type === 1) {
    return year + '-' + month + '-' + day + ' 00:00:00'
  } else {
    return year + '-' + month + '-' + day + ' 23:59:59'
  }
}

/** 获取当前月份 **/
function currentMonth(dateline, type) {
  const year = dateline.getFullYear()
  const month = fillZero(dateline.getMonth() + 1)
  if (type === 1) {
    return year + '-' + month
  } else {
    return year + '-' + month
  }
}

/** 补足0 **/
function fillZero(data) {
  return data < 10 ? '0' + data : data
}

function padLeftZero(str) {
  return ('00' + str).substr(str.length)
}
