import request from '../utils/request'

/**
 * 用户信息
 * @returns {*}
 */
export function getUserId(form) {
  return request({
    url: '/api/ding/userId',
    method: 'post',
    data: {...form}
  });
}



