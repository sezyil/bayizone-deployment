<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ICustomerOrderShowResponse } from '/@src/shared/response/interface-customer-order-show-response';
const emit = defineEmits<{
    (e: 'lookLineHistory', value: number): void;
}>();

const props = defineProps<{
    lineData: ICustomerOrderShowResponse['lines'];
    grandTotal: ICustomerOrderShowResponse['detail']['grand_total'];
    activeLineId: ICustomerOrderShowResponse['lines'][0]['id'] | null;
}>();

const { t } = useI18n();
</script>
<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ürünler</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table  table-striped">
                    <thead>
                        <tr>
                            <th>{{ t('common.product') }}</th>
                            <th>{{ t('common.quantity') }}</th>
                            <th>{{ t('variant.COLOR') }}</th>
                            <th>{{ t('variant.DIMENSION') }}</th>
                            <th>{{ t('common.sent') }}</th>
                            <th>{{ t('components.orders.remain') }}</th>
                            <th>{{ t('common.productUnitPrice') }}</th>
                            <th>{{ t('common.productTotalPrice') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in lineData" :key="product.id"
                            :class="product.remaining_quantity > 0 ? 'bg-danger-100' : ''">
                            <td>
                                <!-- name with code -->
                                <span>{{ product.product_name }}</span>
                                <span class="badge bg-info-100 rounded-pill text-black">{{ product.product_code
                                    }}</span>
                            </td>
                            <td>{{ product.quantity }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-column" v-if="product.color">
                                    <span v-for="color in product.color" class="badge bg-secondary text-white">
                                        {{ color.variant.name }}
                                        : {{ color.variant_value.name }}
                                    </span>
                                </div>
                                <span v-else>---</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-column" v-if="product.dimension">
                                    <span v-for="dimension in product.dimension" class="badge bg-secondary text-white">

                                        {{ dimension.variant.name }}
                                        :
                                        <br />
                                        {{ t('components.offer_request.elements.product.length') }}:
                                        {{ dimension.variant_value.value.length }}cm x
                                        {{ t('components.offer_request.elements.product.width') }}:
                                        {{ dimension.variant_value.value.width }}cm x
                                        {{ t('components.offer_request.elements.product.height') }}:
                                        {{ dimension.variant_value.value.height }}cm
                                    </span>
                                </div>
                                <span v-else>---</span>
                            </td>
                            <td>{{ product.sent_quantity }}</td>
                            <td>{{ product.remaining_quantity }}</td>
                            <td>{{ product.unit_price }}</td>
                            <td>{{ product.total_price }}</td>
                            <td>
                                <button class="btn  btn-sm"
                                    :class="activeLineId !== product.id ? 'btn-secondary' : 'btn-info'"
                                    @click="emit('lookLineHistory', product.id)">
                                    <i class="fas fa-history"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-right">{{ t('common.total') }}</td>
                            <td>{{ grandTotal }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</template>


<style scoped></style>