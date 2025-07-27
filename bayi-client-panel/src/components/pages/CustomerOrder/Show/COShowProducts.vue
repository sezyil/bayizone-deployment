<script setup lang="ts">
import { ICustomerOrderShowResponse } from '/@src/shared/response/interface-customer-order-show-response';
const emit = defineEmits<{
    (e: 'lookLineHistory', value: number): void;
}>();

const props = defineProps<{
    lineData: ICustomerOrderShowResponse['lines'];
    grandTotal: ICustomerOrderShowResponse['detail']['grand_total'];
    activeLineId: ICustomerOrderShowResponse['lines'][0]['id'] | null;
}>();
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
                            <th>Ürün Adı</th>
                            <th>Adet</th>
                            <th>Renk</th>
                            <th>Boyut</th>
                            <th>Gönderilen</th>
                            <th>Kalan</th>
                            <th>Birim Fiyat</th>
                            <th>Toplam Fiyat</th>
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
                                <span v-if="product.color">
                                    <span v-for="color in product.color"
                                        class="badge bg-info-100 rounded-pill text-black">
                                        {{ color.variant.name }}: {{ color.variant_value.name }}
                                    </span>
                                </span>
                                <span v-else>---</span>
                            </td>
                            <td>
                                <span v-if="product.dimension">
                                    <span v-for="dimension in product.dimension"
                                        class="badge bg-info-100 rounded-pill text-black">
                                        {{ dimension.variant.name }}:
                                        Uzunluk: {{ dimension.variant_value.value.length }}cm x
                                        Genişlik: {{ dimension.variant_value.value.width }}cm x
                                        Yükseklik: {{ dimension?.variant_value.value.height }}cm
                                    </span>

                                </span>
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
                            <td colspan="7" class="text-right">Toplam</td>
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