import request from '../utils/request'

/**
 * 获取一级部门
 * @returns {*}
 */
export function getUserNotify() {
  return request({
    url: '/api/ding/userNotify',
    method: 'post'
  });
}


