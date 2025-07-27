<script setup lang="ts">
import DashboardRequestApi from '/@src/request/request-dashboard';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';

const dataList = ref<IResponse>();
const isLoading = ref(false);
const api = new DashboardRequestApi()

interface IResponse {
    order: Order[]
    offer: Offer[]
}

interface Order {
    id: string
    company: string
    currency: string
    order_date: string
    grand_total: string
    order_no: string
    status: string
}

interface Offer {
    id: string
    company: string
    currency: string
    offer_date: string
    grand_total: string
    offer_no: string
    status: string
}


const getData = async () => {
    isLoading.value = true;
    try {
        const { data } = await api.get('last_offers_orders');
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
        <div class="col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Son 5 Sipariş</h6>

                    <div class="header-elements">
                        <RouterLink to="/app/customer_orders" class="text-body"><i class="icon-eye"></i>
                        </RouterLink>
                    </div>
                </div>

                <div class="card-body">
                    <VLoader :active="isLoading" />
                    <ul class="media-list" v-if="dataList?.order?.length ?? 0">
                        <li class="media" v-for="item in dataList?.order" :key="item.id"
                            style="border: 1px solid #e0e0e0;border-radius: 5px;padding: 10px;margin-bottom: 10px;">
                            <div class="mr-3 position-relative">
                                <i
                                    class="icon-package  icon-2x text-secondary border-secondary border-3 rounded-circle rounded-round p-2"></i>
                            </div>

                            <div class="media-body">
                                <div class="d-flex justify-content-between">
                                    <div class="media-title">
                                        Müşteri: {{ item.company }}
                                    </div>
                                    <span class="font-size-sm text-muted">
                                        {{ item.order_date }}
                                    </span>
                                </div>
                                <RouterLink :to="'/app/customer_orders/' + item.id + '/show'"
                                    class="font-weight-semibold text-default">
                                    No: #{{ item.order_no }}
                                </RouterLink>
                                <p>
                                    Durumu: {{ item.status }}
                                </p>
                            </div>
                        </li>
                    </ul>
                    <ul class="media-list" v-else>
                        <li class="media">
                            <div class="media-body">
                                <div class="d-flex justify-content-between">
                                    <div class="media-title">
                                        Sipariş bulunamadı.
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Son 5 Teklif</h6>

                    <div class="header-elements">
                        <RouterLink to="/app/offers" class="text-body"><i class="icon-eye"></i>
                        </RouterLink>
                    </div>
                </div>

                <div class="card-body">
                    <VLoader :active="isLoading" />
                    <ul class="media-list" v-if="dataList?.offer?.length ?? 0">
                        <li class="media" v-for="item in dataList?.offer" :key="item.id"
                            style="border: 1px solid #e0e0e0;border-radius: 5px;padding: 10px;margin-bottom: 10px;">
                            <div class="mr-3 position-relative">
                                <i
                                    class="icon-calendar2 icon-2x text-secondary border-secondary border-3 rounded-circle rounded-round p-2"></i>
                            </div>

                            <div class="media-body">
                                <div class="d-flex justify-content-between">
                                    <div class="media-title">
                                        Müşteri: {{ item.company }}
                                    </div>
                                    <span class="font-size-sm text-muted">
                                        {{ item.offer_date }}
                                    </span>
                                </div>
                                <RouterLink :to="'/app/offers/' + item.id + '/edit'"
                                    class="font-weight-semibold text-default">
                                    No: #{{ item.offer_no }}
                                </RouterLink>
                                <p>
                                    Durumu: {{ item.status }}
                                </p>
                            </div>
                        </li>
                    </ul>
                    <ul class="media-list" v-else>
                        <li class="media">
                            <div class="media-body">
                                <div class="d-flex justify-content-between">
                                    <div class="media-title">
                                        Teklif bulunamadı.
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped>
.media{
    align-items: center;
}
</style>