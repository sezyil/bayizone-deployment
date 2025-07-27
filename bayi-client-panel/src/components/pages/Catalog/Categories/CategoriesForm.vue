<script setup lang="ts">
import { add, get, update } from '/@src/request/request-category';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError, extractErrors } from '/@src/utils/api/catchFormErrors';
import { ICategoriesForm } from '/@src/shared/form/interface-categories';
import { ICategoriesDataResponse } from '/@src/shared/response/interface-categories-response';
import { useSwal } from '/@src/composables/useSwal';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();
const props = defineProps({
    param_id: {
        type: Number,
        default: 0,
        required: false
    }
});

const disabledOptions = ref<number[]>([]);

const formData = ref<ICategoriesForm>({
    name: {
        en: "",
        tr: ""
    },
    parent_id: 0
});

const formErrors = ref<{
    name: {
        en: string[],
        tr: string[]
    },
    parent_id: string[]
}>({
    name: {
        en: [],
        tr: []
    },
    parent_id: []
});

type FormType = keyof ICategoriesForm;

const resetErrors = () => {
    formErrors.value = {
        name: {
            en: [],
            tr: []
        },
        parent_id: []
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
const _redirect = () => router.push('/app/catalog/categories');

const getData = async () => {
    viewWrapper.setLoading(true);

    try {
        const { data } = await get(props.param_id)
        const response = data.data as ICategoriesDataResponse;
        formData.value = {
            parent_id: response.parent_id,
            name: {
                en: response.descriptions_lang.find((item) => item.language === 'en')?.name || "",
                tr: response.descriptions_lang.find((item) => item.language === 'tr')?.name || ""
            }
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
                <InputWithError :errors="formErrors?.parent_id">
                    <label>Üst Kategori:</label>
                    <CategorySelector id="form_parent_id" :propVal="formData.parent_id"
                        :disabled-values="disabledOptions" @option-selected="formData.parent_id = $event"
                        :with-default="false" />
                </InputWithError>
                <InputWithError :errors="formErrors.name?.tr">
                    <label>Kategori İsmi:</label>
                    <InputWithLanguage language="tr">
                        <input type="text" id="form_name" class="form-control" v-model="formData.name.tr" required
                            placeholder="Kategori İsmi">
                    </InputWithLanguage>
                </InputWithError>

                <InputWithError :errors="formErrors.name?.en">
                    <InputWithLanguage language="en">
                        <input type="text" id="form_name" class="form-control" v-model="formData.name.en"
                            placeholder="Category Name">
                    </InputWithLanguage>
                </InputWithError>

                <div class="text-right">
                    <button class="btn btn-bayi-red" type="submit"><i class="fas fa-save mr-1"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</template>
