<script setup lang="ts">
import DashboardRequestApi from '/@src/request/request-dashboard';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';

const dataList = ref<IResponse>();
const isLoading = ref(false);
const api = new DashboardRequestApi()

interface IResponse {
    total_offer_count: number
    total_offer_request_count: number
    total_customer_count: number
    total_product_count: number
    total_order_count: number
}

const iconColors = {
    product: "#FFD700", // Soft sarı
    customer: "#87CEEB", // Soft mavi
    offer_request: "#98FB98", // Soft yeşil
    order: "#FFA07A" // Soft pembe
};



const getData = async () => {
    isLoading.value = true;
    try {
        const { data } = await api.get('cards');
        dataList.value = data.data as IResponse;
    } catch (error) {
        catchFieldError(error);
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    getData();
})
</script>
<template>
    <div class="row">
        <div class="col-md-12">
            <VLoader :active="isLoading" />
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <StatisticsCard :number-area="dataList?.total_product_count" icon="fas fa-box fa-3x"
                        class="cursor-pointer font-bold" @click.prevent="$router.push('/app/catalog/product')"
                        icon-position="left" text-content="Ürünler" />
                </div>
                <div class="col-sm-6 col-lg-3">
                    <StatisticsCard :number-area="dataList?.total_customer_count" icon="fas fa-users fa-3x"
                        icon-position="left" class="cursor-pointer font-bold"
                        @click.prevent="$router.push('/app/customers')" text-content="Müşteriler" />
                </div>
                <div class="col-sm-6 col-lg-3">
                    <StatisticsCard :number-area="dataList?.total_offer_request_count" icon="fas fa-file-alt fa-3x"
                        icon-position="left" class="cursor-pointer font-bold"
                        @click.prevent="$router.push('/app/offer_requests')" text-content="Bekleyen Teklif İstekleri" />
                </div>
                <!-- incoming payment -->
                <div class="col-sm-6 col-lg-3">
                    <StatisticsCard :number-area="dataList?.total_order_count" icon="fas fa-file-alt fa-3x"
                        icon-position="left" class="cursor-pointer font-bold"
                        @click.prevent="$router.push('/app/customer_orders')" text-content="Toplam Sipariş" />
                </div>
            </div>
        </div>
    </div>
</template>



<style scoped></style>