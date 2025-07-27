<script setup lang="ts">
import VueEasyLightbox from 'vue-easy-lightbox'
import { GLOB_URIS, get_product_image_url } from '/@src/utils/global_uris';
import { IOfferProductCard } from '/@src/request/request-offer-request';
import { useI18n } from 'vue-i18n';
const emit = defineEmits<{
    (e: 'add-to-cart'): void
}>()
const props = defineProps({
    product: {
        type: Object as PropType<IOfferProductCard>,
        required: true
    },
})
const { t } = useI18n();

const visibleRef = ref(false)
const indexRef = ref(0) // default 0
const onHide = () => (visibleRef.value = false)
const productImages = computed(() => {
    let __product = { ...props.product };
    return __product?.images ? __product?.images.map((item) => get_product_image_url(item)) : [GLOB_URIS.NO_IMAGE]
})
const modalImageClick = () => {
    indexRef.value = 0
    visibleRef.value = true
}
const productImage = computed(() => {
    return props.product.image ? get_product_image_url(props.product.image) : GLOB_URIS.NO_IMAGE
})



onMounted(() => {
    $(() => {
        //@ts-ignore
        $('[data-toggle="tooltip"]').tooltip()
    })
})

</script>
<template>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="card-img-actions">
                    <VueEasyLightbox :visible="visibleRef" :imgs="productImages" :index="indexRef" @hide="onHide" />
                    <a href="" data-popup="lightbox" ref="lightbox" @click.prevent="modalImageClick()">
                        <img :src="productImage" class="card-img img-fluid" width="96" alt="">
                        <span class="card-img-actions-overlay card-img" @click.prevent="modalImageClick()">
                            <i class="icon-plus3 icon-2x"></i>
                        </span>
                    </a>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered text-center detail-table">
                            <tbody>
                                <tr>
                                    <!-- width -->
                                    <td><i class="fas fa-ruler-horizontal"
                                            :title="t('components.offer_request.elements.product.width')"
                                            data-toggle="tooltip"></i></td>
                                    <!-- height -->
                                    <td><i class="fas fa-ruler-vertical"
                                            :title="t('components.offer_request.elements.product.height')"
                                            data-toggle="tooltip"></i></td>
                                    <!-- length -->
                                    <td><i class="fas fa-ruler-combined"
                                            :title="t('components.offer_request.elements.product.length')"
                                            data-toggle="tooltip"></i></td>
                                    <!-- package -->
                                    <td><i class="fas fa-box"
                                            :title="t('components.offer_request.elements.product.package')"
                                            data-toggle="tooltip"></i></td>
                                    <!-- weight -->
                                    <td><i class="fas fa-weight"
                                            :title="t('components.offer_request.elements.product.weight')"
                                            data-toggle="tooltip"></i></td>
                                    <!-- volume -->
                                    <td><i class="fas fa-box-open"
                                            :title="t('components.offer_request.elements.product.volume')"
                                            data-toggle="tooltip"></i></td>
                                </tr>
                                <tr>
                                    <td>{{ product.width }}</td>
                                    <td>{{ product.height }}</td>
                                    <td>{{ product.length }}</td>
                                    <td>{{ product.package }}</td>
                                    <td>{{ product.weight }}</td>
                                    <td>{{ product.volume }} m<sup>3</sup></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="card-body text-center d-flex align-items-center justify-content-center flex-column">
                <div class="mb-2">
                    <h6 class="font-weight-semibold mb-0">
                        <a href="#" class="text-body">{{ product.name }}</a>
                    </h6>

                    <a href="#" class="text-muted">Model:{{ product.model }}</a>
                </div>


                <button type="button" class="btn btn-teal" @click.prevent="emit('add-to-cart')">
                    <i class="icon-cart-add mr-2"></i>
                    {{ t('actions.addToCart') }}
                </button>
            </div>
        </div>


    </div>
</template>

<style scoped>
.card img {
    height: 200px;
    object-fit: cover;
}

.detail-table {
    width: 100%;
}

.detail-table td {
    width: 16.66666666666667%;
    padding: 0.2rem;
    font-size: 0.8rem;
    text-wrap: nowrap;
}

.detail-table i {
    font-size: 20px;
    color: #007bff;
}

.detail-table i:hover {
    color: #0056b3;
}

/* disable number input arrows */
#formQuantity::-webkit-inner-spin-button,
#formQuantity::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

#formQuantity {
    -moz-appearance: textfield;
    -webkit-appearance: none;
}

#formQuantity {
    max-width: 100px;
    text-align: center;
}
</style>