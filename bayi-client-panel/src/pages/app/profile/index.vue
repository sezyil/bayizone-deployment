<script setup lang="ts">
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Şirket Bilgileri');

</script>

<template>
    <PageContent>
        <ProfileIndex />
    </PageContent>
</template>