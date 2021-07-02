<template>
    <div>
        <Card>
            <template #title>
                Shop Information
            </template>
            <template #content>
                <div class="p-fluid">
                    <div class="p-field">
                        <label for="image">Image</label>
                        <FileUpload name="image" id="image" mode="basic" v-model="shop.image" :chooseLabel="chooseLabel" required="true" :multiple="true"  accept="image/*" :auto="true" :customUpload="true" @uploader="attachFile" :loading="isSelecting" @click="selectedFile" />
                    </div>    
                    <div class="p-field">
                        <div class="p-field">
                            <label for="name">Shop Name</label>
                            <div class="p-inputgroup">
                            <span class="p-inputgroup-addon">
                                <i class="pi pi-home"></i>
                            </span>
                                <InputText id="name" v-model="shop.name"/>
                            </div>
                        </div>
                    </div>
                    <div class="p-form-grid p-grid">
                        <div class="p-field p-col">
                            <label for="email">E-mail Address</label>
                            <div class="p-inputgroup">
                            <span class="p-inputgroup-addon">
                                <i class="pi pi-envelope"></i>
                            </span>
                                <InputText type="email" id="email" v-model="shop.email"/>
                            </div>
                        </div>
                        <div class="p-field p-col">
                            <label for="telephone">Telephone Number</label>
                            <div class="p-inputgroup">
                            <span class="p-inputgroup-addon">
                                <i class="pi pi-phone"></i>
                            </span>
                                <InputText type="tel" id="telephone" v-model="shop.tel_nr"/>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="p-field">
                        <label for="age">Age</label>
                        <InputNumber id="age" v-model="age" />
                    </div> -->
                </div>
            </template>
            <template #footer>
                <div class="p-grid p-nogutter p-justify-between">
                    <Button label="Back" @click="prevPage()" icon="pi pi-angle-left" />
                    <Button label="Next" @click="nextPage()" icon="pi pi-angle-right" iconPos="right"/>
                </div>
            </template>
        </Card>
    </div>
</template>

<script>

export default {
    props: ['formData'],
    data () {
        return {
            shop: {},
            chooseLabelText: 'Select a file',
            isSelecting: false,
            attachment: null,
        }
    },
    computed: {
        chooseLabel() {
            return this.attachment ? this.attachment.name : this.chooseLabelText
        }
    },
    methods : {
        nextPage() {
            this.$emit('nextPage', {formData: {shop: this.shop, attachment: this.attachment}, pageIndex: 1});
        },
        prevPage() {
            this.$emit('prevPage', {pageIndex: 1});
        },
        attachFile(event) {
            this.attachment = event.files[0] 
        },
        selectedFile() {
        this.isSelecting = true
        window.addEventListener('focus', () => {
            this.isSelecting = false
        }, 
            { once: true })
        },
    }
}
</script>


