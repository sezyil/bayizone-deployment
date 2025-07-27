<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import { updateImage } from '/@src/request/request-company';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { GLOB_URIS, get_product_image_url } from '/@src/utils/GLOB_URIS';
const swal = useSwal();
const props = defineProps({
    image: {
        type: null as unknown as PropType<string | null>,
        default: null,
    },
    editable: {
        type: Boolean,
        default: false
    }
});
const isLoading = ref(false);
const fileinput = ref<HTMLInputElement | null>(null);

const profileImage = computed(() => {
    return props.image ? get_product_image_url(props.image) : GLOB_URIS.NO_IMAGE;
});

const handleFileChange = async (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (files && files.length) {
        const file = files[0];
        const formData = new FormData();
        formData.append('image', file);
        isLoading.value = true;
        try {
            const { data } = await updateImage(formData);
            const _data = data.data as { trial_activated: boolean };
            let _msg = 'Resim güncellendi';
            if (_data.trial_activated) {
                _msg = 'Resim güncellendi. 7 Günlük deneme süreniz başladı.';
            }

            swal.fire('Başarılı', _msg, 'success').then(() => {
                window.location.reload();
            });

        } catch (error) {
            swal.fire('Hata', 'Resim güncellenirken bir hata oluştu', 'error');
        } finally {
            isLoading.value = false;
        }
    }
}

</script>

<template>
    <div class="card-img-actions d-inline-block mb-3">
        <VLoader :active="isLoading" />
        <img class="img-fluid rounded-circle" :src="profileImage" width="170" height="170" alt="">
        <div class="card-img-actions-overlay rounded-circle" v-if="editable" :class="{ 'flashing': !props.image }">
            <!-- only jpg,jpeg,png -->
            <input type="file" class="d-none" ref="fileinput" @change="handleFileChange"
                accept="image/jpeg,image/png,image/jpg" />
            <a href="#" class="btn btn-outline-white border-2 btn-icon rounded-pill" @click="fileinput?.click()">
                <i class="fa fa-edit"></i>
            </a>
        </div>
    </div>
</template>


<style scoped>
.flashing {
    animation: flashing 1s infinite;
}

.flashing.rounded-circle {
    visibility: visible;
    /* background nearly visible red */
    background-color: rgba(255, 0, 0, 0.3);
}

@keyframes flashing {
    0% {
        opacity: 1;
    }

    50% {
        opacity: 0;
    }

    100% {
        opacity: 1;
    }
}
</style>