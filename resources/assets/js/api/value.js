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


