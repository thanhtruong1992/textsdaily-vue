import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Main from '../components/Main';
import Login from '../components/Auth/Login';

const routes = [
    {
        path: '/',
        name: 'main',
        component: Main
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