<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import PaymentApi, { IRequestPayResponse, IRequestPayDataResponse } from '/@src/request/request-payment';
import { usePaymentStore } from '/@src/stores/paymentStore';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const swal = useSwal();
//get the plan id from the route
const route = useRoute();
const router = useRouter();
const paymentModalActive = ref(false);
const { id } = route.params as { id: string };
//get success query
const { success } = route.query as { success: string };

const viewWrapper = useViewWrapper();
const api = new PaymentApi();
const paymentStore = usePaymentStore();
const transferSubmitBtn = ref<HTMLButtonElement | null>(null);
const getPlanData = async () => {
    viewWrapper.setLoading(true);
    try {
        const { data } = await api.get(id);
        const xData = data.data as IRequestPayDataResponse;
        paymentStore.setOrderData(xData);
    } catch (error) {
        catchFieldError(error);
    } finally {
        viewWrapper.setLoading(false);
    }
};

const storeData = computed(() => paymentStore?.orderData);

const handlePurchase = async () => {
    if (!storeData.value) return;
    if (storeData.value.payment_method === 'BANK_TRANSFER') {
        paymentModalActive.value = true;
    } else {
        await handleCreditCard();
    }
};

const triggerSubmit = () => {
    if (transferSubmitBtn.value) {
        transferSubmitBtn.value.click();
    }
};

const handleBankTransfer = async () => {
    if (!storeData.value)
        return;
    try {
        viewWrapper.setLoading(true);
        const { data } = await api.sendPaymentNotification(id, {
            transfer_date: storeData.value.transfer_date as string,
            transfer_account_name: storeData.value.transfer_account_name as string,
            transfer_bank_name: storeData.value.transfer_bank_name as string,
            transfer_reference_no: storeData.value.transfer_reference_no as string,
        });
        swal.fire({
            title: 'Banka Transferi',
            html: data.msg,
            icon: 'success',
        });
        paymentModalActive.value = false;
        await getPlanData();
    } catch (error) {
        catchFieldError(error);
    } finally {
        viewWrapper.setLoading(false);
    }
};

const handleCreditCard = async () => {
    viewWrapper.setLoading(true);
    try {
        const { data: _data } = await api.purchase(id, paymentStore.getInvoice);
        const { iyzico_redirect, payment_disallowed } = _data.data
        if (!payment_disallowed && iyzico_redirect) {
            swal.fire({
                title: 'Ödeme İşlemi',
                html: 'Ödeme işlemi için yönlendiriliyorsunuz.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            }).then(() => {
                window.location.href = _data.data.iyzico_redirect;
            });
        } else {
            swal.fire({
                title: 'Ödeme İşlemi',
                html: 'Şu anda ödeme sistemi kullanılamıyor. Lütfen daha sonra tekrar deneyiniz.',
                icon: 'error',
            });
        }

    } catch (error) {
        const errs = catchFieldError(error);
        let errMsg = 'Ödeme işlemi sırasında bir hata oluştu.';
        if (errs) {
            //is object
            for (const [key, value] of Object.entries(errs)) {
                errMsg += `<br>${value}`;
            }
        }

        swal.fire({
            title: 'Hata',
            html: errMsg,
            icon: 'error',
        });
    } finally {
        viewWrapper.setLoading(false);
    }
};

onMounted(async () => {
    if (!id) {
        router.push('/app/payments');
    }

    if (success) {
        if (success === '0') {
            swal.fire({
                title: 'Ödeme İşlemi',
                html: 'Ödeme işlemi başarısız oldu. Lütfen tekrar deneyiniz. Eğer sorun devam ederse destek ekibimizle iletişime geçiniz.',
                icon: 'error',
            });
        }
    }

    await getPlanData();
});

//unmount the store data
onUnmounted(() => {
    paymentStore.$reset();
    paymentStore.$dispose();
});
</script>

<template>
    <PageContent>
        <div v-if="storeData">
            <PaymentPreview @purchase="handlePurchase" @refresh="getPlanData" />
            <div v-if="storeData.payment_method === 'BANK_TRANSFER'">
                <VModal :show="paymentModalActive" @close="paymentModalActive = false"
                    title="Banka Transferi Bildirimi">
                    <form @submit.prevent="handleBankTransfer">
                        <!-- ödenmesi gereken tutar -->
                        <div class="form-group">
                            <label for="amount">Ödenmesi Gereken Tutar</label>
                            <input type="text" class="form-control" id="amount" v-model="storeData.converted_total"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="transfer_date">Havale/Eft Gönderim Tarihi</label>
                            <input type="date" class="form-control" id="transfer_date" v-model="storeData.transfer_date"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="transfer_account_name">Havale/Eft Gönderen Adı</label>
                            <input type="text" class="form-control" id="transfer_account_name"
                                v-model="storeData.transfer_account_name" required>
                        </div>
                        <div class="form-group">
                            <label for="transfer_bank_name">Havale/Eft Gönderen Banka Adı</label>
                            <input type="text" class="form-control" id="transfer_bank_name"
                                v-model="storeData.transfer_bank_name" required>
                        </div>
                        <div class="form-group">
                            <label for="transfer_reference_no">Havale/Eft Referans Numarası</label>
                            <input type="text" class="form-control" id="transfer_reference_no"
                                v-model="storeData.transfer_reference_no" required>
                        </div>
                        <button type="submit" class="d-none" ref="transferSubmitBtn">Submit</button>
                    </form>

                    <template #footer>
                        <button type="button" class="btn btn-primary" @click.prevent="triggerSubmit">Gönder</button>
                    </template>
                </VModal>
            </div>
        </div>

    </PageContent>
</template>

<style scoped>
#iyzico-iframe {
    height: 100vh;
    width: 100%;
    border: none;
}
</style>