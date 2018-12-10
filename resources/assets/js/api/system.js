import request from '../utils/request'

/**
 * 获取全部菜单
 * @returns {*}
 */
export function getmenus() {
    return request({
        url: '/api/menu/getmenus',
        method: 'get'
    });
}

/**
 * 获取菜单选择器
 * @returns {*}
 */
export function getMenuSelecter() {
    return request({
        url: '/api/menu/menuselecter',
        method: 'get'
    });
}

/**
 * 创建组织机构
 * @returns {*}
 */
export function add(form) {
    return request({
        url: '/api/menu/add',
        method: 'post',
        data: { ...form }
    });
}
