<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">Register</div>
                    <div class="card-body">
                        <form>
                            <div class="form-row"> 
                                <div class="form-group col-md-6">
                                    <label for="first_name" class="col-form-label text-md-right">First Name</label>
                                    <input id="first_name" type="text" class="form-control p-inputtext p-component" v-model="first_name" required autofocus>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="last_name" class="col-form-label text-md-right">Last Name</label>
                                    <input id="last_name" type="text" class="form-control p-inputtext p-component" v-model="last_name" required autofocus>
                                </div>
                            </div>
                            <div class="form-row"> 
                                <div class="form-group col-md-6">
                                    <label for="phone_number" class="col-form-label text-md-right">Phone Number</label>
                                    <input id="phone_number" type="text" class="form-control p-inputtext p-component" v-model="phone_number" required autofocus>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email" class="col-form-label text-md-right">E-mail Address</label>
                                    <input id="email" type="email" class="form-control p-inputtext p-component" v-model="email" required autofocus>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="password" class="col-form-label text-md-right">Password</label>
                                    <Password v-model="password" toggleMask></Password>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="password-confirm" class="col-form-label text-md-right">Confirm Password</label>
                                    <Password v-model="password_confirmation"></Password>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 p-field-checkbox">
                                    <Checkbox id="mail_signup" v-model="mail_signup" :binary="true" />
                                    <label class="mailSignup" for="mail_signup">Yes, I want to receive updates via email</label>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary" @click="handleSubmit">
                                        Register
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props : ['nextUrl'],
        data () {
            return {
                first_name : "",
                last_name : "",
                email : "",
                phone_number : "",
                password : "",
                password_confirmation : "",
                mail_signup : false,
            }
        },
        methods : {
            handleSubmit(e) {
                e.preventDefault()
                if ( this.password !== this.password_confirmation || this.password.length <= 0 ) {
                    this.password = ""
                    this.password_confirmation = ""
                    return alert('Passwords do not match')
                }

                let first_name = this.first_name
                let last_name = this.last_name
                let phone_number = this.phone_number
                let email = this.email
                let password = this.password
                let c_password = this.password_confirmation
                let mail_signup = this.mail_signup

                axios.post('api/register', { first_name, last_name, phone_number, email, password, c_password, mail_signup }).then(response => {
                    let data = response.data
                    localStorage.setItem('vue-laravel-ecommerce.user', JSON.stringify(data.user))
                    localStorage.setItem('vue-laravel-ecommerce.jwt', data.token)
                    
                    if ( localStorage.getItem('vue-laravel-ecommerce.jwt') ) {
                        this.$emit('loggedIn')
                        let nextUrl = this.$route.params.nextUrl
                        this.$router.push((nextUrl != null ? nextUrl : '/'))
                    }
                })
            }
        }

    }
</script>

<style lang="scss" scoped>
.mailSignup {
    margin-bottom:0px;
}
::v-deep .p-password {
    display:block;
}
::v-deep .p-password-input {
    width:100%;
}
</style>
