import request from '../utils/request'

/**
 * 获取角色列表
 * @returns {*}
 */
export function getRoles() {
  return request({
    url: '/api/role/roles',
    method: 'get'
  });
}

/**
 * 创建角色
 * @returns {*}
 */
export function add(form) {
  return request({
    url: '/api/role/add',
    method: 'post',
    data: {...form}
  });
}

/**
 * 设置菜单权限
 * @returns {*}
 */
export function setRoleMenus(selected, roleid) {
  return request({
    url: '/api/role/setrolemenus',
    method: 'post',
    data: {selected, roleid}
  });
}

/**
 * 设置默认角色
 * @returns {*}
 */
export function setDefaultRole(params) {
  return request({
    url: '/api/role/setDefaultRole',
    method: 'post',
    data: {...params}
  });
}

/**
 * 获取部门树
 * @returns {*}
 */
export function getDepartmentTree(roleId) {
  return request({
    url: '/api/role/getDepartmentTree',
    method: 'post',
    data: {roleId}
  });
}

/**
 * 编辑数据权限
 * @returns {*}
 */
export function editRoleDep(params) {
  return request({
    url: '/api/role/editRoleDep',
    method: 'post',
    data: {params}
  });
}

/**
 * 删除角色
 * @returns {*}
 */
export function deleteRoleData(id) {
  return request({
    url: '/api/role/deleteRoleData',
    method: 'post',
    data: {id}
  });
}

/**
 * 编辑角色
 * @returns {*}
 */
export function edit(form) {
  return request({
    url: '/api/role/edit',
    method: 'post',
    data: {...form}
  });
}
