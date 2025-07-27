<script setup lang="ts">
import AutoComplete, { AutoCompleteChangeEvent, AutoCompleteCompleteEvent, AutoCompleteItemSelectEvent } from 'primevue/autocomplete';
import { useSwal } from '/@src/composables/useSwal';
import ApiStore, { ICategoryItem, IStoreProductFilter } from '/@src/request/api-store';
import { useShopStore } from '/@src/stores/shopStore';
import { useI18n } from 'vue-i18n';
import { IStoreCartData } from '/@src/shared/cart';
import Dropdown from 'primevue/dropdown';
//@ts-ignore
import { VisenzeApi } from '/@src/utils/visenze_initer'
const { t } = useI18n();
const route = useRoute();
const swal = useSwal();
const { id: storeId } = route.params as { id: string };
//search query
const { search: searchQuery } = route.query as { search: string };
const isLoading = ref<boolean>(false);
const productToShow = 16;
const productList = ref<IStoreCartData[]>([])
const filter = ref<IStoreProductFilter>({
    search: searchQuery || '',
    category: '',
    page: 1,
    limit: productToShow,
})
const categoryFilter = ref<ICategoryItem | null>(null);
const suggestions = ref<ICategoryItem[]>([]);

const getProducts = async () => {
    isLoading.value = true;
    const api = new ApiStore(storeId);
    //sleep for 600ms
    await new Promise(resolve => setTimeout(resolve, 600));
    try {
        const { data } = await api.getProducts(filter.value);
        productList.value = data.data as IStoreCartData[];
        await VisenzeApi.recommWidget.init()
    } catch (error) {
        swal.fire('Hata', t('errors.notRetrievedProducts'), 'error');
    }
    isLoading.value = false;
}

const getCategories = async () => {
    const api = new ApiStore(storeId);
    suggestions.value = [];
    try {
        const { data } = await api.getCategories();
        let options: ICategoryItem[] = [{ id: 0, name: t('form.all'), is_default: false, parent_id: 0, product_count: 0 }];
        let _mapped = data.data.map((item: ICategoryItem) => {
            return {
                id: item.id,
                name: item.name + ` (${item.product_count})`,
                is_default: item.is_default,
                parent_id: item.parent_id
            }
        });
        suggestions.value = options.concat(_mapped as ICategoryItem[]);
        console.log(suggestions.value);
    } catch (error) {
        swal.fire('Hata', t('errors.notRetrievedCategories'), 'error');
    }
}

watch(categoryFilter, async (value) => {
    if (value) {
        filter.value.category = value.id !== 0 ? value.id : '';
        await getProducts();
    } else {
        await getProducts();
    }
})


onMounted(async () => {
    if (storeId) {
        await getCategories();
        await getProducts();
    } else {
        swal.fire('Hata', t('common.storeNotFound'), 'error');
    }
})

watch(() => route.query, async (value) => {
    if (value.search) {
        filter.value.search = value.search as string;
        await getProducts();
    }
})

onBeforeRouteUpdate(async (to, from) => {
    if (to.query.search !== from.query.search) {
        filter.value.search = to.query.search as string;
        await getProducts();
    } else {
        //search query is same
        //do nothing
    }
})

onBeforeUnmount(async () => {
    await VisenzeApi.recommWidget.dispose();
})

</script>
<template>
    <section class="bg-white py-8">

        <div class="container mx-auto flex items-center flex-wrap pb-12">

            <!-- <nav id="store" class="w-full z-0 top-0 px-6 py-1">
                <div class="w-full container mx-auto flex flex-wrap items-center justify-between mt-0 px-2 py-3">


                    <div class="flex items-center" id="store-nav-content">

                        <div class="max-w-md mx-auto">
                            <label for="default-search"
                                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 cursor-pointer">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <Dropdown v-model="categoryFilter" :options="suggestions" optionLabel="name"
                                    id="default-search" :empty-filter-message="t('errors.notRetrievedCategories')"
                                    @complete="getCategories" :placeholder="t('common.searchInCategory')"
                                    class="h-10 w-full md:w-14rem" />
                            </div>
                        </div>

                    </div>



                </div>
            </nav> -->


            <div v-if="isLoading" class="w-full flex flex-wrap">
                <div class="w-full md:w-1/3 xl:w-1/4 p-6 flex flex-col border-1 rounded-sm border-gray-300"
                    v-for="i in 6">
                    <div role="status"
                        class="max-w-sm p-4 border border-gray-200 rounded shadow animate-pulse md:p-6 dark:border-gray-700">
                        <div class="flex items-center justify-center h-48 mb-4 bg-gray-300 rounded dark:bg-gray-700">
                            <svg class="w-10 h-10 text-gray-200 dark:text-gray-600" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                                <path
                                    d="M14.066 0H7v5a2 2 0 0 1-2 2H0v11a1.97 1.97 0 0 0 1.934 2h12.132A1.97 1.97 0 0 0 16 18V2a1.97 1.97 0 0 0-1.934-2ZM10.5 6a1.5 1.5 0 1 1 0 2.999A1.5 1.5 0 0 1 10.5 6Zm2.221 10.515a1 1 0 0 1-.858.485h-8a1 1 0 0 1-.9-1.43L5.6 10.039a.978.978 0 0 1 .936-.57 1 1 0 0 1 .9.632l1.181 2.981.541-1a.945.945 0 0 1 .883-.522 1 1 0 0 1 .879.529l1.832 3.438a1 1 0 0 1-.031.988Z" />
                                <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.98 2.98 0 0 0 .13 5H5Z" />
                            </svg>
                        </div>
                        <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-48 mb-4"></div>
                        <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700 mb-2.5"></div>
                        <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700 mb-2.5"></div>
                        <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                        <span class="sr-only">{{ t('common.loading') }}</span>
                    </div>
                </div>

            </div>
            <div class="w-full flex flex-wrap" v-if="!isLoading && productList.length">
                <ProductItem v-for="product in productList" :key="product.uuid" :product="product"
                    :store-id="storeId" />
            </div>
            <div v-else class="w-full flex flex-wrap">
                <div class="w-full text-center">
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ t('common.productNotFound') }}
                    </h1>
                </div>
            </div>




        </div>

    </section>
</template>

<style>
#default-search_list .p-dropdown-item[aria-selected="true"] {
    @apply !bg-gray-400 !text-white;
}

#default-search_list .p-dropdown-item {
    @apply !bg-white !text-gray-800;
}

.p-dropdown.p-focus {
    @apply !border-gray-400;
}
</style>