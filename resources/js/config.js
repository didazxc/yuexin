var root_url = '';
switch( process.env.NODE_ENV ){
  case 'development':
    root_url = 'http://localhost:8000';
    break;
  case 'production':
    root_url = 'http://www.production.com';
    break;
}

export const CONFIG = {
  ROOT_URL: root_url,
  API_URL: root_url+'/api/v1',
};