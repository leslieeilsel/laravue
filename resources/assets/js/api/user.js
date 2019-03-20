import request from '../utils/request'

/**
 * 获取用户列表
 * @returns {*}
 */
export function getUsers() {
  return request({
    url: '/api/user/users',
    method: 'get'
  });
}

/**
 * 创建用户
 * @returns {*}
 */
export function registUser(form) {
  return request({
    url: '/api/user/regist',
    method: 'post',
    data: {...form}
  });
}

/**
 * 修改密码
 * @returns {*}
 */
export function resetPassword(form) {
  return request({
    url: '/api/user/resetPassword',
    method: 'post',
    data: {...form}
  });
}

/**
 * 获取数据字典
 * @returns {*}
 */
export function getUserDictData(dictName) {
  return request({
    url: '/api/user/getUserDictData',
    method: 'post',
    data: {dictName}
  });
}
