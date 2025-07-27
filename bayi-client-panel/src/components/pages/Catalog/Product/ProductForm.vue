<script setup lang="ts">
import { add, get, update } from "/@src/request/request-product";
import { useCatalogProductStore } from "/@src/stores/catalog/product";
import { useViewWrapper } from "/@src/stores/viewWrapper";
import TProductGeneral from "/@src/components/Product/Tabs/TProductGeneral.vue"
import { SwalInstance } from "/@src/shared/common/type-swal";
import { catchFieldError } from "/@src/utils/api/catchFormErrors";
import { highlightErrorTabs } from "/@src/utils/form/product_form";
import { IProduct, IProductForm, IProductVariantDimensionValue } from "/@src/shared/product/interface-product";
import { useSwal } from "/@src/composables/useSwal";
const emit = defineEmits(['save-triggered']);
const props = defineProps({
    product_id: {
        type: Number,
        required: false,
    },
    saveTriggered: {
        type: Boolean,
        default: false,
        required: false,
    }
})
const loaded = ref(false);
const viewWrapper = useViewWrapper();
const swal = useSwal();

const productStore = useCatalogProductStore();
const productName = computed(() => ' ' + productStore.productData.name);


const router = useRouter();



const sendForm = async (e: Event) => {
    e.preventDefault();
    //get form validation errors
    /* clearErrors(); */
    if (!props.product_id) await addRequest();
    else await updateRequest();
}

const addRequest = async () => {
    try {
        const { data } = await productStore.registerProduct();
        swal.fire('Başarılı', 'Ürün Eklendi', 'success').then(() => {
            location.href = `/app/catalog/product/`
        });
    } catch (e) {
        formErrorHandler(e);
    }
}

const formErrorHandler = (e: any) => {
    const errors = catchFieldError(e);
    productStore.setErrors(errors);


    let msg = e.response.data.message;
    swal.fire('Hata', msg, 'error');
}

const updateRequest = async () => {
    if (!props.product_id) return;
    try {
        await productStore.updateProduct(props.product_id);
        swal.fire('Başarılı', 'Ürün Güncellendi', 'success').then(() => {
            location.reload();
        });
    } catch (e) {
        formErrorHandler(e);
    }
}
const getData = async () => {
    if (!props.product_id) return;
    viewWrapper.setLoading(true);
    try {
        const { data } = await get(props.product_id);
        let __data = data.data as IProduct;

        let __mapped: IProductForm = {
            ...__data,
            colors: __data.colors.map((color) => {
                return {
                    id: color.id,
                    variant_id: color.variant_id,
                    variant_value_id: color.value.map((value) => value.variant_value_id)
                }
            }),
            dimensions: __data.dimensions.map((dimension) => {
                let _val = dimension.value.map((value) => value.value);
                let __values: IProductVariantDimensionValue[] = [];
                _val.forEach((val) => {
                    __values.push(val)
                });

                return {
                    id: dimension.id,
                    variant_id: dimension.variant_id,
                    value: __values
                }
            })
        }
        //console.log(__mapped);

        productStore.productData = __mapped
    } catch (e) {
        console.log(e);
        catchFieldError(e).then(() => router.push('/app/catalog/product'))
    }
    loaded.value = true;
    viewWrapper.setLoading(false);
}
onMounted(() => {
    if (props.product_id)
        getData();
    else loaded.value = true;
});

onUnmounted(() => {
    productStore.$reset();
    productStore.$dispose();
});

watch(() => props.saveTriggered, async (value) => {
    if (value) {
        await sendForm(new Event('submit'));
        emit('save-triggered', false);
    }
});


</script>
<template>
    <div class="container-fluid px-3">
        <form id="form-product" @submit.prevent="sendForm">
            <div class="row">
                <div class="col-12 col-xl-8 order-2 order-xl-1">
                    <TProductGeneral />
                    <ProductDropzone v-if="loaded" />
                    <TProductVariant />
                    <TProductAdvanced />
                </div>
                <div class="col-12 col-xl-4 order-1 order-xl-2">
                    <TProductPricing />
                    <TProductUnits />
                </div>
            </div>
            <div class="text-right mt-2">
                <button type="submit" class="btn btn-primary d-none">Kaydet <i
                        class="icon-paperplane ml-2"></i></button>
            </div>
        </form>


    </div>
</template>

<style scoped>
.has-error {
    border: 1px solid #dc3545 !important;
    border-radius: 7px !important;
    animation: flashAnimation 1s infinite;
    color: #dc3545 !important;
    /* 1 saniyede bir sürekli tekrarlanan animasyon */
}

@keyframes flashAnimation {
    0% {
        opacity: 1;
    }

    50% {
        opacity: 0;
    }

    100% {
        opacity: 1;
    }
}
</style>