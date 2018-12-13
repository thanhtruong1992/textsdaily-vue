import UserHasPermissions from './UserHasPermissions'
import RedirectIfAuthenticated from './RedirectIfAuthenticated'

export default function middleware (router) {
    UserHasPermissions(router)
    RedirectIfAuthenticated(router)
}