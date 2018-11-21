import axios from 'axios';

axios.defaults.baseURL = 'http://textsdaily-vue.local/admin';
axios.defaults.headers.common['Authorization'] = '';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['Accept'] = 'application/json';

class ApiService {
    get (path, params = null) {
        return new Promise((resolve, reject) => {
            axios.get(
                path, 
                {
                    params: params
                }
            )
                .then(resolve)
                .catch(reject);
        });
    }

    post (path, params) {
        return new Promise((resolve, reject) => {
            axios.post(path, params)
                .then(resolve)
                .catch(reject);
        });
    }
    
    put () {
        return new Promise((resolve, reject) => {
            axios.post(path, params)
                .then(resolve)
                .catch(reject);
        });
    }
    
    destroy (path, id) {
        return new Promise((resolve, reject) => {
            axios.post(path, params)
                .then(resolve)
                .catch(reject);
        });
    }
    
    upload () {
        return new Promise((resolve, reject) => {
            axios.post(path, params)
                .then(resolve)
                .catch(reject);
        });
    }
}



export default new ApiService();