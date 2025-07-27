<script setup lang="ts">
</script>
<template>
    <div class="page-content">
        <div class="content d-flex justify-content-center align-items-center">

            <!-- Container -->
            <div class="flex-fill">

                <!-- Error title -->
                <div class="text-center mb-4">
                    <img src="/images/error_bg.svg" class="img-fluid mb-3" height="230" alt="">
                    <h1 class="display-2 font-weight-semibold line-height-1 mb-2">404</h1>
                    <h6 class="w-md-25 mx-md-auto">Bir hata oluştu. <br> İstenen kaynak bu sunucuda bulunamadı.</h6>
                </div>
                <!-- /error title -->


                <!-- Error content -->
                <div class="text-center">
                    <RouterLink to="/" class="btn btn-primary">Anasayfaya Git</RouterLink>
                </div>
                <!-- /error wrapper -->

            </div>
            <!-- /container -->

        </div>
    </div>
</template>