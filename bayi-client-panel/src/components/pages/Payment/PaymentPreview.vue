<script setup lang="ts">
import { usePaymentStore } from '/@src/stores/paymentStore';
import PaymentApi, { IRequestPayBankTransferDetails, TypeOrderPaymentMethod, getOrderPaymentMethods } from '/@src/request/request-payment';
import { useSwal, useSwalToast } from '/@src/composables/useSwal';
import moment from 'moment';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const emit = defineEmits<{
    (e: 'purchase'): void,
    (e: 'refresh'): void
}>();
const paymentStore = usePaymentStore();
const storeData = computed(() => paymentStore.orderData);
const api = new PaymentApi();
const availablePaymentMethods = getOrderPaymentMethods();
const swal = useSwal();
const firstInit = ref(true);
const swalToast = useSwalToast({
    timer: 1500,
});

const paymentMethod = ref<TypeOrderPaymentMethod | null>(null);
const lastValue = ref<TypeOrderPaymentMethod | null>(null);
const bankTransferDetail = ref<IRequestPayBankTransferDetails['data'] | null>(null);

const getAvailableBankDetails = async () => {
    try {
        const { data } = await api.getBankTransferDetails();
        bankTransferDetail.value = data.data
    } catch (error: any) {
        swal.fire({
            title: 'Hata',
            text: error.message,
            icon: 'error',
        });
    }
};

const changePaymentMethod = async (value: TypeOrderPaymentMethod, oldValue: any) => {
    if (!storeData.value) return;
    if (firstInit.value) {
        firstInit.value = false;
        if (storeData.value.payment_method !== null) {
            return;
        }
    }
    try {
        await api.changePaymentMethod(storeData.value.id, value);
        swalToast.fire({
            title: 'Başarılı',
            text: 'Ödeme yöntemi değiştirildi',
            icon: 'success',
        });
        lastValue.value = value;
        storeData.value.payment_method = value;
        emit('refresh');
    } catch (error: any) {
        lastValue.value = oldValue;
        paymentMethod.value = oldValue;
        catchFieldError(error);
    }
};

const registerCoupon = async () => {
    if (!storeData.value) return;
    /* ask swal */
    const { value: coupon_code } = await swal.fire({
        title: 'Kupon Kullan',
        input: 'text',
        inputLabel: 'Kupon Kodu',
        inputPlaceholder: 'Kupon Kodu Giriniz',
        showCancelButton: true,
        confirmButtonText: 'Kupon Kullan',
        cancelButtonText: 'İptal',
        inputValidator: (value) => {
            if (!value) {
                return 'Kupon kodu boş olamaz';
            } else {
                return null;
            }
        }
    });

    if (!coupon_code) return;

    try {
        await api.registerCoupon(storeData.value.id, coupon_code);
        swalToast.fire({
            title: 'Başarılı',
            text: 'Kupon başarıyla uygulandı',
            icon: 'success',
        });
        emit('refresh');
    } catch (error: any) {
        catchFieldError(error);
    }
};

watch(() => paymentMethod.value, async (value, oldValue) => {
    console.log(value, oldValue, lastValue.value);
    if (!storeData.value) return;
    if (value === null) {
        paymentMethod.value = oldValue;
        return swalToast.fire({
            title: 'Hata',
            text: 'Ödeme yöntemi seçiniz',
            icon: 'error',
        });
    }
    if (lastValue.value === value) return;
    await changePaymentMethod(value, oldValue);
});
onMounted(() => {
    if (storeData.value) {
        paymentMethod.value = storeData.value.payment_method;

    }
    getAvailableBankDetails();
});
</script>
<template>
    <div class="card" v-if="storeData">
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
                                <li>Sipariş Tarihi:
                                    <span class="font-weight-semibold">
                                        {{ moment(storeData.created_at).format('DD/MM/YYYY HH:mm') }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-lg-flex flex-lg-wrap">
                <PaymentPreviewInvoice />

                <div class="mb-2 ml-auto">
                    <span class="text-muted">Ödeme Detayları:</span>
                    <div class="d-flex flex-wrap wmin-lg-400">
                        <ul class="list list-unstyled mb-0">
                            <li>
                                <h5 class="my-2">Toplam:</h5>
                            </li>
                            <li>Kupon</li>
                            <li>Ödeme Yöntemi:</li>
                        </ul>

                        <ul class="list list-unstyled text-right mb-0 ml-auto">
                            <li>
                                <h5 class="font-weight-semibold my-2">${{ storeData.total }}</h5>
                            </li>
                            <li v-if="storeData.coupon_id">
                                <div class="d-flex justify-content-end cursor-pointer" @click.prevent="registerCoupon">
                                    <div>
                                        <span>{{ storeData.coupon_code }} ({{ storeData.discount_percentage }}%)</span>
                                    </div>
                                    <div class="ml-2">
                                        <i class="fa fa-tag "></i>
                                    </div>
                                </div>
                            </li>
                            <li v-else>
                                <div class="d-flex justify-content-end cursor-pointer" @click.prevent="registerCoupon">
                                    <div>
                                        <span>Kupon Kullan</span>
                                    </div>
                                    <div class="ml-2">
                                        <i class="fa fa-tag "></i>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <!-- select -->
                                <select class="form-control" v-model="paymentMethod"
                                    :disabled="storeData.waiting_transfer_approve">
                                    <option :value="null"> Seçiniz </option>
                                    <option v-for="method in availablePaymentMethods" :key="method.value"
                                        :value="method.value">
                                        {{ method.text }}
                                    </option>
                                </select>
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
                        <th class="text-center">Ürün</th>
                        <th class="text-center">Fiyat</th>
                        <th class="text-center">Miktar</th>
                        <th class="text-center">Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in storeData.lines" :key="item.id">
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

                    <div v-if="bankTransferDetail && paymentMethod == 'BANK_TRANSFER'">
                        <h6 class="mb-3">Banka Hesap Bilgileri</h6>
                        <ul class="list-unstyled text-muted">
                            <li class="text-danger font-weight-bold">3 gün içinde ödenmeyen siparişler iptal edilecektir
                            </li>
                            <li>Banka: {{ bankTransferDetail.bank_name }}</li>
                            <li>Şube: {{ bankTransferDetail.account_name }}</li>
                            <li>IBAN: {{ bankTransferDetail.iban }}</li>
                        </ul>
                    </div>

                    <div v-if="storeData.payment_method == 'BANK_TRANSFER' && storeData.waiting_transfer_approve"
                        class="card bg-light border-secondary text-secondary py-2 px-3 mt-3">
                        <h6 class="mb-3">İletilen Ödeme Bilgileri</h6>
                        <ul class="list-unstyled text-muted">
                            <li>Transfer Tarihi: {{ moment(storeData.transfer_date).format('DD/MM/YYYY') }}</li>
                            <li>Gönderen Adı: {{ storeData.transfer_account_name }}</li>
                            <li>Gönderen Banka Adı: {{ storeData.transfer_bank_name }}</li>
                            <li>Referans Numarası: {{ storeData.transfer_reference_no }}</li>
                        </ul>
                    </div>


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

                    <div class="text-right mt-3">
                        <div v-if="storeData.payment_method == 'CREDIT_CARD'">
                            <button type="button" class="btn btn-primary" @click="emit('purchase')">
                                <i class="fa fa-credit-card"></i>
                                <div class="btn-label">Ödeme Sayfasına Git</div>
                            </button>
                        </div>
                        <div v-else-if="storeData.payment_method == 'BANK_TRANSFER'">
                            <button v-if="!storeData.waiting_transfer_approve" type="button" class="btn btn-success"
                                @click="emit('purchase')">
                                <i class="fa fa-check"></i>
                                <div class="btn-label">Ödeme Bildirimi Yap</div>
                            </button>
                            <button v-else type="button" class="btn btn-warning" disabled
                                @click.prevent="$event.preventDefault()">
                                <i class="fa fa-spinner fa-spin spinner"></i>
                                <div class="btn-label">Sistem Ödeme Durumunu Kontrol Ediyor</div>
                            </button>
                        </div>
                        <!-- else warning -->
                        <button v-else type="button" class="btn btn-warning" @click.prevent="$event.preventDefault()"
                            disabled>
                            <i class="fas fa-exclamation-triangle"></i>
                            <div class="btn-label">Ödeme Yöntemi Seçiniz</div>
                        </button>

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