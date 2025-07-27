<script setup lang="ts">
import { swalPermissionDenied, useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { list } from '/@src/request/request-customer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const swal = useSwal();
const router = useRouter();
const viewWrapper = useViewWrapper();
const userStore = useUserSession();

viewWrapper.setPageTitle('Müşteri Teklifleri');
const permission = useUserPermission().getByName('customer_offer');
const customerPermission = useUserPermission().getByName('company_customer');
onBeforeMount(async () => {
    if (!permission.view) {
        await swalPermissionDenied(() => router.push('/app'));
    }
});

const swalCreatePopup = async () => {
    const customers = (await list()).data.data as { id: string, name: string }[];
    const options = customers.map((customer) => customer.name);
    if (options.length === 0) {
        if (!customerPermission.create) {
            errorHandler(
                'Müşteri Bulunamadı',
                'Müşteri bulunamadı.Bu işlemi yapabilmek kayıtlı müşteriniz olması gerekmektedir.Ekleme yetkiniz olmadığı için yöneticiye başvurunuz.',
                '/app/',
                'Anasayfaya Dön'
            );
            return;
        }
        errorHandler('Müşteri Bulunamadı', 'Müşteri bulunamadı.Bu işlemi yapabilmek için lütfen müşteri ekleyiniz.', '/app/customers/create');
        return;
    }

    swal.fire({
        title: 'Müşteri Seç',
        input: 'select',
        inputOptions: options,
        showCancelButton: true,
        inputPlaceholder: 'Müşteri seçin',
        confirmButtonText: 'Seç',
        cancelButtonText: 'Vazgeç',
        inputValidator: (value) => {
            return new Promise((resolve) => {
                if (value !== '') {
                    resolve('');
                } else {
                    resolve('Lütfen bir müşteri seçin!');
                }
            });
        },
    }).then((result) => {
        if (result.isConfirmed) {
            const selectedCustomerId = customers[result.value].id;
            router.push(`/app/offers/create?customer_id=${selectedCustomerId}`);
        }
    });
}

const wizardStatus = computed(() => userStore.wizardStatus);
onMounted(async () => {
    if (permission.view) {
        if (!wizardStatus.value.hasImage) {
            errorHandler('Profil Resmi', 'Profil resminiz bulunmamaktadır.Bu işlemi yapabilmek için lütfen profil resminizi yükleyiniz.', '/app/profile?tab=profile');
        } else if (!wizardStatus.value.companyInfo) {
            errorHandler('Şirket Bilgisi', 'Şirket bilgileriniz bulunmamaktadır.Bu işlemi yapabilmek için lütfen şirket bilgilerinizi giriniz.', '/app/profile?tab=company');
        }
    }
});

const errorHandler = (title: string, text: string, route: string, buttonLabel: string = '') => {
    let confirmButtonText = buttonLabel?.length > 0 ? buttonLabel : 'Sayfaya Git';
    swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: false,
        confirmButtonText: confirmButtonText,
    }).then((result) => {
        router.push(route);
    });
}

</script>
<template>
    <PageContent>
        <template #header v-if="wizardStatus.companyInfo && wizardStatus.hasImage && permission.view">
            <VButtonCreate @click="swalCreatePopup" />
        </template>
        <OfferIndex v-if="wizardStatus.companyInfo && wizardStatus.hasImage && permission.view" />
    </PageContent>
</template>



<style scoped></style>