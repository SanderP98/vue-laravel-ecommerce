<template>
    <div>
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <router-link :to="{name: 'home'}" class="navbar-brand">vue-laravel-ecommerce</router-link>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto"></ul>
                    <ul class="navbar-nav ml-auto">
                        <router-link :to="{ name: 'login' }" class="nav-link" v-if="!isLoggedIn">Login</router-link>
                        <router-link :to="{ name: 'register' }" class="nav-link" v-if="!isLoggedIn">Register</router-link>
                        <span v-if="isLoggedIn">
                            <router-link :to="{ name: 'userboard' }" class="nav-link" v-if="user_type == 0"><Button v-if="isLoggedIn" icon="pi pi-user" :label="'Hi, ' + name" class="p-button-raised p-button-rounded"/></router-link>
                            <router-link :to="{ name: 'admin' }" class="nav-link" v-if="user_type == 1"><Button v-if="isLoggedIn" icon="pi pi-user" :label="'Hi, ' + name" class="p-button-raised p-button-rounded"/></router-link>
                        </span>

                    </ul>
                    <ul class="navbar-nav mr-auto">
                        <li v-if="isLoggedIn" class="nav-link"><Button :disabled="totalItems == '0'" icon="pi pi-shopping-cart" id="body" class="p-button-raised p-button-rounded p-button-secondary" :label="totalItems" @click="showCart" /></li> <!---->
                        <li class="nav-link" v-if="isLoggedIn" @click="logout"><Button v-if="isLoggedIn" icon="pi pi-sign-in" label="Log out" class="p-button-raised p-button-danger p-button-rounded"/></li>
                        <OverlayPanel ref="op" appendTo="body" :showCloseIcon="true" id="overlay_panel" style="width: 550px">

                            <DataTable :value="cartItems">
                                <template #header>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="badge badge-primary badge-pill">{{totalItems}}</span>
                                    {{formatCurrency(totalPrice)}}
                                </li>
                                </template>
                                <Column field="name">
                                    <template #body="slotProps">
                                        {{slotProps.data.name}}
                                    </template>
                                </Column>
                                <Column>
                                    <template #body="slotProps">
                                        <img :src="'/images/' + slotProps.data.image" :alt="slotProps.data.image" class="product-image" />
                                    </template>
                                </Column>
                                <Column field="price">
                                    <template #body="slotProps">
                                        {{formatCurrency(slotProps.data.subtotal)}}
                                    </template>
                                </Column>
                                <Column field="quantity" class="cartArrows">
                                    <template #body="slotProps">
                                        <div class="p-grid mt-0 p-0">
                                            <div class="p-col-9 mt-0 mb-0">
                                                <InputNumber v-model="slotProps.data.quantity" inputClass="amountItem" decrementButtonClass="decreaseAmount" incrementButtonClass="increaseAmount" showButtons @input="changeQuantityCartItem(slotProps.data)" />
                                            </div>
                                            <div class="p-col-3 mt-0 mb-0">
                                                <Button v-model="slotProps.data.id" class="p-button-rounded p-button-danger deleteFromCartBtn p-col-6" icon="pi pi-times" @click="deleteItemFromCart(slotProps.data)" />
                                            </div>
                                        </div>
                                    </template>
                                </Column>
                                <!-- <Column field="action">
                                    <template #body="slotProps">
                                        <Button v-model="slotProps.data.id" class="p-button-rounded p-button-danger deleteFromCartBtn p-col-6" icon="pi pi-times" @click="removeFromCart(slotProps.data)" />
                                    </template>
                                </Column> -->
                            </DataTable>
                            <router-link :to="{ name: 'cart' }" v-if="user_type == 0 || user_type == 1"><Button class="p-button-raised float-right m-2" @click="showCart()">Checkout</Button></router-link>
                        </OverlayPanel>
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-4">
            <router-view @loggedIn="change"></router-view>
        </main>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                name : null,
                user_type : 0,
                cartItems: [],
                newCartItem: {
                    id: 0,
                    name: 0,
                    price: 0,
                    quantity: 0,
                    image: '',
                    units: 0,
                },
                totalPrice: 0,
                quantity: 0,
                totalItems: "0",
                isLoggedIn: localStorage.getItem('vue-laravel-ecommerce.jwt') != null
            }
        },
        mounted() {
            this.setDefaults()
            if (localStorage.getItem('vue-laravel-ecommerce.shopCart')) {
                this.cartItems = Object.values(JSON.parse(localStorage.getItem('vue-laravel-ecommerce.shopCart')));
                this.totalItems = String(this.cartItems.reduce((total, item)=> {
                    return total + item.quantity;
                }, 0));
                this.totalPrice = this.cartItems.reduce((total, item)=> {
                    return total + item.subtotal;
                }, 0);
            }
        },
        created() {
            this.$on('emptyCart', this.emptyCart)
            this.$on('addToCart', this.addToCart)
        },
        methods : {
            formatCurrency(value) {
                return value.toLocaleString('nl-NL', {style: 'currency', currency: 'EUR'});
            },
            setDefaults() {
                if(this.isLoggedIn) {
                    let user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.user'))
                    this.name = user.name
                    this.user_type = user.is_admin
                }
            },
            change() {
                this.isLoggedIn = localStorage.getItem('vue-laravel-ecommerce.jwt') != null
                this.setDefaults()
            },
            logout() {
                localStorage.removeItem('vue-laravel-ecommerce.jwt')
                localStorage.removeItem('vue-laravel-ecommerce.user')
                localStorage.removeItem('vue-laravel-ecommerce.shopCart');
                this.cartItems = [];
                this.totalItems = "0";
                this.totalPrice = 0;
                this.change()
                this.$router.push('/').catch(()=>{});
            },
            showCart(event) {
                this.$refs.op.toggle(event);
            },
            addToCart(product) {
                let findProduct = this.cartItems.find(o => o.id === product.id)
                if(findProduct){
                    findProduct.quantity +=1;
                    findProduct.subtotal = findProduct.quantity * findProduct.price;
                } else {
                    this.newCartItem.id = product.id
                    this.newCartItem.name = product.name;
                    this.newCartItem.price = product.price;
                    this.newCartItem.quantity = product.quantity;
                    this.newCartItem.image = product.image;
                    this.newCartItem.subtotal = product.price;
                    this.newCartItem.units = product.units;
                    this.cartItems.push(this.newCartItem);
                    this.newCartItem = {}
                }
                this.storeToCart();
            },
            storeToCart() {
                let parsed = JSON.stringify(this.cartItems);
                localStorage.setItem('vue-laravel-ecommerce.shopCart', parsed)
                this.totalItems = String(this.cartItems.reduce((total, item)=> {
                    return total + item.quantity;
                }, 0));
                this.totalPrice = this.cartItems.reduce((total, item)=> {
                    return total + item.subtotal;
                }, 0);
            },
            changeQuantityCartItem(product) {
                let findProduct = this.cartItems.find(o => o.id === product.id)
                if (findProduct) {
                    findProduct.quantity = product.quantity;
                    findProduct.subtotal = product.quantity * findProduct.price;
                }    
                this.storeToCart();           
            },
            deleteItemFromCart(product) {
                let findProduct = this.cartItems.find(o => o.id === product.id)
                if (findProduct) {
                    this.cartItems.pop(this.findProduct);
                }   
                this.storeToCart();                      
            },
            emptyCart() {
                localStorage.removeItem('vue-laravel-ecommerce.shopCart');
                this.cartItems = [];
                this.totalPrice = 0;
                this.totalItems = "0";
            }
        },
    }
</script>

<style lang="scss">
.product-image {
    width: 50px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23)
}
.amountItem, .amountItem + span {
    width: 20%;
}
.decreaseAmount, .increaseAmount {
    width: 1.5rem!important;
}
.cartArrows {
    padding-left:0;
}
</style>
