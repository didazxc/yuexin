import Vue from 'vue';
import Vuex from 'vuex';
import UserApi from './api/user';
import ProjectApi from './api/project'

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    userLoadStatus: 2,//2已登陆，1登陆中，0未登陆，3登陆异常
    //基本用户信息
    user:null,
    //菜单
    menus:{
      "/overview":{index:"/overview",title:"总览",fa:"fa-dashboard"},
      "/preprocess":{index:"/preprocess",title:"预处理",fa:"fa-flash"},
      "/pick":{index:"/pick",title:"颗粒挑选",fa:"fa-eyedropper"},
      "/2d":{index:"/2d",title:"2D分组",fa:"fa-clone"},
      "/3d":{index:"/3d",title:"3D分组",fa:"fa-cube"},
      "/3dfix":{index:"/3dfix",title:"3D精修",fa:"fa-snowflake-o"}
    },
    //用户列表，是个map，包含project
    users:{},
    //项目信息
    project:localStorage.project?JSON.parse(localStorage.project):{},
  },
  getters: {
    getUserLoadStatus(state) {
      return state.userLoadStatus;
    },
    getUser(state) {
      return state.user;
    },
    getMenus(state){
      return state.menus;
    },
    getUsers(state){
      return state.users;
    },
    getProject(state){
      return state.project;
    }
  },
  mutations: {
    setUserLoadStatus(state, status) {
      state.userLoadStatus = status;
    },
    setUser(state,user){
      state.user=user;
    },
    setUsers(state,users){
      state.users=users;
    },
    setProject(state,form){
      /*for(var key in form){
        if(form.hasOwnProperty(key)){
          state.project[key] = form[key];
        }
      }*/
      state.project=form;
      localStorage.project=JSON.stringify(state.project);
    }
  },
  actions: {
    reset(state){
      state.commit('setUserLoadStatus',0);
      state.commit('setUser',null);
      state.commit('setProject',{});
    },
    /*login(context,form){
      UserApi.login(form.username,form.password).then(res=>{
        context.dispatch('initUser');
      })
    },*/
    initUser(context){
      return UserApi.user().then(res=>{
        context.commit('setUser',res.data);
      }).catch(res=>{
        window.location.replace("/login");
      });
    },
    refreshUsers(context){
      return UserApi.users().then(res=>{
        context.commit('setUsers',res.data);
      });
    },
    logout(context){
      UserApi.logout().then(res=>{
        context.dispatch('reset');
        window.location.replace("/login");
      });
    },
    async createProject(context,form){
      await ProjectApi.createProject(form).then(res=>{
        context.commit('setProject',res.data);
      });
      await context.dispatch('refreshUsers');
      return 'done';
    }
  },
});
