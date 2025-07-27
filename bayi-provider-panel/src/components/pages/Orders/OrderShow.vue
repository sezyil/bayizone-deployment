<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useSwal } from '/@src/composables/useSwal';
import OrderApi, { IRequestOrderDetail } from '/@src/request/request-order'
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { get_product_image_url } from '/@src/utils/global_uris';
const { t } = useI18n()
const swal = useSwal()
const router = useRouter()
const props = defineProps({
    id: {
        type: String,
        required: true
    }

})
const isLoading = ref(false)
const modalActive = ref(false)
const api = new OrderApi();
const dataList = ref<IRequestOrderDetail>()

const getData = async () => {
    isLoading.value = true
    try {
        const { data } = await api.get(props.id)
        dataList.value = data.data as IRequestOrderDetail
    } catch (e) {
        swal.fire('Hata', t('common.data.retrievalError'), 'error').then(() => {
            router.push('/app/orders')
        })
    } finally {
        isLoading.value = false
    }
}

const openOrderStatusHistoryModal = () => {
    modalActive.value = true
}

onMounted(async () => {
    await getData()
})
</script>
<template>
    <div class="card">
        <VLoader :active="isLoading" />
        <div v-if="dataList">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-4">
                            <img src="/images/logo_dark.png" class="mb-3 mt-2" alt="" style="width: 120px;">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-4">
                            <div class="text-sm-right">
                                <h4 class="text-primary mb-2 mt-lg-2">{{ t('common.order.no') }}: {{ dataList?.order_no
                                    }}</h4>
                                <ul class="list list-unstyled mb-0">
                                    <li>{{ t('common.order.date') }}: <span class="font-weight-semibold">{{
                                        dataList?.order_date
                                            }}</span>
                                    </li>
                                    <!-- order status with history icon -->
                                    <li class="text-info font-weight-semibold cursor-pointer"
                                        @click="openOrderStatusHistoryModal()"><i class="icon-history mr-1"></i>
                                        {{ }}: <span class="font-weight-semibold">
                                            {{ dataList?.status_text }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-lg-flex flex-lg-wrap">
                    <div class="mb-4 mb-lg-2">
                        <span class="text-muted">{{ t('common.buyer') }}:</span>
                        <ul class="list list-unstyled mb-0">
                            <li>
                                <h5 class="my-2">{{ dataList?.company_customer.authorized_name }}</h5>
                            </li>
                            <li><span class="font-weight-semibold">{{ dataList?.company_customer.company_name }}</span>
                            </li>
                            <li>{{ dataList?.company_customer.address }}</li>
                            <li>{{ dataList?.company_customer.state_name }}/{{ dataList?.company_customer.city_name }}
                            </li>
                            <li>{{ dataList?.company_customer.country_name }}</li>
                            <li>{{ dataList?.company_customer.phone }}</li>
                            <li>{{ dataList?.company_customer.email }}</li>
                        </ul>
                    </div>

                    <div class="mb-2 ml-auto">
                        <span class="text-muted">{{ t('common.order.invoiceDetail') }}:</span>
                        <div class="d-flex flex-wrap wmin-lg-400">
                            <ul class="list list-unstyled mb-0">
                                <li>
                                    <h5 class="my-2">{{ t('common.total') }}:</h5>
                                </li>
                                <li>{{ t('common.province') }}/{{ t('common.district') }}:</li>
                                <li>{{ t('common.country') }}:</li>
                                <li>{{ t('common.address') }}:</li>
                                <li>{{ t('common.order.overseasSale') }}:</li>
                            </ul>

                            <ul class="list list-unstyled text-right mb-0 ml-auto">
                                <li>
                                    <h5 class="font-weight-semibold my-2">{{ dataList?.grand_total }}</h5>
                                </li>
                                <li>{{ dataList?.billing_state_name }}/{{ dataList?.billing_city_name }}</li>
                                <li>{{ dataList?.billing_country_name }}</li>
                                <li>{{ dataList?.billing_address }}</li>
                                <li>
                                    <span class="font-weight-semibold">{{
                                        t(dataList?.is_international ? 'actions.yes' : 'actions.no')
                                        }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-lg table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">{{ t('common.product') }}</th>
                            <th class="text-center">{{ t('common.price') }}</th>
                            <th class="text-center">{{ t('common.quantity') }}</th>
                            <th class="text-center">{{ t('common.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in dataList?.lines" :key="item.id">
                            <td>
                                <div>
                                    <!-- with image -->
                                    <div class="d-flex align-items-center">
                                        <a :href="get_product_image_url(item.product_image_url)" target="_blank">
                                            <img :src="get_product_image_url(item.product_image_url)" alt=""
                                                class="rounded mr-2 cursor-pointer" style="width: 50px;">
                                        </a>
                                        <div>
                                            <h6 class="mb-0">{{ item.product_name }}</h6>
                                            <span class="text-muted">{{ item.product_code }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">{{ item.unit_price }}</td>
                            <td class="text-center">
                                {{ item.quantity }}({{ item.product_unit }})
                            </td>
                            <td class="text-right">{{ item.total_price }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-body">
                <div class="d-lg-flex flex-lg-wrap">
                    <div class="pt-2 mb-3">

                    </div>

                    <div class="pt-2 mb-3 wmin-lg-400 ml-auto">
                        <h6 class="mb-3">{{ t('common.total') }}</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>{{ t('common.subtotal') }}:</th>
                                        <td class="text-right">{{ dataList?.total_price }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ t('common.tax') }}: </th>
                                        <td class="text-right">{{ dataList?.total_tax }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ t('common.total') }}:</th>
                                        <td class="text-right text-primary">
                                            <h5 class="font-weight-semibold">{{ dataList?.grand_total }}</h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <span class="text-muted">{{ t('common.thankForChoosingUs') }}</span>
            </div>
            <VModal :show="modalActive" @close="modalActive = false" :title="t('common.statusHistory')" size="lg">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">{{ t('common.status') }}</th>
                            <th class="text-center">{{ t('common.date') }}</th>
                            <th class="text-center">{{ t('common.note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in dataList.histories" :key="item.id">
                            <td>{{ item.status_text }}</td>
                            <td>{{ item.created_at }}</td>
                            <td>{{ item.note }}</td>
                        </tr>
                    </tbody>
                </table>
            </VModal>
        </div>

    </div>



</template>
<style scoped></style>