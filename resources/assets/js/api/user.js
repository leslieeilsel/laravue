import request from '../utils/request'

/**
 * 获取table数据
 * @param startMonth 开始时间
 * @param endMonth 结束时间
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

