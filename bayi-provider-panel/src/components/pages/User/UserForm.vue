<script setup lang="ts">
import { add, get, update } from '/@src/request/request-user';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { IUserForm } from '/@src/shared/form/interface-user';
import { useSwal } from '/@src/composables/useSwal';
import { useI18n } from 'vue-i18n';
const viewWrapper = useViewWrapper();
const router = useRouter();
const swal = useSwal();
const { t } = useI18n()

const props = defineProps({
    user_id: {
        type: String,
    }
})

const formData = ref<IUserForm>({
    fullname: "",
    email: "",
    password: "",
    repass: "",
    phone: "",
});

const formErrors = ref<{
    fullname: string[],
    email: string[],
    password: string[],
    repass: string[],
    phone: string[],
}>({
    fullname: [],
    email: [],
    password: [],
    repass: [],
    phone: [],
});

const resetErrors = () => {
    formErrors.value = {
        fullname: [],
        email: [],
        password: [],
        repass: [],
        phone: [],
    };
}

type FormType = keyof IUserForm;

const sendForm = async () => {
    resetErrors();
    //check password and repassword is equal
    if (formData.value.password !== formData.value.repass) {
        formErrors.value.repass.push(t('components.users.form.passwordNotEqual'));
        return;
    }

    viewWrapper.setLoading(true);

    try {
        if (props.user_id) await update(formData.value, props.user_id);
        else await add(formData.value);
        swal.fire(t('common.success'), t('actions.processSuccess'), 'success').then(() => {
            router.push('/app/users');
        });
    } catch (err: any) {
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
    if (props.user_id)
        getData();
});


</script>
<template>
    <div class="card">
        <div class="card-body">
            <form action="#" autocomplete="off" @submit.prevent="sendForm" autosave="off">
                <InputWithError :errors="formErrors.fullname">
                    <label>{{ t('components.users.form.name') }}</label>
                    <input type="text" class="form-control" v-model="formData.fullname" required
                        :placeholder="t('components.users.form.name')">
                </InputWithError>

                <InputWithError :errors="formErrors.email">
                    <label>{{ t('components.users.form.email') }}</label>
                    <input type="email" class="form-control" v-model="formData.email" required :readonly="editMode"
                        :placeholder="t('components.users.form.email')">
                </InputWithError>

                <InputWithError :errors="formErrors.phone">
                    <label>{{ t('components.users.form.phone') }}:</label>
                    <input type="text" class="form-control" v-model="formData.phone" required
                        :placeholder="t('components.users.form.phone')">
                </InputWithError>

                <!-- password with show eye -->
                <div class="row" v-if="!user_id">
                    <div class="col-md-12">
                        <label>{{ t('components.users.form.password') }}:</label>
                        <InputWithError :errors="formErrors.password" :icon="passwordIconClass" :clickable="true"
                            type="right-icon" @icon-click="showPassword = !showPassword">
                            <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                v-model="formData.password" required :placeholder="t('components.users.form.password')">
                        </InputWithError>
                    </div>
                </div>


                <!-- repassword with eye -->
                <div class="row" v-if="!user_id">
                    <div class="col-md-12">
                        <label>{{ t('components.users.form.repass') }}:</label>
                        <InputWithError :errors="formErrors.repass" :icon="passwordIconClass" type="right-icon">
                            <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                v-model="formData.repass" required :placeholder="t('components.users.form.repass')">
                        </InputWithError>
                    </div>
                </div>


                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        {{ t('actions.save') }} <i class="icon-paperplane ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>