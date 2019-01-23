import request from '../utils/request'

/**
 * 获取操作日志列表
 * @returns {*}
 */
export function getOperationlogs() {
  return request({
    url: '/api/log/operationlogs',
    method: 'get'
  });
}
