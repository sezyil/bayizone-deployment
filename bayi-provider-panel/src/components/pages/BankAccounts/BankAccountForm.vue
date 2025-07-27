<script setup lang="ts">
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { SwalInstance } from '/@src/shared/common/type-swal';
import CustomerBankAccountApi from '/@src/request/request-customer-bank-accounts';
import { ICustomerBankAccount, ICustomerBankAccountErrors } from '/@src/shared/form/interface-company-customer-bank';
import { useI18n } from 'vue-i18n';
const viewWrapper = useViewWrapper();
const { t } = useI18n();
const router = useRouter();
const swal = inject('$swal') as SwalInstance;
const props = defineProps({
    bank_account_id: {
        type: String,
    }
})
const apiClass = new CustomerBankAccountApi();

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
const _redir = () => router.push('/app/bank_accounts');
const sendForm = async () => {
    resetErrors();
    viewWrapper.setLoading(true);

    try {
        if (props.bank_account_id) await apiClass.update(formData.value, props.bank_account_id);
        else await apiClass.add(formData.value);
        swal.fire(t('common.success'), t('actions.processSuccess'), 'success').then(() => _redir());
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
                            <label>{{ t('components.bank_accounts.index.bankName') }}</label>
                            <input type="text" v-model="formData.bank_name" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.branch_name">
                            <label>{{ t('components.bank_accounts.index.branchName') }}</label>
                            <input type="text" v-model="formData.branch_name" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.account_name">
                            <label>{{ t('components.bank_accounts.index.accountName') }}</label>
                            <input type="text" v-model="formData.account_name" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.account_number">
                            <label>{{ t('components.bank_accounts.form.accountNumber') }}</label>
                            <input type="text" v-model="formData.account_number" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.iban">
                            <label>{{ t('components.bank_accounts.index.iban') }}</label>
                            <input type="text" v-model="formData.iban" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.swift_code">
                            <label>{{ t('components.bank_accounts.form.swift') }}</label>
                            <input type="text" v-model="formData.swift_code" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <VInputCurrencies :errors="formErrors.currency" v-model="formData.currency" />
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.status">
                            <label>{{ t('common.status') }}</label>
                            <select v-model="formData.status" class="form-control">
                                <option :value="true">{{ t('common.active') }}</option>
                                <option :value="false">{{ t('common.passive') }}</option>
                            </select>
                        </InputWithError>
                    </div>
                </div>


                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        {{ t('actions.save') }} <i class="icon-paperplane ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>