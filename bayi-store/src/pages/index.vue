<script setup lang="ts">
import { useUserSession } from '/@src/stores/userSession'
const router = useRouter()
onBeforeMount(() => {
    location.href = 'https://bayizone.com'
})

</script>

<template>
    <div></div>
</template>
