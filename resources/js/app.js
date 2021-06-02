/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
//Imports
require('./bootstrap');

import Vue from 'vue'

import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/confirmationservice';
import Toast from 'primevue/toast'
import MultiSelect from 'primevue/multiselect'
import ConfirmDialog from 'primevue/confirmdialog'
import ToastService from 'primevue/toastservice'
import VueRouter from 'vue-router'
import ProgressSpinner from 'primevue/progressspinner'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import ColumnGroup from 'primevue/columngroup'
import Row from 'primevue/row'
import Toolbar from 'primevue/toolbar'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Dialog from 'primevue/dialog'
import TextArea from 'primevue/textarea'
import FileUpload from 'primevue/fileupload'
import Tag from 'primevue/tag'

//Stylesheets
import 'primevue/resources/themes/bootstrap4-light-blue/theme.css'
import 'primevue/resources/primevue.min.css'
import 'primeicons/primeicons.css'

//Services
Vue.use(PrimeVue)
Vue.use(ToastService)
Vue.use(ConfirmationService)
Vue.use(VueRouter)

//Components
Vue.use(MultiSelect)
Vue.use(ProgressSpinner)
Vue.use(ConfirmDialog)
Vue.use(DataTable)
Vue.use(Column)
Vue.use(ColumnGroup)
Vue.use(Row)
Vue.use(Toolbar)
Vue.use(Button)
Vue.use(InputText)
Vue.use(InputNumber)
Vue.use(Dialog)
Vue.use(TextArea)
Vue.use(FileUpload)
Vue.use(Tag)

Vue.component('Toast', Toast)
Vue.component('MultiSelect', MultiSelect)
Vue.component('ProgressSpinner', ProgressSpinner)
Vue.component('ConfirmDialog', ConfirmDialog)
Vue.component('DataTable', DataTable)
Vue.component('Column', Column)
Vue.component('ColumnGroup', ColumnGroup)
Vue.component('Row', Row)
Vue.component('Toolbar', Toolbar)
Vue.component('Button', Button)
Vue.component('InputText', InputText)
Vue.component('InputNumber', InputNumber)
Vue.component('Dialog', Dialog)
Vue.component('TextArea', TextArea)
Vue.component('FileUpload', FileUpload)
Vue.component('Tag', Tag)

//Views
import App from './views/App'
import Home from './views/Home'
import Login from './views/Login'
import Register from './views/Register'
import SingleProduct from './views/SingleProduct'
import Checkout from './views/Checkout'
import Confirmation from './views/Confirmation'
import UserBoard from './views/UserBoard'
import Admin from './views/Admin'

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/register',
            name: 'register',
            component: Register
        },
        {
            path: '/products/:id',
            name: 'single-products',
            component: SingleProduct
        },
        {
            path: '/confirmation',
            name: 'confirmation',
            component: Confirmation
        },
        {
            path: '/checkout',
            name: 'checkout',
            component: Checkout,
            props: (route) => ({ pid: route.query.pid })
        },
        {
            path: '/dashboard',
            name: 'userboard',
            component: UserBoard,
            meta: {
                requiresAuth: true,
                is_user: true
            }
        },
        {
            path: '/admin/:page',
            name: 'admin-pages',
            component: Admin,
            meta: {
                requiresAuth: true,
                is_admin: true
            }
        },
        {
            path: '/admin',
            name: 'admin',
            component: Admin,
            meta: {
                requiresAuth: true,
                is_admin: true
            }
        },
    ],
})

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (localStorage.getItem('vue-laravel-ecommerce.jwt') == null) {
            next({
                path: '/login',
                params: { nextUrl: to.fullPath }
            })
        } else {
            let user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.user'))
            if (to.matched.some(record => record.meta.is_admin)) {
                if (user.is_admin == 1) {
                    next()
                }
                else {
                    next({ name: 'userboard' })
                }
            }
            else if (to.matched.some(record => record.meta.is_user)) {
                if (user.is_admin == 0) {
                    next()
                }
                else {
                    next({ name: 'admin' })
                }
            }
            next()
        }
    } else {
        next()
    }
})

    const app = new Vue({
        el: '#app',
        components: { App },
        router,
    });