<template>
    <div>
        <div class="container-fluid hero-section d-flex align-content-center justify-content-center flex-wrap ml-auto">
            <h2 class="title">Admin Dashboard</h2>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2">
                    <ul style="list-style-type:none" class="mx-auto d-table">
                        <li class="active"><button class="btn" @click="setComponent('main')">Dashboard</button></li>
                        <li><button class="btn" @click="setComponent('orders')">Orders</button></li>
                        <li><button class="btn" @click="setComponent('products')">Products</button></li>
                        <li><button class="btn" @click="setComponent('users')">Users</button></li>
                    </ul>
                </div>
                <div class="col-lg-8">
                    <component :is="activeComponent"></component>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Main from '../components/admin/Main'
    import Users from '../components/admin/Users'
    import Products from '../components/admin/Products'
    import Orders from '../components/admin/Orders'

    export default {
        data () {
            return {
                user: null,
                activeComponent: null
            }
        },
        components: {
            Main, Users, Products, Orders
        },
        beforeMount() {
            this.setComponent(this.$route.params.page)
            this.user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce. d fuser'))
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('vue-laravel-ecommerce.jwt')
        },
        methods: {
            setComponent(value) {
                switch(value) {
                    case "users":
                        this.activeComponent = Users
                        this.$router.push({ name: 'admin-pages', params: {page: 'users' }}).catch(() => {});
                        break;
                    case "orders":
                        this.activeComponent = Orders
                        this.$router.push({ name: 'admin-pages', params: {page: 'orders' }}).catch(() => {});
                        break;    
                    case "products":
                        this.activeComponent = Products
                        this.$router.push({ name: 'admin-pages', params: {page: 'products' }}).catch(() => {});
                        break; 
                    default: 
                        this.activeComponent = Main
                        this.$router.push({name: 'admin'}).catch(() => {});
                        break;                       
                }
            }
        }
    }
</script>
