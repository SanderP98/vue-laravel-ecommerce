/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
//Imports
require('./bootstrap');

//Stylesheets
import 'primevue/resources/themes/saga-blue/theme.css'
import 'primevue/resources/primevue.min.css'
import 'primeicons/primeicons.css'
import 'primeflex/primeflex.css'

//PrimeVue
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
import DataView from 'primevue/dataview'
import Dropdown from 'primevue/dropdown'
import DataViewLayoutOptions  from 'primevue/dataviewlayoutoptions'
import Rating from 'primevue/rating'
import Carousel from 'primevue/carousel'
import OverlayPanel from 'primevue/overlaypanel'
import Card from 'primevue/card'
import Divider from 'primevue/divider'
import Avatar from 'primevue/avatar'
import AvatarGroup from 'primevue/avatargroup'
import Steps from 'primevue/steps'
import RadioButton from 'primevue/radiobutton'
import ScrollPanel from 'primevue/scrollpanel'
import ProgressBar from 'primevue/progressbar'
import Galleria from 'primevue/galleria'

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
Vue.use(DataView)
Vue.use(Dropdown)
Vue.use(DataViewLayoutOptions)
Vue.use(Rating)
Vue.use(Carousel)
Vue.use(OverlayPanel)
Vue.use(Card)
Vue.use(Divider)
Vue.use(Avatar)
Vue.use(AvatarGroup)
Vue.use(Steps)
Vue.use(RadioButton)
Vue.use(ScrollPanel)
Vue.use(ProgressBar)
Vue.use(Galleria)

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
Vue.component('DataView', DataView)
Vue.component('Dropdown', Dropdown)
Vue.component('DataViewLayoutOptions', DataViewLayoutOptions)
Vue.component('Rating', Rating)
Vue.component('Carousel', Carousel)
Vue.component('OverlayPanel', OverlayPanel)
Vue.component('Card', Card)
Vue.component('Divider', Divider)
Vue.component('Avatar', Avatar)
Vue.component('AvatarGroup', AvatarGroup)
Vue.component('Steps', Steps)
Vue.component('RadioButton', RadioButton)
Vue.component('ScrollPanel', ScrollPanel)
Vue.component('ProgressBar', ProgressBar)
Vue.component('Galleria', Galleria)


//Views
import App from './views/App'
import Home from './views/Home'
import Login from './views/Login'
import Register from './views/Register'
import SingleProduct from './views/SingleProduct'
import Checkout from './views/Checkout'
import Address from './components/order-steps/Address'
import Payment from './components/order-steps/Payment'
import OrderCheck from './components/order-steps/OrderCheck'
import Confirmation from './views/Confirmation'
import UserBoard from './views/UserBoard'
import Admin from './views/Admin'
import Cart from './views/Cart'

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
            path: '/cart',
            name: 'cart',
            component: Cart,
            meta: {
                requiresAuth: true,
            },
            props: (route) => ({ pid: route.query.pid })
        },        
        {
            path: '/checkout',
            component: Checkout,
            children: [
                {
                // path: '',
                //     component: Personal,
                // }, later afrekenen als gast toevoegen 
                    name: 'checkout',              
                    path: '',
                    component: Address,
                },
                {
                    path: '/checkout/payment',
                    component: Payment,
                },                
                {
                    path: '/checkout/confirm',
                    component: OrderCheck,
                }
            ],
            meta: {
                requiresAuth: true,
            },
            props: true
        },

        {
            path: '/dashboard/:page',
            name: 'user-pages',
            component: UserBoard,
            children: [
                {
                  path: '/dashboard/:page/:id',
                  name: 'order-page',
                  component: UserBoard,
                },
            ],
            meta: {
                requiresAuth: true,
                is_user: true
            },
            props: (route) => ({ success: route.query.clear_cart })
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