import { CONFIG } from '../config.js';

export default {
  createProject(projectForm){return axios.post(CONFIG.API_URL+'/project/create',{projectForm:projectForm});},
  overview(projectDir){return axios.get(CONFIG.API_URL+'/project/overview',{params:{projectDir:projectDir}});},
  getMrc(path){return axios.get(CONFIG.API_URL+'/mrc',{params:{path:path}});},
  getConf(projectDir){return axios.get(CONFIG.API_URL+'/project/conf',{params:{projectDir:projectDir}});},
  setConf(projectDir,conf){return axios.post(CONFIG.API_URL+'/project/conf',{projectDir:projectDir,conf:conf});},
  test(projectDir,name){return axios.post(CONFIG.API_URL+'/project/test',{projectDir:projectDir,name:name});},
}
