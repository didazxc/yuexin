import { CONFIG } from '../config.js';

import store from "../store";

export default {

  createProject(projectForm){
    return axios.post(CONFIG.API_URL+'/project/create',{projectForm:projectForm});
  },

  getPng(module,name,ext){
    var projectDir=store.getters.getProject.directory;
    return axios.get(CONFIG.API_URL+'/project/png',{params:{projectDir:projectDir,module:module,name:name,ext:ext}});
  },

  clear(){
    var projectDir=store.getters.getProject.directory;
    return axios.post(CONFIG.API_URL+'/project/clear',{projectDir:projectDir});
  },

  overview(){
    var projectDir=store.getters.getProject.directory;
    return axios.get(CONFIG.API_URL+'/project/overview',{params:{projectDir:projectDir}});
  },
  getConf(){
    var projectDir=store.getters.getProject.directory;
    return axios.get(CONFIG.API_URL+'/project/conf',{params:{projectDir:projectDir}});
  },
  setConf(conf){
    var projectDir=store.getters.getProject.directory;
    return axios.post(CONFIG.API_URL+'/project/conf',{projectDir:projectDir,conf:conf});
  },
  test(modules,names){
    var projectDir=store.getters.getProject.directory;
    return axios.post(CONFIG.API_URL+'/project/test',{projectDir:projectDir,modules:modules,names:names});
  },

  preprocess(){
    var projectDir=store.getters.getProject.directory;
    return axios.get(CONFIG.API_URL+'/project/preprocess',{params:{projectDir:projectDir}});
  },
  setMark(marks){
    var projectDir=store.getters.getProject.directory;
    return axios.post(CONFIG.API_URL+'/project/preprocess/mark',{projectDir:projectDir,marks:marks});
  },

  pick(){
    var projectDir=store.getters.getProject.directory;
    return axios.get(CONFIG.API_URL+'/project/pick',{params:{projectDir:projectDir}});
  },
  getPick(name){
    var projectDir=store.getters.getProject.directory;
    return axios.get(CONFIG.API_URL+'/project/pick/mark',{params:{projectDir:projectDir,name:name}});
  },
  setPick(name,arr){
    var projectDir=store.getters.getProject.directory;
    return axios.post(CONFIG.API_URL+'/project/pick/mark',{projectDir:projectDir,name:name,arr:arr});
  },

}
