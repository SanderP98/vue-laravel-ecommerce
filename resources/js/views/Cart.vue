
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="order-box" v-for="(product, index) in cart" :key="index">
                    <!-- {{product}} -->
                    <img :src="'/products/'+product.image" :alt="product.name" height="50px" width="50px">
                    <h2 class="title" v-html="product.name"></h2>
                    <p class="small-text text-muted float-left">{{formatCurrency(product.price * product.quantity)}}</p>
                    <p class="small-text text-muted float-right">Available Units: {{product.units}}</p>
                    <br>
                    <hr>
                    <label class="row"><span class="col-md-2 float-left">Quantity: </span><input type="number" name="quantity" :max="product.units" v-model="product.quantity" class="col-md-2 float-left" @input="checkUnits(product)"></label>
                    <!-- <InputNumber v-model="product.quantity" :min="1" :max="product.units" /> -->
                </div>
                <br>
                <div>
                    <div v-if="!isLoggedIn">
                        <h2>You need to login to continue</h2>
                        <button class="col-md-4 btn btn-primary float-left" @click="login">Login</button>
                        <button class="col-md-4 btn btn-danger float-right" @click="register">Create an account</button>
                    </div>
                    <div v-if="isLoggedIn">
                        <button class="col-md-4 btn btn-sm btn-success float-right" v-if="isLoggedIn" @click="placeOrder">Continue</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props : ['pid'],
    data () {
        return { 
            isLoggedIn : null,
            totalPrice: 0,
            cart: [],
            product: {}
        }
    },
    computed : {
        products: function() {
            return this.$store.getters.cart
        },
        singleProduct() {
            return this.$store.getters.singleOrder
        }
    },
    beforeMount() {
        this.isLoggedIn = localStorage.getItem('vue-laravel-ecommerce.jwt') != null
        if ( this.$route.query.pid ) {
            axios.get(`/api/products/${this.pid}`).then(response => {
                this.$store.commit('singleOrder', response.data)
                this.cart = this.$store.state.singleOrder
            });
        } else {
            this.cart = this.$store.state.cart
        }
        if ( localStorage.getItem('vue-laravel-ecommerce.jwt' ) != null ) {
            this.user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.user'))
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('vue-laravel-ecommerce.jwt')
        }
    },
    methods : {
        formatCurrency(value) {
            if(value) {
                return value.toLocaleString('nl-NL', {style: 'currency', currency: 'EUR'});
            }
        },
        login() {
            this.$router.push({ name: 'login', params: {nextUrl: this.$route.fullPath }}).catch(() => {});
        },
        register() {
            this.$router.push({ name: 'register', params: {nextUrl: this.$route.fullPath }}).catch(() => {});
        },
        placeOrder(e) {
            e.preventDefault();
            if ( this.$route.query.pid ) {
                this.$router.push({ name: 'checkout', params: {nextUrl: this.$route.fullPath, pid: this.$route.query.pid, quantity: this.cart[0].quantity }}).catch(() => {});
            } else {
                this.$router.push({ path: '/checkout', params: {nextUrl: this.$route.fullPath }}).catch(() => {});               
            }
            // let products = this.products
            // let totalPrice = this.products.reduce((total, item)=> {
            //     return total + item.quantity * item.price;
            // }, 0);

            // // axios.post('api/molliepayment', { products, totalPrice }).then(response => {
            // //     window.location.href = response.data.data._links.checkout.href;
            // // }).catch(() => {});
        },
        checkUnits(product) {
            if ( !this.$route.query.pid ) {
                this.$store.commit('changeQuantityItem', product)
                this.$store.commit('totalItems');
                this.$store.commit('totalPrice');    
            } else {
                this.$store.commit('changeQuantitySingleOrder', product)
            }

            //this.$forceUpdate();
        },
    }
}
</script>