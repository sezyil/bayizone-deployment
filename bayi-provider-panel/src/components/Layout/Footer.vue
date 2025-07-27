<script setup lang="ts">
const curDate = new Date().getFullYear();
//@ts-ignore
const version = __APP_VERSION__; //Vite injects the version number
</script>
<template>
  <div class="navbar navbar-expand-lg navbar-light">
    <span class="navbar-text">
      &copy; 2023 - {{ curDate }} <a href="#">Bayizone</a> <strong>v{{ version }}</strong>
    </span>
  </div>
</template>