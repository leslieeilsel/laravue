window.Vue = require('vue');

import App from './app.vue';
import store from './store';
import i18n from './lang';
import {router} from './router';
import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';
import moment from 'moment'

Vue.prototype.$moment = moment;
moment.locale('zh-cn');

Vue.use(ViewUI);

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
