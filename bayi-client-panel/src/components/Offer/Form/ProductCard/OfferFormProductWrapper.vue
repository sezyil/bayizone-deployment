<script setup lang="ts">
import { useSwal } from "/@src/composables/useSwal";
import { list } from "/@src/request/request-product";
import VariantsApi, { IVariantListItem } from "/@src/request/request-variant";
import { IOfferFormLine, IOfferFormProduct } from "/@src/shared/form/interface-offer";
import { IProductList } from "/@src/shared/product/interface-product";
import { OfferRequestLine } from "/@src/shared/response/interface-offer-request-response";
import { useOfferFormStore } from "/@src/stores/offerFormStore";
import { catchFieldError } from "/@src/utils/api/catchFormErrors";
import { getCurrencySymbol } from "/@src/utils/currency_helper";
import { addOfferFormLine } from '/@src/utils/form/offer_form';
const swal = useSwal();
const store = useOfferFormStore();
const productData = ref<IOfferFormProduct[]>([]);
const variantApi = new VariantsApi();
const props = defineProps({
    requestProducts: {
        type: Array as PropType<OfferRequestLine[]>,
        required: false
    },
    requestCurrency: {
        type: String,
        required: false
    }
})
const getProductData = async () => {
    try {
        const response = await list(undefined, {
            withOptions: true,
        });
        let x_data = response.data.data as IProductList[];
        productData.value = x_data.map((product) => {
            return {
                ...product,
                price: 0,
                note: "",
            };
        });
    } catch (error) {
        catchFieldError(error);
    }
};
const productModalCard = ref<{
    active: boolean,
    productIndex: number | null
}>({
    active: false,
    productIndex: null
});

const offerLines = computed(() => {
    return store.offerForm.lines;
})


const addLine = () => {
    //check has empty line
    const emptyLine = store.offerForm.lines.find(line => line.product_id == 0)
    if (emptyLine) {
        //lütfen yeni bir satır açmadan önce mevcut satırı doldurunuz.
        swal.fire({
            title: "Satır Boş",
            text: "Lütfen yeni bir satır açmadan önce mevcut satırı doldurunuz.",
            icon: "warning",
            confirmButtonText: "Tamam",
        });
        return;
    }
    handleTriggerDetail(null);
}

const selectOfferRequestProducts = async () => {
    if (props.requestProducts) {
        let i = 0;
        props.requestProducts.forEach((line) => {
            const findProduct = productData.value.find((product) => product.id === line.product_id);
            if (findProduct) {
                let _formData: IOfferFormLine = ({ ...addOfferFormLine() });
                _formData.product_id = findProduct.id
                _formData.unit_price = findProduct.price
                _formData.quantity = line.quantity;
                _formData.note = line.note;
                _formData.unit_package = findProduct.package
                _formData.unit_volume = findProduct.volume

                if (line.color) {
                    _formData.color = line.color.map((color) => {
                        return {
                            product_variant_id: color.product_variant_id,
                            value_id: color.variant_value_id,
                            variant_id: color.variant_id,
                        }
                    });
                }
                if (line.dimension) {
                    _formData.dimension = line.dimension.map((dimension) => {
                        return {
                            product_variant_id: dimension.product_variant_id,
                            value_id: dimension.variant_value_id,
                            variant_id: dimension.variant_id,
                        }
                    });
                }
                if (props.requestCurrency) {
                    let _selectedCurrency = props.requestCurrency;
                    switch (_selectedCurrency) {
                        case "0":
                            _selectedCurrency = "tl";
                            break;
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
                    //@ts-ignore
                    findProduct.price = findProduct[`price_${_selectedCurrency}`];
                    _formData.unit_price = findProduct.price;
                }
                store.addLine(_formData);
            }
            i++;
        })
    }
}

const handleTriggerDetail = (index: number | null) => {
    productModalCard.value = {
        active: true,
        productIndex: index
    }
}

const closeModal = () => {
    productModalCard.value = {
        active: false,
        productIndex: 0
    }
}

const variantList = ref<IVariantListItem[]>([]);
const getVariants = async () => {
    try {
        const { data } = await variantApi.listWithValues();
        variantList.value = data.data
    } catch (error) {
        catchFieldError(error);
    }
}

const dimensionVariants = computed(() => {
    return variantList.value.filter(variant => variant.type === 'DIMENSION');
})

const colorVariants = computed(() => {
    return variantList.value.filter(variant => variant.type === 'COLOR');
})

onMounted(async () => {
    await getVariants();
    await getProductData();
    await selectOfferRequestProducts();
});
</script>
<template>
    <div class="card">
        <div class="card-header bg-transparent header-elements-sm-inline py-sm-0">
            <h6 class="card-title py-sm-3">Ürünler</h6>
            <div class="header-elements">
                <button type="button" class="btn btn-secondary" @click="addLine">Ekle</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row" v-if="offerLines.length > 0">
                <div class="col-12 col-md-4 col-xl-3" v-for="(line, index) in offerLines" :key="index">
                    <OfferFormProductCard :dataIndex="index" :errors="store.formErrors.lines[index] ?? []"
                        :color-list="colorVariants" :dimension-list="dimensionVariants" :productData="productData"
                        :lineData="line" @detail="handleTriggerDetail(index)" @delete="store.deleteLine(index)" />

                </div>
                <!-- blank card to add -->
                <div class="col-12 col-md-4 col-xl-3">
                    <div class="card h-100 border-2 border-dashed border-secondary justify-content-center align-items-center cursor-pointer"
                        @click.prevent="addLine">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                            <i class="fa fa-plus fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-warning" v-else>
                <i class="fa fa-exclamation-triangle"></i>
                Lütfen ürün ekleyiniz.
            </div>
        </div>
        <div class="card-footer" v-if="offerLines.length > 0">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 ml-auto rounded p-1">
                    <OfferFormSummary />
                </div>
            </div>
        </div>

        <OfferFormProductModal :productIndex="productModalCard.productIndex" :active="productModalCard.active"
            :color-list="colorVariants" :dimension-list="dimensionVariants" :product-data="productData"
            @close="closeModal" />

    </div>
</template>


<style scoped></style>