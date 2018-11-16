require('./bootstrap');

import Vue from 'vue';
import routes from './Router/routes.js';
import i18n from './lang/lang';

new Vue({
    i18n,
    router: routes
}).$mount('#root');