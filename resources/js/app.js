/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue').default;
Vue.component('pagination', require('laravel-vue-pagination'));
import Swal from 'sweetalert2'
window.Swal = require('sweetalert2')


import router from './router'
import store from './store'

const app = new Vue({
    el: '#app',
    router:router,
    store:store
});
