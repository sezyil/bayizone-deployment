<script setup lang="ts">
import { add, get, update, get_roles } from '/@src/request/request-user';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { IUserForm } from '/@src/shared/form/interface-user';
import { IUserFormExternalResponse, IUserFormExternalRole } from '/@src/shared/response/interface-user-response';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useUserSession } from '/@src/stores/userSession';
import { useSwal } from '/@src/composables/useSwal';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();
const userSession = useUserSession();
const props = defineProps({
    user_id: {
        type: String,
    }
})
const roles = ref<IUserFormExternalRole[]>([]);

const getRoles = async () => {
    try {
        const { data } = await get_roles();
        const response = data.data as IUserFormExternalResponse;
        roles.value = response.roles;
    }
    catch (err) {
        swal.fire('Hata', 'Rol bilgileri alınamadı', 'error');
    }
}

const formData = ref<IUserForm>({
    fullname: "",
    email: "",
    password: "",
    repass: "",
    role_id: "",
    phone: "",
});

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
    //check password and repassword is equal
    if (formData.value.password !== formData.value.repass) {
        formErrors.value.repass.push('Şifreler eşleşmiyor');
        return;
    }

    viewWrapper.setLoading(true);

    try {
        if (props.user_id) await update(formData.value, props.user_id);
        else {
            await add(formData.value)
            userSession.subscriptionUpdater.user.decrement();
        };
        swal.fire('Başarılı', 'Kaydedildi!', 'success').then(() => {
            router.push('/app/users');
        });
    } catch (err: any) {
        console.log(err);
        catchFieldError(err, (field: FormType, fieldErr: any) => {
            formErrors.value[field].push(fieldErr);
        });
    }
    viewWrapper.setLoading(false);
}

const getData = async () => {
    if (!props.user_id) return;
    viewWrapper.setLoading(true);
    try {
        const { data } = await get(props.user_id)
        if (data.success) {
            formData.value = data.data;
        }
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

const showPassword = ref<boolean>(false);

const passwordIconClass = computed(() => {
    let _class = 'fa fa-eye';
    if (showPassword.value) _class = 'fa fa-eye-slash';
    return _class;
});

const editMode = computed(() => typeof props.user_id !== 'undefined' && props.user_id !== '');

onMounted(() => {
    getRoles();
    if (props.user_id)
        getData();
});


</script>
<template>
    <div class="card">
        <div class="card-body">
            <form action="#" autocomplete="off" @submit.prevent="sendForm" autosave="off">
                <InputWithError :errors="formErrors.fullname">
                    <label>Ad-Soyad:</label>
                    <input type="text" class="form-control" v-model="formData.fullname" required placeholder="Ad-Soyad">
                </InputWithError>

                <InputWithError :errors="formErrors.email">
                    <label>E-Mail:</label>
                    <input type="email" class="form-control" v-model="formData.email" required :readonly="editMode"
                        placeholder="E-Mail">
                </InputWithError>

                <InputWithError :errors="formErrors.phone">
                    <label>Telefon:</label>
                    <input type="text" class="form-control" v-model="formData.phone" required placeholder="Telefon">
                </InputWithError>

                <InputWithError :errors="formErrors.role_id">
                    <label>Rol:</label>
                    <select class="custom-select" v-model="formData.role_id">
                        <option value="" disabled>Rol Seçiniz</option>
                        <option :value="item.id" v-for="(item, index) in roles" :key="index">{{ item.name }}</option>
                    </select>
                </InputWithError>

                <!-- password with show eye -->
                <div class="row" v-if="!user_id">
                    <div class="col-md-12">
                        <label>Şifre:</label>
                        <InputWithError :errors="formErrors.password" :icon="passwordIconClass" :clickable="true"
                            type="right-icon" @icon-click="showPassword = !showPassword">
                            <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                v-model="formData.password" required placeholder="Şifre">
                        </InputWithError>
                    </div>
                </div>


                <!-- repassword with eye -->
                <div class="row" v-if="!user_id">
                    <div class="col-md-12">
                        <label>Şifre Tekrar:</label>
                        <InputWithError :errors="formErrors.repass" :icon="passwordIconClass" type="right-icon">
                            <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                v-model="formData.repass" required placeholder="Şifre Tekrar">
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