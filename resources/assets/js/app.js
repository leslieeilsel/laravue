//require('./bootstrap');

window.Vue = require('vue');

import App from './app.vue'
import store from './store'
import i18n from './lang'
import {router} from './router'
import VueResource from 'vue-resource'

import iView from 'iview'
import 'iview/dist/styles/iview.css'

import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/antd.css'

Vue.use(iView);
Vue.use(Antd);
Vue.use(VueResource);

Vue.prototype.$Message.config({
  duration: 5
});

const app = new Vue({
  el: '#app',
  i18n,
  store,
  router,
  render: h => h(App)
});
