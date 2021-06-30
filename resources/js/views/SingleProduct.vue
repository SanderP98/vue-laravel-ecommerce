<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div v-for="(product, index) in product" :key="index">
                    <!--{{product.product_image}}-->
                    <Card class="p-mb-3">
                        <template #content>
                            <div class="p-grid">
                                <div class="p-col-12 p-lg-7">
                                    <Galleria :value="product.product_image" :responsiveOptions="responsiveOptions" :numVisible="5" style="max-width: 640px">
                                        <template #item="slotProps">
                                            <img :src="slotProps.item.image" :alt="slotProps.item.name" height="250px" />
                                        </template>
                                        <template #thumbnail="slotProps">
                                            <img :src="slotProps.item.image" :alt="slotProps.item.name" width="50px"/>
                                        </template>
                                    </Galleria>
                                </div>
                                <div class="p-col-12 p-lg-5 p-p-0">
                                    <h3 class="title" v-html="product.name"></h3>
                                    
                                    <div class="p-col-12 p-lg-12 p-d-flex p-jc-between">
                                        <span class="small-text text-muted">
                                            {{formatCurrency(product.price)}}
                                        </span>
                                        <Rating :value="averageRating" :readonly="true" :cancel="false" class="p-mr-3 text-right"></Rating>
                                    </div>
                                    <Divider />
                                    <span class="small-text">Available Quantity: {{product.units}}</span>
                                    <div class="p-d-flex">
                                        <router-link :to="{ path: '/cart?pid='+product.id }" class="p-button p-mr-1">Buy Now</router-link>
                                        <Button icon="pi pi-shopping-cart" :disabled="product.units === '0'" @click="addToCart(product)"></Button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        </Card>
                        <Card class="p-mb-3">
                        <template #title>
                            <div class="p-grid">
                                <div class="p-col-12 p-lg-4 p-p-2"><span>Reviews ({{totalRatings}})</span><br/></div>
                                <div class="p-col-12 p-lg-8 p-d-flex"><h1 class="p-mr-3">{{averageRating}}</h1><Rating :value="averageRating" :readonly="true" :cancel="false" class="p-mr-3"></Rating></div>
                            </div>
                        </template>
                        <template #content>
                                <div v-for="ratingCount in ratingCounts" :key="ratingCount.index">
                                    <div class="p-grid">
                                        <div class="p-col-12 p-lg-4" style="padding: 0.5rem!important;">{{ratingCount.name}}</div>
                                        <div class="p-col-12 p-lg-8"><ProgressBar :value="ratingCount.averageOfTotal" :class="'value-'+ratingCount.value" :showValue="false"></ProgressBar></div>
                                    </div>
                                </div>
                        </template>
                        <template #footer>
                            <Button icon="pi pi-pencil" label="Write a review" class="p-button-secondary" @click="newReview(product)" :disabled="hasReview"/>
                        </template>
                    </Card>
                    <div v-for="(product_rating, index) in product.product_rating" :key="index">
                        <Card class="p-mb-3">
                            <template #content>
                                    <div class="p-grid">
                                        <div class="p-col-12 p-lg-4" style="padding: 0.5rem!important;">
                                            <!--Profielfoto later hierrrr-->
                                            <h5>{{product_rating.user.name}}</h5>
                                        </div>
                                        <div class="p-col-12 p-lg-8">
                                            <div class="p-d-flex"><Rating :value="product_rating.rating" :readonly="true" :cancel="false" class="p-mr-3"></Rating><span class="productRating">{{product_rating.rating}}</span></div><span>{{new Date(product_rating.created_at).toLocaleDateString()}}</span>
                                            <p class="mt-4"><strong>{{product_rating.title}}</strong></p>
                                            <p class="reviewDescription">"{{product_rating.description}}"</p>
                                            <!-- <i class="pi pi-thumbs-up"></i>
                                            <i class="pi pi-thumbs-down"></i> -->
                                        </div>
                                    </div>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
            <Dialog header="Write a review" :visible.sync="display" :style="{width: '50vw'}" >
                <p v-if="errors.length">
                    <b>Please correct the following error(s):</b>
                    <ul>
                    <li v-for="error in errors" :key="error">{{ error }}</li>
                    </ul>
                </p>
                <div class="p-field">
                    <label for="rating">Rating</label>
                    <Rating id="rating" v-model="review.rating" class="p-mr-3" :cancel="false" required="true"></Rating>
                </div>                
                <div class="p-field">
                    <label for="title">Title</label><br/>
                    <InputText id="title" style="with:100%!important;" v-model="review.title" type="text" required="true"/>
                </div>
                <div class="p-field">
                    <label for="description">Description</label><br/>
                    <TextArea id="description" v-model="review.description" required="true" rows="3" cols="20" maxlength="250"/>
                </div>
                <template #footer>
                    <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="hideDialog"/>
                    <Button label="Save" icon="pi pi-check" class="p-button-text" @click="addReview" />
                </template>
            </Dialog>

            <Toast />
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                user: null,
                product : [],
                overallRating: [],
                averageRating: 0,
                totalRatings: 0,
                review: {},
                errors: [],
                ratingCounts: {
                    fiveStars: {
                        name: "Excellent",
                        value: 5,
                        totalRatings: 0,
                        averageOfTotal: 0,
                    },
                    fourStars: {
                        name: "Good",
                        value: 4,
                        totalRatings: 0,
                        averageOfTotal: 0,
                    },
                    threeStars: {
                        name: "Average",
                        value: 3,
                        totalRatings: 0,
                        averageOfTotal: 0,
                    },
                    twoStars: {
                        name: "Below Average",
                        value: 2,
                        totalRatings: 0,
                        averageOfTotal: 0,
                    },
                    oneStar: {
                        name: "Poor",
                        value: 1,
                        totalRatings: 0,
                        averageOfTotal: 0,
                        },
                    },
                responsiveOptions: [
                    {
                        breakpoint: '1024px',
                        numVisible: 5
                    },
                    {
                        breakpoint: '768px',
                        numVisible: 3
                    },
                    {
                        breakpoint: '560px',
                        numVisible: 1
                    }
                ],
                responsiveOptions2: [
                    {
                        breakpoint: '768px',
                        numVisible: 3
                    },
                    {
                        breakpoint: '560px',
                        numVisible: 1
                    }
                ],
                display: false,
                hasReview: false,
            }
        },
        beforeMount() {
            this.getReviews();
        },
        watch: {
            display: function(val) {
                //do something when the data changes.
                if (val == false) {
                    this.review = {}
                }
            }
        },
        methods : {
            formatCurrency(value) {
                 return value.toLocaleString('nl-NL', {style: 'currency', currency: 'EUR'});               
            },
            addToCart(product) {
                product.quantity = 1;
                this.$parent.$emit('addToCart', product);
            },
            hideDialog() {
                this.display = false
            },
            newReview() {
                this.display = true  
            },
            addReview() {
                let product = this.product[0].id
                let user = this.user.id
                let title = this.review.title
                let rating = this.review.rating
                let description = this.review.description
                
                this.errors = [];

                if (!rating) {
                    this.errors.push("Rating required.");
                }
                if(!/[A-Za-z]+$/.test(title)) {
                    this.errors.push('Title is empty or contains numeric values.');
                } 
                if (!description) {
                    this.errors.push('Description required.')
                }

                if (!this.errors.length) {
                    axios.post(`/api/add-review/`, { product, user, rating, title, description }).then(response => {
                    this.$toast.add({severity:'success', summary: 'Successful', detail: response.data.message, life: 3000});
                    this.getReviews();
                    })
                    this.display = false
                }

            },
            getReviews() {
                let url = `/api/products/${this.$route.params.id}`
                axios.get(url).then(response => {
                    this.product = response.data
                    let product_rating = this.product[0].product_rating
                    this.totalRatings = Object.keys(product_rating).length
                    let averageRating = product_rating.reduce((sum, { rating }) => sum + rating, 0) / product_rating.length
                    if ( averageRating ) {
                        this.averageRating = Number(averageRating.toFixed(1))
                    } else {
                        this.averageRating = 0
                    }

                    if ( localStorage.getItem('vue-laravel-ecommerce.user') ) {
                        this.user = JSON.parse(localStorage.getItem('vue-laravel-ecommerce.user'))
                        let findReview = product_rating.find(o => o.user_id === this.user.id)
                        if ( findReview ) {
                            this.hasReview = true
                        }
                    }

                    this.ratingCounts.fiveStars.totalRatings = product_rating.filter(item => item.rating === 5).length
                    this.ratingCounts.fiveStars.averageOfTotal = Math.round((this.ratingCounts.fiveStars.totalRatings / this.totalRatings) * 100)
                    this.ratingCounts.fourStars.totalRatings = product_rating.filter(item => item.rating === 4).length
                    this.ratingCounts.fourStars.averageOfTotal = Math.round((this.ratingCounts.fourStars.totalRatings / this.totalRatings) * 100)
                    this.ratingCounts.threeStars.totalRatings = product_rating.filter(item => item.rating === 3).length
                    this.ratingCounts.threeStars.averageOfTotal = Math.round((this.ratingCounts.threeStars.totalRatings / this.totalRatings) * 100)
                    this.ratingCounts.twoStars.totalRatings = product_rating.filter(item => item.rating === 2).length
                    this.ratingCounts.twoStars.averageOfTotal = Math.round((this.ratingCounts.twoStars.totalRatings / this.totalRatings) * 100)
                    this.ratingCounts.oneStar.totalRatings = product_rating.filter(item => item.rating === 1).length
                    this.ratingCounts.oneStar.averageOfTotal = Math.round((this.ratingCounts.oneStar.totalRatings / this.totalRatings) * 100)
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    .small-text {
        font-size: 18px;
    }
    .title {
        font-size: 36px;
    }
    .productRating {
        line-height:22px;
        font-weight:bold;
    }
    .totalReviews {
        font-size: 20px;
        font-weight: normal;
    }
    .reviewDescription {
        word-wrap: break-word;
    }
    ::v-deep .value-5 > .p-progressbar-value {
        background:#4DA24C;
    }    
    ::v-deep .value-4 > .p-progressbar-value {
        background:#A4D730;
    }    
    ::v-deep .value-3 > .p-progressbar-value {
        background:#F6E437;
    }    
    ::v-deep .value-2 > .p-progressbar-value {
        background:#F4A41E;
    }   
    ::v-deep .value-1 > .p-progressbar-value {
        background:#EE3A0F;
    }
    ::v-deep .p-rating .p-rating-icon.pi-star, .p-rating:not(.p-disabled):not(.p-readonly) .p-rating-icon:hover  {
        color: #F7BD17 !important;
    }
    ::v-deep .p-inputtext {
        width:100%;
        resize:none;
    }
    ::v-deep .p-galleria-thumbnail-container {
        background: none;
    }
    ::v-deep .p-galleria-thumbnail-item {
        opacity:1;
        padding:1rem;
    }
    @media screen and (max-width: 560px) {
        ::v-deep .p-galleria-thumbnail-container {
            justify-content:center;
        }
        ::v-deep .p-galleria-thumbnail-next-icon, ::v-deep .p-galleria-thumbnail-prev-icon {
            color:black;
        }
        ::v-deep .p-dialog {
            width: 100vh!important;
            margin: 15px;
        }
    }
</style>
