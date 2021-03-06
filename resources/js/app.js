/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import moment from 'moment';
import { Form, HasError, AlertError } from 'vform';

import Gate from './Gate';
Vue.prototype.$gate = new Gate(window.user);
Vue.prototype.$translations = window.translations;
Vue.prototype.$language = window.language;
Vue.prototype.$http = '/api/v1/admin/';


import Swal from 'sweetalert2';
window.Swal = Swal;

import './plugins/vee-validate';

import Vue2Editor from "vue2-editor";
Vue.use(Vue2Editor);

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

window.Toast = Toast;

window.Form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);
Vue.component('pagination', require('laravel-vue-pagination'));

import VueProgressBar from 'vue-progressbar'
Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'green',
    height: '4px'
});

import VueRouter from 'vue-router';
Vue.use(VueRouter);

let routes = [
    { path: '/dashboard', component: require('./components/Dashboard').default },
    { path: '/menus', component: require('./components/Menus').default },
    { path: '/slides', component: require('./components/Slides').default },
    { path: '/advantages', component: require('./components/Advantages').default },
    { path: '/how-to', component: require('./components/HowTo').default },
    { path: '/news', component: require('./components/News').default },
    { path: '/settings', component: require('./components/Settings').default },
    { path: '/feedback', component: require('./components/Feedback').default },
    { path: '/developer', component: require('./components/Developer').default },
    { path: '/users', component: require('./components/Users').default },
    { path: '/profile', component: require('./components/Profile').default  },
    { path: '*', component: require('./components/NotFound').default  }
];

const router = new VueRouter({
    mode: 'history',
    routes
});

Vue.filter('toCapitalize', function (text) {
    return text.charAt(0).toUpperCase() + text.slice(1)
});

Vue.filter('created', function(date){
    return moment(date).format('MMMM Do YYYY');
});

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

Vue.component(
    'not-found',
    require('./components/NotFound').default
);

window.Fire = new Vue();



/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    data: {
        search: ''
    },
    methods: {
        searchIt(){
            Fire.$emit('searching');
        }
    }
});
