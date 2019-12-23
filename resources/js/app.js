
require('./bootstrap');

window.Vue = require('vue');

//font-awesome
import 'font-awesome/scss/font-awesome.scss'

// element-ui
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI);

//konva
import VueKonva from 'vue-konva'
Vue.use(VueKonva);

//echarts
import 'echarts'
//import 'echarts-gl'
import ECharts from 'vue-echarts'
Vue.component('v-chart', ECharts);

// 实例化路由
import router from './router';

//使用vuex
import store from './store';

//初始化
const app = new Vue({
  store,
  router,
  el: '#app',
});
