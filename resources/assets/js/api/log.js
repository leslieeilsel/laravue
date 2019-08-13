import request from '../utils/request'

/**
 * 获取操作日志列表
 * @returns {*}
 */
export function getOperationLogs(form) {
  return request({
    url: '/api/log/getOperationLogs',
    method: 'post',
    data: {...form}
  });
}
