import request from '../utils/request'

/**
 * 获取部门
 * @returns {*}
 */
export function getDeptlist() {
  return request({
    url: '/api/department/depts',
    method: 'get'
  });
}

/**
 * 获取部门选择器
 * @returns {*}
 */
export function getDeptSelecter() {
  return request({
    url: '/api/department/deptselecter',
    method: 'get'
  });
}

/**
 * 创建组织机构
 * @returns {*}
 */
export function add(form) {
  return request({
    url: '/api/department/add',
    method: 'post',
    data: { ...form }
  });
}
