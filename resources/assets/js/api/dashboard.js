import ApiService from './index';

class DashboardApi {

    totalSend (data) {
        return new Promise((resolve, reject) => {
            ApiService.get('/campaigns/total-send', data)
                .then(res => {
                    resolve(res.data);
                })
                .catch(err => {
                    reject(errerr.response.data);
                });
        });
    }
}

export default new DashboardApi();