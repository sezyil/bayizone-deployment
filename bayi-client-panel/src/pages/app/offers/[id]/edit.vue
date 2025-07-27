<script setup lang="ts">
import { swalPermissionDenied, useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import CustomerOfferApi from '/@src/request/request-customer-offer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useOfferFormStore } from '/@src/stores/offerFormStore';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const store = useOfferFormStore();
const viewWrapper = useViewWrapper();
const swal = useSwal();
const triggerBtn = ref<HTMLFormElement>();
const api = new CustomerOfferApi();
const router = useRouter();
viewWrapper.setPageTitle('Proforma Düzenle');

const route = useRoute();

const permission = useUserPermission().getByName('customer_offer');
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'));
}

const { id: offer_id } = route.params as { id: string };

//if param id not string or empty redirect to offer page
if (!offer_id) router.push('/app/offers');


const countLineItems = computed(() => {
    /* count product id not equal to 0 */
    return store.offerForm.lines.filter((line) => line.product_id !== 0).length;
})


const submitForm = async (e: Event) => {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    const errors = [];

    if (!store.offerForm.detail.company_customer_id) errors.push('Müşteri seçimi yapınız.');
    if (countLineItems.value === 0) errors.push('En az bir ürün ekleyiniz.');
    if (!store.offerForm.detail.offer_date) errors.push('Teklif tarihi seçiniz.');
    if (!store.offerForm.detail.offer_due_date) errors.push('Geçerlilik tarihi seçiniz.');



    if (errors.length > 0) {
        swal.fire({
            title: 'Hata',
            html: errors.join('<br>'),
            icon: 'error',
            confirmButtonText: 'Tamam',
        });
        return;
    }
    let customer_id = store.offerForm.detail.company_customer_id;
    api.setCompanyId(customer_id);
    viewWrapper.setLoading(true);
    store.resetFormErrors();
    try {
        let _form = store.sanitizeFormData();
        await api.update(_form, offer_id);
        swal.fire({
            title: 'Başarılı',
            html: 'Proforma başarıyla güncellendi.',
            icon: 'success',
            confirmButtonText: 'Tamam',
        });
        router.push(`/app/offers?customer_id=${customer_id}`);

    } catch (error) {
        const errors = catchFieldError(error);
        store.setErrors(errors);
    }
    viewWrapper.setLoading(false);



}

const triggerSubmit = () => triggerBtn.value?.click();


</script>
<template>
    <PageContent>
        <template #header>
            <!-- save button -->
            <button type="button" class="btn btn-bayi-red" @click="triggerSubmit">
                <i class="fa fa-save"></i>
                Kaydet
            </button>
        </template>

        <form id="offer_form" @submit.prevent="submitForm">
            <!-- hidden btn for click event -->
            <button type="submit" class="d-none" ref="triggerBtn"></button>
            <OfferForm :offer_id="offer_id" />
        </form>
    </PageContent>
</template>

<style scoped></style>