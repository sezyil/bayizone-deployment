<script setup lang="ts">
const emit = defineEmits<{
    (e: 'detail'): void;
    (e: 'delete'): void;
}>();

import { IVariantListItem } from "/@src/request/request-variant";
import { ICustomerOrderFormLine, ICustomerOrderFormLineErrors, ICustomerOrderFormProduct } from "/@src/shared/form/interface-customer-order";
import { useCustomerOrderFormStore } from "/@src/stores/customerOrderFormStore";
import { GLOB_URIS, get_product_image_url } from "/@src/utils/GLOB_URIS";
import { getCurrencySymbol } from "/@src/utils/currency_helper";
const store = useCustomerOrderFormStore();

const props = defineProps({
    lineData: {
        type: Object as PropType<ICustomerOrderFormLine>,
        required: true,
    },
    errors: {
        type: Object as PropType<ICustomerOrderFormLineErrors>,
        required: true,
    },
    productData: {
        type: Array as PropType<ICustomerOrderFormProduct[]>,
        required: true,
    },
    colorList: {
        type: Array as PropType<IVariantListItem[]>,
        required: true,
    },
    dimensionList: {
        type: Array as PropType<IVariantListItem[]>,
        required: true,
    }
});

const activeProduct = computed(() => {
    return props.productData.find(
        (product) => product.id === props.lineData.product_id
    );
});

const computedImage = computed(() => {
    let img = GLOB_URIS.NO_IMAGE;
    if (activeProduct.value?.image) {
        const findProduct = props.productData.find(
            (product) => product.id === props.lineData.product_id
        );
        if (findProduct?.image) {
            img = get_product_image_url(findProduct.image);
        }
    }
    return img;
});

const selectedCurrency = computed(() => {
    return getCurrencySymbol(store.orderForm.detail.currency);
});

const totalPrice = computed(() => {
    let withoutTax = props.lineData.quantity * props.lineData.unit_price;
    //discount
    withoutTax -= (withoutTax * props.lineData.unit_discount_rate) / 100;
    //tax
    let tax = withoutTax * (props.lineData.tax_rate / 100);
    return (withoutTax + tax).toFixed(2);
});

const basePrice = computed(() => {
    let withoutTax = props.lineData.quantity * props.lineData.unit_price;
    return withoutTax.toFixed(2);
});

const withDiscount = computed(() => {
    let withoutTax = props.lineData.quantity * props.lineData.unit_price;
    let discount = (withoutTax * props.lineData.unit_discount_rate) / 100;
    return (withoutTax - discount).toFixed(2);
});

const totalDiscount = computed(() => {
    let withoutTax = props.lineData.quantity * props.lineData.unit_price;
    let discount = (withoutTax * props.lineData.unit_discount_rate) / 100;
    return discount.toFixed(2);
});

const totalTax = computed(() => {
    let withoutTax = props.lineData.quantity * props.lineData.unit_price;
    /* add discount */
    withoutTax -= (withoutTax * props.lineData.unit_discount_rate) / 100;

    let tax = withoutTax * (props.lineData.tax_rate / 100);
    return tax.toFixed(2);
});

//total volume
const totalVolume = computed(() => {
    return (props.lineData.unit_volume * props.lineData.quantity).toFixed(2);
});

//total package
const totalPackage = computed(() => {
    return (props.lineData.unit_package * props.lineData.quantity).toFixed(2);
});

const isInternational = computed(() => {
    return store.orderForm.detail.is_international;
});

const productColors = computed(() => {
    if (!props.lineData || !props.lineData.color) return [];
    let _colors: {
        name: string;
        value: string;
    }[] = [];
    props.lineData.color?.forEach((color) => {
        let findColor = props.colorList.find((item) => item.id === color.variant_id);
        if (findColor) {
            findColor.values?.forEach((value) => {
                if (value.id === color.value_id) {
                    _colors.push({
                        name: findColor.name,
                        value: value.name,
                    });
                }
            });
        }
    });
    return _colors;
});

const productDimensions = computed(() => {
    if (!props.lineData || !props.lineData.dimension) return [];
    let _dimensions: {
        name: string;
        length: number;
        width: number;
        height: number;
    }[] = [];
    props.lineData.dimension?.forEach((dimension) => {
        let findDimension = props.dimensionList.find(
            (item) => item.id === dimension.variant_id
        );
        let _productData = activeProduct.value?.dimensions;

        if (findDimension) {
            _productData?.forEach((item) => {
                if (item.variant_id === dimension.variant_id) {
                    item.value?.forEach((value) => {
                        if (value.id === dimension.value_id) {
                            let { length, width, height } = value.value;
                            _dimensions.push({
                                name: findDimension.name,
                                length,
                                width,
                                height,
                            });
                        }
                    });
                }
            });
        }
    });
    return _dimensions;
});

</script>
<template>
    <div class="card">
        <div class="card-img-actions mb-2 pb-1">
            <img class="img-fluid thumbnail-image" :src="computedImage" alt="">
            <div class="card-img-actions-overlay">
                <button class="btn btn-outline-white border-2 ml-2" @click.prevent="emit('detail')">
                    Düzenle
                </button>
            </div>
        </div>

        <div class="card-body pt-1 mt-1">
            <h6 class="card-title font-weight-semibold text-center">{{ activeProduct?.name }} / {{ activeProduct?.model
                }}</h6>
            <ul class="list-unstyled mb-0 product-card-list">
                <li>
                    <span class="font-weight-semibold">Adet: </span>
                    <span>{{ props.lineData.quantity }}</span>
                </li>
                <li>
                    <span class="font-weight-semibold">Birim Fiyat: </span>
                    <span>{{ selectedCurrency }}{{ props.lineData.unit_price }}</span>
                </li>
                <li>
                    <span class="font-weight-semibold">Toplam İndirim: </span>
                    <span>{{ selectedCurrency }}{{ totalDiscount }}</span>
                </li>
                <li>
                    <span class="font-weight-semibold">Toplam Vergi: </span>
                    <span>{{ selectedCurrency }}{{ totalTax }}</span>
                </li>
                <li>
                    <span class="font-weight-semibold">Toplam Fiyat: </span>
                    <span>{{ selectedCurrency }}{{ totalPrice }}</span>
                </li>
                <!-- not -->
                <li>
                    <span class="font-weight-semibold">Not: </span>
                    <span v-if="props.lineData.note">{{ props.lineData.note }}</span>
                    <span v-else>---</span>
                </li>
                <!-- renk -->
                <li>
                    <span class="font-weight-semibold">Renk: </span>
                    <span v-if="props.lineData.color">
                        <span v-for="(color, index) in productColors" :key="index" class="d-block">
                            {{ color.name }}: {{ color.value }}
                            {{ index !== productColors.length - 1 ? ', ' : '' }}
                        </span>
                    </span>
                    <span v-else>---</span>
                </li>
                <!-- dimension -->
                <li>
                    <span class="font-weight-semibold">Ölçü: </span>
                    <div v-if="props.lineData.dimension">
                        <span v-for="(dimension, index) in productDimensions" :key="index" class="d-block">
                            {{ dimension.name }}: {{ dimension.length }}cm - G: {{ dimension.width }}cm - Y: {{
                                dimension.height }}cm

                        </span>

                    </div>
                    <span v-else>---</span>
                </li>
            </ul>
        </div>
        <div class="ribbon-container">
            <div class="ribbon bg-indigo text-white !small">
                {{ totalVolume }} m<sup>3</sup>
            </div>
        </div>
        <div class="ribbon-container-left">
            <div class="ribbon bg-success text-white !small">
                {{ totalPackage }} paket
            </div>
        </div>

        <div class="card-footer  d-flex justify-content-between ">
            <div class="d-flex align-items-center">
                <a href="#" class="text-danger" @click.prevent="emit('delete')">
                    <i class="fa fa-trash "></i>
                    Sil
                </a>
            </div>
            <div class="d-flex align-items-center">
                <a href="#" class="text-warning" @click.prevent="emit('detail')">
                    <i class="fa fa-edit "></i>
                    Düzenle
                </a>
            </div>
        </div>
    </div>
</template>


<style scoped>
.thumbnail-image {
    position: relative;
    overflow: hidden;
    border-radius: 0.25rem;
    box-shadow: 0 0 0 1px rgba(68, 68, 68, 0.1);
    object-position: center;
    object-fit: cover;
    width: 100%;
    height: 200px;
}

.product-card-list li {

    padding: 5px 0;
}

.ribbon-container-left {
    width: 6.5rem;
    height: 6.625rem;
    overflow: hidden;
    position: absolute;
    left: -1px;
    top: -1px;
    /* rotate */
    transform: rotate(-90deg);
}

.ribbon {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>