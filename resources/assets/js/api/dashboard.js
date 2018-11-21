import ApiService from './index';

class DashboardApi {

    index () {
        return new Promise((resolve, reject) => {
            ApiService.get('/dashboard')
                .then(res => {
                    var data = res.data.data;
                    localStorage.setItem('dashboard', JSON.stringify(data));
                    resolve(data);
                })
                .catch(err => {
                    localStorage.removeItem('dashboard');
                    reject(err.response.data);
                });
        });
    }
}

export default new DashboardApi();