<script setup lang="ts">
import { AxiosError } from 'axios';
import ApiStore from '/@src/request/api-store';
import { useSwal } from '/@src/composables/useSwal';
import Galleria from 'primevue/galleria';
import { useShopStore } from '/@src/stores/shopStore';
import VueEasyLightbox from 'vue-easy-lightbox';
import { useI18n } from 'vue-i18n';
import { IStoreCartData } from '/@src/shared/cart';
import { IStoreProduct } from '/@src/shared/product';
import { AvailableLanguages } from '/@src/shared/common/common-language';
import { IApiCartAddToCart } from '/@src/request/api-cart';
//@ts-ignore
import { VisenzeApi } from '/@src/utils/visenze_initer'
const { t, locale } = useI18n();
const swal = useSwal();
const route = useRoute();
const router = useRouter();
//get id and productid param
const { id, productid } = route.params as { id: string, productid: string };
const store = useShopStore();
const isLoading = ref<boolean>(false);
const productData = ref<IStoreProduct | null>(null);
const indexRef = ref(0);
const visibleRef = ref(false);
const currentLang = computed(() => locale.value);
const isInList = computed(() => {
    return store.cartList.some((item) => item.uuid === productData.value?.uuid);
})
const responsiveOptions = ref([
    {
        breakpoint: '1300px',
        numVisible: 4
    },
    {
        breakpoint: '575px',
        numVisible: 2
    }
]);

interface ISelectedAttributes {
    color: ISelectedAttributeValue[]
    dimension: ISelectedAttributeValue[]
}

interface ISelectedAttributeValue {
    id: string;
    value: string;
}


const selectedAttributes = ref<ISelectedAttributes>({
    color: [],
    dimension: []
});



const getProductData = async () => {
    isLoading.value = true;
    const api = new ApiStore(id);
    try {
        const { data } = await api.getProduct(productid);
        productData.value = data.data as IStoreProduct;
        await generateSelectedAttributes();
        await VisenzeApi.recommWidget.init()
    } catch (error: AxiosError | any) {
        if (error instanceof AxiosError) {
            if (error.response?.status === 403) {
                swal.fire({
                    title: 'Permission Denied',
                    text: 'You do not have permission to access this page.',
                    icon: 'error'
                });
            } else {
            }

        } else {
            swal.fire({
                title: 'Error',
                text: 'Hata oluştu. Lütfen tekrar deneyin.',
                icon: 'error'
            });
        }
    } finally {
        isLoading.value = false;
    }
};

const generateSelectedAttributes = async () => {
    //productData
    selectedAttributes.value.color = productData.value!.colors.map((color) => {
        return {
            id: color.id,
            value: ''
        }
    });
    selectedAttributes.value.dimension = productData.value!.dimensions.map((dimension) => {
        return {
            id: dimension.id,
            value: ''
        }
    });
}

const changeQuantity = async (data: number) => {
    productData.value!.quantity = data;
};

const addItem = async () => {
    try {
        if (!productData.value) return;
        let filteredColor = selectedAttributes.value.color.filter((color) => color.value !== '');
        let filteredDimension = selectedAttributes.value.dimension.filter((dimension) => dimension.value !== '');

        let _tmpProduct: IApiCartAddToCart = {
            product_id: productData.value!.uuid,
            quantity: productData.value!.quantity,
            color: filteredColor,
            dimension: filteredDimension,
        };
        if (productData.value) await store.addItem(_tmpProduct);
        productData.value!.quantity = 1;
        productData.value!.variants = [];
        await generateSelectedAttributes();
    } catch (error) {
        console.log(error);
        swal.fire({
            title: 'Error',
            text: t('common.hasError'),
            icon: 'error'
        });
    }
};

const triggerVariantChange = (e: Event, type: string, id: string) => {
    const target = e.target as HTMLSelectElement;
    if (type === 'color') {
        const index = selectedAttributes.value.color.findIndex((color) => color.id === id);
        if (index !== -1) {
            selectedAttributes.value.color[index].value = target.value;
        }
    } else if (type === 'dimension') {
        const index = selectedAttributes.value.dimension.findIndex((dimension) => dimension.id === id);
        if (index !== -1) {
            selectedAttributes.value.dimension[index].value = target.value;
        }
    }
};




//life cycle
onBeforeMount(async () => {
    if (id && productid) {
        await getProductData();
    } else if (!id) {
        router.push('/:all(.*)');
    } else if (!productid) {
        router.push(`/${id}`);
    }
});

onBeforeUnmount(async () => {
    await VisenzeApi.recommWidget.dispose();
})


</script>

<template>
    <div class="py-6">
        <FullPageLoader v-if="isLoading" type="product" />
        <div style='background-color:rgba(0, 0, 0, 0)' v-if="!isLoading && productData">
            <div class="container px-5 py-12 mx-auto" style="cursor: auto;">
                <div class="lg:w-4/5 mx-auto flex flex-wrap">
                    <div class="card h-full w-full lg:w-1/2 md:p-8" v-if="productData && productData.images.length > 0">
                        <Galleria :value="productData.images" :responsiveOptions="responsiveOptions" :numVisible="5"
                            @update:active-index="indexRef = $event" :circular="true" containerStyle="max-width: 640px">
                            <template #item="slotProps">
                                <img :src="slotProps.item.itemImageSrc" style="width: 100%; display: block"
                                    @click="visibleRef = true" />
                                <div class="absolute top-0 left-0" v-if="store.getStore?.ai_support">
                                    <div class="flex items-center justify-between p-1">
                                        <span
                                            class="bg-zinc-100 text-zinc-800 text-xs font-medium inline-flex items-center px-2 py-0.5 rounded-full dark:bg-gray-700 dark:text-zinc-400 border border-zinc-400">
                                            Discover similar
                                            <div class="ps-widget-5384 ml-1" :data-pid="productid"></div>
                                        </span>
                                    </div>
                                </div>
                            </template>
                            <template #thumbnail="slotProps">
                                <div class="grid grid-nogutter justify-center">
                                    <img :src="slotProps.item.itemImageSrc" style="width: 100%; display: block"
                                        class="rounded-lg h-20" />
                                </div>
                            </template>
                        </Galleria>
                        <VueEasyLightbox :visible="visibleRef" :imgs="productData.images.map((e) => e.itemImageSrc)"
                            :index="indexRef" @hide="visibleRef = false" />
                    </div>
                    <div class="lg:w-1/2 w-full lg:pl-10 lg:py-6 mt-6 lg:mt-0" style="cursor: auto;">
                        <div class="text-left">
                            <h1 class="text-gray-900 text-3xl font-medium mb-2 p-4 rounded-l">
                                {{ productData?.name }}
                            </h1>
                        </div>
                        <div class="rounded-lg overflow-hidden" v-if="productData?.description.length > 0">
                            <div class=" px-4 py-2">
                                <h2 class="text-gray-800 text-sm font-semibold">
                                    {{ t('product.description') }}
                                </h2>
                            </div>
                            <div class="p-4">
                                <p class="text-gray-700">{{ productData?.description }}</p>
                            </div>
                        </div>

                        <ProductInformationArea :detail="productData.detail" />

                        <!-- variants select list -->
                        <div class="rounded-lg overflow-hidden">
                            <div class=" px-4 py-2">
                                <h2 class="text-gray-800 text-sm font-semibold">
                                    {{ t('product.variants') }}
                                </h2>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <!-- color -->
                                    <div v-for="(color, index) in productData.colors" :key="color.id">
                                        <p class="text-gray-600 font-semibold">{{ color.name }}:
                                        </p>
                                        <select :value="selectedAttributes.color.find((c) => c.id === color.id)?.value"
                                            @change="($event) => triggerVariantChange($event, 'color', color.id)"
                                            class="w-full bg-gray-50 border rounded border-gray-300 h-12 items-center font-medium text-center text-gray-900 text-sm focus:ring-blue-500 
                                                roundedfocus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="">{{ t('form.select') }}</option>
                                            <option v-for="(value, index) in color.value" :key="value.id"
                                                :value="value.id">
                                                {{ value.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- dimension -->
                                    <div v-for="(dimension, index) in productData.dimensions" :key="dimension.id">
                                        <p class="text-gray-600 font-semibold">{{ dimension.name }}:
                                        </p>
                                        <select
                                            :value="selectedAttributes.dimension.find((c) => c.id === dimension.id)?.value"
                                            @change="($event) => triggerVariantChange($event, 'dimension', dimension.id)"
                                            class="w-full bg-gray-50 border rounded border-gray-300 h-12 items-center font-medium text-center text-gray-900 text-sm focus:ring-blue-500 
                                                roundedfocus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="">{{ t('form.select') }}</option>
                                            <option v-for="(_dimension, index) in dimension.value" :key="_dimension.id"
                                                :value="_dimension.id">
                                                {{ t('product.selectedDimension', {
                                                    width: _dimension.value.width,
                                                    height: _dimension.value.height,
                                                    length: _dimension.value.length
                                                }) }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ProductDetailButtons :productData="productData" :is-in-list="isInList"
                            @change-quantity="changeQuantity" @add-item="addItem" />



                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <StoreNotFound errorType="product" :storeId="id" />
        </div>
    </div>

</template>

<style scoped>
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type='number'] {
    -moz-appearance: textfield;
}

.custom-number-input input:focus {
    outline: none !important;
}

.custom-number-input button:focus {
    outline: none !important;
}
</style>