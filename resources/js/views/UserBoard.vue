<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2">
                    <ul style="list-style-type:none" class="mx-auto d-table">
                        <li class="active"><button class="btn" @click="setComponent('main')">Dashboard</button></li>
                        <li><button class="btn" @click="setComponent('orders')">Orders</button></li>
                    </ul>
                </div>
                <div class="col-lg-10" v-bind:class="[this.$route.params.id ? 'order-page' : '']">
                    <component :is="activeComponent"></component>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Main from '../components/user/Main'
    import Orders from '../components/user/Orders'
    import Order from '../components/user/Order'
    export default {
        props: ['success'],
        data() {
            return {
                user : null,
                orders : [],
                selectedOrder: null,
                activeComponent : null,
            }
        },
        components : {
            Main, Orders, Order
        },
        beforeMount() {
            this.$on('setComponent', this.setComponent)
            if ( this.$route.query.success ) {
                this.activeComponent = Main;   
                this.setComponent(this.$route.params.page)
                this.$parent.$emit('emptyCart')
            }
            else if(this.$route.params.id) {
                this.activeComponent = Order;
                this.$router.push({ name: 'order-page', params: {page: 'orders', id: this.$route.params.id }}).catch(() => {});
            } 
            else
            {
                this.activeComponent = Main;   
                this.setComponent(this.$route.params.page)
            }
            this.user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.user'))
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('vue-laravel-ecommerce.jwt')
        },
        methods : {
            setComponent(value, id) {
                //if a single order is selected
                this.selectedOrder = id;
                switch(value) {
                    case "main":
                        this.activeComponent = Main;
                        this.$router.push({ name: 'userboard'}).catch(() => {});
                        break;
                    case "orders":
                        this.activeComponent = Orders;
                        this.$router.push({ name: 'user-pages', params: {page: 'orders' }}).catch(() => {});
                        break;                    
                    case "order":
                        this.activeComponent = Order;
                        this.$router.push({ name: 'order-page', params: {page: 'orders', id: this.selectedOrder }}).catch(() => {});
                        break;
                    default:
                        this.activeComponent = Main;
                        this.$router.push({ name: 'userboard'}).catch(() => {});
                        break;
                }
            },
        }
    }
</script>

<style scoped>
.small-text { 
    font-size: 14px; 
}
.product-box { 
    border: 1px solid #cccccc; padding: 10px 15px; 
}
.hero-section { 
    background: #ababab; 
    height: 20vh; align-items: center; 
    margin-bottom: 20px; 
    margin-top: -20px; 
}
.title { 
    font-size: 60px; 
    color: #ffffff; 
}
.order-page {
    position: absolute;
    margin-left: auto;
    margin-right: auto;
    left: 0;
    max-width: 100%;   
    pointer-events:all;
    z-index:-1; 
}
@media screen and (max-width: 64em) {
    .order-page {
        margin-top:100px;
    }
}
</style>