
<script setup lang="ts">
interface IWizardStatus {
    hasAddress: boolean;
    hasImage: boolean;
}
const props = withDefaults(defineProps<IWizardStatus>(), {
    hasAddress: false,
    hasImage: false,
});
</script>
<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card border-top-warning border-bottom-warning rounded-0">
                <div class="card-body">
                    <div class="wizard-stepper">
                        <!-- Company Info -->
                        <div class="alert alert-warning bg-white alert-styled-left" v-if="!hasAddress">
                            <span class="font-weight-semibold">Şirket Bilgileri</span> bulunmamaktadır.Panelde işlemlerinizi
                            yapabilmek için
                            <RouterLink to="/app/profile?tab=company">şirket bilgilerinizi giriniz.</RouterLink>
                        </div>
                        <!-- Company Image -->
                        <div class="alert alert-warning bg-white alert-styled-left" v-if="!hasImage">
                            <span class="font-weight-semibold">Şirket logosu</span> bulunmamaktadır.<RouterLink
                                to="/app/profile?tab=profile">Lütfen logo yükleyiniz.</RouterLink>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                    <div>
                        <span class="badge badge-warning align-top mr-2">Uyarı</span>
                        Adımlar tamamlanmadan panelde sınırlı işlemlere izin verilecektir. Adımlar tamamlandıktan sonra 7 günlük deneme süreniz başlayacaktır.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes flash {
    0% {
        opacity: 1;
    }

    50% {
        opacity: .4;
    }

    100% {
        opacity: 1;
    }
}

.alert-warning a {
    /* flash effect */
    animation: flash 1s infinite;

}
</style>