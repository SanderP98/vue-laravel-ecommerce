<template>
    <div class="container">
        <!-- <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="order-box" v-for="(product, index) in products" :key="index">
                    <img :src="'/images/'+product.image" :alt="product.name" height="50px" width="50px">
                    <h2 class="title" v-html="product.name"></h2>
                    <p class="small-text text-muted float-left">{{formatCurrency(product.price * product.quantity)}}</p>
                    <p class="small-text text-muted float-right">Available Units: {{product.units}}</p>
                    <br>
                    <hr>
                    <label class="row"><span class="col-md-2 float-left">Quantity: </span><input type="number" name="quantity" min="1" :max="product.units" v-model="product.quantity" class="col-md-2 float-left" @input="checkUnits(product)"></label>
                    <InputNumber v-model="product.quantity" :min="1" :max="product.units" />
                </div>
                <br>
                <div>
                    <div v-if="!isLoggedIn">
                        <h2>You need to login to continue</h2>
                        <button class="col-md-4 btn btn-primary float-left" @click="login">Login</button>
                        <button class="col-md-4 btn btn-danger float-right" @click="register">Create an account</button>
                    </div>
                    <div v-if="isLoggedIn">
                        <div class="row">
                            <label for="address" class="col-md-3 col-form-label">Delivery Address</label>
                            <div class="col-md-9">
                                <input id="address" type="text" class="form-control" v-model="address" required>
                            </div>
                        </div>
                        <br>
                        <button class="col-md-4 btn btn-sm btn-success float-right" v-if="isLoggedIn" @click="placeOrder">Continue</button>
                    </div>
                </div>
            </div>
        </div> -->

            <div class="content-section implementation">
                <div class="p-card p-mb-4">
                    <div class="p-card-body">
                        <Steps :model="items" :readonly="true" /> 
                    </div>
                </div>

                <keep-alive>
                    <router-view :formData="formObject" :pid="this.products" @prevPage="prevPage($event)" @nextPage="nextPage($event)" @complete="complete" />
                </keep-alive>
            </div>
        </div>
</template>

<script>
    export default {
        props : ['pid', 'quantity'],
        data () {
            return {
                // address : "",
                // isLoggedIn : null,
                // products : [],
                // product: {},
                // totalPrice: 0,
                singleProduct: false,
                user: null,
                items: [
                    {
                        label: 'Address',
                        to: '/checkout'
                    },
                    {
                        label: 'Payment',
                        to: '/checkout/payment' 
                    },
                    {
                        label: 'Confirm',
                        to: '/checkout/confirm'
                    },
                    // {
                    //     label: 'Payment',
                    //     to: '/steps/payment'
                    // },
                    // {
                    //     label: 'Confirmation',
                    //     to: '/steps/confirmation'
                    // }
                ],
                formObject: {},
                product: {
                    id: 0,
                    name: 0,
                    price: 0,
                    quantity: 0,
                    image: '',
                    units: 0,
                },
                products: [],
            }
        },
        created() {

        },
        mounted() {
            this.isLoggedIn = localStorage.getItem('vue-laravel-ecommerce.jwt') != null
        },
        beforeMount() {
            console.log(this.$route.params.pid);
            console.log(this.$route.params.quantity);
            // console.log(this.pid)
            if ( this.$route.params.pid ) {
                this.singleProduct = true;
                axios.get(`/api/products/${this.pid}`).then(response => {
                    this.product.id = response.data.id
                    this.product.name = response.data.name
                    this.product.units = response.data.units
                    this.product.image = response.data.image
                    this.product.price = response.data.price
                    this.product.quantity = this.$route.params.quantity
                    this.products.push(this.product);
                    this.product = []
                });
            }
            // } else {
            //     this.products = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.shopCart')); 
            // }

            if ( localStorage.getItem('vue-laravel-ecommerce.jwt' ) != null ) {
                this.user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.user'))
                axios.defaults.headers.common['Access-Control-Allow-Headers'] = '*'
                axios.defaults.headers.common['Access-Control-Allow-Methods'] = '*'
                axios.defaults.headers.common['Content-Type'] = 'application/json'
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('vue-laravel-ecommerce.jwt')
                // console.log(this.products);
            }
        },
        created () {
            this.$on('updateQuantityNonCartItem', this.updateQuantityNonCartItem)
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
            // placeOrder(e) {
            //     e.preventDefault();

            //     let products = this.products
            //     let totalPrice = this.products.reduce((total, item)=> {
            //         return total + item.quantity * item.price;
            //     }, 0);

            //     axios.post('api/molliepayment', { products, totalPrice }).then(response => {
            //         window.location.href = response.data.data._links.checkout.href;
            //     }).catch(() => {});
            // },
            // checkUnits(product) {
            //     this.$parent.$emit('changeQuantityCartItem', product)
            //     this.$forceUpdate();
            // },
            nextPage(event) {
                //console.log(event.formData)
                for (let field in event.formData) {
                    this.formObject[field] = event.formData[field];
                }
                this.$router.push(this.items[event.pageIndex + 1].to);
            },
            prevPage(event) {
                this.$router.push(this.items[event.pageIndex - 1].to);
            },
            complete(event) {
                for (let field in event.formData) {
                    this.formObject[field] = event.formData[field];
                }
                let address_id = this.formObject.address_id
                let country = this.formObject.address.country
                let address = this.formObject.address.address
                let address_2 = this.formObject.address.address_2
                let city = this.formObject.address.city
                let state = this.formObject.address.state
                let postal_code = this.formObject.address.postal_code
                let user = this.user.id
                if ( !this.formObject.hasAddress ) {
                    //store     
                    axios.post(`/api/address`, { user, country, address, address_2, city, state, postal_code });              
                } else if ( this.formObject.hasAddress && this.formObject.editAddress ) {
                    //update
                    axios.put(`/api/address/${address_id}`, { user, country, address, address_2, city, state, postal_code }); 
                }
                if ( this.singleProduct == true ) {
                    var products = this.products
                } else {
                    var products = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.shopCart'))        
                }
                let totalPrice = products.reduce((total, item)=> {
                    return total + item.quantity * item.price;
                }, 0);
                let paymentMethod = this.formObject.paymentMethod
                let isSingleProduct = this.singleProduct
                axios.post('/api/molliepayment', { products, totalPrice, paymentMethod, isSingleProduct }).then(response => {
                    window.location.href = response.data.data._links.checkout.href;
                    // if (response.data.data.status == "paid") {
                    //     alert('betaald')
                    // }
                    // let config = {
                    // headers: {
                    //     'Content-Type': 'application/json;charset=UTF-8',
                    //     "Access-Control-Allow-Origin": "*",
                    // }
                    // }
                    // axios.get(response.data.data._links.self.href, config).then(response => {
                    //     console.log(response.data.data)
                    // })

                }).catch(() => {});
                this.singleProduct = false;
            },
            updateQuantityNonCartItem(product) {
                let findProduct = this.products.find(o => o.id === product.id)
                if (findProduct) {
                    console.log(findProduct)
                    if ( product.quantity > product.units ) {
                        product.quantity = product.units
                    } else {
                        findProduct.quantity = product.quantity;
                    }
                    findProduct.subtotal = product.quantity * findProduct.price;
                } 
            }
        }
    }
</script>


<style lang="scss" scoped>
.small-text { 
    font-size: 18px; 
}
.order-box { 
    border: 1px solid #cccccc; padding: 10px 15px; 
}
.title { 
    font-size: 36px; 
}
::v-deep(b) {
    display: block;
}
::v-deep(.p-card-body) {
    padding: 2rem;
}
</style>