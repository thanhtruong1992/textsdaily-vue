import AuthApi from '../api/auth';

export default function RedirectIfAuthenticated (router) {
    /**
     * If the user is already authenticated he shouldn't be able to visit 
     * pages like login, register, etc...
     */
    router.beforeEach((to, from, next) => {
        
        let token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('auth'));

        if(_.isEmpty(user)) {
            AuthApi.findMe()
                .then(res => {
                    router.push('/dashboard');
                })
                .catch(err => {
                    router.push('/login');
                });
        }

        next()
    })
}