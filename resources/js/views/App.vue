<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light bg-white" v-if="shopInfo">
            <div class="container-fluid">
                <div class="col-lg-6">
                    <div class="navbar-collapse dual-nav collapse">
                        <ul class="navbar-nav telAndMail mt-2 mt-lg-0 w-100 p-jc-start">
                            <li class="nav-item p-pr-1">
                                <i class="pi pi-phone nav-link"><span class="p-component p-ml-2">{{shopInfo.tel_nr}}</span></i>
                            </li>             
                            <li class="nav-item p-pl-1 p-pr-1">
                                <i class="pi pi-envelope nav-link p-pr-1"><span class="p-component p-ml-2">{{shopInfo.email}}</span></i>
                            </li>
                        </ul>                   
                    </div>
                </div>
                <div class="col-lg-6 p-text-right navbar-collapse collapse dual-nav">
                    <ul class="navbar-nav mt-2 mt-lg-0 w-100 p-jc-end">
                        <li class="nav-item p-pr-1 p-pl-0">
                            <span class="p-component">Our showroom</span>
                        </li>
                        <li class="nav-item p-pl-1 p-pr-1">
                            <span class="p-component">Helpdesk</span>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <nav class="navbar navbar-expand-lg navbar-light bg-white p-mb-3">
            <div class="container-fluid">
                <div class="col-lg-2">
                    <button class="navbar-toggler" data-toggle="collapse" data-target=".dual-nav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="navbar-collapse collapse dual-nav w-25">
                        <ul class="navbar-nav">
                            <li class="nav-item active">
                                <router-link :to="{ name: 'home' }"><img v-if="shopInfo" class="navbar-brand mx-auto d-block w-75" :src="'/shop/' + shopInfo.image" :alt="shopInfo.name" /></router-link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="navbar-collapse dual-nav collapse nav-link">
                        <ul class="navbar-nav mt-2 mt-lg-0 w-100 p-jc-center">
                            <li class="nav-item p-pl-1 p-pr-1 w-25">
                                <Dropdown v-model="selectedCategory" :options="categories" optionLabel="name" class="w-100" placeholder="Select a Category" />
                            </li>             
                            <li class="nav-item p-pl-1 p-pr-1 w-50">
                                <AutoComplete v-model="selectedProduct" :suggestions="filteredProducts" @complete="searchProduct($event)" placeholder="Search for a Product">
                                    <template #item="slotProps">
                                        <img :alt="slotProps.item.product_image[0].name" :src="'/products/' + slotProps.item.product_image[0].image" width="50px" height="50px" />
                                        <div>{{slotProps.item.name}}</div>
                                    </template>
                                </AutoComplete>
                            </li>
                            <li class="nav-item p-pl-1 p-pr-1 w-10">
                                <Button label="Search" class="w-100"  />
                            </li>
                        </ul>                   
                    </div>
                </div>
                <div class="col-lg-2 p-text-right navbar-collapse collapse dual-nav">
                    <ul class="navbar-nav mt-2 mt-lg-0 w-100 p-jc-end">
                        <li class="nav-item">
                            <span class="fa-stack fa-2x has-badge" :data-count="totalItems" @click="showCart" >
                                <i class="fa fa-circle fa-stack-1x fa-inverse"></i>
                                <i style="" class="fa fa-shopping-cart fa-stack-1x red-cart"></i>
                            </span>
                        </li>
                        <li class="nav-link" v-if="isLoggedIn" @click="logout">
                            <Button v-if="isLoggedIn" icon="pi pi-sign-in" label="Log out" class="p-button-raised p-button-danger p-button-rounded"/>
                        </li>
                    </ul>
                    <!-- <div><li class="nav-link" v-if="isLoggedIn" @click="logout"><Button v-if="isLoggedIn" icon="pi pi-sign-in" label="Log out" class="p-button-raised p-button-danger p-button-rounded"/></li></div>
                    <Button :disabled="totalItems == '0'" icon="pi pi-shopping-cart" id="body" class="p-button-raised p-button-rounded p-button-secondary ml-auto" :label="String(totalItems)" @click="showCart" /> -->
                    <OverlayPanel ref="op" appendTo="body" :showCloseIcon="true" id="overlay_panel" style="width: 550px">
                        <DataTable :value="cart">
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
                                    <img :src="'/products/' + slotProps.data.image" :alt="slotProps.data.image" class="product-image" />
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
                                            <InputNumber v-model="slotProps.data.quantity" inputClass="amountItem" decrementButtonClass="decreaseAmount" incrementButtonClass="increaseAmount" showButtons @input="changeQuantityItem(slotProps.data)" />
                                        </div>
                                        <div class="p-col-3 mt-0 mb-0">
                                            <Button v-model="slotProps.data.id" class="p-button-rounded p-button-danger deleteFromCartBtn p-col-6" icon="pi pi-times" @click="removeFromCart(slotProps.data)" />
                                        </div>
                                    </div>
                                </template>
                            </Column>
                        </DataTable>

                        <router-link :to="{ name: 'cart' }" v-if="user_type == 0 || user_type == 1"><Button class="p-button-raised float-right m-2" @click="showCart()">Checkout</Button></router-link>
                    </OverlayPanel>
                </div>

                <!-- <div class="collapse navbar-collapse dual-nav w-50" id="navbarSupportedContent"> 
                    <ul class="navbar-nav mt-2 mt-lg-0">
                        <li class="nav-item me-2">
                            <Dropdown v-model="selectedCategory" :options="categories" optionLabel="name" placeholder="Select a Category" />
                        </li>             
                        <li class="nav-item">
                            <AutoComplete v-model="selectedProduct" :suggestions="filteredProducts" @complete="searchProduct($event)" field="name" />
                        </li>
                        <li>
                            <Button label="Search" />
                        </li>
                    </ul>
                </div> -->
            </div>
        </nav>
        
        <!-- <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
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
                        <li v-if="isLoggedIn" class="nav-link"><Button :disabled="totalItems == '0'" icon="pi pi-shopping-cart" id="body" class="p-button-raised p-button-rounded p-button-secondary" :label="String(totalItems)" @click="showCart" /></li>
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
                                <Column field="action">
                                    <template #body="slotProps">
                                        <Button v-model="slotProps.data.id" class="p-button-rounded p-button-danger deleteFromCartBtn p-col-6" icon="pi pi-times" @click="removeFromCart(slotProps.data)" />
                                    </template>
                                </Column>
                            </DataTable>
                            <router-link :to="{ name: 'cart' }" v-if="user_type == 0 || user_type == 1"><Button class="p-button-raised float-right m-2" @click="showCart()">Checkout</Button></router-link>
                        </OverlayPanel>
                    </ul>
                </div>
            </div>
        </nav> -->
        <main class="py-0">
   <!-- {{error}}          -->
            <Toast position="top-right">{{error}}</Toast>
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
                shopInfo: [],
                cartItems: [],
                products: [],
                categories: [],
                filteredProducts: null,
                selectedProduct: null,
                selectedCategory: null,
                newCartItem: {
                    id: 0,
                    name: 0,
                    price: 0,
                    quantity: 0,
                    image: '',
                    units: 0,
                },
                totalItemsString: '0',
                isLoggedIn: localStorage.getItem('vue-laravel-ecommerce.jwt') != null
            }
        },
        computed : {
            cart() {
                return this.$store.state.cart
            },
            totalItems() {
                return this.$store.state.totalItems
            },
            totalPrice() {
                return this.$store.state.totalPrice
            },
            error: function() {
                if (this.$store.state.error !== null ) {
                    this.$toast.add({severity:'warn', summary: 'Warn Message', detail:this.$store.state.error, life: 3000});
                    this.$store.commit('clearError')
                }
                return this.$store.state.error
            }
        },
        beforeMount() {
            axios.get("api/products/").then(response => {
                this.products = response.data.products
                this.categories = response.data.categories
            })
            axios.get('/api/shop').then( response => {
                this.shopInfo = response.data[0] 
            })
            this.$store.commit('totalItems');
            this.$store.commit('totalPrice');
        },
        mounted() {
            this.setDefaults()
            this.$on("updateNav", function() {
                axios.get('/api/shop').then( response => {
                    this.shopInfo = response.data[0] 
                })                   
            });
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
            changeQuantityItem ( product ) {
                this.$store.commit('changeQuantityItem', product)
                this.$store.commit('totalItems');
                this.$store.commit('totalPrice');    
            },
            removeFromCart ( product ) {  
                this.$store.commit('removeFromCart', product);       
                this.$store.commit('totalItems');
                this.$store.commit('totalPrice');         
            },
            searchProduct(event) {
                setTimeout(() => {
                    if (!event.query.trim().length) {
                        this.filteredProducts = [...this.products];
                    }
                    else {
                        this.filteredProducts = this.products.filter(product => {
                            if ( this.selectedCategory ) {
                                return product.name.toLowerCase().startsWith(event.query.toLowerCase()) && product.category_id == this.selectedCategory.id;
                            } else {
                                return product.name.toLowerCase().startsWith(event.query.toLowerCase())
                            }
                        });
                    }
                }, 250);
            },
        },
    }
</script>

<style lang="scss" scoped>
::v-deep .p-autocomplete, ::v-deep .p-autocomplete > input {
    width:100%!important;
}
.navbar:nth-child(2) {
    box-shadow: rgb(30 50 93 / 25%) 0px 0px 5px -1px, rgb(0 0 0 / 30%) 0px 1px 3px -1px;
}
::v-deep .p-overlaypanel::after, .p-overlaypanel:before {
 background:none !important;
 border:none !important;
}
::v-deep .p-datatable-thead {
    display:none;
}
::v-deep .p-overlaypanel-close {
    z-index:1;
}
</style>

<style>
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
.telAndMail {
    pointer-events:none;
}
.fa-shopping-cart:hover {
    cursor: pointer;
}
</style>
