import request from '../utils/request'

/**
 * 获取用户列表
 * @returns {*}
 */
export function getUsers() {
    return request({
        url: '/api/user/users',
        method: 'get'
    });
}

/**
 * 创建用户
 * @returns {*}
 */
export function registUser(form) {
    return request({
        url: '/api/user/regist',
        method: 'post',
        data: { ...form }
    });
}

