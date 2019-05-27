import request from '../utils/request'

/**
 * 用户信息
 * @returns {*}
 */
export function getUserNotify(form) {
  return request({
    url: '/api/ding/userNotify',
    method: 'post',
    data: {...form}
  });
}


