<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { useViewWrapper } from "/@src/stores/viewWrapper";
const viewWrapper = useViewWrapper();
const { t } = useI18n();
viewWrapper.setPageTitle(t('components.offer_requests.title'));
</script>
<template>
    <PageContent>
        <template #header>
            <RouterLink :to="`/app/offer_requests/create`" class="btn btn-indigo">
                <i class="fas fa-plus mr-2"></i> {{ t('common.new') }}
            </RouterLink>
        </template>
        <OfferRequestIndex />
    </PageContent>
</template>

<style scoped></style>
