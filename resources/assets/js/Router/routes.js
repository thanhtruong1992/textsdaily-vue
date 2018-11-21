import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Main from '../components/Main';
import Login from '../components/Auth/Login';
import Dashboard from '../components/Admin/Dashboard/js/index.vue';

const routes = [
    {
        path: '/',
        name: 'main',
        component: Main,
        children : [
            {
                path: '/dashboard',
                component: Dashboard
            }
        ]
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
];

export default new VueRouter({
    routes,
    mode: 'history'
});