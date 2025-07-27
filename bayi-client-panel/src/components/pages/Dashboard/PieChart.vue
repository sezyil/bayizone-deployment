<script setup lang="ts">
import DashboardRequestApi from '/@src/request/request-dashboard';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';

const dataList = ref<IResponse>();
const isLoading = ref(false);
const api = new DashboardRequestApi()


interface IResponse {
    best_selling_product: IPieChartBestSellingProduct[]
    best_selling_country: IPieChartBestSellingCountry[]
    best_selling_category: IPieChartBestSellingCategory[]
}

export interface IPieChartBestSellingProduct {
    product_id: number
    product_name: string
    total: string
}

export interface IPieChartBestSellingCountry {
    country_id: number
    country_name: string
    total: number
}

export interface IPieChartBestSellingCategory {
    category_id: number
    category_name: string
    total: string
}



const getData = async () => {
    isLoading.value = true;
    try {
        const { data } = await api.get('pie_chart');
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
interface LastTransaction {
    id: string
    date: string
    customer_id: string
    customer_name: string
    due_date: string
    fiche_no: string
    fiche_type: string
    is_paid: number
}
</script>
<template>
    <div class="row">
        <div class="col-sm-12 col-lg-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Önemli Ürünler</h6>
                </div>

                <div class="card-body">
                    <VLoader v-if="isLoading" :active="isLoading" />
                    <div v-else>
                        <ProductPie v-if="dataList?.best_selling_product.length" :data="dataList?.best_selling_product"  />
                        <!-- no data -->
                        <div class="alert alert-info alert-styled-left alert-dismissible" v-else>
                            Veri bulunamadı.
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Önemli Ülkeler</h6>
                </div>

                <div class="card-body">
                    <VLoader v-if="isLoading" :active="isLoading" />
                    <div v-else>
                        <CountryPie v-if="dataList?.best_selling_country.length"
                            :data="dataList?.best_selling_country" />
                        <div v-else>
                            <!-- no data -->
                            <div class="alert alert-info alert-styled-left alert-dismissible">
                                Veri bulunamadı.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Önemli Kategoriler</h6>
                </div>

                <div class="card-body">
                    <VLoader v-if="isLoading" :active="isLoading" />
                    <div v-else>
                        <CategoryPie  v-if="dataList?.best_selling_category.length" :data="dataList?.best_selling_category" />
                        <div v-else>
                            <!-- no data -->
                            <div class="alert alert-info alert-styled-left alert-dismissible">
                                Veri bulunamadı.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped></style>