//require('./bootstrap');

window.Vue = require('vue');

import App from './app.vue'
import store from './store'
import i18n from './lang'
import { router } from './router'
import BaiduMap from 'vue-baidu-map'

import iView from 'iview'
import 'iview/dist/styles/iview.css'

import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/antd.css'

Vue.use(BaiduMap, {
  ak: '6pIbWNF8NrG10j4NXcGMW6PQDUAHd1Is'
});
Vue.use(iView);
Vue.use(Antd);
const app = new Vue({
  el: '#app',
  i18n,
  store,
  router,
  render: h => h(App)
});
