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
    method: 'get',
    data: {...form}
  });
}

/**
 * 获取项目进度填报已审核的项目列表（附加权限控制）
 * @returns {*}
 */
export function getAuditedProjects() {
  return request({
    url: '/api/ding/getAuditedProjects',
    method: 'get'
  });
}


