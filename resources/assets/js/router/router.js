// 所有路由信息的配置
import layout from 'views/layout'
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
  { path: '/ding/project/list', component: require('views/ding/project/list') },
  { path: '/ding/project/projectSchedule', component: require('views/ding/project/projectSchedule') },
  { path: '/ding/project/projectScheduleList', component: require('views/ding/project/projectScheduleList') },
  { path: '/ding/project/projectWarning', component: require('views/ding/project/projectWarning') },
  { path: '/ding/project/projectInfo', component: require('views/ding/project/projectInfo') },
  { path: '/ding/project/projectScheduleEdit', component: require('views/ding/project/projectScheduleEdit') },
  { path: '/ding/project/projectScheduleAudit', component: require('views/ding/project/projectScheduleAudit') },
  { path: '/ding/project/notify', component: require('views/ding/project/notify') },
  {
    path: '/',
    component: layout,
    redirect: '/home',
    children: [{
      path: 'projects/preview',
      component: require('views/project/projects/preview'),
      // name: 'projectPreview',
      meta: {
        title: '查看项目',
        noCache: true,
        icon: '',
        roles: ['admin']
      }
    }]
  },
];

// 导出所有的路由配置
export const routes = [
  ...mainRouter,
  ...constantRouterMap,
  // ...singleRouter
];
  