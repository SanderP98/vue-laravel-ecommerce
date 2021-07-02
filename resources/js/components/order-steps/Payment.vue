<template>
<div>
    <Card>
        <template #title>
            Payment
        </template>
        <template #content>
            <p class="p-text-secondary">Select a payment method to continue</p>
            <div class="p-fluid">
                <div class="p-field">
                    <small v-show="validationErrors.selectedMethod && submitted" class="p-error">Country is required.</small>
                    <div class="form-check" v-for="paymentMethod in paymentMethods" :key="paymentMethod.name" :class="{'p-invalid': validationErrors.selectedMethod && submitted}">
                        <input class="form-check-input" type="radio" :id="paymentMethod.key" name="name" :value="paymentMethod" v-model="selectedMethod" :disabled="paymentMethod.key === 'R'" >
                        <label class="form-check-label" :for="paymentMethod.key">
                            {{paymentMethod.name.toUpperCase()}}
                        </label>
                    </div>
                    <small v-show="validationErrors.selectedMethod && submitted" class="p-error">Payment method is required.</small>
                </div>
            </div>
        </template>
        <template #footer>
            <div class="p-grid p-nogutter p-justify-between">
                <Button label="Back" @click="prevPage()" icon="pi pi-angle-left" />
                <Button label="Next" @click="nextPage()" icon="pi pi-angle-right" iconPos="right" />
            </div>
        </template>
    </Card>
</div>
</template>

<script>
export default {
    props : ['formData'],
    data () {
        return {
            paymentMethods: [],
            selectedMethod: null,
            validationErrors: {},
        }
    },
    beforeMount() {
        if( Object.entries(this.$props.formData).length === 0 ) {
            this.$router.push({ path: '/' })
        }
        axios.get('/api/payment-methods/1').then(response => { 
            this.paymentMethods = response.data
        });
    },
    methods: {
        nextPage() {
            this.$emit('nextPage', {formData: {paymentMethod: this.selectedMethod.name}, pageIndex: 1});
        },
        prevPage() {
            this.$emit('prevPage', {pageIndex: 1});
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

<style lang="scss" scoped>
::v-deep .custom { 
    height: 200px;
}
::v-deep .custom .p-scrollpanel-wrapper {
    border-right: 9px solid #f4f4f4;
}

::v-deep .custom .p-scrollpanel-bar {
    background-color: #1976d2;
    opacity: 1;
    transition: background-color .3s;
}

::v-deep .custom .p-scrollpanel-bar:hover {
    background-color: #135ba1;
}
</style>