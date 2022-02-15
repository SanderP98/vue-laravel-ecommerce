<template>
    <div>
        <div>
            <Toolbar class="mb-4">
                <template #start>
                    <Button label="Delete" icon="pi pi-trash" class="p-button-danger" @click="confirmDeleteSelected" :disabled="!selectedOrders || !selectedOrders.length" />
                </template>

                <template #end>
                    <Button label="Export" icon="pi pi-upload" class="p-button-help" @click="exportCSV($event)" />
                </template>
            </Toolbar>

            <DataTable ref="dt" :value="orders" :selection.sync="selectedOrders" dataKey="id"
            :paginator="true" :rows="10" :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown" :rowsPerPageOptions="[5,10,25]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} products" responsiveLayout="scroll">
                <template #header>
                    <div class="table-header p-d-flex p-flex-column p-flex-md-row p-jc-md-between">
                        <h5 class="p-m-0">Manage orders</h5>
                        <span class="p-input-icon-left">
                            <i class="pi pi-search"/>
                            <InputText v-model="filters['global']" placeholder="Search..." />
                        </span>
                    </div>
                </template>

                <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                <Column field="id" header="Order Number" sortable>
                    <template #body="slotProps">
                        {{ "#" + slotProps.data.id }}
                    </template>
                </Column>
                <Column field="user" header="Name" sortable>
                    <template #body="slotProps">
                        {{ slotProps.data.user.first_name + " " + slotProps.data.user.last_name }}
                    </template>                
                </Column>
                <!-- <Column header="Product(s)" sortable>
                    <template #body="slotProps">
                        <DataTable :value="slotProps.data.order_details">
                            <Column field="product.name" headerStyle="display:none"></Column>
                        </DataTable>
                    </template>            
                </Column> -->
                <!-- <Column field="quantity" header="Quantity" sortable> -->
                <!-- <Column field="cost" header="Cost" sortable>
                    <template #body="slotProps">
                        {{formatCurrency(slotProps.data.order_details)}}
                    </template>
                </Column> -->
                <!-- {{orders.order_details}} -->
                    <!-- <template #body="slotProps">
                        <DataTable :value="slotProps.data.order_details">
                            <Column field="quantity" headerStyle="display:none"></Column>
                        </DataTable>
                    </template>     
                </Column> -->
                <Column field="address" header="Delivery Address" sortable>
                    <template #body="slotProps">
                        {{slotProps.data.address.country + ", " + slotProps.data.address.address + ", "}}<span v-if="slotProps.data.address.address_2 !== null">{{slotProps.data.address.address_2 + ", "}}</span>{{slotProps.data.address.city + ", " + slotProps.data.address.postal_code}}
                    </template>                
                </Column>
                <Column field="order_status" header="Status" sortable>
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
                <Column field="is_delivered" header="Delivery" sortable>
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.is_delivered == 1 ? 'Shipped' : 'Awaiting'" 
                        :icon="slotProps.data.is_delivered == 1 ? 'pi pi-check' : 'pi pi-spin pi-spinner'" 
                        :severity="slotProps.data.is_delivered == 1 ? 'success' : 'info'">
                        </Tag>
                    </template>
                </Column>
                <Column>
                    <template #body="slotProps">
                        <Button icon="pi pi-send" class="p-button-rounded p-button-success p-mr-2" @click="confirmDeliver(slotProps.data)" :disabled="slotProps.data.is_delivered == 1"/>
                        <Button icon="pi pi-trash" class="p-button-rounded p-button-warning" @click="confirmDeleteOrder(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog :visible.sync="orderDialog" :style="{width: '450px'}" header="Confirm" :modal="true">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle p-mr-3" style="font-size: 2rem" />
                <span v-if="order">Are you sure you want to deliver the order <b>{{order.name}}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" class="p-button-text" @click="orderDialog = false" />
                <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deliverOrder" />
            </template>
        </Dialog>

        <Dialog :visible.sync="deleteOrderDialog" :style="{width: '450px'}" header="Confirm" :modal="true">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle p-mr-3" style="font-size: 2rem" />
                <span v-if="order">Are you sure you want to delete <b>{{order.name}}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" class="p-button-text" @click="deleteOrderDialog = false" />
                <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deleteOrder" />
            </template>
        </Dialog>

        <Dialog :visible.sync="deleteOrdersDialog" :style="{width: '450px'}" header="Confirm" :modal="true">
            <div class="confirmation-content">
                <i class="pi exclamation-check-triangle p-mr-3" style="font-size: 2rem" />
                <span v-if="order">Are you sure you want to delete the selected orders?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" class="p-button-text" @click="deleteOrdersDialog = false"/>
                <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deleteSelectedOrders" />
            </template>
        </Dialog>

        <Toast />
    </div>
</template>

<script>
    export default {
        data () {
            return {
                orders : [],
                orderDialog: false,
                deleteOrderDialog: false,
                deleteOrdersDialog: false,
                order: {},
                selectedOrders : null,
                filters: {},
                submitted: false,
            }
        },
        beforeMount() {
            axios.get('/api/orders/').then(response => this.orders = response.data)
        },
        methods : {
            formatCurrency(value) {
                return value.toLocaleString('nl-NL', {style: 'currency', currency: 'EUR'});
            },
            confirmDeliver(order) {
                this.order = {...order};
                this.orderDialog = true;
            },
            deliverOrder() {
                if (this.order.id) {
                this.$set(this.orders, this.findIndexById(this.order.id), this.order)
                    axios.patch(`/api/orders/${this.order.id}/deliver`, { is_delivered: '1'}).then(response => {
                        axios.get('/api/orders/').then(response => this.orders = response.data)
                    });
                }
                this.orderDialog = false;
                this.order = {};
            },
            confirmDeleteOrder(order) {
                this.order = order;
                this.deleteOrderDialog = true;
            },
            deleteOrder() {
                axios.delete(`/api/orders/${this.order.id}/delete`).then(response => {
                    if(response.data.status) {
                        this.orders = this.orders.filter(val => val.id !== this.order.id);
                        this.deleteOrderDialog = false;
                        this.$toast.add({severity:'success', summary: 'Success Message', detail: response.data.message, life: 3000});
                        this.order = {};
                    }
                });
            },
            confirmDeleteSelected() {
                this.deleteOrdersDialog = true;
            },
            deleteSelectedOrders() {
                this.selectedOrders = this.orders.filter(val => this.selectedOrders.includes(val));
                this.orders = this.orders.filter(val => !this.selectedOrders.includes(val));
                this.selectedOrdersIds = this.orders.map(({id}) => id);


                axios.delete(`/api/orders/${this.selectedOrdersIds}/deleteMany`).then(response => {
                    this.$toast.add({severity:'success', summary: 'Success Message', detail: response.data.message, life: 3000});
                });

                this.deleteOrdersDialog = false;
                this.selectedOrders = null;
            },
            findIndexById(id) {
                let index = -1;
                for (let i = 0; i < this.orders.length; i++) {
                    if (this.orders[i].id === id) {
                        index = i;
                        break;
                    }
                }
                return index;
            },
        }
    }
</script>

<style scoped>
.table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
</style>