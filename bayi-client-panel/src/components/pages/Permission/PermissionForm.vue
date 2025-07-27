<script setup lang="ts">
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { IUserForm } from '/@src/shared/form/interface-user';
import { SwalInstance } from '/@src/shared/common/type-swal';
import PermissionApi from '/@src/request/request-permission';
import { PermissionType } from '/@src/composables/useUserPermission';
import { useSwal } from '/@src/composables/useSwal';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();
const props = defineProps({
    role_id: {
        type: String,
        required: true,
    }
})

const roleName = ref<string>('');
const tableLabel = computed(() => {
    let label = '';
    if (roleName.value) {
        label = roleName.value + ' | '
    }
    return label + 'Grup Yetkileri Düzenle';
});

interface IPermissionForm {
    name: PermissionName
    permissions: IPermissionItem
}

interface IPermissionItem {
    create?: IPermissionItemDetail
    view: IPermissionItemDetail
    update: IPermissionItemDetail
    delete?: IPermissionItemDetail
}

interface IPermissionItemDetail {
    id: string
    operation_label: string
    operation: keyof PermissionType
    checked: boolean
}




const formData = ref<IPermissionForm[]>([]);

const formErrors = ref<{
    fullname: string[],
    email: string[],
    password: string[],
    repass: string[],
    role_id: string[],
    phone: string[],
}>({
    fullname: [],
    email: [],
    password: [],
    repass: [],
    role_id: [],
    phone: [],
});

const resetErrors = () => {
    formErrors.value = {
        fullname: [],
        email: [],
        password: [],
        repass: [],
        role_id: [],
        phone: [],
    };
}

type FormType = keyof IUserForm;

const sendForm = async () => {
    resetErrors();

    viewWrapper.setLoading(true);

    //swal bu işlem bu gruptaki bütün kullanıcıların oturumunu kapatacaktır devam etmek istediğinize emin misiniz?
    await swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlem bu gruptaki bütün kullanıcıların oturumunu kapatacaktır devam etmek istediğinize emin misiniz?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'Hayır',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await PermissionApi.update(formData.value, props.role_id);

                swal.fire('Başarılı', 'Kaydedildi!', 'success').then(() => {
                    router.push('/app/permissions');
                });
            } catch (err: any) {
                catchFieldError(err, (field: FormType, fieldErr: any) => {
                    formErrors.value[field].push(fieldErr);
                });
            }
        }
    });
    viewWrapper.setLoading(false);
}

const getData = async () => {
    viewWrapper.setLoading(true);
    try {
        const { data } = await PermissionApi.get(props.role_id);
        formData.value = data.data.items as IPermissionForm[];
        roleName.value = data.data.name;
        viewWrapper.setLoading(false);
    } catch (error) {
        catchFieldError(error, (field: FormType, fieldErr: any) => {
            formErrors.value[field].push(fieldErr);
        })?.then(() => {
            router.push('/app/users');
            viewWrapper.setLoading(false);
        });

    }

}


onMounted(() => {
    getData();
});


</script>
<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ tableLabel }}</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Yetki Adı</th>
                        <th>Oluşturma</th>
                        <th>Görüntüleme</th>
                        <th>Güncelleme</th>
                        <th>Silme</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in formData" :key="index">
                        <td>{{ item.name }}</td>
                        <td>
                            <label class="custom-control custom-switch" v-if="item.permissions.create">
                                <input type="checkbox" class="custom-control-input"
                                    v-model="item.permissions.create.checked">
                                <span class="custom-control-label p-0"></span>
                            </label>
                        </td>
                        <td>
                            <label class="custom-control custom-switch" v-if="item.permissions.view">
                                <input type="checkbox" class="custom-control-input"
                                    v-model="item.permissions.view.checked">
                                <span class="custom-control-label p-0"></span>
                            </label>
                        </td>
                        <td>
                            <label class="custom-control custom-switch" v-if="item.permissions.update">
                                <input type="checkbox" class="custom-control-input"
                                    v-model="item.permissions.update.checked">
                                <span class="custom-control-label p-0"></span>
                            </label>
                        </td>
                        <td>
                            <label class="custom-control custom-switch" v-if="item.permissions.delete">
                                <input type="checkbox" class="custom-control-input"
                                    v-model="item.permissions.delete.checked">
                                <span class="custom-control-label p-0"></span>
                            </label>
                        </td>
                    </tr>
                </tbody>
                <!-- save button -->
                <tfoot>
                    <tr>
                        <td colspan="5">
                            <button class="btn btn-bayi-red float-right" @click="sendForm"><i class="fas fa-save"></i>
                                Kaydet
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>