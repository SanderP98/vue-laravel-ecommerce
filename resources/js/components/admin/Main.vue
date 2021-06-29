<template>
    <div class="row">
        <div class="col-md-4 product-box d-flex align-content-center justify-content-center flex-wrap big-text">
            <a href='/admin/orders' v-if="!isLoading">Orders ({{orders.length}})</a><ProgressSpinner v-if="isLoading" style="width:50px;height:50px" strokeWidth="8" fill="#EEEEEE" animationDuration=".5s"/>
        </div>
        <hr>
        <div class="col-md-4 product-box d-flex align-content-center justify-content-center flex-wrap big-text">
            <a href='/admin/products' v-if="!isLoading">Products ({{products.length}})</a><ProgressSpinner v-if="isLoading" style="width:50px;height:50px" strokeWidth="8" fill="#EEEEEE" animationDuration=".5s"/>
        </div>
        <div class="col-md-4 product-box d-flex align-content-center justify-content-center flex-wrap big-text">
            <a href='/admin/users' v-if="!isLoading">Users ({{users.length}})</a><ProgressSpinner v-if="isLoading" style="width:50px;height:50px" strokeWidth="8" fill="#EEEEEE" animationDuration=".5s"/>
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
        mounted() {
            axios.get('/api/users/').then(response => {
                this.users = response.data;
            });
            axios.get('/api/products/').then(response => {
                this.products = response.data
            });
            axios.get('/api/orders/').then(response => {
                this.orders = response.data
                this.isLoading = false;
            });
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
