import request from '../utils/request'

/**
 * 获取用户列表
 * @returns {*}
 */
export function getUsers(form) {
  return request({
    url: '/api/user/users',
    method: 'post',
    data: {...form}
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

/**
 * 删除用户
 * @returns {*}
 */
export function deleteUserData(id) {
  return request({
    url: '/api/user/deleteUserData',
    method: 'post',
    data: {id}
  });
}

/**
 * 修改用户
 * @returns {*}
 */
export function editRegistUser(form) {
  return request({
    url: '/api/user/editRegistUser',
    method: 'post',
    data: {...form}
  });
}

/**
 * 获取单条用户
 * @returns {*}
 */
export function getUser(form) {
  return request({
    url: '/api/user/getUser',
    method: 'post',
    data: {...form}
  });
}

