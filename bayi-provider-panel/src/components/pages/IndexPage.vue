<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useApi } from '/@src/composables/useApi';
import { GLOB_URIS, get_product_image_url } from '/@src/utils/global_uris';
interface IDashboard {
    offers: Offers
    offer_request: OfferRequest
}

interface Offers {
    total: number
}
interface OfferRequest {
    pending: number
    accepted: number
    rejected: number
    last5: Last5[]
}
interface Last5 {
    id: string
    request_no: string
    status: string
    totalProduct: number
    created_at: string
    from_store: string
}

const api = useApi();
const data = ref<IDashboard>({
    offers: {
        total: 0
    },
    offer_request: {
        pending: 0,
        accepted: 0,
        rejected: 0,
        last5: []
    }
});
const isLoading = ref<boolean>(false);
const { t } = useI18n()
const getData = async () => {
    isLoading.value = true;
    try {
        const { data: _data } = await api.get('/dashboard')
        data.value = _data.data as IDashboard;
    } catch (error) {
        console.log(error);
    }
    isLoading.value = false;
}

const statusConverter = (status: string) => {
    let badge = '';
    let statusText = '';
    switch (status) {
        case 'PENDING':
            badge = 'badge-warning';
            statusText = t('pages.dashboard.offerStatuses.pending')
            break;
        case 'ACCEPTED':
            badge = 'badge-success';
            statusText = t('pages.dashboard.offerStatuses.accepted')
            break;
        case 'REJECTED':
            badge = 'badge-danger';
            statusText = t('pages.dashboard.offerStatuses.rejected')
            break;
        default:
            badge = 'badge-warning';
            statusText = t('pages.dashboard.offerStatuses.pending')
            break;
    }
    return `<span class="badge ${badge}">${statusText}</span>`;
}
const modalId = ref<string>('');

const circleGenerator = (status: string) => {
    //text-success border-success
    let _class = '';
    switch (status) {
        case 'PENDING':
            _class = 'text-warning border-warning';
            break;
        case 'ACCEPTED':
            _class = 'text-success border-success';
            break;
        case 'REJECTED':
            _class = 'text-danger border-danger';
            break;
        default:
            _class = 'text-warning border-warning';
            break;
    }
    return _class;
}

onMounted(async () => {
    await getData();
})
</script>

<template>
    <div class="row">
        <VLoader :active="isLoading" />
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <StatisticsCard background-color="#FFFFFF" :number-area="data.offers.total" icon-position="left"
                        class="cursor-pointer" @card-clicked="$router.push('/app/offers')"
                        icon="fa fa-shopping-cart fa-2x" :text-content="t('pages.dashboard.totalOffer')" />
                </div>
                <div class="col-sm-6 col-lg-3">
                    <StatisticsCard background-color="#FFFFFF" :number-area="data.offer_request.pending"
                        class="cursor-pointer" @card-clicked="$router.push('/app/offer_requests')"
                        icon="fa fa-clock fa-2x" icon-position="left"
                        :text-content="t('pages.dashboard.pendingOffer')" />
                </div>
                <div class="col-sm-6 col-lg-3">
                    <StatisticsCard background-color="#FFFFFF" :number-area="data.offer_request.accepted"
                        class="cursor-pointer" @card-clicked="$router.push('/app/offer_requests')"
                        icon="fa fa-check fa-2x" icon-position="left"
                        :text-content="t('pages.dashboard.approvedOffer')" />
                </div>
                <div class="col-sm-6 col-lg-3">
                    <StatisticsCard background-color="#FFFFFF" :number-area="data.offer_request.rejected"
                        class="cursor-pointer" @card-clicked="$router.push('/app/offer_requests')"
                        icon="fa fa-times fa-2x" icon-position="left"
                        :text-content="t('pages.dashboard.rejectedOffer')" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-lg-9">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h6 class="card-title">{{ t('pages.dashboard.last5OfferRequest') }}</h6>

                            <div class="header-elements">
                                <RouterLink to="/app/offer_requests" class="btn btn-primary btn-sm">
                                    {{ t('actions.seeAll') }}
                                </RouterLink>
                            </div>
                        </div>

                        <div class="card-body">
                            <ul class="media-list">
                                <li class="media" v-for="(item, index) in data.offer_request.last5" :key="index">

                                    <div class="mr-2 position-relative">
                                        <i class="icon-file-text2 
                                                icon-2x border-3 rounded-circle rounded-round p-2"
                                            :class="circleGenerator(item.status)"></i>
                                    </div>


                                    <div class="media-body">
                                        <div class="d-flex justify-content-between">
                                            <div class="media-title">
                                                <!-- click modalId change -->
                                                <a href="#" class="font-weight-semibold"
                                                    @click.prevent="modalId = item.id">
                                                    {{ item.request_no }}
                                                </a>
                                            </div>
                                            <span class="font-size-sm text-muted">{{ item.created_at }}</span>
                                        </div>
                                        <div v-html="statusConverter(item.status)" />
                                        <div class="font-size-sm text-muted mt-1">{{ item.totalProduct }} {{
                                            t('common.product') }}</div>
                                    </div>
                                </li>


                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <OfferRequestModal :id="modalId" @close="modalId = ''" />
</template>

<style scoped></style>