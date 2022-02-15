<template>
<div>
        <div>
            <Toolbar class="mb-4">
                <template #start>
                    <Button label="New" icon="pi pi-plus" class="p-button-success mr-2" @click="addProduct" />
                    <Button label="Delete" icon="pi pi-trash" class="p-button-danger" @click="confirmDeleteSelected" :disabled="!selectedProducts || !selectedProducts.length" />
                </template>

                <template #end>
                    <Button label="Add product category" icon="pi pi-plus" class="p-button-info mr-2" @click="addProductCategory($event)" />
                    <Button label="Export" icon="pi pi-upload" class="p-button-help" @click="exportCSV($event)" />
                </template>
            </Toolbar>

            <DataTable ref="dt" :value="products" :selection.sync="selectedProducts" dataKey="id"
            :paginator="true" :rows="10" :filters="filters"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown" :rowsPerPageOptions="[5,10,25]"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} products" responsiveLayout="scroll">
                <template #header>
                    <div class="table-header p-d-flex p-flex-column p-flex-md-row p-jc-md-between">
                        <h5 class="p-m-0">Manage products</h5>
                        <span class="p-input-icon-left">
                            <i class="pi pi-search"/>
                            <InputText v-model="filters['global']" placeholder="Search..." />
                        </span>
                    </div>
                </template>
                <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                <Column field="name" header="Name" :sortable="true"></Column>
                <Column field="description" header="Description" :sortable="true"></Column>
                <Column header="Image">
                    <template #body="slotProps">
                        <img :src="'/products/' + slotProps.data.product_image[0].image" :alt="slotProps.data.product_image[0].name" class="product-image" />
                    </template>
                </Column>
                <Column field="price" header="Price" :sortable="true">
                    <template #body="slotProps">
                        {{formatCurrency(slotProps.data.price)}}
                    </template>
                </Column>
                <Column field="units" header="Units" :sortable="true">
                    <template #body="slotProps">
                        <span :class="'product-badge status-' + slotProps.data.units">{{slotProps.data.units}}</span>
                    </template>
                </Column>
                <Column>
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" class="p-button-rounded p-button-success p-mr-2" @click="editProduct(slotProps.data)" />
                        <Button icon="pi pi-trash" class="p-button-rounded p-button-warning" @click="confirmDeleteProduct(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog :visible.sync="productCategoryDialog" :style="{width: '450px'}" header="Add Product Category" :modal="true" class="p-fluid">
            <p v-if="errors.length">
                <b>Please correct the following error(s):</b>
                <ul>
                <li v-for="error in errors" :key="error">{{ error }}</li>
                </ul>
            </p>
            <div class="p-field">
                <label for="name">Name</label>
                <InputText id="name" v-model.trim="category.name" required="true" autofocus :class="{'p-invalid': submitted && !category.name}" />
                <small class="p-invalid" v-if="submitted && !category.name">Name is required</small>
            </div>
            <div class="p-field">
                <label for="description">Description</label>
                <TextArea id="description" v-model="category.description"/>
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="hideDialog"/>
                <Button label="Save" icon="pi pi-check" class="p-button-text" @click="saveCategory" />
            </template>
        </Dialog>

        <Dialog :visible.sync="productDialog" :style="{width: '450px'}" header="Product Details" :modal="true" class="p-fluid">
            <img :src="'/products/' + product.product_image[0].image" :alt="product.product_image[0].name" class="product-image mb-2" v-if="product.product_image" />
            <div class="p-field">
                <label for="name">Name</label>
                <InputText id="name" v-model.trim="product.name" required="true" autofocus :class="{'p-invalid': submitted && !product.name}" />
                <small class="p-invalid" v-if="submitted && !product.name">Name is required</small>
            </div>
            <div class="p-field">
                <label for="category">Category</label>
                <Dropdown id="category" v-model="product.category" :options="categories" optionLabel="name" placeholder="Select a category"/>
            </div>
            <div class="p-field">
                <label for="description">Description</label>
                <TextArea id="description" v-model="product.description" required="true" rows="3" cols="20" />
            </div>
             <div class="p-field">
                <label for="image">Image</label>
                <FileUpload name="image" id="image" mode="basic" v-model="product.image" :chooseLabel="chooseLabel" required="true" :multiple="true"  accept="image/*" :auto="true" :customUpload="true" @uploader="attachFile" :loading="isSelecting" @click="selectedFile" />
            </div>           
            <div class="p-form-grid p-grid">
                <div class="p-field p-col">
                    <label for="price">Price</label>
                    <InputNumber id="price" v-model="product.price" mode="currency" required="true" currency="EUR" locale="nl-NL" />
                </div>
                <div class="p-field p-col">
                    <label for="units">Quantity</label>
                    <InputNumber id="units" v-model="product.units" required="true" integeronly />
                </div>
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="hideDialog"/>
                <Button label="Save" icon="pi pi-check" class="p-button-text" @click="uploadFile" />
            </template>
        </Dialog>

        <Dialog :visible.sync="deleteProductDialog" :style="{width: '450px'}" header="Confirm" :modal="true">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle p-mr-3" style="font-size: 2rem" />
                <span v-if="product">Are you sure you want to delete <b>{{product.name}}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" class="p-button-text" @click="deleteProductDialog = false" />
                <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deleteProduct" />
            </template>
        </Dialog>

        <Dialog :visible.sync="deleteProductsDialog" :style="{width: '450px'}" header="Confirm" :modal="true">
            <div class="confirmation-content">
                <i class="pi exclamation-check-triangle p-mr-3" style="font-size: 2rem" />
                <span v-if="product">Are you sure you want to delete the selected products?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" class="p-button-text" @click="deleteProductsDialog = false"/>
                <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deleteSelectedProducts" />
            </template>
        </Dialog>

        <Toast />
    </div>
</template>

<script>
    export default {
        data () {
            return {
                products: [],
                categories: [],
                productDialog: false,
                productCategoryDialog: false,
                deleteProductDialog: false,
                deleteProductsDialog: false,
                product: {},
                category: {},
                selectedProducts : null,
                filters: {},
                submitted: false,
                attachment: null,
                chooseLabelText: 'Select a file',
                isSelecting: false,
                errors: [],
            }
        },
        computed: {
            chooseLabel() {
                return this.attachment ? this.attachment.name : this.chooseLabelText
            }
        },
        mounted() {
            this.getProducts();
        },
        methods : {
            formatCurrency(value) {
                return value.toLocaleString('nl-NL', {style: 'currency', currency: 'EUR'});
            },
            addProduct() {
                this.chooseLabelText = "Select a file";
                this.product = {};
                this.submitted = false;
                this.productDialog = true;
            },
            addProductCategory() {
                this.category = {};
                this.submitted = false;
                this.productCategoryDialog = true;
            },
            hideDialog() {
                this.productDialog = false;
                this.productCategoryDialog = false;
                this.submitted = false;
            },
            attachFile(event) {
                this.attachment = event.files[0] 
            },
            uploadFile() {
                if ( this.attachment != null && this.product.id) {
                    var formData = new FormData();
                    formData.append("image", this.attachment)
                    let headers = {'Content-Type': 'multipart/form-data'}
                    axios.post("/api/upload-file", formData, {headers}).then(response => {
                        let newImage = response.data 
                        this.saveProduct(newImage);
                        // alert(response.data);
                    });
                } else {
                    this.saveProduct();
                }
            },
            saveProduct(newImage) {
                this.submitted = true;
                if (this.product.name.trim()) {
                    if (this.product.id) {
                        this.$set(this.products, this.findIndexById(this.product.id), this.product)

                        let category = this.product.category.id
                        let name = this.product.name
                        let units = this.product.units
                        let price = this.product.price
                        let description = this.product.description
                        let image = newImage ? newImage : this.product.image
                        
                        axios.put(`/api/products/${this.product.id}`, {category, name, units, price, description, image})
                        .then(response => {
                            axios.get('/api/products/').then(response => this.products = response.data.products)
                            this.$toast.add({severity:'success', summary: 'Successful', detail: 'Product Updated', life: 3000});
                        });
                    } else {
                        let category = this.product.category.id
                        let name = this.product.name
                        let units = this.product.units
                        let price = this.product.price
                        let description = this.product.description

                        if ( this.attachment != null ) {
                            var formData = new FormData();
                            formData.append("image", this.attachment)
                            let headers = {'Content-Type': 'multipart/form-data'}
                            axios.post("/api/upload-file", formData, {headers}).then(response => {
                                this.product.image = response.data
                                let image = this.product.image
                                this.$emit('close', this.product)

                                axios.post("/api/products/", {category, name, units, price, description, image});

                                this.getProducts();

                                this.$toast.add({severity:'success', summary: 'Successful', detail: 'Product Created', life: 3000});
                            });
                        }
                    }

                    this.productDialog = false;
                    this.product = {};
                    this.attachment = {};
                }
            },
            saveCategory(newCategory) {
                this.submitted = true;
                let name = this.category.name;
                let description = this.category.description;

                if (this.errors.length === 0) {
                    axios.post("/api/addProductCategory/", {name, description}).then(response => {
                        this.productCategoryDialog = false;
                        this.errors = {};
                        this.category = {};
                        this.$toast.add({severity:'success', summary: 'Success Message', detail: response.data.message, life: 3000});
                    });
                }

                this.getProducts();
            },
            editProduct(product) {
                this.chooseLabelText = product.image;
                product.category = product.product_category
                this.product = {...product};
                this.productDialog = true;
            },
            confirmDeleteProduct(product) {
                this.product = product;
                this.deleteProductDialog = true;
            },
            deleteProduct() {
                axios.delete(`/api/products/${this.product.id}/delete`).then(response => {
                    if(response.data.status) {
                        this.products = this.products.filter(val => val.id !== this.product.id);
                        this.deleteProductDialog = false;
                        this.$toast.add({severity:'success', summary: 'Warning Message', detail: response.data.message, life: 3000});
                        this.product = {};
                    } else {
                        this.products = this.products.filter(val => val.id !== this.product.id);
                        this.deleteProductDialog = false;
                        this.$toast.add({severity:'success', summary: 'Success Message', detail: response.data.message, life: 3000});
                        this.product = {};
                    }
                });
            },
            findIndexById(id) {
                let index = -1;
                for (let i = 0; i < this.products.length; i++) {
                    if (this.products[i].id === id) {
                        index = i;
                        break;
                    }
                }
                return index;
            },
            exportCSV() {
                this.$refs.dt.exportCSV();
            },
            confirmDeleteSelected() {
                this.deleteProductsDialog = true;
            },
            deleteSelectedProducts() {

                this.selectedProducts = this.products.filter(val => this.selectedProducts.includes(val));
                this.products = this.products.filter(val => !this.selectedProducts.includes(val));
                this.selectedProductsIds = this.selectedProducts.map(({id}) => id);
                axios.delete(`/api/products/${this.selectedProductsIds}/deleteMany`).then(response => {
                    this.$toast.add({severity:'success', summary: 'Success Message', detail: response.data.message, life: 3000});
                });

                this.deleteProductsDialog = false;
                this.selectedProducts = null;
            },
            selectedFile() {
            this.isSelecting = true
            window.addEventListener('focus', () => {
                this.isSelecting = false
            }, 
                { once: true })
            },
            getProducts() {
                axios.get('/api/products/').then(response => {
                    this.products = response.data.products
                    this.categories = response.data.categories
                });
            }
        },
    }
</script>

<style scoped>
.table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.product-image {
    width: 100px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23)
}
</style>
