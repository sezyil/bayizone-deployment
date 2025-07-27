<script setup lang="ts">
import { useOfferRequest } from '/@src/stores/offerRequest';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const viewWrapper = useViewWrapper();
viewWrapper.setPreviousButton(false);
const store = useOfferRequest();
const getProducts = async () => {
    viewWrapper.setLoading(true);
    await store.getProducts();
    viewWrapper.setLoading(false);
}
const paginationData = computed(() => store.paginationData)

const step = computed(() => store.step)

const modalProductId = ref<string | null>(null);

const handleProductModal = (id: string) => {
    console.log(id);
    modalProductId.value = id;
}

onMounted(async () => {
    await getProducts();
});
</script>
<template>
    <div class="offer-request-wrapper">
        <div class="product-select-wrapper" v-show="step === 'product'">
            <!-- Grid -->
            <div class="row">
                <OfferProductCard v-for="product in store.productList" :key="product.id" :product="product"
                    @add-to-cart="handleProductModal(product.id)" />
            </div>
            <!-- /grid -->


            <!-- Pagination -->
            <OfferRequestPaginator :total="paginationData.total" :limit="paginationData.limit"
                :componentPage="paginationData.page" @pageChange="store.setPage" />
            <!-- /pagination -->
        </div>
        <div class="offer-checkout-wrapper" v-show="step === 'checkout'">
            <OfferCheckout />
        </div>

        <OfferProductModal v-if="modalProductId" :product_id="modalProductId" @close="modalProductId = null" />
    </div>
</template>

<style scoped></style>