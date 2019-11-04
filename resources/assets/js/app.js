//require('./bootstrap');

window.Vue = require('vue');

import App from './app.vue'
import store from './store'
import i18n from './lang'
import { router } from './router'

import iView from 'iview'
import 'iview/dist/styles/iview.css'

import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/antd.css'

require('video.js/dist/video-js.css')
require('vue-video-player/src/custom-theme.css')
import VideoPlayer from 'vue-video-player'
// require('videojs-flash')
// import 'videojs-flash'
Vue.use(VideoPlayer)
 
Vue.use(iView);
Vue.use(Antd);
const app = new Vue({
  el: '#app',
  i18n,
  store,
  router,
  render: h => h(App)
});
