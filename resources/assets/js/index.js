require('./bootstrap');

import Vue from 'vue';
import router from './Router/routes.js';
import i18n from './lang/lang';
import middleware from './Middleware/index.js';

middleware(router);

new Vue({
    i18n,
    router: router
}).$mount('#root');