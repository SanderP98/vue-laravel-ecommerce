<template>
    <div class="layout-content">
        <div v-for="(ratings, index) in products.product_ratings" :key="index">
            {{ratings}}
        </div>
        <DataView :value="products" :layout="layout" :paginator="true" :rows="9" :sortOrder="sortOrder" :sortField="sortField">
            <template #header>
                <div class="p-grid p-nogutter">
                    <div class="p-col-6" style="text-align: left">
                        <Dropdown v-model="sortKey" :options="sortOptions" optionLabel="label" placeholder="Sort By Price" @change="onSortChange($event)"/>
                    </div>
                    <div class="p-col-6" style="text-align: right">
                        <DataViewLayoutOptions v-model="layout" />
                    </div>
                </div>
            </template>
            <template #list="slotProps">
                <div class="p-col-12" v-if="slotProps.data.units !== 0">
                    <div class="product-list-item">
                        <img :src="'/images/' + slotProps.data.image" :alt="slotProps.data.name"/>
                        <div class="product-list-detail">
                            <div class="product-name">{{slotProps.data.name}}</div>
                            <div class="product-description">{{slotProps.data.description}}</div>
                            <Rating :value="slotProps.data.averageRating" :readonly="true" :cancel="false"></Rating>
                            <i class="pi pi-tag product-category-icon"></i><span class="product-category">{{slotProps.data.category}}</span>
                        </div>
                        <div class="product-list-action">
                            <span class="product-price">{{formatCurrency(slotProps.data.price)}}</span>
                            <Button icon="pi pi-shopping-cart" label="Add to Cart" :disabled="slotProps.data.units === '0'"></Button>
                            <span :class="'product-badge status-'+slotProps.data.units">{{slotProps.data.units}}</span>
                        </div>
                    </div>
                </div>
            </template>

            <template #grid="slotProps">
                <div class="p-col-12 p-md-4" v-if="slotProps.data.units !== 0">
                    <div class="product-grid-item card">
                        <div class="product-grid-item-content">
                            <img :src="'/images/' + slotProps.data.image" :alt="slotProps.data.name"/>
                            <div class="product-name">{{slotProps.data.name}}</div>
                            <div class="product-description">{{slotProps.data.description}}</div>
                            <Rating :value="slotProps.data.averageRating" :readonly="true" :cancel="false"></Rating>
                        </div>
                        <div class="product-grid-item-bottom">
                            <span class="product-price">{{formatCurrency(slotProps.data.price)}}</span>
                            <div class="row">
                                <router-link :to="{ path: '/products/'+slotProps.data.id }"><Button class="btn btn-primary mr-2">Buy now</Button></router-link>
                                <div v-if="!isLoggedIn">
                                    <router-link :to="{ path: 'login' }"><Button icon="pi pi-shopping-cart"></Button></router-link>                                   
                                </div>
                                <div v-if="isLoggedIn">
                                    <Button icon="pi pi-shopping-cart" :disabled="slotProps.data.units == '0'" @click="addToCart(slotProps.data)"></Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </DataView>
    </div>
</template>



<script>
    export default {
        data() {
            return {
                products : [],
                product: {},
                isLoggedIn : null,
                visibleLeft : false,
                layout: 'grid',
                sortKey: null,
                sortOrder: null,
                sortField: null,
                sortOptions: [
                    {label: 'Price High to Low', value: '!price'},
                    {label: 'Price Low to High', value: 'price'},
                ],
                responsiveOptions: [
				{
					breakpoint: '1024px',
					numVisible: 3,
					numScroll: 3
				},
				{
					breakpoint: '600px',
					numVisible: 2,
					numScroll: 2
				},
				{
					breakpoint: '480px',
					numVisible: 1,
					numScroll: 1
				}
			    ],
            }
        },
        mounted() {
            this.isLoggedIn = localStorage.getItem('vue-laravel-ecommerce.jwt') != null
            axios.get("api/products/").then(response => {
                let products = response.data.products
                products = products.map(product => {
                const totalRatings = product.product_rating.reduce((acc, { rating }) => acc += Number(rating), 0)
                const averageRating = totalRatings/product.product_rating.length
                return {...product, averageRating}
                })

                this.products = products.map(product => {
                const category = product.product_category.name
                return {...product, category}
                })
            });
        },
        methods : {
            formatCurrency(value) {
                return value.toLocaleString('nl-NL', {style: 'currency', currency: 'EUR'});
            },
            onSortChange(event){
                const value = event.value.value;
                const sortValue = event.value;

                if (value.indexOf('!') === 0) {
                    this.sortOrder = -1;
                    this.sortField = value.substring(1, value.length);
                    this.sortKey = sortValue;
                }
                else {
                    this.sortOrder = 1;
                    this.sortField = value;
                    this.sortKey = sortValue;
                }
            },
            addToCart(product) {
                product.quantity = 1;
                this.$parent.$emit('addToCart', product);
            }
        },
    }
</script>

<style lang="scss" scoped>
.product-item {
    .product-item-content {
        border: 1px solid var(--surface-d);
        border-radius: 3px;
        margin: .3rem;
        text-align: center;
        padding: 2rem 0;

    }

    .product-image {
        width: 200px;
        height: 150px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23)
    }
}
.p-dropdown {
    width: 14rem;
    font-weight: normal;
}
.product-name {
	font-size: 1.5rem;
	font-weight: 700;
}
.product-description {
	margin: 0 0 1rem 0;
}
.product-category-icon {
	vertical-align: middle;
	margin-right: .5rem;
}
.product-category {
	font-weight: 600;
	vertical-align: middle;
}
::v-deep .product-list-item {
	display: flex;
	align-items: center;
	padding: 1rem;
	width: 100%;
    padding: 2rem;
	img {
		width: 150px;
		box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
		margin-right: 2rem;
	}
	.product-list-detail {
		flex: 1 1 0;
	}
	.p-rating {
		margin: 0 0 .5rem 0;
	}
	.product-price {
		font-size: 1.5rem;
		font-weight: 600;
		margin-bottom: .5rem;
		align-self: flex-end;
	}
	.product-list-action {
		display: flex;
		flex-direction: column;
	}
	.p-button {
		margin-bottom: .5rem;
	}
}
::v-deep .product-grid-item {
    padding: 2rem;
	.product-grid-item-top,
	.product-grid-item-bottom {
		display: flex;
		align-items: center;
		justify-content: space-between;
	}
	img {
		width: 150px;
        height: 150px;
		box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
		margin: 2rem 0;
	}
	.product-grid-item-content {
		text-align: center;
	}
	.product-price {
		font-size: 1.5rem;
		font-weight: 600;
	}
}
@media screen and (max-width: 576px) {
	.product-list-item {
		flex-direction: column;
		align-items: center;
		img {
			width: 75%;
			margin: 2rem 0;
		}
		.product-list-detail {
			text-align: center;
		}
		.product-price {
			align-self: center;
		}
		.product-list-action {
			display: flex;
			flex-direction: column;
		}
		.product-list-action {
			margin-top: 2rem;
			flex-direction: row;
			justify-content: space-between;
			align-items: center;
			width: 100%;
		}
	}
}
</style>