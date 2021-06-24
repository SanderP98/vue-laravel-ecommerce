<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <img :src="'/images/'+product.image" :alt="product.name" height="50px" width="50px">
                <h3 class="title" v-html="product.name"></h3>
                <p class="text-muted">{{product.description}}</p>
                <h4>
                    <span class="small-text text-muted float-left">$ {{product.price}}</span>
                    <span class="small-text float-right">Available Quantity: {{product.units}}</span>
                </h4>
                <br>
                <hr>
                <div class="text-right">
                <router-link :to="{ path: '/cart?pid='+product.id }" class="col-md-4 p-button">Buy Now</router-link>
                <Button icon="pi pi-shopping-cart" :disabled="product.units === '0'" @click="addToCart(product)"></Button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                product : []
            }
        },
        beforeMount() {
            let url = `/api/products/${this.$route.params.id}`
            axios.get(url).then(response => this.product = response.data)
        },
        methods : {
            addToCart(product) {
                product.quantity = 1;
                this.$parent.$emit('addToCart', product);
            }
        }
    }
</script>

<style scoped>
    .small-text {
        font-size: 18px;
    }
    .title {
        font-size: 36px;
    }
</style>
