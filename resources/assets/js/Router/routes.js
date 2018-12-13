import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Main from '../components/Main';
import Login from '../components/Auth/Login';
import Dashboard from '../components/Admin/Dashboard/js/index';
import Client from '../components/Admin/Client/js/index';

const routes = [
    {
        path: '/',
        name: 'main',
        component: Main,
        meta: {
            requiresLogin: true,
        },
        children : [
            {
                path: '/dashboard',
                component: Dashboard,
                meta: {
                    requiresLogin: true,
                    requiredPermissions: ['GROUP1', 'GROUP2'],
                    permissionType: 'AtLeastOne',
                },
            },
            {
                path: '/clients',
                component: Client,
                meta: {
                    requiresLogin: true,
                    requiredPermissions: ['GROUP1', 'GROUP2'],
                    permissionType: 'AtLeastOne',
                },
            },
        ],
        
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
];


export default new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: "active",
    linkExactActiveClass: "exact-active",
});