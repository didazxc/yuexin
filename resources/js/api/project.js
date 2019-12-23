import { CONFIG } from '../config.js';

export default {
  createProject(project){return axios.post(CONFIG.API_URL+'/project/create',{project:project});},
  getFiles(dir,ext){return axios.get(CONFIG.API_URL+'/project/files',{params:{dir:dir,ext:ext}});},
  getMrc(path){return axios.get(CONFIG.API_URL+'/mrc',{params:{path:path}});},
}
