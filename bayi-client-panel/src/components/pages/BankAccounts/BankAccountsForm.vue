<script setup lang="ts">
import { add, get, update } from '/@src/request/request-bank-accounts';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { IBankAccountForm, IBankAccountFormErrors } from '/@src/shared/product/interface-bank-accounts';
import InputMask from 'primevue/inputmask';
import { useSwal } from '/@src/composables/useSwal';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();

const props = defineProps({
    bank_account_id: {
        type: String,
    }
})

const formData = ref<IBankAccountForm>({
    bank_name: "",
    branch_name: "",
    account_name: "",
    account_number: "",
    iban: "",
    swift_code: "",
    currency: 'tl',
    status: true
});

const formErrors = ref<IBankAccountFormErrors>({
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

type FormType = keyof IBankAccountFormErrors;

const sendForm = async () => {
    resetErrors();
    viewWrapper.setLoading(true);

    try {
        if (props.bank_account_id) await update(formData.value, props.bank_account_id);
        else await add(formData.value);
        swal.fire('Başarılı', 'Kaydedildi!', 'success').then(() => {
            router.push('/app/bank_accounts/')
        });
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
        const { data } = await get(props.bank_account_id)
        if (data.success) {
            formData.value = data.data;
        }
        viewWrapper.setLoading(false);
    } catch (error) {
        catchFieldError(error, (field: FormType, fieldErr: any) => {
            formErrors.value[field].push(fieldErr);
        })?.then(() => {
            router.push('/app/bank_accounts');
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
                            <!-- required -->
                            <label>Banka Adı <span class="text-danger">*</span></label>
                            <input type="text" v-model="formData.bank_name" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.branch_name">
                            <label>Şube Adı <span class="text-danger">*</span></label>
                            <input type="text" v-model="formData.branch_name" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.account_name">
                            <label>Hesap Adı <span class="text-danger">*</span></label>
                            <input type="text" v-model="formData.account_name" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.account_number">
                            <label>Hesap Numarası <span class="text-danger">*</span></label>
                            <input type="text" v-model="formData.account_number" class="form-control">
                        </InputWithError>
                    </div>
                    <div class="col-md-6">
                        <InputWithError :errors="formErrors.iban">
                            <label>IBAN <span class="text-danger">*</span></label>
                            <InputMask id="basic" v-model="formData.iban" class="form-control"
                                mask="TR99 9999 9999 9999 9999 9999 99"
                                placeholder="TR99 9999 9999 9999 9999 9999 99" />
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