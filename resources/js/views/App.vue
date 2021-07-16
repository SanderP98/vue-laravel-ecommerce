<template>
  <div>
    <nav class="topBar">
      <div class="container">
        <ul class="list-inline float-left d-none d-md-inline-flex">
          <li>
            <span class="text-primary">Have a question? </span>{{shopInfo.tel_nr}}
            
          </li>
        </ul>
        <ul class="topBarNav float-right">
          <li class="dropdown">
            <ul class="dropdown-menu w-100" role="menu"></ul>
          </li>
          <li class="dropdown">
            <a
              href="#"
              class="dropdown-toggle"
              data-toggle="dropdown"
              data-hover="dropdown"
              data-close-others="false"
            >
              <i class="fa fa-user mr-5"></i
              ><span class="d-none d-md-inline-block"
                >My Account<i class="fa fa-angle-down ml-5"></i
              ></span>
            </a>
            <ul class="dropdown-menu w-150" role="menu">
              <li><a href="login.html">Login</a></li>
              <li><a href="register.html">Create Account</a></li>
              <li class="divider"></li>
              <li><a href="wishlist.html">Wishlist (5)</a></li>
              <li><a href="cart.html">My Cart</a></li>
              <li><a href="checkout.html">Checkout</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" @click="showCart">
              <i class="fa fa-shopping-basket mr-5"
                ><sup
                  class="text-primary d-inline-block d-sm-none"
                  >{{totalItems}}</sup
                ></i
              >
              <span class="d-none d-md-inline-block">
                Cart<sup class="text-primary">{{totalItems}}</sup>
                <i class="fa fa-angle-down ml-5"></i>
              </span>
            </a>
            <OverlayPanel
              ref="op"
              appendTo="body"
              :showCloseIcon="true"
              id="overlay_panel"
            >
            <li
            class="list-group-item d-flex justify-content-between align-items-center p-datatable-header"
            >
            <span
                class="badge badge-primary badge-pill"
                >{{totalItems}}</span
            >
            {{formatCurrency(totalPrice)}}
            </li>
            <ScrollPanel style="width: 100%; height: 200px" class="custom">
            <div v-for="(cartItem, index) in cart" :key="index">
                <div class="p-grid p-p-3 cartItem">
                    <div class="p-col-3 col-centered">
                        <img :src="'/products/' + cartItem.image" :alt="cartItem.name" class="product-image"/>
                    </div>
                    <div class="p-col-9 p-grid">
                        <div class="p-col-6">
                            {{cartItem.name}}
                        </div>
                        <div class="p-col-6">
                            <div class="p-grid">
                                <div class="p-col-12">
                                    {{formatCurrency(cartItem.subtotal)}}
                                </div>
                                <div class="p-col-12 d-flex align-middle">
                                    <InputNumber
                                    v-model="cartItem.quantity"
                                    inputClass="amountItem"
                                    decrementButtonClass="decreaseAmount"
                                    incrementButtonClass="increaseAmount"
                                    showButtons
                                    @input="changeQuantityItem(cartItem)"
                                    />
                                    <Button
                                    v-model="cartItem.id"
                                    class="p-button-rounded p-button-danger deleteFromCartBtn"
                                    icon="pi pi-times"
                                    @click="removeFromCart(cartItem)"
                                    /> 
                                </div>
                            </div>
                        </div>                 
                    </div>
                </div>
                    <!-- <div class="p-grid p-p-1 cartItemQty">
                        <div class="p-col-8"></div>

                        </div>
                    </div> -->
              <!-- <DataTable :value="cart">
                <div class="p-grid">
                    <div class="p-col-9">
                        <Column class="p-col-9">
                            <template #body="slotProps">
                            <img
                            :src="'/products/' + slotProps.data.image"
                            :alt="slotProps.data.image"
                            class="product-image"
                            />
                            </template>
                        </Column>
                    </div>
                    <div class="p-col-3">
                        <Column field="name">
                        <template #body="slotProps">
                            {{slotProps.data.name}}
                        </template>
                        </Column>
                        <Column field="price">
                        <template #body="slotProps">
                            {{formatCurrency(slotProps.data.subtotal)}}
                        </template>
                        </Column>
                    </div>
                </div>
                <div class="p-grid">
                    <div class="p-col-12">
                        <Column field="quantity" class="cartArrows">
                            <template #body="slotProps">
                                <div class="p-grid mt-0 p-0">
                                <div class="p-col-9 mt-0 mb-0">
                                    <InputNumber
                                    v-model="slotProps.data.quantity"
                                    inputClass="amountItem"
                                    decrementButtonClass="decreaseAmount"
                                    incrementButtonClass="increaseAmount"
                                    showButtons
                                    @input="changeQuantityItem(slotProps.data)"
                                    />
                                </div>
                                <div class="p-col-3 mt-0 mb-0">
                                    <Button
                                    v-model="slotProps.data.id"
                                    class="p-button-rounded p-button-danger deleteFromCartBtn p-col-6"
                                    icon="pi pi-times"
                                    @click="deleteItemFromCart(slotProps.data)"
                                    />
                                </div>
                                </div>
                            </template>
                        </Column>
                    </div>
                </div>
              </DataTable> -->
            </div>
            </ScrollPanel>
            <div class="checkout">
              <router-link
                :to="{ name: 'cart' }"
                v-if="user_type == 0 || user_type == 1"
                ><Button
                  class="p-button-raised float-right m-2"
                  @click="showCart()"
                  >Checkout</Button
                ></router-link
              >
            </div>

            </OverlayPanel>
          </li>
        </ul>
      </div>
    </nav>
    <div class="middleBar">
      <div class="container">
        <div class="row display-table m-0">
          <div class="col-sm-3 col-md-1 col-lg-3 vertical-align text-left d-none d-md-block p-0">
            <a href="javascript:void(0);">
              <img
                width=""
                :src="'/shop/' + shopInfo.image"
                alt=""
                style="max-height:50px"
            /></a>
          </div>
          <div class="col-sm-2 d-sm-none d-md-none d-lg-block"></div>
          <div class="col-sm-12 col-md-11 col-lg-7 vertical-align text-center">
            <form>
              <div class="row grid-space-1 m-0">
                <div class="col-sm-12 col-md-5 p-0">
                  <AutoComplete
                    v-model="selectedProduct"
                    :suggestions="filteredProducts"
                    placeholder="Search for a Product.."
                    @complete="searchProduct($event)"
                    field="name"
                  />
                </div>
                <div class="col-sm-12 col-md-4 col-lg-5 col-xl-4">
                  <Dropdown
                    v-model="selectedCategory"
                    :options="categories"
                    optionLabel="name"
                    placeholder="Select a Category"
                  />
                </div>
                <div class="col-sm-12 col-md-2 col-xl-3 p-0">
                  <Button class="btn-block btn-lg" label="Search" />
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <nav class="bottomBar navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
            <ul class="nav navbar-nav">
                <li class=""><a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="false">Home</a></li>
                <li class="dropdown megaDropMenu">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="false">Shop <i class="fa fa-angle-down ml-5"></i></a>
                  <ul class="dropdown-menu row">
                    <li class="col-sm-3 col-xs-12">
                      <ul class="list-unstyled">
                        <li>Products Grid View</li>
                        <li><a href="#">Products</a></li>
                        <li><a href="#">Sidebar Left</a></li>
                        <li><a href="#">Products Left</a></li>
                     </ul>
                    </li>
                    <li class="col-sm-3 col-xs-12">
                      <ul class="list-unstyled">
                        <li>Products List View</li>
                        <li><a href="#"> Sidebar Left</a></li>
                        <li><a href="#">Products Left</a></li>
                        <li><a href="#">Products Sidebar</a></li>
                       </ul>
                    </li>
                    <li class="col-sm-3 col-xs-12">
                      <ul class="list-unstyled">
                        <li>Checkout</li>
                        <li><a href="#">Step 1</a></li>
                        <li><a href="#">Step 2</a></li>
                        <li><a href="#">Step 3</a></li>
                     </ul>
                    </li>
                    <!-- <li class="col-sm-3 col-xs-12">
                        <ul class="list-unstyled">
                        <li>Sumit Kumar</li>
                     </ul>
                      <img src="https://web.archive.org/web/20200928092706im_/https://lh3.googleusercontent.com/-uwagl9sPHag/WM7WQa00ynI/AAAAAAAADtA/hio87ZnTpakcchDXNrKc_wlkHEcpH6vMwCJoC/w140-h148-p-rw/profile-pic.jpg" class="img-responsive" alt="menu-img">
                    </li> -->
                  </ul>
                </li>
                  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="false">Page <i class="fa fa-angle-down ml-5"></i></a>
                  <ul class="dropdown-menu dropdown-menu-left">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Register</a></li>
                    <li><a href="#">Register or Login</a></li>
                    <li><a href="#">Login</a></li>
                    <li><a href="#">Password Recovery</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms &amp; Conditions</a></li>
                    <li><a href="#">404 Not Found</a></li>
                    <li><a href="#">Short Code</a></li>
                    <li><a href="#">Coming Soon</a></li>
                  </ul>
                </li>
                <li class=""><a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="false">Blog</a></li>
              </ul>
            </div>
        </div>
    </nav>
    <main class="py-0">
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
.cartItem {
  border: 1px solid #e9ecef;  
  border-width: 1px 0 1px 0;
  border-top:none;
}
::v-deep .p-overlaypanel-content li {
  background: #f8f9fa;
  color: #495057;
  border: 1px solid #e9ecef;
  border-width: 1px 0 1px 0;
  border-top:none;
  padding: 1rem;
  font-weight: 600;    
}
sup {
	font-family: "Nunito", sans-serif;
}
.p-button-raised {

}
.p-overlaypanel {
	width: 550px;
}
::v-deep .p-overlaypanel-content {
    padding:0;
}

::v-deep .p-autocomplete,
::v-deep .p-autocomplete>input {
	width: 100%;
}

.navbar:nth-child(2) {
	box-shadow: rgb(30 50 93 / 25%) 0px 0px 5px -1px, rgb(0 0 0 / 30%) 0px 1px 3px -1px;
}
.checkout {
    border-top: 1px solid #e9ecef;
}
.p-overlaypanel:before {
	border-bottom-color: #f8f9fa;
} 

::v-deep .p-datatable-thead {
	display: none;
}

::v-deep .p-overlaypanel-close {
	z-index: 1;
}

.middleBar .p-dropdown {
	width: 178.47px!important;
	margin-bottom: 1rem;
}

::v-deep .middleBar .p-dropdown-label,
::v-deep .middleBar .p-dropdown-items {
	text-align: left;
}
::v-deep .p-overlaypanel-close {
    height: 1.5rem;
    width: 1.5rem;
    right: -0.5rem;
    top: -0.5rem;
}
::v-deep .p-datatable-tbody .p-inputnumber {
    width: 100%;
}
.p-overlaypanel:after {
    display: none;
}
::v-deep .p-datatable-tbody .p-col-9 {
    width: 90%;
    padding-bottom: 0!important;
}
    ::v-deep .p-datatable-tbody .p-col-3 {
    width: 10%;
    padding-bottom: 0!important;
}
    ::v-deep .p-datatable-tbody>tr {
    border: 1px solid #e9ecef;
    border-width: 0 0 1px 0;
}
    ::v-deep .p-overlaypanel {
    width: 50px!important;
}
::v-deep .custom .p-scrollpanel-wrapper {
    padding-right:0;
}

::v-deep .custom .p-scrollpanel-bar {
    background-color: #1976d2;
    opacity: 1;
    transition: background-color .3s;
}

::v-deep .custom .p-scrollpanel-bar:hover {
    background-color: #135ba1;
}
@media (max-width: 768px) {
  .middleBar .col-md-2 {
    padding: 0px 15px!important;
  }
  .p-overlaypanel {
      margin: 5px!important;
      width:auto!important;
  }
	.dropdown-toggle>i {
		margin-right: 0!important;
	}
	.dropdown-toggle>.fa-user {
		padding-right: 15px;
	}
	.dropdown-toggle>.fa-shopping-basket {
		padding-left: 15px;
	}
	.search-autocomplete {
		width: 100%;
	}
	.middleBar .p-autocomplete {
		margin-top: 1rem;
        padding-left:15px;
        padding-right:15px;
	}
	.middleBar .p-autocomplete,
    .middleBar .p-dropdown,
	.middleBar .p-button {
		width: 100%!important;
		margin-bottom: 1rem;
	}
	.search-dropdown,
	.shop-image,
	.search-button {
		display: none;
		width: 0px!important;
		position: absolute!important;
		left: -999em!important;
	}
	.search-section {
		flex-direction: row;
	}
	.navbar>.container-fluid {
		flex-wrap: nowrap;
		display: flex;
		align-items: center;
	}
	.navbar>.container-fluid>.col-lg-2 {
		padding: 0;
	}
	.navbar>.container-fluid>.col-lg-8,
	.navbar>.container-fluid>.col-lg-8>.nav-link {
		padding: 0;
	}
	.nav-item>.fa-stack[data-count]:after {
		right: 0.2em;
	}
	.nav-item>.fa-stack {
		width: 2.1em;
	}
}
</style>

<style>
.product-image {
	width: 100px;
	box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23)
}

.amountItem,
.amountItem+span {
	width: 50px;
}

.decreaseAmount,
.increaseAmount {
	width: 1.5rem!important;
}

.cartArrows {
	padding-left: 0;
}

.telAndMail {
	pointer-events: none;
}

.fa-shopping-cart:hover {
	cursor: pointer;
}
</style>

<style lang="scss" scoped>
.mr-5 {
	margin-right: 5px;
}

.ml-5 {
	margin-left: 5px;
}

.w-100 {
	width: 100px;
}

.w-150 {
	width: 150px;
}

.w-250 {
	width: 250px;
}

.middleBar {
	background-color: #ffffff;
	padding: 10px 0 0 0;
	margin: 0;
	min-height: 70px;
	border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.topBar .dropdown-toggle::after {
	display: none;
}

.topBar>.container {
	display: table;
}

.topBar {
	background-color: #ffffff;
	border-bottom: 1px solid rgba(0, 0, 0, 0.08);
	font-size: 13px;
}

.topBar ul {
	margin: 0;
}

.topBar ul li {
	line-height: 42px;
}

.topBar a {
	color: #878c94;
	text-decoration: none;
}

.text-primary {
	color: #0cd4d2;
}
.topBar i {
    font-size:13px;
}
.topBar ul.topBarNav {
	margin: 0;
	padding: 0;
	list-style-type: none;
}

.topBar ul.topBarNav li {
	position: relative;
	display: inline-block;
	margin-right: -4px;
	border-right: 1px solid rgba(0, 0, 0, 0.08);
}

.topBar ul.topBarNav li:last-child {
	border-right: none;
}

.topBar ul.topBarNav li a {
	display: block;
	padding-left: 12px;
	padding-right: 12px;
}

.topBar ul.topBarNav li ul {
	background-color: #ffffff;
	position: absolute;
	top: 42px;
	left: auto;
	min-width: 10px;
	right: 4px;
	border-radius: 0px;
	border: solid 0px;
	margin-right: -4px;
	padding: 0;
	list-style-type: none;
	z-index: 9999;
	-webkit-transition: all 0.1s ease-in-out;
	-moz-transition: all 0.1s ease-in-out;
	-ms-transition: all 0.1s ease-in-out;
	-o-transition: all 0.1s ease-in-out;
	transition: all 0.1s ease-in-out;
	-webkit-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08);
	-moz-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08);
	-ms-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08);
	box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08);
}

.topBar ul.topBarNav li ul li {
	display: block;
	line-height: 30px;
	width: 100%;
	border: none;
}

.topBar ul.topBarNav li a {
	display: block;
	padding-left: 12px;
	padding-right: 12px;
}

.topBar .dropdown-menu>li>a {
	display: block;
	padding: 6px 20px;
	clear: both;
	font-weight: normal;
	line-height: 1.42857143;
	color: #878c94;
	font-size: 15px;
	white-space: nowrap;
}

.topBar .dropdown-menu>li>a:hover,
.dropdown-menu>li>a:focus {
	color: #00BCD4;
	text-decoration: none;
	background-color: rgba(0, 0, 0, 0.02);
}

.topBar ul.topBarNav li ul.cart .cart-items {
	padding: 10px;
	height: 200px;
	overflow: auto;
}

.topBar ul.topBarNav li ul.cart .cart-items .items {
	margin: 0;
	padding: 0;
	list-style-type: none;
}

.topBar ul.topBarNav li ul.cart .cart-items .items li {
	overflow: hidden;
	clear: left;
	padding-bottom: 10px;
	margin-bottom: 10px;
	border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.topBar ul.topBarNav li ul.cart .cart-items .items li .product-image {
	width: 60px;
	float: left;
}

.topBar ul.topBarNav li ul.cart .cart-items .items li a {
	margin: 0;
	padding: 0;
	line-height: normal;
	background-color: transparent;
	display: inline;
}

.topBar ul.topBarNav li ul.cart .cart-items .items li .product-details {
	position: relative;
	margin-left: 60px;
	padding: 0 15px 0 10px;
}

.topBar ul.topBarNav li ul.cart .cart-items .items li .product-details .close-icon {
	position: absolute;
	top: 0;
	right: 0;
	font-size: 10px;
	line-height: normal;
}

.topBar ul.topBarNav li ul.cart .cart-footer {
	overflow: hidden;
	background-color: rgba(0, 0, 0, 0.02);
}

.topBar ul.topBarNav li ul.cart .cart-footer a {
	text-align: center;
	padding: 10px 20px;
	margin: 0;
	background-color: transparent;
}

.navbar {
	-webkit-box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	-moz-box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	background-color: #ffffff;
	margin-bottom: 0;
	border: none;
}

.navbar-nav>li>a {
	color: rgb(119, 119, 119);
}

.navbar .navbar-nav>.open>a,
.navbar .navbar-nav>.open>a:hover,
.navbar .navbar-nav>.open>a:focus {
	color: #0cd4d2;
	background-color: transparent;
}

.navbar .navbar-nav>.open>a,
.navbar .navbar-nav>.open>a:hover,
.navbar .navbar-nav>.open>a:after {
	border-top: solid 2px #0cd4d2;
}

.navbar .dropdown-menu {
	padding: 0;
	font-size: 14px;
	background-color: #ffffff;
	color: #878c94;
	border: none;
	-webkit-border-radius: 0;
	-moz-border-radius: 0;
	-ms-border-radius: 0;
	border-radius: 0;
	-webkit-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08);
	-moz-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08);
	-ms-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08);
	box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08);
	-webkit-transition: all 0.1s ease-in;
	-moz-transition: all 0.1s ease-in;
	-ms-transition: all 0.1s ease-in;
	-o-transition: all 0.1s ease-in;
	transition: all 0.1s ease-in;
}

.dropdown-menu>li>a {
	display: block;
	padding: 6px 20px;
	clear: both;
	font-weight: normal;
	line-height: 1.42857143;
	color: #878c94;
	white-space: nowrap;
}

.dropdown-menu>li>a:hover {
	color: #0cd4d2;
}

.navbar .container {
	position: relative;
}

.navbar .navbar-collapse li.dropdown.megaDropMenu {
	position: static;
}

.navbar .navbar-collapse li.dropdown.megaDropMenu .dropdown-menu {
	width: 100%;
}

.navbar .navbar-collapse li.dropdown.megaDropMenu .dropdown-menu li ul li:first-child {
	padding: 20px 0px 5px 15px;
	font-size: 16px;
	text-transform: uppercase;
	color: #0cd4d2;
}

.navbar .navbar-collapse li.dropdown.megaDropMenu .dropdown-menu li ul li a {
	display: block;
	color: #878c94;
	font-size: 14px;
	text-decoration: none;
	padding: 8px 15px;
}

.navbar .navbar-collapse li.dropdown.megaDropMenu .dropdown-menu li ul li a:hover {
	color: #00BCD4;
	text-decoration: none;
	background-color: rgba(0, 0, 0, 0.02);
}

.navbar .navbar-collapse li.dropdown.megaDropMenu .dropdown-menu img {
	display: block;
	max-width: 100%;
	padding: 20px;
}

.dropdown-toggle::after {
	display: none;
}

.nav>li>a {
	position: relative;
	display: block;
	padding: 10px 15px;
}

.bottomBar .dropdown-toggle i {
	margin-left: 0!important;
}

@media (max-width: 768px) {
	.amountItem,
	.amountItem+span {
		width: auto!important;
	}
}

</style>