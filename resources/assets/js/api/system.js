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
 * 获取树状菜单选择器
 * @returns {*}
 */
export function getMenuTree(id) {
    return request({
        url: '/api/menu/menutree',
        method: 'post',
        data: { id }
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

/**
 * 获取权限数据
 * @returns {*}
 */
export function getRouter () {
    return request({
        url: '/api/menu/getrouter',
        method: 'get'
    });
}
