<template>
<div>
    <DataTable :value="approvableReviews" :paginator="true" class="p-datatable-customers" :rows="10"
        dataKey="id" :rowHover="true" :selection.sync="selectedCustomers" :filters="filters" :loading="loading"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown" :rowsPerPageOptions="[10,25,50]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries">
        <template #header>
            <div class="table-header">
                Unapproved reviews
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText v-model="filters['global']" placeholder="Global Search" />
                </span>
            </div>
        </template>
        <template #empty>
            No reviews found.
        </template>
        <template #loading>
            Loading reviews data. Please wait.
        </template>
        <Column selectionMode="multiple" headerStyle="width: 3em"></Column>
        <Column field="product.id" header="ID" :sortable="true">
            <template #body="slotProps">
                {{slotProps.data.product.id}}
            </template>
        </Column>
        <Column field="title" header="Title" :sortable="true">
            <template #body="slotProps">
                {{slotProps.data.title}}
            </template>
        </Column>
        <Column field="description" header="Description" :sortable="true">
            <template #body="slotProps">
                {{ truncate(slotProps.data.description) }}
            </template>
        </Column>
        <Column field="rating" header="Rating" :sortable="true">
            <template #body="slotProps">
                {{ slotProps.data.rating + "/5" }}
            </template>
        </Column>
        <Column header="Author" :sortable="true">
            <template #body="slotProps">
                {{slotProps.data.user.first_name + ' ' + slotProps.data.user.last_name}}
            </template>
        </Column>
        <Column field="created_at" header="Time of publication" :sortable="true">
            <template #body="slotProps">
                {{slotProps.data.created_at}}
            </template>
        </Column>
        <Column>
            <template #body="slotProps">
                <Button icon="pi pi-eye" class="p-button-rounded" @click="viewReview(slotProps.data)"></Button>
                <Button icon="pi pi-trash" class="p-button-rounded p-button-warning p-mr-2" @click="viewReview(slotProps.data)"></Button>
            </template>
        </Column>
    </DataTable>

    <DataTable :value="approvedReviews" :paginator="true" class="p-datatable-customers" :rows="10"
        dataKey="id" :rowHover="true" :selection.sync="selectedCustomers" :filters="filters" :loading="loading"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown" :rowsPerPageOptions="[10,25,50]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries">
        <template #header>
            <div class="table-header">
                Approved reviews
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText v-model="filters['global']" placeholder="Global Search" />
                </span>
            </div>
        </template>
        <template #empty>
            No reviews found.
        </template>
        <template #loading>
            Loading reviews data. Please wait.
        </template>
        <Column selectionMode="multiple" headerStyle="width: 3em"></Column>
        <Column field="product.id" header="ID" :sortable="true">
            <template #body="slotProps">
                {{slotProps.data.product.id}}
            </template>
        </Column>
        <Column field="title" header="Title" :sortable="true">
            <template #body="slotProps">
                {{slotProps.data.title}}
            </template>
        </Column>
        <Column field="description" header="Description" :sortable="true">
            <template #body="slotProps">
                {{ truncate(slotProps.data.description) }}
            </template>
        </Column>
        <Column field="rating" header="Rating" :sortable="true">
            <template #body="slotProps">
                {{ slotProps.data.rating + "/5" }}
            </template>
        </Column>
        <Column header="Author" :sortable="true">
            <template #body="slotProps">
                {{slotProps.data.user.first_name + ' ' + slotProps.data.user.last_name}}
            </template>
        </Column>
        <Column field="created_at" header="Time of publication" :sortable="true">
            <template #body="slotProps">
                {{slotProps.data.created_at}}
            </template>
        </Column>
        <Column>
            <template #body="slotProps">
                <Button icon="pi pi-eye" class="p-button-rounded" @click="viewReview(slotProps.data)"></Button>
                <Button icon="pi pi-trash" class="p-button-rounded p-button-warning p-mr-2" @click="viewReview(slotProps.data)"></Button>
            </template>
        </Column>
    </DataTable>

    <Dialog :visible.sync="reviewDialog" :style="{width: '650px'}" :modal="true" :header="review.product.name" class="p-fluid" v-if="reviewDialog">
        <div class="p-grid">
            <div class="p-grid p-fluid p-formgrid p-col-6">
                <div class="p-field p-col-12">
                    <img :src="'/products/' + review.product_image[0].image" :alt="review.product_image[0]" class="productImage" height="200px" v-if="review.product_image" />
                </div>
            </div>
            <div class="p-fluid p-formgrid p-grid p-col-6">
                <div class="p-field p-col-12">
                    <label for="author">Author</label>
                    <InputText id="author" type="text" :value="review.user.first_name + ' ' + review.user.last_name" readonly/>
                </div>
                <div class="p-field p-col-12">
                    <label for="title">Title</label>
                    <InputText id="title" type="text" v-model.trim="review.title" readonly/>
                </div>
                <div class="p-field p-col-12">
                    <label for="description">Description</label>
                    <TextArea class="p-inputtextarea p-inputtext p-component p-filled resize-none" id="description" rows="4" cols="20" v-model="review.description" readonly/>
                </div>
                <div class="p-field p-col-12">
                    <label for="rating">Rating</label>
                    <Rating id="rating" :value="review.rating" :readonly="true" :cancel="false" class="p-mr-3"/>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Approve" icon="pi pi-check" class="p-button-success" @click="approveReview" :disabled="review.is_approved == 1" />
            <Button label="Refuse" icon="pi pi-times" class="p-button-danger" @click="refuseReview"/>
        </template>
    </Dialog>
    
</div>

</template>

<script>

export default {
    data() {
        return {
            approvableReviews : [],
            approvedReviews : [],
            review: {},
            reviewDialog: false,
            selectedCustomers: null,
            filters: {},
            loading: true,
        }
    },
    beforeMount() {
        axios.get('/api/approvableReviews/').then(response => {
            this.approvableReviews = response.data
            this.loading = true;
        });
        axios.get('/api/approvedReviews/').then(response => {
            this.approvedReviews = response.data
            this.loading = false;
        });
    },
    methods : {
        truncate(value) {
            if ( value.length > 20 ) {
                return value.substring(0, 20) + "..";
            } else {
                return value
            }
        },
        viewReview(review) {
            this.review = {...review};
            this.reviewDialog = true;
        },
        approveReview() {
            this.loading = true;
            axios.patch(`/api/approveReview/${this.review.id}`, { is_approved: '1'})
            .then(response => {
                axios.get('/api/approvableReviews/').then(response => {
                    this.approvableReviews = response.data
                });
                axios.get('/api/approvedReviews/').then(response => {
                    this.approvedReviews = response.data
                    this.loading = false;
                });
                this.reviewDialog = false;
            });
        },
        refuseReview() {
            this.loading = true;

            axios.delete(`/api/refuseReview/${this.review.id}`)
            .then(response => {
                axios.get('/api/approvableReviews/').then(response => {
                    this.approvableReviews = response.data
                });
                axios.get('/api/approvedReviews/').then(response => {
                    this.approvedReviews = response.data
                    this.loading = false;
                });

                this.reviewDialog = false;
            });           
        }
    }
}
</script>

<style scoped>
.resize-none {
    resize: none;
}
.productImage {
    width:200px;
    margin-left: auto;
    margin-right: auto;
    display: inherit;
}
::v-deep .p-card-header {
    padding:1rem;
}
/*
::v-deep .p-card-title p {
    font-size:initial;
    font-weight:initial;
    margin-bottom:0;
}
::v-deep .p-card-header, .p-card-header img {
    padding-top: 10px;
}
.p-card-header img {
    width:auto;
    margin-left: auto;
    margin-right: auto;
    display:block;
}
::v-deep .p-card-content {
    background: #607D8B;
    padding:1rem;
    color: #ffffff;
    border-radius: 3px;
}
::v-deep .p-card-content p {
    margin:0;
    font-weight:600;
} */
::v-deep .p-rating .p-rating-icon.pi-star, .p-rating:not(.p-disabled):not(.p-readonly) .p-rating-icon:hover  {
    color: #F7BD17 !important;
}
.table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
</style>