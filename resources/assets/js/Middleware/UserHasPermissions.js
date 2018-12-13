/**
 * This is where all the authorization login is stored
 */
import Authorization from './Authorization'

export default function UserHasPermissions (router) {
    /**
     * Before each route we will see if the current user is authorized
     * to access the given route
     */
    router.beforeEach((to, from, next) => {
        let authorized = false
        // let user = JSON.parse(window.localStorage.getItem('atiiv.auth-user'))
        
        /**
         * Remember that access object in the routes? Yup this why we need it.
         *
         */
        if (!_.isEmpty(to.meta)) {
            authorized = Authorization.authorize(
                to.meta.requiresLogin || null,
                to.meta.requiredPermissions || [],
                to.meta.permissionType || null
            )

            if (authorized === 'loginIsRequired') {
                router.push('/login');
            }

            if (authorized === 'notAuthorized') {
                /**
                 * Redirects to a "default" page 
                 */
                router.push('/login');
            }
        }
        /**
         * Everything is fine? Let's to the page then.
         */
        next()
    })
}