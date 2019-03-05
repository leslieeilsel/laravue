import request from '../utils/request'

/**
 * 获取一级部门
 * @returns {*}
 */
export function initProjectInfo() {
  return request({
    url: '/api/project/getProjects',
    method: 'get'
  });
}

/**
 * 获取所有部门
 * @returns {*}
 */
export function getAllProjects() {
  return request({
    url: '/api/project/getAllProjects',
    method: 'get'
  });
}

/**
 * 获取所有部门
 * @returns {*}
 */
export function getAllWarning() {
  return request({
    url: '/api/project/getAllWarning',
    method: 'get'
  });
}

/**
 * 获取子部门
 * @returns {*}
 */
export function loadPlan(id) {
  return request({
    url: `/api/project/loadPlan/${id}`,
    method: 'get',
    data: { id }
  });
}

/**
 * 新增项目
 * @returns {*}
 */
export function addProject(form) {
  return request({
    url: '/api/project/addProject',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 新增项目计划
 * @returns {*}
 */
export function addProjectPlan(form) {
  return request({
    url: '/api/project/addProjectPlan',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 修改项目信息，项目计划信息
 * @returns {*}
 */
export function edit(form) {
  return request({
    url: '/api/project/edit',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 删除项目
 * @returns {*}
 */
export function deleteProject(parentsIds, yearIds, monthIds) {
  return request({
    url: '/api/project/deleteProject',
    method: 'post',
    data: { parentsIds, yearIds, monthIds }
  });
}
/**
 * 投资项目进度填报
 * @returns {*}
 */
export function projectProgress(form) {
  return request({
    url: '/api/project/projectProgress',
    method: 'post',
    data: { ...form }
  });
}