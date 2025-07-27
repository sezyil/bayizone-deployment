<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import { add, update, get } from '/@src/request/request-option';
import { IOptionForm, IOptionFormErrors, IOptionValue } from '/@src/shared/form/interface-options';
import { IOptionsDataResponse } from '/@src/shared/response/interface-options-response';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError, extractErrors } from '/@src/utils/api/catchFormErrors';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();

const props = defineProps({
    param_id: {
        default: 0,
        type: Number,
        required: false
    }
})

const formData = ref<IOptionForm>({
    name: {
        tr: "",
        en: ""
    },
    values: []
});

const formErrors = ref<IOptionFormErrors>({
    name: {
        tr: [],
        en: []
    },
    values: []
})

const addRow = () => {
    let newItem: IOptionValue = {
        id: 0,
        name: {
            tr: "",
            en: ""
        }
    };
    formData.value.values.push(newItem)
};
const removeRow = (row_index: any) => {
    formData.value.values.splice(row_index, 1);
};

const resetErrors = () => {
    formErrors.value = {
        name: {
            tr: [],
            en: []
        },
        values: []
    }
}

type FormType = keyof IOptionForm;

const sendForm = async () => {
    viewWrapper.setLoading(true)
    resetErrors();
    try {
        if (props.param_id) await update(formData.value, props.param_id)
        else await add(formData.value)
        swal.fire('Başarılı', 'İşlem Başarılı!', 'success').then(() => {
            _redirect();
        });
    } catch (error) {
        formErrors.value = extractErrors(catchFieldError(error));
    }
    viewWrapper.setLoading(false)
}

const _redirect = () => router.push('/app/catalog/options');

const getData = async () => {
    viewWrapper.setLoading(true)

    try {
        const { data } = await get(props.param_id);
        const res = data.data as IOptionsDataResponse
        formData.value.name = {
            tr: res.descriptions.find(e => e.language === 'tr')?.name || "",
            en: res.descriptions.find(e => e.language === 'en')?.name || ""
        }
        formData.value.values = res.option_value.map(e => {
            return {
                id: e.id,
                name: {
                    tr: e.descriptions.find(d => d.language === 'tr')?.name || "",
                    en: e.descriptions.find(d => d.language === 'en')?.name || ""
                }
            }
        })
    } catch (error) {
        let msg = "";
        catchFieldError(error, (field: FormType, fieldErr: any) => {
            msg += fieldErr + "\n";
        });
        swal.fire('Hata', msg, 'error');
    }

    viewWrapper.setLoading(false)
}
onMounted(() => {
    if (props.param_id)
        getData();
});


</script>

<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="#" autocomplete="off" @submit.prevent="sendForm">
                        <InputWithError :errors="formErrors.name?.tr">
                            <label>Seçenek Adı:</label>
                            <InputWithLanguage language="tr">
                                <input type="text" id="form_name" class="form-control" v-model="formData.name.tr"
                                    required placeholder="Seçenek Adı">
                            </InputWithLanguage>
                        </InputWithError>

                        <InputWithError :errors="formErrors.name?.en">
                            <InputWithLanguage language="en">
                                <input type="text" id="form_name" class="form-control" v-model="formData.name.en"
                                    placeholder="Option Name" required>
                            </InputWithLanguage>
                        </InputWithError>
                        <div class="table-responsive">
                            <table id="option-value" class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td class="text-left required">Seçenek Değer Adı</td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="formData.values.length" v-for="(item, index) in formData.values"
                                        :key="index">
                                        <td class="text-left">
                                            <InputWithLanguage language="tr">
                                                <input type="text" class="form-control" v-model="item.name.tr" required>
                                            </InputWithLanguage>
                                            <InputWithLanguage language="en">
                                                <input type="text" class="form-control" v-model="item.name.en" required>
                                            </InputWithLanguage>

                                        </td>
                                        <td class="text-center">
                                            <button type="button" @click.prevent="removeRow(index)" title="Kaldır"
                                                class="btn btn-danger">
                                                <i class="fa fa-minus-circle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="2" class="text-center">Seçenek Değeri Yok</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="1"></td>
                                        <td class="text-center">
                                            <button type="button" @click.prevent="addRow" data-toggle="tooltip" title=""
                                                class="btn btn-primary" data-original-title="Değer Ekle">
                                                <i class="fa fa-plus-circle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="text-right mt-2">
                            <button class="btn btn-bayi-red" type="submit"><i class="fas fa-save mr-1"></i>
                                Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>