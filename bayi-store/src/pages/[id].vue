<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import ApiStore from '/@src/request/api-store';
import { useShopStore } from '/@src/stores/shopStore';
import { VisenzeApi } from '/@src/utils/visenze_initer'
const route = useRoute();
const store = useShopStore();
const storeFounded = ref<boolean>(false);
const isLoading = ref<boolean>(true);
const { t } = useI18n();

const { id: storeId } = route.params as { id: string };

const getStoreDetail = async () => {
    if (storeId) {

        const api = new ApiStore(storeId);
        try {
            const { data } = await api.getStoreDetails();
            store.setStore(data.data);
            store.getCurrentCart();
            storeFounded.value = true;
            if (store.getStore) {
                const { ai_catalog_id, ai_support } = store.getStore
                if (ai_support) await VisenzeApi.searchWidget.init()
            }


        } catch (error) {
            storeFounded.value = false;
        }
    }
}

onBeforeMount(async () => {
    if (storeId) {
        await getStoreDetail();
    }
    isLoading.value = false;
})



</script>
<template>
    <FullPageLoader v-if="isLoading" type="page" />
    <StoreLayout v-else-if="storeFounded">
        <!-- Content Wrapper -->
        <RouterView v-slot="{ Component }">
            <Transition name="fade-fast" mode="out-in">
                <component :is="Component" />
            </Transition>
        </RouterView>

        <button @click="store.setModalActive(true)"
            class="flex items-center fixed bottom-0 right-0 m-4  text-white p-4 rounded-full bg-gray-700 hover:bg-gray-800">
            <span class="pi pi-shopping-cart mr-1"></span> {{ t('common.cart') }} ({{ store.cartList.length }})
        </button>
    </StoreLayout>

    <StoreNotFound error-type="store" v-else />
</template>

<style scoped></style>