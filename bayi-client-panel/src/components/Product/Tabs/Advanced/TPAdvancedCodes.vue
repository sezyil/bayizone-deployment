<script setup lang="ts">
import { useCatalogProductStore } from "/@src/stores/catalog/product";
const store = useCatalogProductStore();
</script>
<template>
    <div>
        <div class="form-group">
            <!-- upc -->
            <label for="input-upc">UPC</label>
            <InputWithError :errors="store.formErrors.upc">
                <input type="text" v-model="store.productData.upc" placeholder="UPC" id="input-upc"
                    class="form-control">
                <!-- <div class="form-text">Evrensel Ürün Kodu</div> -->
            </InputWithError>
        </div>

        <!-- ean group -->
        <div class="form-group">
            <label for="input-ean">EAN</label>
            <InputWithError :errors="store.formErrors.ean">
                <input type="text" v-model="store.productData.ean" placeholder="EAN" id="input-ean"
                    class="form-control">
                <!-- <div class="form-text">Avrupa Makale Numarası</div> -->
            </InputWithError>
        </div>

        <!-- mpn -->
        <div class="form-group">
            <label for="input-mpn">MPN</label>
            <InputWithError :errors="store.formErrors.mpn">
                <input type="text" v-model="store.productData.mpn" placeholder="MPN" id="input-mpn"
                    class="form-control">
                <!-- <div class="form-text">Üretici parti numarası</div> -->
            </InputWithError>
        </div>
    </div>
</template>



<style scoped></style>