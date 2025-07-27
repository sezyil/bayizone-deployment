<script setup lang="ts">
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError, extractErrors } from '/@src/utils/api/catchFormErrors';
import { useSwal } from '/@src/composables/useSwal';
import { IVariantValueForm, IVariantValueFormErrors, getAllVariantValueTypes } from '/@src/shared/form/interface-variant-value';
import VariantValuesApi from '/@src/request/request-variant-values';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();
const api = new VariantValuesApi();
const props = defineProps({
    param_id: {
        type: String,
        required: false
    }
});
const variantTypeList = getAllVariantValueTypes();

const formData = ref<IVariantValueForm>({
    is_active: true,
    is_default: false,
    variant_type: "COLOR",
    names: {
        tr: '',
        en: ''
    }
});

const formErrors = ref<IVariantValueFormErrors>({
    is_active: [],
    is_default: [],
    names: {
        tr: [],
        en: []
    },
    variant_type: []
});

const getDefaultErrors = () => {
    return {
        is_active: [],
        is_default: [],
        names: {
            tr: [],
            en: []
        },
        variant_type: []
    }
}

const resetErrors = () => {
    formErrors.value = {
        ...getDefaultErrors()
    }
}

const sendForm = async () => {
    viewWrapper.setLoading(true);
    resetErrors();
    try {
        if (props.param_id) await api.update(props.param_id, formData.value);
        else await api.add(formData.value);
        swal.fire('Başarılı', 'İşlem Başarılı', 'success').then(() => {
            _redirect();
        });

    } catch (error) {
        ;
        formErrors.value = {
            ...getDefaultErrors(),
            ...extractErrors(catchFieldError(error))
        }
        /*  catchFieldError(error, (field: keyof IVariantFormErrors, fieldErr: any) => {
             if (field === 'names') {
                 formErrors.value.names.tr.push(fieldErr);
                 formErrors.value.names.en.push(fieldErr);
             } else {
                 formErrors.value[field].push(fieldErr);
             }
 
         }); */
    }

    viewWrapper.setLoading(false);

}

const _redirect = () => router.push('/app/catalog/variant_values');

const getData = async () => {
    if (!props.param_id) return;
    viewWrapper.setLoading(true);
    try {
        const { data } = await api.get(props.param_id)
        const response = data.data as IVariantValueForm;
        formData.value = response;
    } catch (error) {
        catchFieldError(error);
    }

    viewWrapper.setLoading(false);
}

onMounted(() => {
    if (props.param_id)
        getData();
});



</script>
<template>
    <div class="card">
        <div class="card-body">
            <form action="#" autocomplete="off" @submit.prevent="sendForm">
                <InputWithError :errors="formErrors.variant_type">
                    <label>Tür:</label>
                    <select class="form-control" id="form_type" v-model="formData.variant_type">
                        <option v-for="type in variantTypeList" :value="type.key">{{ type.value }}</option>
                    </select>
                    <small class="form-text text-muted">Şimdilik sadece renk değeri desteklenmektedir.</small>
                </InputWithError>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <InputWithError :errors="formErrors.names.tr">
                            <label>Değer Adı:</label>
                            <InputWithLanguage language="tr">
                                <input type="text" id="form_name" class="form-control" v-model="formData.names.tr"
                                    placeholder="Değer Adı">
                            </InputWithLanguage>
                        </InputWithError>
                    </div>
                    <div class="col-12 col-md-6">
                        <InputWithError :errors="formErrors.names.en">
                            <label>Değer Adı:</label>
                            <InputWithLanguage language="en">
                                <input type="text" id="form_name" class="form-control" v-model="formData.names.en"
                                    placeholder="Değer Adı">
                            </InputWithLanguage>
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
