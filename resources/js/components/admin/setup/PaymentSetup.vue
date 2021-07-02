<template>
    <div>
        <Card>
            <template #content>
                <div class="p-field">
                    <div class="p-field">
                        <label for="mollie">Mollie API Key</label>
                        <div class="p-inputgroup">
                        <span class="p-inputgroup-addon">
                            <i class="pi pi-globe"></i>
                        </span>
                            <InputText id="mollie" v-model="payment.mollieApiKey"/>
                        </div>
                    </div>
                </div>
                <p class="p-text-secondary">Select the payment methods you want to use</p>
                <div class="p-fluid">
                    <div class="p-field">
                        <MultiSelect v-model="payment.selectedMethods" :options="paymentMethods" optionLabel="name" placeholder="Payment Methods" />
                        <!--<small v-show="validationErrors.selectedMethods && submitted" class="p-error">Payment method is required.</small>-->
                    </div>
                </div>
            </template>
            <template #footer>
                <div class="p-grid p-nogutter p-justify-between">
                    <Button label="Back" @click="prevPage()" icon="pi pi-angle-left" />
                    <Button label="Complete" @click="complete()" icon="pi pi-check" iconPos="right" class="p-button-success"/>
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
            payment: {},
            paymentMethods: [],
        }
    },
    created() {
        axios.get('/api/payment-methods/0').then(response => { 
            this.paymentMethods = response.data
        });
    },
    methods: {
        prevPage() {
            this.$emit('prevPage', {pageIndex: 2});
        },
        complete() {
            this.$emit('complete', {formData: {payment: this.payment}});
        },
    }
}
</script>
