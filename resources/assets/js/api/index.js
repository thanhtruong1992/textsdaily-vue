import axios from 'axios';

axios.defaults.baseURL = 'http://textsdaily-vue.local/admin';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['Accept'] = 'application/json';

axios.interceptors.request.use(function (config) {
    // spinning start to show
    $("#loader").css("display", "block");
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = ''
    }
    return config
}, function (error) {
    return Promise.reject(error);
});

axios.interceptors.response.use(function (response) {
    // spinning hide
    $("#loader").css("display", "none");
    if(response.status == 401) {
        localStorage.removeItem('auth');
        if(this.$router !== undefined) {
            this.$router.push('/login');
        }
    }
    return response;
}, function (error) {
    // spinning hide
    $("#loader").css("display", "none");
    if(error.response.status == 401) {
        localStorage.removeItem('auth');
        if(this.$router !== undefined) {
            this.$router.push('/login');
        }
    }
    return Promise.reject(error);
});

class ApiService {
    get(path, params = null) {
        return new Promise((resolve, reject) => {
            axios.get(path, 
                    {
                        params: params
                    }
                )
                .then(resolve)
                .catch(reject);
        });
    }

    post(path, params) {
        return new Promise((resolve, reject) => {
            axios.post(path, params)
                .then(resolve)
                .catch(reject);
        });
    }

    put(path, params) {
        return new Promise((resolve, reject) => {
            axios.put(path, params)
                .then(resolve)
                .catch(reject);
        });
    }

    destroy(path) {
        return new Promise((resolve, reject) => {
            axios.delete(path)
                .then(resolve)
                .catch(reject);
        });
    }

    upload() {
        return new Promise((resolve, reject) => {
            axios.post(path, params)
                .then(resolve)
                .catch(reject);
        });
    }
}



export default new ApiService();