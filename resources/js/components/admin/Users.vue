<template>
<div>
    <div>
        <Toolbar class="mb-4">
            <template #left>
                <Button label="New" icon="pi pi-plus" class="p-button-success mr-2" @click="addUser" />
                <Button label="Delete" icon="pi pi-trash" class="p-button-danger" @click="confirmDeleteSelected" :disabled="!selectedUsers || !selectedUsers.length" />
            </template>
        </Toolbar>

        <DataTable ref="dt" :value="users" :selection.sync="selectedUsers" dataKey="id"
        :paginator="true" :rows="10" :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPage RowsPerPageDropdown" :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} users">
            <template #header>
                <div class="table-header">
                    <h5 class="p-m-0">Manage users</h5>
                    <span class="p-input-icon-left">
                        <i class="pi pi-search"></i>
                        <InputText v-model="filters['global']" placeholder="Search..." />    
                    </span>                    
                </div>
            </template>

            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="first_name" header="First Name" sortable></Column>
            <Column field="last_name" header="Last Name" sortable></Column>
            <Column field="phone_number" header="Phone Number" sortable></Column>
            <Column field="email" header="E-mail" sortable></Column>
            <Column field="is_admin" header="Role" sortable>
                <template #body="slotProps">
                    <div v-if="slotProps.data.is_admin == 1">Admin</div>
                    <div v-else>User</div>
                </template>
            </Column>
            <Column field="created_at" header="Creation Date" sortable></Column>
        </DataTable>
    </div>
</div>
    <!-- <div class="d-table p-mx-auto">
        <table class="table table-responsive table-striped">
            <thead>
                <tr>
                    <td></td>
                    <td>Name</td>
                    <td>Email</td>
                    <td>Joined</td>
                    <td>Total Orders</td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(user,index) in users" :key="index">
                    <td>{{index+1}}</td>
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <td>{{user.created_at}}</td>
                    <td>{{user.orders.length}}</td>
                </tr>
            </tbody>
        </table>
    </div> -->
</template>

<script>
export default {
    data() {
        return {
            users : [],
            selectedUsers : null,
            filters: {},
            user: {},
            userDialog: false,
            deleteUserDialog: false,
            deleteUsersDialog: false,
            submitted: false,
        }
    },
    beforeMount() {
        axios.get('/api/users/').then(response => this.users = response.data)
    },
    methods: {
        confirmDeleteUser(product) {
            this.product = product;
            this.deleteUserDialog = true;
        },
        deleteUser() {
            axios.delete(`/api/users/${this.user.id}/delete`).then(response => {
                if(response.data.status) {
                    this.users = this.users.filter(val => val.id !== this.user.id);
                    this.deleteProductDialog = false;
                    this.$toast.add({severity:'success', summary: 'Success Message', detail: response.data.message, life: 3000});
                    this.user = {};
                } else {
                    this.$toast.add({severity:'warn', summary: 'Warning Message', detail: response.data.message, life: 3000});
                }
            });
        },
        findIndexById(id) {
            let index = -1;
            for (let i = 0; i < this.users.length; i++) {
                if (this.users[i].id === id) {
                    index = i;
                    break;
                }
            }
            return index;
        },
        confirmDeleteSelected() {
            this.deleteUsersDialog = true;
        },
        deleteSelectedProducts() {

            this.selectedUsers = this.users.filter(val => this.selectedUsers.includes(val));
            this.users = this.users.filter(val => !this.selectedUsers.includes(val));
            this.selectedUserIds = this.selectedUsers.map(({id}) => id);
            
            axios.delete(`/api/products/${this.selectedUserIds}/deleteMany`).then(response => {
                this.$toast.add({severity:'success', summary: 'Success Message', detail: response.data.message, life: 3000});
            });

            this.deleteUsersDialog = false;
            this.selectedUsers = null;
        },
        addUser() {
            this.user = {};
            this.submitted = false;
            this.userDialog = true;
        }
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