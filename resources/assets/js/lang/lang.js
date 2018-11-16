import Vue from 'vue';
import VueI18n from 'vue-i18n';

Vue.use(VueI18n);

// Create VueI18n instance with options
export default new VueI18n({
    locale: window.lang, // set locale
    messages: window.trans, // set locale messages
});