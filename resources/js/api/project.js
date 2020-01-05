import { CONFIG } from '../config.js';

export default {
  getMrc(path){return axios.get(CONFIG.API_URL+'/mrc',{params:{path:path}});},

  createProject(projectForm){return axios.post(CONFIG.API_URL+'/project/create',{projectForm:projectForm});},

  overview(projectDir){return axios.get(CONFIG.API_URL+'/project/overview',{params:{projectDir:projectDir}});},
  getConf(projectDir){return axios.get(CONFIG.API_URL+'/project/conf',{params:{projectDir:projectDir}});},
  setConf(projectDir,conf){return axios.post(CONFIG.API_URL+'/project/conf',{projectDir:projectDir,conf:conf});},
  test(projectDir,name){return axios.post(CONFIG.API_URL+'/project/test',{projectDir:projectDir,name:name});},

  preprocess(projectDir){return axios.get(CONFIG.API_URL+'/project/preprocess',{params:{projectDir:projectDir}});},

  pick(projectDir){return axios.get(CONFIG.API_URL+'/project/pick',{params:{projectDir:projectDir}});},
  getMark(projectDir,name){return axios.get(CONFIG.API_URL+'/project/pick/mark',{params:{projectDir:projectDir,name:name}});},
  setMark(projectDir,name,arr){return axios.post(CONFIG.API_URL+'/project/pick/mark',{projectDir:projectDir,name:name,arr:arr});},

}
