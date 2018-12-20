import request from '../utils/request'

/**
 * 获取角色列表
 * @returns {*}
 */
export function getRoles() {
    return request({
        url: '/api/role/roles',
        method: 'get'
    });
}

/**
 * 创建角色
 * @returns {*}
 */
export function add(form) {
    return request({
        url: '/api/role/add',
        method: 'post',
        data: { ...form }
    });
}

/**
 * 设置菜单权限
 * @returns {*}
 */
export function setRoleMenus(selected, roleid) {
    return request({
        url: '/api/role/setrolemenus',
        method: 'post',
        data: { selected, roleid }
    });
}
