<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { useOfferRequest } from "/@src/stores/offerRequest";
import { useViewWrapper } from "/@src/stores/viewWrapper";
const viewWrapper = useViewWrapper();
const store = useOfferRequest();
const { t } = useI18n();

viewWrapper.setPageTitle(t("sidemenu.createOfferRequest"));
const sendCheckout = () => {
    store.setStep("checkout");
}

onUnmounted(() => {
    store.$reset();
    store.$dispose();
});
</script>

<template>
    <PageContent>
        <template #header>
            <button class="btn btn-primary" @click="store.setStep('product')" v-if="store.step === 'checkout'">
                <i class="icon-arrow-left8 mr-2"></i>
                {{ t('components.offer_request.create.back') }}
            </button>
            <button class="btn btn-success" @click="sendCheckout" v-if="store.step === 'product'"
                :disabled="!store.selectedProductList.length">
                <i class="fa fa-save mr-2"></i>
                {{ t('sidemenu.createOfferRequest') }}
            </button>
        </template>
        <OfferRequestForm />
    </PageContent>
</template>

<style lang="scss" scoped></style>
