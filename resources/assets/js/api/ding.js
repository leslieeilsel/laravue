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

/**
 * 推送消息
 * @returns {*}
 */
export function userNotify(form) {
  return request({
    url: '/api/ding/userNotify',
    method: 'post',
    data: {...form}
  });
}


