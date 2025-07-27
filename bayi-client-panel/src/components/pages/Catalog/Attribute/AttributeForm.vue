<script setup lang="ts">
import { add, update, get } from '/@src/request/request-attribute';
import { IAttributeForm, IAttributeFormErrors } from '/@src/shared/form/interface-attribute';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError, extractErrors } from '/@src/utils/api/catchFormErrors';
import { IAttributeDataResponse } from '/@src/shared/response/interface-attribute-response';
import { useSwal } from '/@src/composables/useSwal';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();

const disabledOptions = ref<number[]>([]);

const props = defineProps({
    param_id: {
        type: Number,
        default: 0,
        required: false
    }
})

const formData = ref<IAttributeForm>({
    attribute_group_id: 0,
    name: {
        en: '',
        tr: ''
    }
});

const formErrors = ref<IAttributeFormErrors>({
    attribute_group_id: [],
    name: {
        en: [],
        tr: []
    }
});

type FormType = keyof IAttributeForm;

const resetErrors = () => {
    formErrors.value.attribute_group_id = [];
    formErrors.value.name = {
        en: [],
        tr: []
    }
}

const sendForm = async () => {
    viewWrapper.setLoading(true);
    resetErrors();
    try {
        if (props.param_id) await update(formData.value, props.param_id);
        else await add(formData.value);
        swal.fire('Başarılı', 'İşlem Başarılı', 'success').then(() => {
            _redirect();
        });
    } catch (error) {
        formErrors.value = extractErrors(catchFieldError(error));
    }
    viewWrapper.setLoading(false);
}

const _redirect = () => router.push('/app/catalog/attributes');

const getData = async () => {
    viewWrapper.setLoading(true);

    try {
        const { data } = await get(props.param_id)
        const response = data.data as IAttributeDataResponse;
        formData.value = {
            attribute_group_id: response.attribute_group_id,
            name: {
                en: response.descriptions.find((desc) => desc.language === 'en')?.name || '',
                tr: response.descriptions.find((desc) => desc.language === 'tr')?.name || ''
            }
        }
    } catch (error) {
        swal.fire('Hata', 'Veri alınamadı', 'error').then(() => {
            _redirect();
        });
    }

    viewWrapper.setLoading(false);
}

onMounted(async () => {
    if (props.param_id)
        await getData();
});


</script>
<template>
    <div class="card">
        <div class="card-body">
            <form action="#" autocomplete="off" @submit.prevent="sendForm">
                <div class="form-group">
                    <label>Özellik Grubu:</label>
                    <AttributeGroupSelector id="form_attribute_group_id" :propVal="formData.attribute_group_id"
                        :disabled-values="disabledOptions" @option-selected="formData.attribute_group_id = $event"
                        :with-default="false" />
                </div>
                <InputWithError :errors="formErrors.name?.tr">
                    <label>Özellik Grubu:</label>
                    <InputWithLanguage language="tr">
                        <input type="text" id="form_name" class="form-control" v-model="formData.name.tr" required
                            placeholder="Özellik İsmi">
                    </InputWithLanguage>
                </InputWithError>

                <InputWithError :errors="formErrors.name?.en">
                    <InputWithLanguage language="en">
                        <input type="text" id="form_name" class="form-control" v-model="formData.name.en"
                            placeholder="Attribute Name" required>
                    </InputWithLanguage>
                </InputWithError>
                <div class="text-right">
                    <button class="btn btn-bayi-red" type="submit"><i class="fas fa-save mr-1"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</template>