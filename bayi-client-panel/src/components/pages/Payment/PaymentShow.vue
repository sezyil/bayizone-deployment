<script setup lang="ts">
import { IRequestPayDataResponse, getOrderPaymentMethods } from '/@src/request/request-payment';

const props = defineProps<{ storeData: IRequestPayDataResponse }>();
const paymentMethod = computed(() => {
    return getOrderPaymentMethods().find((x) => x.value === props.storeData.payment_method)?.text || 'Bilinmiyor';
});
</script>
<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="mb-4">
                        <img src="/images/bayizone_light.png" class="mb-3 mt-2" alt="" style="width: 120px;">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-4">
                        <div class="text-sm-right">
                            <ul class="list list-unstyled mb-0">
                                <li>
                                    Sipariş Tarihi: <span class="font-weight-semibold">{{ storeData?.created_at
                                        }}</span>
                                </li>
                                <li>
                                    Sipariş Numarası: <span class="font-weight-semibold">
                                        #{{ storeData?.order_no }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-lg-flex flex-lg-wrap">
                <div class="mb-4 mb-lg-2">
                    <span class="text-muted">Fatura Detayları:</span>


                    <ul class="list list-unstyled mb-0">
                        <li>
                            <h5 class="my-2">{{ storeData?.invoice_firm_name }}</h5>
                        </li>
                        <li><strong>Adres:</strong>
                            {{ storeData?.invoice_address }}
                        </li>
                        <li>{{ storeData?.invoice_city }}/{{ storeData?.invoice_state }}/{{ storeData?.invoice_country
                            }}</li>
                        <li><strong>V.No/Daire:</strong>{{ storeData?.invoice_tax_no }}/{{
                            storeData?.invoice_tax_administration }}</li>
                        <li><strong>Tel:</strong>{{ storeData?.invoice_phone }}</li>
                        <li><strong>Mail:</strong>{{ storeData?.invoice_email }}</li>
                    </ul>
                </div>

                <div class="mb-2 ml-auto">
                    <span class="text-muted">Ödeme Detayları:</span>
                    <div class="d-flex flex-wrap wmin-lg-400">
                        <ul class="list list-unstyled mb-0">
                            <li>
                                <!-- ödemne durumu -->
                                <h5 class="my-2">Ödeme Durumu:</h5>
                            </li>
                            <li>
                                <h5 class="my-2">Toplam:</h5>
                            </li>
                            <li>Ödeme Yöntemi:</li>
                        </ul>

                        <ul class="list list-unstyled text-right mb-0 ml-auto">
                            <li>
                                <h5 class="font-weight-semibold my-2">
                                    <span v-if="storeData?.is_paid" class="badge badge-success">Ödendi</span>
                                    <span v-else class="badge badge-warning">Ödenmedi</span>
                                </h5>
                            </li>
                            <li>
                                <h5 class="font-weight-semibold my-2">${{ storeData?.total }}</h5>
                            </li>
                            <li><span class="font-weight-semibold">{{ paymentMethod }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-lg table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Ürün</th>
                        <th class="text-center">Fiyat</th>
                        <th class="text-center">Miktar</th>
                        <th class="text-center">Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in storeData?.lines" :key="item.id">
                        <td>
                            <h6 class="mb-0">{{ item.name }}</h6>
                        </td>
                        <td class="text-right">${{ item.price }}</td>
                        <td class="text-center" v-if="item.type == 'subscription'">
                            {{ item.item_data.duration }} (Ay)
                        </td>
                        <td class="text-center" v-else>
                            <span v-if="item.item_data.is_boolean">
                                {{ item.item_data.quantity }} (Ay)
                            </span>
                            <span v-else> {{ item.item_data.amount }} </span>
                        </td>
                        <td class="text-right">${{ item.subtotal }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-body">
            <div class="d-lg-flex flex-lg-wrap">
                <div class="pt-2 mb-3">
                    <h6 class="mb-3">Satıcı Bilgileri</h6>
                    <ul class="list-unstyled text-muted">
                        <li>Bayizone</li>
                        <li>Bursa/Osmangazi</li>
                        <li>888-555-2311</li>
                    </ul>
                </div>

                <div class="pt-2 mb-3 wmin-lg-400 ml-auto">
                    <h6 class="mb-3">Toplam</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Ara Toplam:</th>
                                    <td class="text-right">$ {{ storeData?.subtotal }}</td>
                                </tr>
                                <tr>
                                    <th>Vergi: <span class="font-weight-normal">(20%)</span></th>
                                    <td class="text-right">$ {{ storeData?.tax_amount }}</td>
                                </tr>
                                <!-- kupon -->
                                <tr v-if="storeData?.coupon_id">
                                    <th>Kupon İndirimi</th>
                                    <td class="text-right">$ {{ storeData?.discount_amount }}</td>
                                </tr>
                                <tr>
                                    <th>Toplam:</th>
                                    <td class="text-right text-primary">
                                        <h5 class="font-weight-semibold">$ {{ storeData?.total }}</h5>
                                    </td>
                                </tr>
                                <!-- tl -->
                                <tr>
                                    <th>Kupon İndirimi (TL)</th>
                                    <td class="text-right">₺ {{ storeData?.converted_discount_amount }}</td>
                                </tr>
                                <tr>
                                    <th>Toplam (TL):</th>
                                    <td class="text-right text-primary">
                                        <h5 class="font-weight-semibold">₺ {{ storeData?.converted_total }}</h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <span class="text-muted">Bayizone u tercih ettiğiniz için teşekkürler</span>
        </div>
    </div>
</template>
<style scoped></style>