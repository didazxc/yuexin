import { CONFIG } from '../config.js';

export default {
  /*login(username,password){
    var token = window.Laravel['csrfToken'];
    return axios.post(CONFIG.ROOT_URL+'/login',{_token:token,email:username, password:password});
  },*/
  user(){return axios.get(CONFIG.API_URL+'/user');},
  users(){return axios.get(CONFIG.API_URL+'/users');},
  logout(){return axios.post(CONFIG.ROOT_URL+'/logout');}
}