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
 * 发展积分列表
 * @returns {*}
 */
export function devIntegralList(form) {
  return request({
    url: '/api/value/devIntegralList',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 销售数据(单个)
 * @returns {*}
 */
export function salesData(form) {
  return request({
    url: '/api/value/salesData',
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
 * 销售数据删除
 * @returns {*}
 */
export function salesDataDel(form) {
  return request({
    url: '/api/value/salesDataDel',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 营业情况列表
 * @returns {*}
 */
export function videoPatrolList(form) {
  return request({
    url: '/api/value/videoPatrolList',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 营业情况删除
 * @returns {*}
 */
export function videoPatrolDel(form) {
  return request({
    url: '/api/value/videoPatrolDel',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 营业情况修改
 * @returns {*}
 */
export function videoPatrolEdit(form) {
  return request({
    url: '/api/value/videoPatrolEdit',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 获取片区下的店铺
 * @returns {*}
 */
export function getAreaShop(form) {
  return request({
    url: '/api/value/areaShop',
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
 * 片区积分修改
 * @returns {*}
 */
export function areaMeritsAimEdit(form) {
  return request({
    url: '/api/value/areaMeritsAimEdit',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 片区积分删除
 * @returns {*}
 */
export function areaMeritsAimDel(form) {
  return request({
    url: '/api/value/areaMeritsAimDel',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 片区积分列表
 * @returns {*}
 */
export function superviseServiceList(form) {
  return request({
    url: '/api/value/superviseServiceList',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 片区积分填报
 * @returns {*}
 */
export function superviseServiceAdd(form) {
  return request({
    url: '/api/value/superviseServiceAdd',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 片区积分修改
 * @returns {*}
 */
export function superviseServiceEdit(form) {
  return request({
    url: '/api/value/superviseServiceEdit',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 片区积分删除
 * @returns {*}
 */
export function superviseServiceDel(form) {
  return request({
    url: '/api/value/superviseServiceDel',
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
 * 销售数据修改
 * @returns {*}
 */
export function salesDataEdit(form) {
  return request({
    url: '/api/value/salesDataEdit',
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
 * 活动计划修改
 * @returns {*}
 */
export function activityPlanEdit(form) {
  return request({
    url: '/api/value/activityPlanEdit',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 活动计划删除
 * @returns {*}
 */
export function activityPlanDel(form) {
  return request({
    url: '/api/value/activityPlanDel',
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
 * 活动执行修改
 * @returns {*}
 */
export function activityImplementEdit(form) {
  return request({
    url: '/api/value/activityImplementEdit',
    method: 'post',
    data: { ...form }
  });
}
/**
 * 活动执行删除
 * @returns {*}
 */
export function activityImplementDel(form) {
  return request({
    url: '/api/value/activityImplementDel',
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
 * 获取项目库服务打分数据字典字段
 * @returns {*}
 */
export function dictDataSupervise(form) {
  return request({
    url: '/api/value/dictDataSupervise',
    method: 'post',
    data: {...form}
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
 * 获取片区组织架构字段
 * @returns {*}
 */
export function areaDepartmentList(form) {
  return request({
    url: '/api/value/areaDepartmentList',
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



