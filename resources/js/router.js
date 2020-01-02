import Vue from 'vue';
import store from './store.js';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

//进入项目组件前，需要存在项目信息
function requireProject(to, from, next) {
  var project=store.getters.getProject;
  if(project.name===undefined){
    next('/set_project');
  }else{
    next();
  }
}

//overview中必须存在config配置信息
function requireConfig(to, from, next){
  var conf=store.getters.getConfig;
  if(conf===null){
    store.dispatch("initConfig").then(res=>{
      next();
    }).catch(res=>{
      next('/set_project');
    });
  }else{
    next();
  }
}

const routes = [
  {
    path:'/set_project',
    components: require('./pages/Layout.vue'),
    children:[
      {
        path:'/set_project',
        components: require('./pages/ProjectSelect.vue'),
      }
    ]
  },
  {
    path: '/',
    redirect:'/overview',
    components: require('./pages/Layout.vue'),
    beforeEnter:requireProject,
    children: [
      {
        path: '/overview',
        beforeEnter:requireConfig,
        components: require('./pages/Overview.vue'),
      },
      {
        path: '/preprocess',
        components: require('./pages/Preprocess.vue'),
      },
      {
        path: '/pick',
        components: require('./pages/Pick.vue'),
      },
      {
        path: '/2d',
        redirect: '/overview'
      },
      {
        path: '/3d',
        redirect: '/overview'
      },
      {
        path: '/3dfix',
        redirect: '/overview'
      }
    ]
  }
];

const router = new VueRouter({
  routes: routes
});

//首先需要登陆
router.beforeEach(async (to, from, next) => {
  var user=store.getters.getUser;
  if (user===null) {
    await store.dispatch('initUser');
    var newUser=store.getters.getUser;
    if (newUser !== null) {
      next();
    }else{
      this.$store.dispatch('logout');
    }
    /*store.watch(()=>store.getters.getUser, function () {
      var newUser=store.getters.getUser;
      if (newUser !== null && newUser!=="null") {
        next();
      }else{
        this.$store.dispatch('logout');
      }
    });*/
  }else{
    next();
  }
});

export default router;
