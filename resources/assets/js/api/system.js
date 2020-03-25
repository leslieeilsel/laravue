import request from '../utils/request'

/**############################### 菜单管理 ###############################*/
/**
 * 获取全部菜单
 * @returns {*}
 */
export function getmenus() {
  return request({
    url: '/api/menu/getmenus',
    method: 'get'
  });
}

/**
 * 获取菜单选择器
 * @returns {*}
 */
export function getMenuSelecter() {
  return request({
    url: '/api/menu/menuselecter',
    method: 'get'
  });
}

/**
 * 获取树状菜单选择器
 * @returns {*}
 */
export function getMenuTree(id) {
  return request({
    url: '/api/menu/menutree',
    method: 'post',
    data: { id }
  });
}

/**
 * 创建组织机构
 * @returns {*}
 */
export function add(form) {
  return request({
    url: '/api/menu/add',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 获取权限数据
 * @returns {*}
 */
export function getRouter () {
  return request({
    url: '/api/menu/getrouter',
    method: 'get'
  });
}

/**############################### 部门管理 ###############################*/

/**
 * 获取一级部门
 * @returns {*}
 */
export function initDepartment() {
  return request({
    url: '/api/department/getByParentId/0',
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
    url: `/api/department/getByParentId/${id}`,
    method: 'get',
    data: { id }
  });
}
/**
 * 获取分级部门
 * @returns {*}
 */
export function loadClassDepartment(id) {
  return request({
    url: `/api/department/getClassDepartment`,
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
    url: '/api/department/addDepartment',
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
    url: '/api/department/editDepartment',
    method: 'post',
    data: { ...form }
  });
}

/**############################### 数据字典 ###############################*/

/**
 * 获取所有数据字典
 * @returns {*}
 */
export function getAllDictList() {
  return request({
    url: '/api/dict/dicts',
    method: 'get'
  });
}

/**
 * 添加字典
 * @returns {*}
 */
export function addDict(form) {
  return request({
    url: '/api/dict/addDict',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 修改字典
 * @returns {*}
 */
export function editDict(form) {
  return request({
    url: '/api/dict/editDict',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 删除字典
 * @returns {*}
 */
export function deleteDict(id) {
  return request({
    url: '/api/dict/deleteDict',
    method: 'post',
    data: { id }
  });
}

/**
 * 获取所有数据字典数据
 * @returns {*}
 */
export function getAllDictDataList(form) {
  return request({
    url: `/api/dict/dictDataList`,
    method: 'post',
    data: { ...form }
  });
}

/**
 * 添加字典数据
 * @returns {*}
 */
export function addDictData(form) {
  return request({
    url: '/api/dict/addDictData',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 修改字典
 * @returns {*}
 */
export function editDictData(form) {
  return request({
    url: '/api/dict/editDictData',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 删除字典数据
 * @returns {*}
 */
export function deleteDictData(id) {
  return request({
    url: '/api/dict/deleteDictData',
    method: 'post',
    data: { id }
  });
}

/**############################### 菜单管理 ###############################*/

/**
 * 获取全部菜单
 * @returns {*}
 */
export function getAllMenuList() {
  return request({
    url: '/api/menu/getAllMenuList',
    method: 'get'
  });
}

/**
 * 修改菜单
 * @returns {*}
 */
export function editMenu(form) {
  return request({
    url: '/api/menu/editMenu',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 添加字典
 * @returns {*}
 */
export function addMenu(form) {
  return request({
    url: '/api/menu/addMenu',
    method: 'post',
    data: { ...form }
  });
}

/**
 * 删除菜单
 * @returns {*}
 */
export function deleteMenu(id) {
  return request({
    url: '/api/menu/deleteMenu',
    method: 'post',
    data: { id }
  });
}