<script setup lang="ts">
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const userSession = useUserSession();
const router = useRouter();
onBeforeMount(() => {
    if (userSession.isLoggedIn) {
        router.push('/app');
    } else {
        useViewWrapper().setAuthPageMode('login');
    }
});
</script>

<style scoped></style>
<template>
    <LoginForm />
</template>
