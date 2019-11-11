import request from '../utils/request'
/**
 * 价值积分列表
 * @returns {*}
 */
export function valueIntegralList(form) {
  return request({
    url: '/api/value/valueIntegralList',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 销售数据列表
 * @returns {*}
 */
export function salesDataList(form) {
  return request({
    url: '/api/value/salesDataList',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 片区积分列表
 * @returns {*}
 */
export function areaMeritsAimList(form) {
  return request({
    url: '/api/value/areaMeritsAimList',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 销售数据填报
 * @returns {*}
 */
export function salesDataAdd(form) {
  return request({
    url: '/api/value/salesDataAdd',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 片区积分填报
 * @returns {*}
 */
export function areaMeritsAimAdd(form) {
  return request({
    url: '/api/value/areaMeritsAimAdd',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 活动计划
 * @returns {*}
 */
export function activityPlan(form) {
  return request({
    url: '/api/value/activityPlan',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 活动计划填报
 * @returns {*}
 */
export function activityPlanAdd(form) {
  return request({
    url: '/api/value/activityPlanAdd',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 活动执行
 * @returns {*}
 */
export function activityImplement(form) {
  return request({
    url: '/api/value/activityImplement',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 活动执行填报
 * @returns {*}
 */
export function activityImplementAdd(form) {
  return request({
    url: '/api/value/activityImplementAdd',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 获取项目库数据字典字段
 * @returns {*}
 */
export function dictData(dictName) {
  return request({
    url: '/api/value/dictData',
    method: 'post',
    data: {dictName}
  });
}
/**
 * 获取组织架构字段
 * @returns {*}
 */
export function departmentList(form) {
  return request({
    url: '/api/value/departmentList',
    method: 'post',
    data: {...form}
  });
}
/**
 * 获取组织架构详情
 * @returns {*}
 */
export function departmentInfo(form) {
  return request({
    url: '/api/value/departmentInfo',
    method: 'post',
    data: {...form}
  });
}
/**
 * 巡店填报
 * @returns {*}
 */
export function videoPatrolAdd(form) {
  return request({
    url: '/api/value/videoPatrolAdd',
    method: 'post',
    data: {...form}
  });
}



