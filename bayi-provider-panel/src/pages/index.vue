<script setup lang="ts">
import { useUserSession } from '/@src/stores/userSession'
const router = useRouter()
const userSession = useUserSession()
if (userSession.isLoggedIn) {
  router.push('/app')
} else {
  router.push('/auth')
}


</script>

<template>
  <div class="flex flex-col items-center justify-center h-screen">
    <VLoader :active="true" />
  </div>
</template>
