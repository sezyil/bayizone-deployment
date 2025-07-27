<script setup lang="ts">
const curDate = new Date().getFullYear();
//@ts-ignore
const version = __APP_VERSION__; //Vite injects the version number
</script>
<template>
  <div class="navbar navbar-expand-lg navbar-light">
    <span class="navbar-text">
      &copy; 2023 - {{ curDate }} <a href="https://bayizone.com" target="_blank">Bayizone</a> <strong>v{{ version }}</strong>
      <br />
      <!-- subtext hizmet fiyatları kdv hariçtir -->
      <small class="text-muted">Hizmet fiyatları KDV hariçtir.</small>
    </span>
    <ul class="navbar-nav ml-lg-auto flex-row">
      <!-- Facebook -->
      <li class="nav-item">
        <a href="https://www.facebook.com/bayizone" class="nav-link" target="_blank">
          <i class="fab fa-facebook"></i>
        </a>
      </li>
      <!-- Instagram -->
      <li class="nav-item">
        <a href="https://www.instagram.com/bayizone" class="nav-link" target="_blank">
          <i class="fab fa-instagram"></i>
        </a>
      </li>
      <!-- Linkedin -->
      <li class="nav-item">
        <a href="https://www.linkedin.com/company/bayizone" class="nav-link" target="_blank">
          <i class="fab fa-linkedin"></i>
        </a>
      </li>
    </ul>
  </div>
</template>