<script setup lang="ts">
import { add, get, update } from '/@src/request/request-product-unit';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError, extractErrors } from '/@src/utils/api/catchFormErrors';
import { IProductUnitForm, IProductUnitFormErrors } from '/@src/shared/form/interface-product-unit';
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

const formData = ref<IProductUnitForm>({
    name: {
        en: "",
        tr: ""
    },
    short_name: "",
    is_active: 1
});

const formErrors = ref<IProductUnitFormErrors>({
    name: {
        en: [],
        tr: []
    },
    short_name: [],
    is_active: []
});

type FormType = keyof IProductUnitFormErrors;

const resetErrors = () => {
    formErrors.value = {
        name: {
            en: [],
            tr: []
        },
        short_name: [],
        is_active: []
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

const _redirect = () => router.push('/app/catalog/product_units');

const getData = async () => {
    viewWrapper.setLoading(true);

    try {
        const { data } = await get(props.param_id)
        const response = data.data as IProductUnitForm;
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
                <InputWithError :errors="formErrors.name?.tr">
                    <label>Birim Adı:</label>
                    <InputWithLanguage language="tr">
                        <input type="text" id="form_name" class="form-control" v-model="formData.name.tr" required
                            placeholder="Birim Adı">
                    </InputWithLanguage>
                </InputWithError>

                <InputWithError :errors="formErrors.name?.en">
                    <InputWithLanguage language="en">
                        <input type="text" id="form_name" class="form-control" v-model="formData.name.en"
                            placeholder="Unit Name">
                    </InputWithLanguage>
                </InputWithError>
                <InputWithError :errors="formErrors.short_name">
                    <label>Birim Kısa Adı:</label>
                    <input type="text" id="form_short_name" class="form-control" v-model="formData.short_name" required
                        placeholder="Birim Kısa Adı">
                </InputWithError>

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