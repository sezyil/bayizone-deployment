<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const swal = inject('$swal') as SwalInstance;
const router = useRouter();
const viewWrapper = useViewWrapper();
const userStore = useUserSession();

const { t } = useI18n();
viewWrapper.setPageTitle(t('sidemenu.shipments'));

</script>
<template>
    <PageContent>
        <ShipmentIndex />
    </PageContent>
</template>



<style scoped></style>