<template>
<div>
    <Card>
        <template #content>
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="order-box" v-for="(product, index) in cart" :key="index">
                        <img :src="'/products/'+product.image" :alt="product.name" height="50px" width="50px">
                        <h2 class="title" v-html="product.name"></h2>
                        <p class="small-text text-muted float-left">{{formatCurrency(product.price * product.quantity)}}</p>
                        <p class="small-text text-muted float-right">Available Units: {{product.units}}</p>
                        <br>
                        <hr>
                        <!-- <label class="row"><span class="col-md-2 float-left">Quantity: </span><input type="number" name="quantity" min="1" :max="product.units" v-model="product.quantity" class="col-md-2 float-left" @input="checkUnits(product)"></label> -->
                        <InputNumber v-model="product.quantity" @input="checkUnits(product)" :min="0" showButtons />
                    </div>
                    <br>
                </div>
            </div>
        </div>
        </template>
        <!-- <template #title>
            Payment
        </template>
         <template #content>
            <p class="p-text-secondary">Select a payment method to continue</p>
            <div class="p-fluid">
                <div class="p-field">
                     <InputText id="country" v-model="country" :class="{'p-invalid': validationErrors.selectedCity && submitted}" />
                    <small v-show="validationErrors.selectedMethod && submitted" class="p-error">Country is required.</small>
                    <ScrollPanel class="custom">
                        <div v-for="paymentMethod in paymentMethods" :key="paymentMethod.name" class="p-field-radiobutton" :class="{'p-invalid': validationErrors.selectedMethod && submitted}">
                            <RadioButton :id="paymentMethod.key" name="paymentMethod" :value="paymentMethod" v-model="selectedMethod" :disabled="paymentMethod.key === 'R'" />
                            <label :for="paymentMethod.key">{{paymentMethod.name.toUpperCase()}}</label>
                        </div>
                    </ScrollPanel>
                    <small v-show="validationErrors.selectedMethod && submitted" class="p-error">Payment method is required.</small>
                </div>
            </div>
        </template> -->
        <template #footer>
            <div class="p-grid p-nogutter p-justify-between">
                <Button label="Back" @click="prevPage()" icon="pi pi-angle-left" />
                <Button label="Complete" @click="complete()" icon="pi pi-check" iconPos="right" class="p-button-success"/>
            </div>
        </template>
    </Card>
</div>
</template>

<script>

export default {
    props: ['pid', 'formData'],
    data () {
        return {
            cart: [],
        }
    },
    computed : {
        products() {
            return this.$store.getters.products
        }
    },
    beforeMount() {
        if( Object.entries(this.$props.formData).length === 0 ) {
            this.$router.push({ path: '/' })
        }
        if ( Object.entries(this.$props.pid).length !== 0 ) {
            this.cart = this.$props.pid
        } else {
            this.cart = this.products
        }
    },
    methods : {
        formatCurrency(value) {
            return value.toLocaleString('nl-NL', {style: 'currency', currency: 'EUR'}); 
        },
        prevPage() {
            this.$emit('prevPage', {pageIndex: 2});
        },
        complete() {
            this.$emit('complete', {formData: {products: this.products}});
            // let products = this.products

            // let totalPrice = products.reduce((total, item)=> {
            //     return total + item.quantity * item.price;
            // }, 0);
            // axios.post('api/molliepayment', { products, totalPrice }).then(response => {
            //     window.location.href = response.data.data._links.checkout.href;
            // }).catch(() => {});
        },
        checkUnits(product) {
            if ( Object.entries(this.$props.pid).length === 0 ) {
                this.$parent.$parent.$emit('changeQuantityCartItem', product)
            } else { 
                this.$parent.$emit('updateQuantityNonCartItem', product)
            }
            this.$forceUpdate();
        },
    }
}
</script>
