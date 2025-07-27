<script setup lang="ts">
import { IOfferFormProductModalVariants } from '/@src/components/Offer/Form/ProductCard/OfferFormProductModal.vue';
import { useSwal } from '/@src/composables/useSwal';
import { IVariantListItem } from '/@src/request/request-variant';
import { ICustomerOrderFormLine, ICustomerOrderFormProduct } from '/@src/shared/form/interface-customer-order';
import { useCustomerOrderFormStore } from '/@src/stores/customerOrderFormStore';
import { get_product_image_url } from '/@src/utils/GLOB_URIS';
import { getCurrencySymbol } from '/@src/utils/currency_helper';
import { addOrderFormLine } from '/@src/utils/form/customer_order_form';
const store = useCustomerOrderFormStore();
export interface IPropsOrderFormProductModal {
    productIndex: number | null;
    active: boolean;
    productData: ICustomerOrderFormProduct[];
    colorList: IVariantListItem[];
    dimensionList: IVariantListItem[];
}
const emit = defineEmits<{
    (e: 'close'): void;
}>();
const props = defineProps<IPropsOrderFormProductModal>();
const swal = useSwal();
const dimensionForm = ref<IOfferFormProductModalVariants[]>([]);
const colorForm = ref<IOfferFormProductModalVariants[]>([]);
const formData = ref<ICustomerOrderFormLine>({ ...addOrderFormLine() });

const resetFormData = () => {
    formData.value = { ...addOrderFormLine() };
    resetDimensionAndColor();
}

const resetDimensionAndColor = () => {
    colorForm.value = [];
    dimensionForm.value = [];
}
const submitBtn = ref<HTMLButtonElement | null>(null);
const clickSubmit = () => {
    if (submitBtn.value) {
        submitBtn.value.click();
    }
}
const activeProduct = computed(() => {
    return props.productData.find(
        (product) => product.id === formData.value.product_id
    );
});

const addProduct = () => {
    if (!formData.value.product_id || formData.value.product_id === 0) {
        swal.fire({
            title: "Ürün Seçiniz",
            text: "Lütfen bir ürün seçiniz.",
            icon: "warning",
            confirmButtonText: "Tamam",
        });
        return;
    }

    formData.value.color = colorForm.value;
    formData.value.dimension = dimensionForm.value;

    if (props.productIndex !== null) {
        store.updateLine(props.productIndex, formData.value);
    } else {
        store.addLine(formData.value);
    }

    emit('close');
}

//#region computed
const computedImage = computed(() => {
    let img = '/images/no-image.png';
    if (formData.value.product_id) {
        const findProduct = props.productData.find(
            (product) => product.id === formData.value.product_id

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
    let withoutTax = formData.value.quantity * formData.value.unit_price;
    //discount
    withoutTax -= (withoutTax * formData.value.unit_discount_rate) / 100;
    //tax
    let tax = withoutTax * (formData.value.tax_rate / 100);
    return (withoutTax + tax).toFixed(2);
});

const basePrice = computed(() => {
    let withoutTax = formData.value.quantity * formData.value.unit_price;
    return withoutTax.toFixed(2);
});

const withDiscount = computed(() => {
    let withoutTax = formData.value.quantity * formData.value.unit_price;
    let discount = (withoutTax * formData.value.unit_discount_rate) / 100;
    return (withoutTax - discount).toFixed(2);
});

const totalDiscount = computed(() => {
    let withoutTax = formData.value.quantity * formData.value.unit_price;
    let discount = (withoutTax * formData.value.unit_discount_rate) / 100;
    return discount.toFixed(2);
});

const totalTax = computed(() => {
    let withoutTax = formData.value.quantity * formData.value.unit_price;
    /* add discount */
    withoutTax -= (withoutTax * formData.value.unit_discount_rate) / 100;

    let tax = withoutTax * (formData.value.tax_rate / 100);
    return tax.toFixed(2);
});
//#endregion computed



watch(() => formData.value.product_id, (value) => {
    colorForm.value = [];
    dimensionForm.value = [];
    if (value === 0) {
        resetFormData();
    } else {
        const findProduct = props.productData.find((product) => product.id === value);
        if (findProduct) {
            resetDimensionAndColor();
            formData.value.product_id = findProduct.id;
            formData.value.unit_price = findProduct.price;
            formData.value.quantity = 1;
            formData.value.note = findProduct.note;
            formData.value.unit_package = findProduct.package;
            formData.value.unit_volume = findProduct.volume;

            let _selectedCurrency = store.orderForm.detail.currency;
            switch (_selectedCurrency) {
                case "euro":
                    _selectedCurrency = "euro";
                    break;
                case "usd":
                    _selectedCurrency = "usd";
                    break;
                case "tl":
                    _selectedCurrency = "tl";
                    break;
                case "gbp":
                    _selectedCurrency = "gbp";
                    break;
                default:
                    _selectedCurrency = "tl";
                    break;
            }
            findProduct.price = findProduct[`price_${_selectedCurrency}`];
            formData.value.unit_price = findProduct.price;


            if (props.productIndex !== null) {
                if (store.orderForm.lines[props.productIndex]) {
                    if (store.orderForm.lines[props.productIndex].product_id === value) {
                        formData.value = { ...store.orderForm.lines[props.productIndex] }

                        formData.value.color?.forEach((color) => {
                            colorForm.value.push({
                                product_variant_id: color.product_variant_id,
                                variant_id: color.variant_id,
                                value_id: color.value_id
                            });
                        });
                        formData.value.dimension?.forEach((dimension) => {
                            dimensionForm.value.push({
                                product_variant_id: dimension.product_variant_id,
                                variant_id: dimension.variant_id,
                                value_id: dimension.value_id
                            });
                        });
                    } else {
                        createVariantData(findProduct);
                    }
                }
            } else {
                createVariantData(findProduct);
            }



        } else {
            swal.fire({
                title: "Ürün Bulunamadı",
                text: "Ürün bulunamadı. Lütfen başka bir ürün seçiniz.",
                icon: "warning",
                confirmButtonText: "Tamam",
            });
        }
    }
}, { immediate: true });

const createVariantData = (findProduct: ICustomerOrderFormProduct | undefined) => {
    if (!findProduct) return;
    resetDimensionAndColor();
    findProduct.colors.forEach((color) => {
        colorForm.value.push({
            product_variant_id: color.id,
            variant_id: color.variant_id,
            value_id: ''
        });
    });

    findProduct.dimensions.forEach((dimension) => {
        dimensionForm.value.push({
            product_variant_id: dimension.id,
            variant_id: dimension.variant_id,
            value_id: ''
        });
    });
}

const activeColors = computed(() => {
    //in colorlist and active product
    if (activeProduct.value) {
        //return props.colorList.filter((color) => color.id === activeProduct.value?.colors.find((_color) => _color.variant_id === color.id)?.variant_id);
        return activeProduct.value.colors.map((color) => {
            let find = props.colorList.find((color_val) => color_val.id === color.variant_id);
            if (find) {
                return {
                    ...color,
                    name: find.name,
                    //find in color.values[]
                    values: find.values?.map((value) => {
                        if (color.value.find((val) => val.root_variant_value_id === value.id)) {
                            return {
                                ...value,
                            }
                        }
                    }).filter((value) => value !== null && value !== undefined)
                }
            } else {
                return null;
            }

        }).filter((color) => color !== null && color !== undefined);
    }
    return [];
})

const activeDimensions = computed(() => {
    //in colorlist and active product
    if (activeProduct.value) {
        return activeProduct.value.dimensions.map((dimension) => {
            let find = props.dimensionList.find((dimension_val) => dimension_val.id === dimension.variant_id);
            if (find) {
                return {
                    ...dimension,
                    name: find.name,
                    values: find.values
                }
            } else {
                return null;
            }

        }).filter((dimension) => dimension !== null && dimension !== undefined);
    }
    return [];
})

const handleColorChange = (e: Event, product_variant_id: string, _variant_id: string) => {
    e.preventDefault();
    e.stopPropagation();
    const target = e.target as HTMLSelectElement;
    const _val = target.value;
    //if has in formdata.colors
    const findIndex = colorForm.value.findIndex((color) => color.product_variant_id === product_variant_id && color.variant_id === _variant_id);
    if (findIndex !== -1) {
        colorForm.value[findIndex].value_id = _val;
    } else {
        colorForm.value.push({
            product_variant_id: product_variant_id,
            variant_id: _variant_id,
            value_id: _val
        });
    }
    formData.value.color = colorForm.value;
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
    } else {
        dimensionForm.value.push({
            product_variant_id: product_variant_id,
            variant_id: _variant_id,
            value_id: _val
        });
    }
    formData.value.dimension = dimensionForm.value;
}
watch(() => props.active, (value) => {
    if (value) {
        resetFormData();
        if (props.productIndex !== null) {
            if (store.orderForm.lines[props.productIndex]) {
                formData.value = { ...store.orderForm.lines[props.productIndex] }
                formData.value.color?.forEach((color) => {
                    colorForm.value.push({
                        product_variant_id: color.product_variant_id,
                        variant_id: color.variant_id,
                        value_id: color.value_id
                    });
                });
                formData.value.dimension?.forEach((dimension) => {
                    dimensionForm.value.push({
                        product_variant_id: dimension.product_variant_id,
                        variant_id: dimension.variant_id,
                        value_id: dimension.value_id
                    });
                });
            }
        }
    }
})

const handleClose = () => {
    swal.fire({
        title: "Kaydetmeden Çık",
        text: "Değişiklikler kaydedilmeden çıkılsın mı?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Evet",
        cancelButtonText: "Vazgeç",
    }).then((result) => {
        if (result.isConfirmed) {
            emit('close');
        }
    });
}

</script>
<template>
    <VModal :show="active" title="Ürün Kaydet" size="md" @close="handleClose" :outside-click="false">
        <div>
            <div class="row">
                <!-- Product Image And Select -->
                <div class="col-12">
                    <!-- image -->
                    <div class="text-center p-3 thumbnail-container">
                        <img :src="computedImage" alt="Product Image" class="thumbnail" />
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12">
                    <form class="" @submit.prevent="addProduct">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="product_id">Ürün</label>
                                    <VInputProduct :prop-val="formData.product_id"
                                        @option-selected="formData.product_id = $event"
                                        @selected-options="formData.product_id = $event" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="unit_price">Birim Fiyat</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                {{ selectedCurrency }}
                                            </span>
                                        </div>
                                        <input type="number" v-model="formData.unit_price" class="form-control" min="1"
                                            required id="unit_price" placeholder="Birim Fiyat">
                                    </div>
                                </div>

                            </div>


                            <div class="col-6">
                                <!-- miktar -->
                                <div class="form-group">
                                    <label for="quantity">Miktar</label>
                                    <input type="number" v-model="formData.quantity" class="form-control" id="quantity"
                                        min="1" placeholder="Miktar">
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="tax_rate">KDV Oranı</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <input type="number" v-model="formData.tax_rate" class="form-control"
                                            placeholder="Yüzde" min="0" max="100" />
                                    </div>

                                    <small class="form-text text-muted">
                                        Vergi tutarı:{{ selectedCurrency }} {{ totalTax }}
                                    </small>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="tax_rate">İndirim Oranı</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <input type="number" v-model="formData.unit_discount_rate" class="form-control"
                                            placeholder="Yüzde" min="0" max="100" />
                                    </div>
                                    <small class="form-text text-muted">İndirim tutarı:
                                        {{ selectedCurrency }} {{ totalDiscount }}
                                    </small>
                                    <small class="form-text text-muted">İndirimli tutar:
                                        {{ selectedCurrency }} {{ withDiscount }}
                                    </small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="total">Toplam</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                {{ selectedCurrency }}
                                            </span>
                                        </div>
                                        <input type="text" :value="totalPrice" class="form-control" readonly disabled />
                                    </div>
                                </div>

                            </div>

                            <div class="col-6">
                                <!-- paket -->
                                <div class="form-group">
                                    <label for="unit_package">Paket</label>
                                    <input type="number" v-model="formData.unit_package" class="form-control"
                                        id="unit_package" placeholder="Paket">
                                </div>
                            </div>
                            <div class="col-6">
                                <!-- hacim -->
                                <div class="form-group">
                                    <label for="unit_volume">Hacim</label>
                                    <input type="number" v-model="formData.unit_volume" class="form-control" step="0.01"
                                        id="unit_volume" placeholder="Hacim">
                                </div>
                            </div>

                            <div class="col-12" v-if="activeProduct">
                                <!-- label -->
                                <div class="card" v-if="activeColors.length > 0">
                                    <div class="card-header">
                                        <h5 class="card-title">Renk</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">

                                            <!-- renk -->
                                            <div class="col-12 col-md-6" v-for="(color, index) in activeColors">
                                                <div class="form-group" v-if="color">
                                                    <label :for="`col_${color.id}`">{{ color.name }}</label>
                                                    <select class="form-control" :id="'color_' + color.id"
                                                        :value="colorForm.find((_color) => _color.variant_id === color.variant_id)?.value_id"
                                                        @change="handleColorChange($event, color.id, color.variant_id)">
                                                        <option value="">Seçiniz</option>
                                                        <option v-for="(color_val, index) in color?.values" :key="index"
                                                            :value="color_val?.id">
                                                            {{ color_val?.name }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- dimension -->
                                <div class="card" v-if="activeDimensions.length > 0">
                                    <div class="card-header header-elements-sm-inline py-sm-">
                                        <h5 class="card-title">Ölçü</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-md-6" v-for="(dimension, index) in activeDimensions">
                                                <div class="form-group" v-if="dimension">
                                                    <label :for="`dim_${dimension.id}`">{{ dimension.name }}</label>
                                                    <select
                                                        :value="dimensionForm.find((_dimension) => _dimension.variant_id === dimension.variant_id)?.value_id"
                                                        @change="handleDimensionChange($event, dimension.id, dimension.variant_id)"
                                                        :id="'dim_' + dimension.id" class="form-control">
                                                        <option value="">Seçiniz</option>
                                                        <option v-for="(_dimension, index) in dimension?.value"
                                                            :key="index" :value="_dimension.id">
                                                            <span v-if="_dimension.value.length > 0">
                                                                U: {{ _dimension.value.length }}cm
                                                            </span>
                                                            <span v-if="_dimension.value.width > 0">
                                                                G: {{ _dimension.value.width }}cm
                                                            </span>
                                                            <span v-if="_dimension.value.height > 0">
                                                                Y: {{ _dimension.value.height }}cm
                                                            </span>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>



                            <div class="col-12">
                                <!-- not -->
                                <div class="form-group">
                                    <label for="note">Not</label>
                                    <textarea v-model="formData.note" class="form-control" id="note"
                                        placeholder="Not"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="d-none" ref="submitBtn">Submit</button>
                    </form>
                </div>

            </div>
        </div>
        <template #footer>
            <button type="button" class="btn btn-bayi-red" @click.prevent="clickSubmit">Kaydet</button>
        </template>
    </VModal>
</template>
<style scoped>
.thumbnail {
    max-width: 100%;
    max-height: 200px;
    border-radius: 5px;
    border: 1px solid #e0e0e0;
    object-fit: contain;

}

.thumbnail-container {
    background-color: #f8f9fa;
    border-radius: 5px;
    border: 1px solid #e0e0e0;
}
</style>