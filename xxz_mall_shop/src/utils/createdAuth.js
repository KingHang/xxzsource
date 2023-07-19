function createdAuth(list, obj) {
  for (let i = 0; i < list.length; i++) {
    const item = list[i]
    if (item.path && typeof (item.path) !== 'undefined' && item.path !== '') {
      const _path = item.path.toLowerCase()
      obj[_path] = true
    }
    if (Object.prototype.toString.call(item.children) === '[object Array]' && item.children.length > 0) {
      createdAuth(item.children, obj)
    }
  }
}

export { createdAuth }
