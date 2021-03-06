import ApiService from './index';

class AuthApi {
    login (params) {
        return new Promise((resolve, reject) => {
            ApiService.post('/login', params)
                .then(res => {
                    var data = res.data.data;
                    localStorage.setItem('auth', JSON.stringify(data));
                    resolve(data);
                })
                .catch(err => {
                    reject(err.response.data);
                });
        });
    }

    findMe () {
        return new Promise((resolve, reject) => {
            ApiService.get('/me')
                .then(res => {
                    var data = res.data.data;
                    localStorage.setItem('auth', JSON.stringify(data));
                    resolve(data);
                })
                .catch(err => {
                    localStorage.removeItem('auth');
                    reject(err.response.data);
                });
        });
    }

    switchAccount (id) {
        return new Promise((resolve, reject) => {
            ApiService.get('/login-with-other-role/' + id)
                .then(res => {
                    var data = res.data.data;
                    localStorage.setItem('auth', JSON.stringify(data));
                    resolve(data);
                })
                .catch(err => {
                    reject(err.response.data);
                });
        });
    }

    returnParent () {
        return new Promise((resolve, reject) => {
            ApiService.get('/return-parent')
                .then(res => {
                    var data = res.data.data;
                    localStorage.setItem('auth', JSON.stringify(data));
                    resolve(data);
                })
                .catch(err => {
                    reject(err.response.data);
                });
        });
    }

    logOut () {
        return new Promise((resolve, reject) => {
            ApiService.get('/logout')
                .then(res => {
                    localStorage.removeItem('auth');
                    resolve();
                })
                .catch(err => {
                    reject(err.response.data);
                });
        });
    }
}

export default new AuthApi();