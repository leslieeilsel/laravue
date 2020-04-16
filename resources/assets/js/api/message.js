import request from '../utils/request';

/**
 * 发送短信
 */
export function sendSms(params) {
  return request({
    url: '/api/sendSms',
    method: 'post',
    data: {...params}
  });
}

/**
 * 保存手机号
 */
export function savePhone(params) {
  return request({
    url: '/api/savePhone',
    method: 'post',
    data: {...params}
  });
}
