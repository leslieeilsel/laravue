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
export function getAllProjects(searchForm) {
  return request({
    url: '/api/project/getAllProjects',
    method: 'post',
    data: {searchForm}
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
    data: {id}
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
    data: {...form}
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
    data: {...form}
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
    data: {...form}
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
    data: {parentsIds, yearIds, monthIds}
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
    data: {...form}
  });
}

/**
 * 投资项目进度列表
 * @returns {*}
 */
export function projectProgressList(form) {
  return request({
    url: '/api/project/projectProgressList',
    method: 'post',
    data: {...form}
  });
}

/**
 * 上传文件
 * @returns {*}
 */
export function uploadPic(form) {
  return request({
    url: '/api/project/uploadPic',
    method: 'post',
    data: {...form}
  });
}

/**
 * 查询项目计划
 * @returns {*}
 */
export function projectPlanInfo(form) {
  return request({
    url: '/api/project/projectPlanInfo',
    method: 'post',
    data: {...form}
  });
}

/**
 * 获取项目库数据字典字段
 * @returns {*}
 */
export function getData(form) {
  return request({
    url: '/api/project/getData',
    method: 'post',
    data: {...form}
  });
}

/**
 * 获取项目库数据字典字段
 * @returns {*}
 */
export function getProjectDictData(dictName) {
  return request({
    url: '/api/project/getProjectDictData',
    method: 'post',
    data: {dictName}
  });
}

/**
 * 添加台账
 * @returns {*}
 */
export function projectLedgerAdd(dictName) {
  return request({
    url: '/api/project/projectLedgerAdd',
    method: 'post',
    data: {dictName}
  });
}

/**
 * 台账列表
 * @returns {*}
 */
export function projectLedgerList(form) {
  return request({
    url: '/api/project/projectLedgerList',
    method: 'post',
    data: {...form}
  });
}

/**
 * 项目季度台账
 * @returns {*}
 */
export function projectQuarter(dictName) {
  return request({
    url: '/api/project/projectQuarter',
    method: 'post',
    data: {dictName}
  });
}

/**
 * 修改填报
 * @returns {*}
 */
export function editProjectProgress(dictName) {
  return request({
    url: '/api/project/editProjectProgress',
    method: 'post',
    data: {dictName}
  });
}

/**
 * 审核填报
 * @returns {*}
 */
export function auditProjectProgress(form) {
  return request({
    url: '/api/project/auditProjectProgress',
    method: 'post',
    data: {...form}
  });
}

/**
 * 构建计划填报表单
 * @returns {*}
 */
export function buildPlanFields(date) {
  return request({
    url: '/api/project/buildPlanFields',
    method: 'post',
    data: {date}
  });
}

/**
 * 构建计划填报表单
 * @returns {*}
 */
export function auditProject(params) {
  return request({
    url: '/api/project/auditProject',
    method: 'post',
    data: {params}
  });
}

/**
 * 构建计划填报表单
 * @returns {*}
 */
export function getEditFormData(id) {
  return request({
    url: '/api/project/getEditFormData',
    method: 'post',
    data: {id}
  });
}

/**
 * 发起项目调整
 * @returns {*}
 */
export function projectAdjustment(status) {
  return request({
    url: '/api/project/projectAdjustment',
    method: 'post',
    data: {status}
  });
}

/**
 * 发起项目调整
 * @returns {*}
 */
export function toAudit(id) {
  return request({
    url: '/api/project/toAudit',
    method: 'post',
    data: {id}
  });
}

/**
 * 发起项目调整
 * @returns {*}
 */
export function toAuditSchedule(id) {
  return request({
    url: '/api/project/toAuditSchedule',
    method: 'post',
    data: {id}
  });
}
/**
 * 填报，当当月实际投资发生改变时，修改累计投资
 * @returns {*}
 */
export function actCompleteMoney(form) {
  return request({
    url: '/api/project/actCompleteMoney',
    method: 'post',
    data: {...form}
  });
}