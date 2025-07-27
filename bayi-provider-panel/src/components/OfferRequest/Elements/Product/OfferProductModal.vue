<script setup lang="ts">
import Dialog from 'primevue/dialog';
import { useI18n } from 'vue-i18n';
import { IOfferCartProduct, IOfferProductCard, IProductVariantColorData, IProductVariantDimensionData } from '/@src/request/request-offer-request';
import { useOfferRequest } from '/@src/stores/offerRequest';
import { useSwal } from '/@src/composables/useSwal';
const emit = defineEmits<{
    (e: 'close'): void
}>()
const props = defineProps({
    product_id: {
        type: String as PropType<string> | null,
        required: true
    },
})
const { t, locale } = useI18n();
const swal = useSwal()
const store = useOfferRequest()
const activeProduct = computed(() => store.productList.find((product) => product.id == props.product_id))
const dialogStatus = ref<boolean>(false);
export interface IOfferFormProductModalVariants {
    product_variant_id: string;
    variant_id: string | null;
    value_id: string;
}
const cartProductData = ref<IOfferCartProduct>({
    id: null,
    quantity: 1,
    product: null,
    note: '',
    color: null,
    dimension: null,
})
const dimensionForm = ref<IOfferFormProductModalVariants[]>([]);
const colorForm = ref<IOfferFormProductModalVariants[]>([]);
const generateDimensionAndColor = () => {
    const _currProduct = { ...activeProduct.value };
    if (!_currProduct) return;
    colorForm.value = []
    dimensionForm.value = []
    if (_currProduct.colors && _currProduct.colors.length > 0) {
        _currProduct.colors.forEach((color) => {
            colorForm.value.push({
                product_variant_id: color.id,
                variant_id: color.variant_id,
                value_id: ''
            })
        })
    }
    if (_currProduct.dimensions && _currProduct.dimensions.length > 0) {
        _currProduct.dimensions.forEach((dimension) => {
            dimensionForm.value.push({
                product_variant_id: dimension.id,
                variant_id: dimension.variant_id,
                value_id: ''
            })
        })
    }
}

const quantityChange = (event: Event) => {
    event.preventDefault();
    let max = 999;
    let min = 1;
    let target = event.target as HTMLInputElement;
    let value = parseInt(target.value);
    //nan
    if (isNaN(value)) {
        value = 1;
    } else if (value < min) {
        value = min;
    } else if (value > max) {
        value = max;
    }
    cartProductData.value.quantity = value;
    target.value = value.toString();
}

const validatedQuantity = computed(() => {
    // Arayüzde gösterilmek üzere, geçerli miktarı döndür
    return cartProductData.value.quantity;
});



const addProductToCart = async () => {
    const _currProduct = { ...activeProduct.value };
    if (!_currProduct) return;
    let _collectedColors: IProductVariantColorData[] = []
    if (colorForm.value.length > 0) {
        colorForm.value.forEach((item) => {
            if (item.value_id) {
                let _findedColor = _currProduct.colors?.find((color) => color.id == item.product_variant_id)
                let _values: IProductVariantColorData['value'] = []
                if (_findedColor) {
                    _findedColor.value.forEach((value) => {
                        if (value.id == item.value_id) {
                            _values.push(value)
                        }
                    })
                    _collectedColors.push({
                        ..._findedColor,
                        value: _values
                    })
                }
            }
        })
    }

    let _collectedDimensions: IProductVariantDimensionData[] = []
    if (dimensionForm.value.length > 0) {
        dimensionForm.value.forEach((item) => {
            if (item.value_id) {
                let _findedDimension = _currProduct.dimensions?.find((dimension) => dimension.id == item.product_variant_id)
                let _values: IProductVariantDimensionData['value'] = []
                if (_findedDimension) {
                    _findedDimension.value.forEach((value) => {
                        if (value.id == item.value_id) {
                            _values.push(value)
                        }
                    })
                    _collectedDimensions.push({
                        ..._findedDimension,
                        value: _values
                    })
                }
            }
        })
    }


    let __newData = {
        ...cartProductData.value,
        color: _collectedColors,
        dimension: _collectedDimensions
    }
    store.addProduct(__newData)
    emit('close')
    swal.fire({
        title: t('common.success'),
        text: t('components.cart.addedSuccessfully'),
        icon: 'success',
        confirmButtonText: t('common.ok'),
    })
}

const quantityClick = (type: 'up' | 'down') => {
    let min = 1;
    let max = 999;
    if (type == 'up') {
        if (cartProductData.value.quantity < max) {
            cartProductData.value.quantity += 1;
        } else {
            cartProductData.value.quantity = max;
        }
    } else {
        if (cartProductData.value.quantity > min) {
            cartProductData.value.quantity -= 1;
        } else {
            cartProductData.value.quantity = min;
        }
    }

    let target = document.getElementById('formQuantity') as HTMLInputElement;
    target.value = cartProductData.value.quantity.toString();
}
const handleColorChange = (e: Event, product_variant_id: string, _variant_id: string) => {
    e.preventDefault();
    e.stopPropagation();
    const target = e.target as HTMLSelectElement;
    const _val = target.value;
    //if has in formdata.colors
    const findIndex = colorForm.value.findIndex((color) => color.product_variant_id === product_variant_id && color.variant_id === _variant_id);
    if (findIndex !== -1) {
        colorForm.value[findIndex].value_id = _val;
    }
}

const handleDimensionChange = (e: Event, product_variant_id: string, _variant_id: string) => {
    e.preventDefault();
    e.stopPropagation();
    const target = e.target as HTMLSelectElement;
    const _val = target.value;
    //if has in formdata.colors
    const findIndex = dimensionForm.value.findIndex((dimension) => dimension.product_variant_id === product_variant_id && dimension.variant_id === _variant_id);
    if (findIndex !== -1) {
        dimensionForm.value[findIndex].value_id = _val;
    }
}

watchEffect(() => {
    if (activeProduct.value) {
        const clonedProduct = JSON.parse(JSON.stringify(activeProduct.value))
        cartProductData.value.product = clonedProduct;
        cartProductData.value.id = props.product_id;
        cartProductData.value.quantity = 1;
        cartProductData.value.note = '';
        cartProductData.value.color = null;
        cartProductData.value.dimension = null;
        generateDimensionAndColor()
    }
    if (props.product_id) {
        dialogStatus.value = true;
    } else {
        dialogStatus.value = false;
    }
})


</script>
<template>
    <div v-if="activeProduct">
        <Dialog :visible="dialogStatus" modal :header="t('common.productDetail')" :style="{ width: '25rem' }"
            @update:visible="val => dialogStatus = val" @hide="emit('close')">
            <div class="row my-2">
                <div class="col-md-12">
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                        <span class="input-group-btn input-group-prepend">
                            <button class="btn btn-light bootstrap-touchspin-down" type="button"
                                :disabled="cartProductData.quantity <= 1" @click="quantityClick('down')">-</button>

                        </span><input type="text" inputmode="numeric" @change="quantityChange" min="1" max="999"
                            id="formQuantity" :value="validatedQuantity" class="form-control touchspin-empty">
                        <span class="input-group-btn input-group-append">
                            <button class="btn btn-light bootstrap-touchspin-up" type="button"
                                @click="quantityClick('up')">+</button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ t('common.productNote') }}</label>
                        <textarea class="form-control" v-model="cartProductData.note"></textarea>
                    </div>
                </div>
                <div class="col-12" v-if="activeProduct.colors && activeProduct.colors.length > 0">
                    <!-- label -->
                    <div class="card"
                        v-if="activeProduct.colors && activeProduct.colors.length > 0 && colorForm.length > 0">
                        <div class="card-header">
                            <h5 class="card-title">Renk</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <!-- renk -->
                                <div class="col-12 col-md-6" v-for="(color, index) in activeProduct.colors">
                                    <div class="form-group" v-if="color">
                                        <label :for="'color_' + color.id">{{ color.name }}</label>
                                        <select class="form-control" :id="'color_' + color.id"
                                            @change="handleColorChange($event, color.id, color.variant_id)">
                                            <option value="">{{ t('actions.select') }}</option>
                                            <option v-for="(color_val, index) in color.value" :key="index"
                                                :value="color_val.id">
                                                {{ color_val.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- dimension -->
                    <div class="card"
                        v-if="activeProduct.dimensions && activeProduct.dimensions.length > 0 && dimensionForm.length > 0">
                        <div class="card-header header-elements-sm-inline py-sm-">
                            <h5 class="card-title">Ölçü</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6" v-for="(dimension, index) in activeProduct.dimensions">
                                    <div class="form-group" v-if="dimension">
                                        <label :for="`dim_${dimension.id}`">{{ dimension.name }}</label>
                                        <select :id="`dim_${dimension.id}`" class="form-control"
                                            @change="handleDimensionChange($event, dimension.id, dimension.variant_id)">
                                            <option value="">{{ t('actions.select') }}</option>
                                            <option v-for="(_dimension, index) in dimension.value" :key="index"
                                                :value="_dimension.id">
                                                {{ t('components.offer_request.elements.product.length') }}:
                                                {{ _dimension.value.length }}cm x
                                                {{ t('components.offer_request.elements.product.width') }}:
                                                {{ _dimension.value.width }}cm x
                                                {{ t('components.offer_request.elements.product.height') }}:
                                                {{ _dimension.value.height }}cm
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" @click="emit('close')">{{
                    t('actions.close') }}</button>
                <button type="button" class="btn btn-primary" @click="addProductToCart">{{
                    t('actions.addToCart') }}</button>
            </div>
        </Dialog>
    </div>

</template>


<style scoped></style>