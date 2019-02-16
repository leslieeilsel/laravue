import request from '../utils/request'

/**
 * 获取一级部门
 * @returns {*}
 */
export function initProjectInfo() {
  return request({
    url: '/api/project/getByParentId/0',
    method: 'get'
  });
}

/**
 * 获取所有部门
 * @returns {*}
 */
export function getAllDepartment() {
  return request({
    url: '/api/department/getAllDepartment',
    method: 'get'
  });
}

/**
 * 获取子部门
 * @returns {*}
 */
export function loadDepartment(id) {
  return request({
    url: `/api/project/getByParentId/${id}`,
    method: 'get',
    data: { id }
  });
}

/**
 * 创建组织机构
 * @returns {*}
 */
export function addDepartment(form) {
  return request({
    url: '/api/project/addDepartment',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 修改组织机构信息
 * @returns {*}
 */
export function editDepartment(form) {
  return request({
    url: '/api/project/editDepartment',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 删除菜单
 * @returns {*}
 */
export function deleteProject(id) {
  return request({
    url: '/api/project/deleteProject',
    method: 'post',
    data: { id }
  });
}