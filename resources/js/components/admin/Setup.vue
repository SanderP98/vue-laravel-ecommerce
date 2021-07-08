<template>
    <div class="container">
        <div class="content-section implementation">
            <keep-alive>
                <router-view :formData="formObject" @prevPage="prevPage($event)" @nextPage="nextPage($event)" @complete="complete" />
            </keep-alive>
        </div>
    </div>
</template>

<script>

export default {
    data () {
        return {
            items: [
                {
                    label: 'Welcome',
                    to: 'welcome'
                },
                {
                    label: 'Shop information',
                    to: 'shop-information' 
                },
                {
                    label: 'Payment setup',
                    to: 'payment-setup'
                },
            ],
            formObject: {},
        }

    },
    methods : {
        nextPage(event) {
            for (let field in event.formData) {
                this.formObject[field] = event.formData[field];
            }
            this.$router.push(this.items[event.pageIndex + 1].to);
        },
        prevPage(event) {
            this.$router.push(this.items[event.pageIndex - 1].to);
        },
        complete(event) {
            for (let field in event.formData) {
                this.formObject[field] = event.formData[field];
            }
            let name = this.formObject.shop.name
            let email = this.formObject.shop.email
            let tel_nr = this.formObject.shop.tel_nr
            let mollie_api_key = this.formObject.payment.mollieApiKey
            let paymentOptions = this.formObject.payment.selectedMethods

            
            if ( this.formObject.attachment != null ) {
                var formData = new FormData();
                formData.append("image", this.formObject.attachment)
                let headers = {'Content-Type': 'multipart/form-data'}
                axios.post("/api/upload/shop-logo", formData, {headers}).then(response => {
                    let image = response.data

                    axios.post("/api/shop/", {name, email, image, tel_nr, mollie_api_key, paymentOptions});

                    this.$router.push({name: 'admin'}).catch(() => {});
                    this.$parent.$parent.$emit('updateNav')
                });
            }

        },
    }
}
</script>
