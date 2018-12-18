// 所有路由信息的配置
import layout from 'views/layout'

/**
 * meta.title : 导航显示的中文名称
 * showParent : 无论子节量，都会显示为二级菜单（默认一个子节点只显示一级菜单）点数
 */

// 权限相关路由
export const mainRouter = [
  {
    path: '/',
    component: layout,
    redirect: 'home',
    children: [{
      path: 'home',
      component: require('views/home/index'),
      name: 'home',
      meta: {
        title: '首页',
        noCache: true,
        icon: 'md-home',
        roles: ['admin']
      }
    }]
  },
  {
    path: '/report',
    component: layout,
    redirect: 'index',
    name: 'repor',
    showParent: true,
    meta: {
      title: '月报表',
      icon: 'md-paper'
    },
    children: [
      {
        path: '/overviewMonth/fxf',
        component: require('views/report/overviewMonth/fxf'),
        name: 'fxfOverviewMonth',
        meta: {
          title: '发行费分配概览表',
          noCache: true,
          roles: ['admin'] // or you can only set roles in sub nav
        }
      },
      {
        path: '/overviewMonth/gyj',
        component: require('views/report/overviewMonth/gyj'),
        name: 'gyjOverviewMonth',
        meta: {
          title: '公益金分配概览表',
          noCache: true,
          roles: ['admin'] // or you can only set roles in sub nav
        }
      },
      {
        path: '/overviewMonth/yj',
        component: require('views/report/overviewMonth/yj'),
        name: 'yjOverviewMonth',
        meta: {
          title: '佣金分配概览表',
          noCache: true,
          roles: ['admin'] // or you can only set roles in sub nav
        }
      },
      {
        path: '/overviewMonth/fj',
        component: require('views/report/overviewMonth/fj'),
        name: 'fjOverviewMonth',
        meta: {
          title: '返奖分配概览表',
          noCache: true,
          roles: ['admin'] // or you can only set roles in sub nav
        }
      },]
  },
  {
    path: '/user',
    component: layout,
    redirect: 'index',
    name: 'user',
    showParent: true,
    meta: {
      title: '用户',
      icon: 'md-person'
    },
    children: [
      {
        path: 'users',
        component: require('views/user/users'),
        name: 'users',
        meta: {
          title: '用户管理',
          noCache: true,
          roles: ['admin'] // or you can only set roles in sub nav
        }
      },
      {
        path: 'role-manage',
        component: require('views/user/role-manage'),
        name: 'role-manage',
        meta: {
          title: '角色权限管理',
          noCache: true,
          roles: ['admin'] // or you can only set roles in sub nav
        }
      },
      {
          path: 'departments',
          component: require('views/user/departments'),
          name: 'departments',
          meta: {
              title: '部门管理',
              noCache: true,
              roles: ['admin'] // or you can only set roles in sub nav
          }
      }]
  },
  {
    path: '/system',
    component: layout,
    redirect: 'index',
    name: 'system',
    showParent: true,
    meta: {
      title: '系统管理',
      icon: 'md-cog'
    },
    children: [
      {
        path: 'menus',
        component: require('views/system/menus'),
        name: 'menus',
        meta: {
          title: '菜单管理',
          noCache: true,
          roles: ['admin'] // or you can only set roles in sub nav
        }
      }
    ]
  },
];

// 无权限相关的路由
export const constantRouterMap = [
  { path: '/login', component: require('views/login/index') },
  { path: '/password/send', component: require('views/login/password/email') },
  { path: '/password/reset/:token', component: require('views/login/password/reset') },
];

// 导出所有的路由配置
export const routes = [
  ...mainRouter,
  ...constantRouterMap
];
