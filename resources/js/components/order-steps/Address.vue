<template>
    <div>
        <Card>
            <template #title>
                Address
            </template>
            <template #content>
                <p class="p-text-secondary">Enter your information</p>
                <div class="p-fluid">
                    <div class="p-field">
                        <label for="country">Country</label>
                        <InputText id="country" v-model="address.country" :class="{'p-invalid': validationErrors.country && submitted}" :disabled="!editAddress" />
                        <!-- <Dropdown v-model="address.country" :options="countries" optionLabel="name" placeholder="Select a Country" :filter="true" filterPlaceholder="Find Country" :class="{'p-invalid': validationErrors.selectedCountry && submitted}" /> -->
                        <small v-show="validationErrors.country && submitted" class="p-error">Country is required.</small>
                    </div>
                    <div class="p-form-grid p-grid">
                        <div class="p-field p-col">
                            <label for="address">Address</label>
                            <InputText id="address" v-model="address.address" :class="{'p-invalid': validationErrors.address && submitted}" :disabled="!editAddress"/>
                            <small v-show="validationErrors.address && submitted" class="p-error">Address is required.</small>
                        </div>
                        <div class="p-field p-col">
                            <label for="address_2">Address 2</label>
                            <InputText id="address_2" v-model="address.address_2" :disabled="!editAddress"/>
                        </div>
                    </div>
                    <div class="p-field">
                        <label for="city">City</label>
                        <!-- <InputText id="country" v-model="country" :class="{'p-invalid': validationErrors.selectedCity && submitted}" /> -->
                        <InputText id="city" v-model="address.city" :class="{'p-invalid': validationErrors.city && submitted}" :disabled="!editAddress"/>
                        <small v-show="validationErrors.city && submitted" class="p-error">City is required.</small>
                    </div>
                    <div class="p-form-grid p-grid">
                        <div class="p-field p-col">
                            <label for="state">State/Province</label>
                            <InputText id="state" v-model="address.state" :class="{'p-invalid': validationErrors.state && submitted}" :disabled="!editAddress"/>
                            <small v-show="validationErrors.state && submitted" class="p-error">State/Province is required.</small>
                        </div>
                        <div class="p-field p-col">
                            <label for="postal_code">Postal Code</label>
                            <InputText id="postal_code" v-model="address.postal_code" :class="{'p-invalid': validationErrors.postal_code && submitted}" :disabled="!editAddress"/>
                            <small v-show="validationErrors.postal_code && submitted" class="p-error">Postal Code is required.</small>
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
                    <Button :disabled="editAddress" label="Update information" @click="updateInfo()" class="p-button-help" />
                    <Button label="Next" @click="nextPage()" class="float-right" icon="pi pi-angle-right" iconPos="right" />
                </div>
            </template>
        </Card>
    </div>
</template>

<script>
export default {
    data () {
        return {
            countries: [],
            address: {
                'country' : '',
                'address' : '',
                'address_2' : '',
                'city' : '',
                'state' : '',
                'postal_code' : '',
            },  
            address_id: 0,         
            selectedCountry: null,
            submitted: false,
            validationErrors: {},
            hasAddress: false,
            editAddress: false,
        }
    },
    beforeMount() {
        // axios.get('http://restcountries.eu/rest/v2/all').then(response => {
        //     response.data.forEach(country => {
        //         this.countries.push(country);
        //     })
        // })
        let user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.user'))
        axios.get(`/api/users/${user.id}/address`).then(response => {
            if ( response.data.length != 0 ) {
                this.address_id = response.data[0].id
                this.address = response.data[0]
                this.hasAddress = true
            } else {
                this.editAddress = true
            }
        }); 

    },
    methods: {
        updateInfo() {
            this.editAddress = true
        },
        nextPage(isFormValid) {
            this.submitted = true;
            // if (!isFormValid) {
            //     return;
            // }
            this.$emit('nextPage', {formData: {address_id: this.address_id, address: this.address, editAddress: this.editAddress, hasAddress: this.hasAddress}, pageIndex: 0});
        },
        validateForm() {
            if (!this.selectedCountry.trim())
                this.validationErrors['selectedCountry'] = true;
            else
                delete this.validationErrors['selectedCountry'];
            if (!this.selectedCountry.trim())
                this.validationErrors['address'] = true;
            else
                delete this.validationErrors['address'];
            return !Object.keys(this.validationErrors).length;
        }
    }
}
</script>
