<template>
    <div>
        <div class="p-grid" v-for="(order, index) in order" :key="index">
            <div class="p-col-12 p-md-3 p-lg-3 mx-auto">
                <Card class="p-3">
                    <template #header>
                        <div class="p-grid p-ai-center vertical-container">
                            <div class="p-col">
                                <span class="p-card-title p-component">Order # {{order.id}}</span>
                            </div>
                            <div class="p-col text-right">                 
                                <Tag v-if="order.order_status == 'paid'" :value="order.order_status" icon="pi pi-check" severity="success"></Tag>
                                <Tag v-else-if="order.order_status == 'open'" :value="order.order_status" icon="pi pi-info-circle" severity="info"></Tag>
                                <Tag v-else-if="order.order_status == 'pending'" :value="order.order_status" icon="pi pi-spin pi-spinner" severity="info"></Tag>
                                <Tag v-else-if="order.order_status == 'failed'" :value="order.order_status" icon="pi pi-times" severity="danger"></Tag>
                                <Tag v-else-if="order.order_status == 'expired'" :value="order.order_status" icon="pi pi-exclamation-triangle" severity="warning"></Tag>
                                <Tag v-else-if="order.order_status == 'partially refunded'" :value="order.order_status" icon="pi pi-info-circle" severity="info"></Tag>
                                <Tag v-else-if="order.order_status == 'partially charged back'" :value="order.order_status" icon="pi pi-info-circle" severity="info"></Tag>
                            </div>
                        </div>
                    </template>
                    <template #content>
                        <AvatarGroup class="p-mb-3" v-if="order.order_details">
                            <div class="order-box" v-for="(order_details, index) in order.order_details.slice(0,3)" :key="index">
                                <Avatar :image="'/images/'+order_details.product.image" size="xlarge"/>
                            </div>
                            <Avatar v-if="order.order_details.length > 3" shape="circle" size="xlarge" :label="stringify(order.order_details.length - 3)" style="background-color:#9c27b0; color: #ffffff" />
                        </AvatarGroup>
                        <Divider align="left">
                        </Divider>
                        <DataTable :value="order.order_details" class="hideHeader">
                            <Column field="product.name"></Column>
                            <Column field="quantity"></Column>
                            <Column field="total_price">
                                <template #body="slotProps">
                                    {{formatCurrency(slotProps.data.total_price)}}
                                </template>
                            </Column>
                        </DataTable>
                        <Divider align="right">
                        </Divider>
                    </template>
                    <template #footer>
                        <div class="p-grid">
                            <div class="p-col-4 p-pl-2 p-card-title p-component">Total</div>
                            <div class="p-col-4 p-p-3">
                            </div>                            
                            <div class="p-col-4 p-p-3">
                                <span v-if="order.total_price">{{formatCurrency(order.total_price)}}</span>
                            </div>                      
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                order: [],
                user: null,
            }
        },
        beforeMount() {
            this.user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.user'))
            let url = `/api/orders/${this.$route.params.id}`
            axios.get(url).then(response => { 
                let order = response.data 
                let checkUserMatch = order.find(o => o.user_id === this.user.id)
                if ( checkUserMatch ) {
                    this.order = order;
                } else {
                    this.$router.push({ name: 'userboard'}).catch(() => {});
                    this.$parent.$emit('setComponent', 'main')
                }
            });            
        },
        methods : {
            formatCurrency(value) {
                return value.toLocaleString('nl-NL', {style: 'currency', currency: 'EUR'});
            },
            stringify(value) {
                return "+" + value
            },
        }
    }
</script>

<style lang="scss" scoped>
::v-deep .hideHeader .p-datatable-thead {
    display:none!important;
}
::v-deep .hideHeader .p-datatable-tbody tr:last-child td {
    border:none
}
::v-deep .p-carousel-indicators {
    display:none
}
::v-deep .p-card-footer {
    padding:0;
}
::v-deep .p-carousel {
    padding-bottom: 1rem;
}
::v-deep .p-card .p-card-content {
    padding:0;
}
.p-datatable-customers .p-datatable-tbody > tr > td .p-column-title {
    display: none;
}
@media screen and (max-width: 64em) {
    ::v-deep .p-datatable.hideHeader .p-datatable-tbody > tr > td {
            text-align: left;
            display: block;
            border: 0 none !important;
            width: 100% !important;
            float: left;
            clear: left;
            border: 0 none;
    }
    ::v-deep .p-datatable.hideHeader .p-datatable-tbody > tr {
        color: #495057;
        box-shadow: 0 2px 1px -1px rgb(0 0 0 / 20%), 0 1px 1px 0 rgb(0 0 0 / 14%), 0 1px 3px 0 rgb(0 0 0 / 50%);
    }
    ::v-deep .p-divider-horizontal.p-divider-left, .p-divider-horizontal.p-divider-right {
        display:none;
    }
    ::v-deep .p-card-footer .p-grid {
        margin-top: .5rem;
    }
    ::v-deep .p-avatar-group {
        justify-content:center;
    }
}
</style>