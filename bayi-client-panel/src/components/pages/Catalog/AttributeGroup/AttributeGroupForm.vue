<script setup lang="ts">
import { add, get, update } from '/@src/request/request-attribute-group';
import { catchFieldError, extractErrors } from '/@src/utils/api/catchFormErrors';
import { InterfaceAttributeGroupForm, InterfaceAttributeGroupFormErrors } from '/@src/shared/form/interface-attribute-group';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { useSwal } from '/@src/composables/useSwal';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal=useSwal();

const props = defineProps({
    param_id: {
        type: Number,
        default: 0,
        required: false
    }
})

const formData = ref<InterfaceAttributeGroupForm>({
    name: {
        en: '',
        tr: ''
    }
});
const formErrors = ref<InterfaceAttributeGroupFormErrors>({
    name: {
        en: [],
        tr: []
    }
})
const resetErrors = () => {
    formErrors.value = {
        name: {
            en: [],
            tr: []
        }
    }
}

type FormType = keyof InterfaceAttributeGroupFormErrors;


const sendForm = async () => {
    viewWrapper.setLoading(true);
    resetErrors();

    try {
        if (props.param_id) await update(formData.value, props.param_id);
        else await add(formData.value);
        swal.fire('Başarılı', 'Kaydedildi!', 'success').then(() => {
            router.push('/app/catalog/attribute_groups');
        });
    } catch (err: any) {
        formErrors.value = extractErrors(catchFieldError(err));
    }
    viewWrapper.setLoading(false);
}

const getData = async () => {
    viewWrapper.setLoading(true);
    try {
        const { data } = await get(props.param_id)
        if (data.success) {
            formData.value = data.data;
        }
    } catch (error) {
        formErrors.value = extractErrors(catchFieldError(error));
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
                <InputWithError :errors="formErrors.name?.tr">
                    <label>Özellik Grubu:</label>
                    <InputWithLanguage language="tr">
                        <input type="text" id="form_name" class="form-control" v-model="formData.name.tr" required
                            placeholder="Özellik Grubu İsmi">
                    </InputWithLanguage>
                </InputWithError>

                <InputWithError :errors="formErrors.name?.en">
                    <InputWithLanguage language="en">
                        <input type="text" id="form_name" class="form-control" v-model="formData.name.en"
                            placeholder="Attribute Group Name" required>
                    </InputWithLanguage>
                </InputWithError>
                <div class="text-right">
                    <button class="btn btn-bayi-red" type="submit"><i class="fas fa-save mr-1"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</template>