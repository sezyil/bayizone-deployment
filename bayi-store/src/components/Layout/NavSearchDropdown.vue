<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useShopStore } from '/@src/stores/shopStore';
const store = useShopStore()
const router = useRouter();
const { t } = useI18n();
const route = useRoute();
const searchText = ref('')
const storeId = computed(() => store.storeId)

const onSearch = () => {
    if (searchText.value.trim() !== '') {
        console.log('storeId', storeId.value)
        router.push({
            name: '/[id]/',
            params: { id: storeId.value },
            query: { search: searchText.value }
        });
    } else {
        router.push({
            name: '/[id]/',
            params: { id: storeId.value }
        });
    }
}

onBeforeRouteUpdate((to, from, next) => {
    const { search: searchQuery } = to.query as { search: string }
    searchText.value = searchQuery || ''
    next()
})

</script>
<template>
    <div>
        <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" data-dropdown-placement="bottom"
            class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400"
            type="button">
            <span class="pi pi-search"></span>
        </button>

        <!-- Dropdown menu -->
        <div id="dropdownSearch" class="z-10 hidden bg-white rounded-lg shadow w-60 dark:bg-gray-700">
            <div class="p-3 w-full sm:w-72 md:w-auto">
                <label for="input-group-search" class="sr-only">{{ t('common.search') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 cursor-pointer"
                        @click="onSearch">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" id="input-group-search"
                        class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        @keypress.enter="onSearch" v-model="searchText" :placeholder="t('common.searchInStore')">
                </div>
            </div>
        </div>


    </div>
</template>



<style scoped></style>