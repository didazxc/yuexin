import Vue from 'vue';
import Vuex from 'vuex';
import UserApi from './api/user';
import ProjectApi from './api/project'

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    //菜单配置
    menus:{
      "/set_project":{title:"项目选择"},
      "/overview":{index:"/overview",title:"总览",fa:"fa-dashboard"},
      "/preprocess":{index:"/preprocess",title:"预处理",fa:"fa-flash"},
      "/pick":{index:"/pick",title:"颗粒挑选",fa:"fa-eyedropper"},
      "/2d":{index:"/2d",title:"2D分组",fa:"fa-clone"},
      "/3d":{index:"/3d",title:"3D分组",fa:"fa-cube"},
      "/3dfix":{index:"/3dfix",title:"3D精修",fa:"fa-snowflake-o"}
    },
    //基本用户信息
    user:null,
    //用户列表，是个map，包含project
    users:{},
    //项目信息
    project:localStorage.project?JSON.parse(localStorage.project):{},
    //配置信息
    config:null
  },
  getters: {
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
    },
    getConfig(state){
      return state.config;
    }
  },
  mutations: {
    setUser(state,user){
      state.user=user;
    },
    setUsers(state,users){
      state.users=users;
    },
    setProject(state,form){
      state.project=form;
      localStorage.project=JSON.stringify(state.project);
    },
    setConfig(state,config){
      state.config = config;
    }
  },
  actions: {
    reset(state){
      state.commit('setUser',null);
      state.commit('setProject',{});
      state.commit('setConfig',null);
    },
    logout(context){
      UserApi.logout().then(res=>{
        context.dispatch('reset');
        window.location.replace("/login");
      });
    },
    initUser(context){
      return UserApi.user().then(res=>{
        context.commit('setUser',res.data);
      }).catch(res=>{
        context.dispatch("logout");
      });
    },
    refreshUsers(context){
      return UserApi.users().then(res=>{
        context.commit('setUsers',res.data);
      });
    },
    async createProject(context,form){
      await ProjectApi.createProject(form).then(res=>{
        context.commit('setProject',res.data);
      });
      await context.dispatch('refreshUsers');
      return 'done';
    },
    initConfig(context){
      var dir = context.getters.getProject.directory;
      if(dir!==undefined){
        return ProjectApi.getConf(dir).then(res=>{
          context.commit('setConfig',res.data);
        });
      }else{
        throw Exception("have not a project directory");
      }
    },
    setConfig(context,conf){
      var dir = context.getters.getProject.directory;
      if(dir!==undefined) {
        return ProjectApi.setConf(dir, conf);
      }else{
        throw Exception("have not a project directory");
      }
    }
  },
});
