<template>
    <div class="row">
        <div class="col-md-4 product-box d-flex align-content-center justify-content-center flex-wrap big-text">
            <a href='/dashboard/orders' v-if="!isLoading">Orders ({{orders.length}})</a><ProgressSpinner v-if="isLoading" style="width:50px;height:50px" strokeWidth="8" fill="#EEEEEE" animationDuration=".5s"/>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                user : null,
                orders : [],
                products : [],
                users : [],
                isLoading : true,
            }
        },
        beforeMount() {
            this.user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.user'))
        },
        mounted() {
            axios.get(`/api/users/${this.user.id}/orders`).then(response => {
                this.orders = response.data
                this.isLoading = false;
            }); 
            // axios.get('/api/products/').then(response => {
            //     this.products = response.data
            // });
            // axios.get('/api/orders/').then(response => {
            //     this.orders = response.data
            //     this.isLoading = false;
            // });
        }
    }
</script>

<style scoped>
.big-text { 
    font-size: 28px; 
}
.product-box { 
    border: 1px solid #cccccc; 
    padding: 10px 15px; 
    height: 20vh 
}
</style>
