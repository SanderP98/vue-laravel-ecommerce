import Vue from 'vue'
import Vuex from 'vuex'
 
Vue.use(Vuex)
 
export default new Vuex.Store({
    state: {
        products: [],
        error: null,
        cart: JSON.parse(localStorage.getItem('cart')) ? JSON.parse(localStorage.getItem('cart')) : [],
        singleOrder: JSON.parse(localStorage.getItem('singleOrder')) ? JSON.parse(localStorage.getItem('singleOrder')) : [], 
        totalItems: 0,
        totalPrice: 0,
        newCartItem: {}
    },
    actions: {    
    },
    mutations: {
        // getProducts(state, products) {
        //     state.products = products
        // },
        addToCart ( state, product ) {
            let findProduct = state.cart.find(o => o.id === product.id)
            if( findProduct ) {
                if ( product.quantity + findProduct.quantity > product.units ) {
                    findProduct.quantity = product.units
                    state.error = "You are trying to exceed the product's available stock."
                } else {
                    findProduct.quantity += product.quantity;
                    findProduct.subtotal = findProduct.quantity * findProduct.price;
                }
            } else {
                state.newCartItem.id = product.id
                state.newCartItem.name = product.name;
                state.newCartItem.price = product.price;
                state.newCartItem.quantity = product.quantity;
                state.newCartItem.image = product.product_image[0].image;
                state.newCartItem.subtotal = product.price;
                state.newCartItem.units = product.units;
                state.cart.push(Object.assign({},state.newCartItem))
                state.newCartItem = {}
            }
            localStorage.setItem('cart', JSON.stringify(state.cart));
        },
        singleOrder ( state, product ) {
            localStorage.removeItem('singleOrder');
            state.singleOrder = []

            state.newCartItem.id = product[0].id
            state.newCartItem.name = product[0].name;
            state.newCartItem.price = product[0].price;
            state.newCartItem.quantity = 1;
            state.newCartItem.image = product[0].product_image[0].image;
            state.newCartItem.subtotal = product[0].price;
            state.newCartItem.units = product[0].units;
            state.singleOrder.push(Object.assign({},state.newCartItem))
            state.newCartItem = {}       
            
            localStorage.setItem('singleOrder', JSON.stringify(state.singleOrder));
        },
        totalItems ( state ) {
            state.totalItems = parseInt(state.cart.reduce((total, item) => {
                return total + item.quantity;
            }, 0))           
        },
        totalPrice ( state ) {
            state.totalPrice = state.cart.reduce((total, item) => {
                return total + item.subtotal;
            }, 0);
        },
        removeFromCart ( state, product ) {
            let findProduct = state.cart.find(o => o.id === product.id)
            if ( findProduct ) {
                state.cart = state.cart.filter( val => val.id !== findProduct.id );
            }  
            localStorage.setItem('cart', JSON.stringify(state.cart));
        },
        changeQuantityItem ( state, product ) {
            let findProduct = state.cart.find(o => o.id === product.id)
            if ( findProduct ) {
                if ( product.quantity > product.units ) {
                    product.quantity = product.units
                    state.error = "You are trying to exceed the product's available stock."
                } else {
                    findProduct.quantity = parseInt(product.quantity);
                }
                findProduct.subtotal = product.quantity * findProduct.price;
            } 
            localStorage.setItem('cart', JSON.stringify(state.cart));
        },
        changeQuantitySingleOrder ( state, product ) {
            let findProduct = state.singleOrder.find(o => o.id === product.id)
            if ( findProduct ) {
                if ( product.quantity > product.units ) {
                    product.quantity = product.units
                    state.error = "You are trying to exceed the product's available stock."
                } else {
                    findProduct.quantity = parseInt(product.quantity);
                }
                findProduct.subtotal = product.quantity * findProduct.price;
            } 
            localStorage.setItem('singleOrder', JSON.stringify(state.singleOrder));           
        },
        clearCart ( state ) {
            localStorage.removeItem('cart');
            state.cart = []           
        },
        clearError ( state ) {
            state.error = null
        }
    },
    getters: {
        cart: state => state.cart,
        singleOrder: state => state.singleOrder,
        error: state => state.error,
        totalItems: state => state.totalItems,
        totalPrice: state => state.totalPrice
    },
})