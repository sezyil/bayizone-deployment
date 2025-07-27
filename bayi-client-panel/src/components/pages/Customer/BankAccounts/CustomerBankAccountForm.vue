<script setup lang="ts">
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { SwalInstance } from '/@src/shared/common/type-swal';
import CustomerBankAccountApi from '/@src/request/request-customer-bank-accounts';
import { ICustomerBankAccount, ICustomerBankAccountErrors } from '/@src/shared/form/interface-company-customer-bank';
import { useSwal } from '/@src/composables/useSwal';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();
const props = defineProps({
    customer_id: {
        type: String,
        required: true
    },
    bank_account_id: {
        type: String,
    }
})
const apiClass = new CustomerBankAccountApi(props.customer_id ?? undefined);

const formData = ref<ICustomerBankAccount>({
    bank_name: "",
    branch_name: "",
    account_name: "",
    account_number: "",
    iban: "",
    swift_code: "",
    currency: 'tl',
    status: true
});

const formErrors = ref<ICustomerBankAccountErrors>({
    bank_name: [],
    branch_name: [],
    account_name: [],
    account_number: [],
    iban: [],
    swift_code: [],
    currency: [],
    status: [],
});

const resetErrors = () => {
    formErrors.value = {
        bank_name: [],
        branch_name: [],
        account_name: [],
        account_number: [],
        iban: [],
        swift_code: [],
        currency: [],
        status: [],
    };
}

type FormType = keyof ICustomerBankAccountErrors;
const _redir = () => router.push('/app/customers/:id?tab=bankAccounts'.replace(':id', props.customer_id ?? ''))
const sendForm = async () => {
    resetErrors();
    viewWrapper.setLoading(true);

    try {
        if (props.bank_account_id) await apiClass.update(formData.value, props.bank_account_id);
        else await apiClass.add(formData.value);
        swal.fire('Başarılı', 'Kaydedildi!', 'success').then(() => _redir());
    } catch (err: any) {
        catchFieldError(err, (field: FormType, fieldErr: any) => {
            formErrors.value[field].push(fieldErr);
        });
    }
    viewWrapper.setLoading(false);
}

const getData = async () => {
    if (!props.bank_account_id) return;
    viewWrapper.setLoading(true);
    try {
        const { data } = await apiClass.get(props.bank_account_id)
        formData.value = data.data as ICustomerBankAccount;
        viewWrapper.setPageTitle(`${formData.value.account_name} - ${formData.value.bank_name} | Banka Bilgileri`);
        viewWrapper.setLoading(false);
    } catch (error) {
        catchFieldError(error, (field: FormType, fieldErr: any) => {
            formErrors.value[field].push(fieldErr);
        })?.then(() => {
            _redir();
            viewWrapper.setLoading(false);
        });

    }

}


onMounted(() => {
    if (props.bank_account_id) getData();
});


</script>
<template>
    <div class="card">
        <div class="card-body">
            <form action="#" autocomplete="off" @submit.prevent="sendForm" autosave="off">
                <div class="row">
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.bank_name">
                            <label>Banka Adı</label>
                            <input type="text" v-model="formData.bank_name" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.branch_name">
                            <label>Şube Adı</label>
                            <input type="text" v-model="formData.branch_name" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.account_name">
                            <label>Hesap Adı</label>
                            <input type="text" v-model="formData.account_name" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.account_number">
                            <label>Hesap Numarası</label>
                            <input type="text" v-model="formData.account_number" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.iban">
                            <label>IBAN</label>
                            <input type="text" v-model="formData.iban" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.swift_code">
                            <label>SWIFT Kodu</label>
                            <input type="text" v-model="formData.swift_code" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <VInputCurrencies :errors="formErrors.currency" v-model="formData.currency" />
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.status">
                            <label>Durum</label>
                            <select v-model="formData.status" class="form-control">
                                <option :value="true">Aktif</option>
                                <option :value="false">Pasif</option>
                            </select>
                        </InputWithError>
                    </div>
                </div>


                <div class="text-right">
                    <button class="btn btn-bayi-red" type="submit"><i class="fas fa-save mr-1"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</template>