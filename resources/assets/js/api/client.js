import ApiService from './index';

class ClientApi {
    getAll () {
        return new Promise((resolve, reject) => {
            ApiService.get('/client')
                .then(res => {
                    resolve(res.data.data);
                })
                .catch(err => {
                    reject(err.response.data);
                });
        });
    }

    get (id) {
        return new Promise((resolve, reject) => {
            ApiService.get('/client/' + id)
                .then(res => {
                    resolve(res.data.data);
                })
                .catch(err => {
                    reject(err.response.data);
                });
        });
    }

    create (params) {
        return new Promise((resolve, reject) => {
            ApiService.post('/client', params)
                .then(res => {
                    resolve(res.data.data);
                })
                .catch(err => {
                    reject(err.response.data);
                });
        });
    }

    update (id, params) {
        return new Promise((resolve, reject) => {
            ApiService.put('/client/' + id, params)
                .then(res => {
                    resolve(res.data.data);
                })
                .catch(err => {
                    reject(err.response.data);
                });
        });
    }

    destroy (id) {
        return new Promise((resolve, reject) => {
            ApiService.destroy('/client/' + id)
                .then(res => {
                    resolve(res.data.data);
                })
                .catch(err => {
                    reject(err.response.data);
                });
        });
    }

    destroyMultiple (params) {
        return new Promise((resolve, reject) => {
            ApiService.post('/client/delete', params)
                .then(res => {
                    resolve(res.data);
                })
                .catch(err => {
                    reject(err.response.data);
                });
        });
    }
}

export default new ClientApi();