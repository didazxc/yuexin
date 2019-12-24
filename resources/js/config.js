var root_url = '';
switch( process.env.NODE_ENV ){
  case 'development':
    root_url = '';
    break;
  case 'production':
    root_url = '';
    break;
}

export const CONFIG = {
  ROOT_URL: root_url,
  API_URL: root_url+'/api/v1',
};