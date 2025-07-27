<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useSwal } from '/@src/composables/useSwal';
import { useShopStore } from '/@src/stores/shopStore';
import ApiStore, { IStoreOfferFormProduct, IStoreOfferFormProductVariant } from '/@src/request/api-store';
import { catchFieldError, extractErrors } from '/@src/utils/api/catchFormErrors';
const swal = useSwal({ isToast: true, timer: 2000 });
const store = useShopStore();
const { t } = useI18n();
const isLoading = ref(false);
const submitButton = ref<HTMLButtonElement | null>(null);

const onSubmit = async (e: Event) => {
    if (!store.cartList.length) {
        swal.fire({
            title: t('common.hasError'),
            text: t('components.cart.errors.cart'),
            icon: 'error'
        });
        return;
    }
    isLoading.value = true;
    try {
        const api = store.getStoreApi() as ApiStore;
        let items: IStoreOfferFormProduct[] = [];
        store.cartList.forEach((item) => {
            //check all id
            if (item.id) {
                items.push({
                    id: item.id,
                    uuid: item.uuid,
                    quantity: item.quantity,
                    color: item.selected_color,
                    dimension: item.selected_dimension
                });
            }

        });
        await api.createOffer({
            cart: items,
            customer: store.customerInfo
        });
        store.resetForm();
        store.getCurrentCart();
        store.setModalActive(false);
        useSwal({ isToast: true, timer: 5000 }).fire({
            title: t('common.success'),
            text: t('components.cart.form.success'),
            icon: 'success',
        });
    } catch (error) {
        store.setFormErrors(extractErrors(catchFieldError(error)))
    } finally {
        isLoading.value = false;
    }

}

const triggerClick = () => {
    if (submitButton.value) {
        submitButton.value.click();
    }
}



watch(() => store.toastData.active, (value) => {
    if (value) {
        const toastData = store.toastData;
        if (toastData.type === 'added') {
            swal.fire({ title: t('components.cart.notify.add'), icon: 'success' })
        } else if (toastData.type === 'removed') {
            swal.fire({ title: t('components.cart.notify.remove'), icon: 'warning' })
        } else if (toastData.type === 'cleared') {
            swal.fire({ title: t('components.cart.notify.clear'), icon: 'info' })
        }

        store.triggerToastData.reset();
    }
}, {
    deep: true
})

</script>
<template>

    <VModal :modal-title="t('common.cart')" :active="store.modalActive" @close="store.setModalActive(false)"
        :wrapperOverrided="true">
        <template #default>
            <!-- customer info -->
            <form @submit.prevent="onSubmit"
                class="flex flex-col sm:flex-col md:flex-col lg:flex-row xl:flex-row 2xl:flex-row">
                <div class="w-full lg:w-1/2 p-3 mb-3">
                    <CartModalCustomerInfo />
                </div>
                <!-- product card list with image  -->
                <div class="w-full lg:w-1/2 p-3">
                    <CartModalItemList />
                </div>
                <button type="submit" class="hidden" ref="submitButton" />
            </form>
        </template>


        <template #footer>
            <div class="p-3 text-center flex flex-row items-center justify-end gap-3">
                <VButton @click="triggerClick" :loading="isLoading" :disabled="!store.cartList.length"
                    class="md:mb-0 bg-slate-600 border border-slate-600 px-5 py-2 text-sm shadow-sm tracking-wider text-white rounded-md hover:shadow- hover:bg-slate-700 flex items-center justify-center self-center">
                    <span class="pi pi-check mr-2"></span>
                    {{ t('common.create') }}
                </VButton>
            </div>
        </template>
    </VModal>

</template>



<style scoped></style>