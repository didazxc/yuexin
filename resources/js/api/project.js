import { CONFIG } from '../config.js';

export default {
  createProject(project){return axios.post(CONFIG.API_URL+'/project/create',{project:project});},
  overview(project){return axios.get(CONFIG.API_URL+'/project/overview',{params:{project:project}});},
  getMrc(path){return axios.get(CONFIG.API_URL+'/mrc',{params:{path:path}});},
}
