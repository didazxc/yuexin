import Vue from 'vue';
import store from './store.js';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

//进入项目组件前，需要存在用户信息和项目信息
function requireProject(to, from, next) {
  var project=store.getters.getProject;
  if(project.name===undefined){
    next('/set_project');
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
    components: require('./pages/Layout.vue'),
    beforeEnter:requireProject,
    children: [
      {
        path: '/overview',
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

router.beforeEach((to, from, next) => {
  var user=store.getters.getUser;
  if (user===null || user==="null") {
    store.dispatch('initUser');
    store.watch(()=>store.getters.getUser, function () {
      var newUser=store.getters.getUser;
      if (newUser !== null && newUser!=="null") {
        next();
      }else{
        this.$store.dispatch('logout');
      }
    });
  }else{
    next();
  }
});



export default router;
