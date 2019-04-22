// 所有路由信息的配置

/**
 * meta.title : 导航显示的中文名称
 * showParent : 无论子节量，都会显示为二级菜单（默认一个子节点只显示一级菜单）点数
 */

// 权限相关路由
export const mainRouter = [];
// 无权限相关的路由
export const constantRouterMap = [
  { path: '/login', component: require('views/login/index') },
  { path: '/password/send', component: require('views/login/password/email') },
  { path: '/password/reset/:token', component: require('views/login/password/reset') },
  { path: '/ding/project', component: require('views/ding/project/index') },
];

// 导出所有的路由配置
export const routes = [
  ...mainRouter,
  ...constantRouterMap
];
  