<template>
    <div v-if="orders.length">
        <router-view></router-view>
        <div class="p-grid">
            <div class="p-col-12 p-md-6 p-lg-6">
                <DataTable :value="orders" class="p-datatable-customers">
                    <Column field="id" header="Order number">
                        <template #body="slotProps">
                            # {{slotProps.data.id}}
                        </template>                    
                    </Column>
                    <Column field="orders.order_details.id" header="Order">
                        <template #body="slotProps">
                            <div v-if="slotProps.data.order_details.length > 1">{{slotProps.data.order_details.length}} products</div>
                            <div v-else>{{slotProps.data.order_details.length}} product</div>
                        </template> 
                    </Column>
                    <Column field="total_price" header="Price">
                        <template #body="slotProps">
                            {{formatCurrency(slotProps.data.total_price)}}
                        </template>
                    </Column>
                    <Column field="order_status" header="Payment">
                        <template #body="slotProps">
                            <Tag v-if="slotProps.data.order_status == 'paid'" :value="slotProps.data.order_status" icon="pi pi-check" severity="success"></Tag>
                            <Tag v-else-if="slotProps.data.order_status == 'open'" :value="slotProps.data.order_status" icon="pi pi-info-circle" severity="info"></Tag>
                            <Tag v-else-if="slotProps.data.order_status == 'pending'" :value="slotProps.data.order_status" icon="pi pi-spin pi-spinner" severity="info"></Tag>
                            <Tag v-else-if="slotProps.data.order_status == 'failed'" :value="slotProps.data.order_status" icon="pi pi-times" severity="danger"></Tag>
                            <Tag v-else-if="slotProps.data.order_status == 'expired'" :value="slotProps.data.order_status" icon="pi pi-exclamation-triangle" severity="warning"></Tag>
                            <Tag v-else-if="slotProps.data.order_status == 'partially refunded'" :value="slotProps.data.order_status" icon="pi pi-info-circle" severity="info"></Tag>
                            <Tag v-else-if="slotProps.data.order_status == 'partially charged back'" :value="slotProps.data.order_status" icon="pi pi-info-circle" severity="info"></Tag>
                        </template>
                    </Column>
                    <Column>
                    <template #body="slotProps">
                    <Button v-if="!isMobile" @click="viewDetails(slotProps.data.id)" label="View" icon="pi pi-eye"></Button>
                    <Button v-else @click="viewOrder(slotProps.data.id)" label="View" icon="pi pi-eye"></Button>
                    </template>
                    </Column>
                </DataTable>
            </div>
            <div class="p-col-12 p-md-6 p-lg-3 p-d-none p-d-md-inline-flex">
                <Card class="p-3">
                    <template #header>
                        <div class="p-grid p-ai-center vertical-container">
                            <div class="p-col">
                                <span class="p-card-title p-component">Order # {{selectedOrder.id}}</span>
                            </div>
                            <div class="p-col text-right">                 
                                <Tag v-if="selectedOrder.order_status == 'paid'" :value="selectedOrder.order_status" icon="pi pi-check" severity="success"></Tag>
                                <Tag v-else-if="selectedOrder.order_status == 'open'" :value="selectedOrder.order_status" icon="pi pi-info-circle" severity="info"></Tag>
                                <Tag v-else-if="selectedOrder.order_status == 'pending'" :value="selectedOrder.order_status" icon="pi pi-spin pi-spinner" severity="info"></Tag>
                                <Tag v-else-if="selectedOrder.order_status == 'failed'" :value="selectedOrder.order_status" icon="pi pi-times" severity="danger"></Tag>
                                <Tag v-else-if="selectedOrder.order_status == 'expired'" :value="selectedOrder.order_status" icon="pi pi-exclamation-triangle" severity="warning"></Tag>
                                <Tag v-else-if="selectedOrder.order_status == 'partially refunded'" :value="selectedOrder.order_status" icon="pi pi-info-circle" severity="info"></Tag>
                                <Tag v-else-if="selectedOrder.order_status == 'partially charged back'" :value="selectedOrder.order_status" icon="pi pi-info-circle" severity="info"></Tag>
                            </div>
                        </div>
                    </template>
                    <template #content>
                        <AvatarGroup class="p-mb-3" v-if="selectedOrder.order_details">
                            <div class="order-box" v-for="(order_details, index) in selectedOrder.order_details.slice(0,4)" :key="index">
                                <Avatar :image="'/images/'+order_details.product.image" size="xlarge"/>
                            </div>
                            <Avatar v-if="selectedOrder.order_details.length > 4" shape="circle" size="xlarge" :label="stringify(selectedOrder.order_details.length - 4)" style="background-color:#9c27b0; color: #ffffff" />
                        </AvatarGroup>
                        <Divider align="left">
                        </Divider>
                        <DataTable :value="selectedOrder.order_details" class="hideHeader">
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
                                <span v-if="selectedOrder.total_price">{{formatCurrency(selectedOrder.total_price)}}</span>
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
                orders: [],
                selectedOrder: {},
                responsiveOptions: [
                    {
                        breakpoint: '1024px',
                        numVisible: 3,
                        numScroll: 3
                    },
                    {
                        breakpoint: '600px',
                        numVisible: 2,
                        numScroll: 2
                    },
                    {
                        breakpoint: '480px',
                        numVisible: 1,
                        numScroll: 1
                    }
                ]
            }
        },
        beforeMount() {
            let user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.user'))

            axios.get(`/api/users/${user.id}/orders`).then(response => {
                this.orders = response.data
                this.selectedOrder = response.data[Object.keys(response.data)[0]]
            }); 
        },
        created() {
            if (this.isMobile()) {
                this.isMobile = true
            }
            else {
                this.isMobile = false
            }
        },
        methods : {
            formatCurrency(value) {
                return value.toLocaleString('nl-NL', {style: 'currency', currency: 'EUR'});
            },
            viewOrder(value) {
                this.$parent.$emit('setComponent', 'order', value);
            },
            viewDetails(order) {
                let selectedOrder = this.orders.find(o => o.id === order)
                if ( this.selectedOrder.id !== order ) { 
                    this.selectedOrder = {}                 
                }
                if(selectedOrder){
                    this.selectedOrder = selectedOrder
                } 
            },
            stringify(value) {
                return "+" + value
            },
            isMobile() {
                if( screen.width <= 760 ) {
                    return true;
                }
                else {
                    return false;
                }
            }
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

.p-datatable-customers .p-datatable-tbody > tr > td .p-column-title {
    display: none;
}

 @media screen and (max-width: 64em) {
	::v-deep .p-datatable.p-datatable-customers .p-datatable-thead > tr > th, .p-datatable.p-datatable-customers .p-datatable-tfoot > tr > td {
		 display: none !important;
	}
	::v-deep .p-datatable.p-datatable-customers .p-datatable-tbody > tr > td {
		 text-align: left;
		 display: block;
		 border: 0 none !important;
		 width: 100% !important;
		 float: left;
		 clear: left;
		 border: 0 none;
	}
    ::v-deep .p-datatable.p-datatable-customers .p-datatable-tbody > tr {
        background: #ffffff;
        color: #495057;
        box-shadow: 0 2px 1px -1px rgb(0 0 0 / 20%), 0 1px 1px 0 rgb(0 0 0 / 14%), 0 1px 3px 0 rgb(0 0 0 / 50%);
        border-radius: 3px;
	}
	::v-deep .p-datatable.p-datatable-customers .p-datatable-tbody > tr > td .p-column-title {
		 padding: 0.4rem;
		 min-width: 30%;
		 display: inline-block;
		 margin: -0.4rem 1rem -0.4rem -0.4rem;
		 font-weight: bold;
	}
}
</style>