<script setup lang="ts">
import Dropzone from "dropzone";
import API_URIS from "/@src/utils/api/api_uris";
import { useCatalogProductStore } from "/@src/stores/catalog/product";
import { get_product_image_url } from "/@src/utils/GLOB_URIS";
const store = useCatalogProductStore();
const dropzone = ref<Dropzone>();
const dropzoneUploader = ref<HTMLFormElement>();

const dropzoneConfig: Dropzone.DropzoneOptions = {
    url: API_URIS.BASE_URL + '/product_image',
    method: "POST",
    headers: {
        "Authorization": "Bearer " + localStorage.getItem("token"),
    },
    addRemoveLinks: true,
    dictDefaultMessage: "Fotoğrafları buraya sürükleyin veya tıklayın",
    dictRemoveFile: "Kaldır",
    dictCancelUpload: "İptal",
    maxFilesize: 10,
    acceptedFiles: "image/jpeg,image/png",
    dictFileTooBig: "Dosya boyutu çok büyük ({{filesize}}",
    dictMaxFilesExceeded: "Maksimum dosya sayısına ulaşıldı",
    dictInvalidFileType: "Geçersiz dosya türü",
    parallelUploads: 3,
    maxFiles: 10,
    thumbnailHeight: 250,
    thumbnailWidth: 250
};
const addExistingImages = () => {
    if (!store.productData.images) return;
    let i = 0;
    store.productData.images.forEach((image) => {
        let img = { ...image };
        if (!img.image) return;
        dropzone.value?.emit("addedfile", img);
        dropzone.value?.emit("thumbnail", img, get_product_image_url(img.image));
        dropzone.value?.emit("complete", img);
    });
};

const createDropzone = () => dropzone.value = new Dropzone("#image-uploader", dropzoneConfig);
const handleDropzoneEvents = () => {
    if (!dropzone.value) return;
    dropzone.value.
        //add existing images
        on("addedfile", (file) => {
            // maybe we can use later
        })
        .on("success", (file, response: any) => {
            const _res: { data: { image_name: string } } = response;
            //on success add image to store
            store.addImage({ image: _res.data.image_name, id: 0, sort_order: store.productData.images.length + 1 });
        })
        .on("error", (file, response) => {
            const _res: { success: false, errors: { file: string[] } } = response as any;
            file.previewElement?.classList.add("dz-error");
            if (_res && _res?.errors?.file)
                file.previewElement.querySelector(".dz-error-message")!.textContent = _res.errors.file.join("\n");
        })
        .on("removedfile", (file) => {
            //@ts-ignore
            if (file?.image) {
                //@ts-ignore
                store.removeImage(file.image);
            }
        });
};

onMounted(() => {
    createDropzone();
    handleDropzoneEvents();
    addExistingImages();
});

onUnmounted(() => {
    dropzone.value?.destroy();
    dropzone.value = undefined;
    console.info("Dropzone destroyed");
});

</script>
<template>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Ürün Galerisi</h4>
            <small class="text-warning">Seçilen ilk fotoğraf, ürünün öne çıkan fotoğrafı olarak kullanılacaktır.</small>
        </div>
        <div class="card-body">
            <form action="/file-upload" class="dropzone" id="image-uploader" ref="dropzoneUploader"></form>
        </div>
    </div>

</template>


<style>
.dz-size {
    display: none;
}

.dz-filename {
    display: none;
}
</style>