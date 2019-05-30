// 导出路由并配置守卫
import Vue from 'vue';
import iView from 'iview';
import store from '../store'
import VueRouter from 'vue-router';
import { getRouter } from 'api/system';
import layout from 'views/layout';

import { constantRouterMap } from './router';
import { getToken } from "utils/storage";

Vue.use(VueRouter);

// 创建并导出 router 实例，然后传 `routes` 配置
export const router = new VueRouter({
  routes: constantRouterMap
});

const whiteList = ['/login', '/password/send', '/password/reset','/ding/project','/ding/project/list','/ding/project/projectSchedule','/ding/project/projectScheduleList'];
export let getRouters; //用来获取后台拿到的路由

router.beforeEach((to, from, next) => {
  iView.LoadingBar.start();

  if (getToken()) {
    if (to.path === '/login') {
      next('/');
    } else {
      // 判断是否已获取用户信息
      if (store.getters.roles.length === 0) {
        store.dispatch('getUserInfo').then(user => {
          const roles = user.roles || ['admin'];
          store.dispatch('generateRoutes', {
            roles
          }).then(() => { // 根据roles权限生成可访问的路由表
            getRouter().then(data => {
              getRouters = data.result; //后台拿到路由
              routerGo(to, next) //执行路由跳转方法
            });
            next({ ...to,
              replace: true
            }); // hack方法 确保addRoutes已完成 ,set the replace: true so the navigation will not leave a history record
          });
        });
      } else {
        next();
      }
    }
  } else {
    if (whiteList.includes(to.path)) {
      // 无 token 白名单可访问路由
      next();
    } else {
      next('/login');
      iView.LoadingBar.finish();
    }
  }
});

function routerGo(to, next) {
  getRouters = filterAsyncRouter(getRouters); //过滤路由
  router.addRoutes(getRouters); //动态添加路由
  next({ ...to,
    replace: true
  })
}

function filterAsyncRouter(asyncRouterMap) { //遍历后台传来的路由字符串，转换为组件对象
  const accessedRouters = asyncRouterMap.filter(route => {
    if (route.component) {
      if (route.component === 'layout') { //Layout组件特殊处理
        route.component = layout;
      } else {
        route.component = require('@/' + route.component + '.vue');
      }
    }
    if (route.children && route.children.length) {
      route.children = filterAsyncRouter(route.children)
    }
    return true
  });

  return accessedRouters;
}

router.afterEach(() => {
  iView.LoadingBar.finish();
});


