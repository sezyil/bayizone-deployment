<script setup lang="ts">
import { list } from '/@src/request/request-bank-accounts';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useOfferFormStore } from '/@src/stores/offerFormStore';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { IRequestBankAccountListQuery } from '/@src/request/request-bank-accounts';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { IBankAccountListResponse } from '/@src/shared/response/interface-bank-accounts-response';
import { useUserPermission } from '/@src/composables/useUserPermission';
import InputMask from 'primevue/inputmask';
import { useSwal } from '/@src/composables/useSwal';
const bankPermission = useUserPermission().getByName('customer_bank_account');
const viewWrapper = useViewWrapper();
const swal = useSwal();
const router = useRouter();

const store = useOfferFormStore();
const dataList = ref<IBankAccountListResponse[]>([]);

const resetPaymentStore = () => {
    store.offerForm.detail.payment_bank_name = '';
    store.offerForm.detail.payment_branch_name = '';
    store.offerForm.detail.payment_account_name = '';
    store.offerForm.detail.payment_account_number = '';
    store.offerForm.detail.payment_iban = '';
    store.offerForm.detail.payment_swift_code = '';
}

const getData = async (currency?: string) => {
    viewWrapper.setLoading(true);
    try {
        if (!currency) return;
        const query: IRequestBankAccountListQuery = {
            currency_code: currency
        }
        const { data } = await list(query)
        dataList.value = data.data as IBankAccountListResponse[];
    } catch (error) {
        catchFieldError(error)
    } finally {
        viewWrapper.setLoading(false);
    }
}

const handleChange = (e: Event) => {
    const target = e.target as HTMLSelectElement;
    const value = target.value;
    if (!value) return resetPaymentStore();
    const data = dataList.value.find(item => item.id === value);
    if (!data) return resetPaymentStore();
    store.offerForm.detail.payment_bank_name = data.bank_name;
    store.offerForm.detail.payment_branch_name = data.branch_name;
    store.offerForm.detail.payment_account_name = data.account_name;
    store.offerForm.detail.payment_account_number = data.account_number;
    store.offerForm.detail.payment_iban = data.iban;
    store.offerForm.detail.payment_swift_code = data.swift_code;
}

const paymentId = computed(() => dataList.value.find(item => item.bank_name === store.offerForm.detail.payment_bank_name)?.id ?? '');
//Bu alan sadece otomatik doldurma için kullanılacak
const bankAccountTooltip = "Banka hesabınızı seçtikten sonra ilgili alanlar otomatik doldurulacaktır. İlgili alanları değiştirmek isterseniz manuel olarak değiştirebilirsiniz."

const generateItemText = (item: IBankAccountListResponse) => {
    return `${item.bank_name}/${item.branch_name}/${item.currency_name} - ${item.account_name} - ${item.account_number}`
}

//watch store detail currency
watch(() => store.offerForm.detail.currency, (value) => {
    getData(value);
})

onMounted(() => {
    getData(store.offerForm.detail.currency);
    //@ts-ignore
    $('[data-bs-toggle="tooltip"]').tooltip();
});

onUnmounted(() => {
    //@ts-ignore
    $('[data-bs-toggle="tooltip"]').tooltip('dispose');
})
</script>
<template>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Ödeme Bilgileri</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <!-- currency -->
                    <VInputCurrencies v-model="store.offerForm.detail.currency"
                        :errors="store.formErrors.detail.currency">
                        <small class="text-info">Seçilen kura göre banka hesabı listelenmektedir.</small>
                    </VInputCurrencies>
                </div>
                <div class="col-md-6">
                    <label>Banka Hesabı <i class="fas fa-info-circle text-info" data-bs-toggle="tooltip"
                            data-bs-placement="top" :title="bankAccountTooltip"></i></label>
                    <select class="form-control" @change="handleChange" :value="paymentId">
                        <option value="">Banka Hesabı Seçiniz</option>
                        <option v-for="item in dataList" :value="item.id">{{ generateItemText(item) }}</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <InputWithError :errors="store.formErrors.detail.payment_bank_name">
                        <label>Banka Adı <span class="text-danger">*</span></label>
                        <input type="text" v-model="store.offerForm.detail.payment_bank_name" class="form-control">
                    </InputWithError>
                </div>
                <div class="col-md-6">
                    <InputWithError :errors="store.formErrors.detail.payment_branch_name">
                        <label>Şube Adı <span class="text-danger">*</span></label>
                        <input type="text" v-model="store.offerForm.detail.payment_branch_name" class="form-control">
                    </InputWithError>
                </div>
                <div class="col-md-6">
                    <InputWithError :errors="store.formErrors.detail.payment_account_name">
                        <label>Hesap Adı <span class="text-danger">*</span></label>
                        <input type="text" v-model="store.offerForm.detail.payment_account_name" class="form-control">
                    </InputWithError>
                </div>
                <div class="col-md-6">
                    <InputWithError :errors="store.formErrors.detail.payment_account_number">
                        <label>Hesap Numarası <span class="text-danger">*</span></label>
                        <input type="text" v-model="store.offerForm.detail.payment_account_number" class="form-control">
                    </InputWithError>
                </div>
                <div class="col-md-6">
                    <InputWithError :errors="store.formErrors.detail.payment_iban">
                        <label>IBAN <span class="text-danger">*</span></label>
                        <InputMask id="basic" v-model="store.offerForm.detail.payment_iban" class="form-control"
                            mask="TR99 9999 9999 9999 9999 9999 99" placeholder="TR99 9999 9999 9999 9999 9999 99" />
                        <!-- <input type="text" v-model="store.offerForm.detail.payment_iban" class="form-control"> -->
                    </InputWithError>
                </div>
                <div class="col-md-6">
                    <InputWithError :errors="store.formErrors.detail.payment_swift_code">
                        <label>SWIFT Kodu</label>
                        <input type="text" v-model="store.offerForm.detail.payment_swift_code" class="form-control">
                    </InputWithError>
                </div>
            </div>
        </div>
    </div>
</template>