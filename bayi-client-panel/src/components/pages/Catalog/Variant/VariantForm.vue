<script setup lang="ts">
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError, extractErrors } from '/@src/utils/api/catchFormErrors';
import { useSwal } from '/@src/composables/useSwal';
import VariantsApi from '/@src/request/request-variant';
import { IVariantForm, IVariantFormErrors, getAllVariantTypes } from '/@src/shared/form/interface-variant';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();
const api = new VariantsApi();
const props = defineProps({
    param_id: {
        type: String,
        required: false
    }
});
const variantTypeList = getAllVariantTypes();

const formData = ref<IVariantForm>({
    is_active: true,
    is_default: false,
    type: "COLOR",
    names: {
        tr: '',
        en: ''
    }
});

const formErrors = ref<IVariantFormErrors>({
    is_active: [],
    is_default: [],
    names: {
        tr: [],
        en: []
    },
    type: []
});

const getDefaultErrors = () => {
    return {
        is_active: [],
        is_default: [],
        names: {
            tr: [],
            en: []
        },
        type: []
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

const _redirect = () => router.push('/app/catalog/variants');

const getData = async () => {
    if (!props.param_id) return;
    viewWrapper.setLoading(true);
    try {
        const { data } = await api.get(props.param_id, {
            withValues: true
        })
        const response = data.data as IVariantForm;
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
                <InputWithError :errors="formErrors.type">
                    <label>Tür:</label>
                    <select class="form-control" id="form_type" v-model="formData.type">
                        <option v-for="type in variantTypeList" :value="type.key">{{ type.value }}</option>
                    </select>
                </InputWithError>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <InputWithError :errors="formErrors.names.tr">
                            <label>Varyant Adı:</label>
                            <InputWithLanguage language="tr">
                                <input type="text" id="form_name" class="form-control" v-model="formData.names.tr"
                                    placeholder="Varyant Adı">
                            </InputWithLanguage>
                        </InputWithError>
                    </div>
                    <div class="col-12 col-md-6">
                        <InputWithError :errors="formErrors.names.en">
                            <label>Varyant Adı:</label>
                            <InputWithLanguage language="en">
                                <input type="text" id="form_name" class="form-control" v-model="formData.names.en"
                                    placeholder="Varyant Adı">
                            </InputWithLanguage>
                        </InputWithError>
                    </div>
                </div>

                <div class="form-group">
                    <label for="form_is_active">Aktiflik Durumu:</label>
                    <select class="form-control" id="form_is_active" v-model="formData.is_active">
                        <option :value="true">Aktif</option>
                        <option :value="false">Pasif</option>
                    </select>
                </div>


                <div class="text-right">
                    <button class="btn btn-bayi-red" type="submit"><i class="fas fa-save mr-1"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</template>
