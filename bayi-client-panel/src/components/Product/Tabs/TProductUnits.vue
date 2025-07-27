<script setup lang="ts">
import { useCatalogProductStore } from '/@src/stores/catalog/product';
const store = useCatalogProductStore();

</script>
<template>
    <div>
        <div class="card">
            <div class="card-body">
                <div class="row  mb-2">
                    <div class="col-12">
                        <small class="text-warning">
                            <i class="fas fa-info-circle mr-1"></i>Eğer varyasyon eklemek istiyorsanız, ölçü alanlarını
                            boş
                            bırakabilirsiniz.</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="input-length">Uzunluk</label>
                            <div class="input-group">
                                <InputWithError :errors="store.formErrors.length">
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-ruler"></i></span>
                                        </span>
                                        <input type="number" step="0" min="0" name="length"
                                            v-model="store.productData.length" placeholder="Uzunluk"
                                            id="input-length-class" class="form-control">
                                        <span class="input-group-append">
                                            <span class="input-group-text">cm</span>
                                        </span>
                                    </div>
                                </InputWithError>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="input-width">
                                Genişlik
                            </label>
                            <InputWithError :errors="store.formErrors.width">
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-ruler-horizontal"></i></span>
                                    </span>
                                    <input type="number" step="0" min="0" name="width" v-model="store.productData.width"
                                        placeholder="Genişlik" id="input-width-class" class="form-control">
                                    <span class="input-group-append">
                                        <span class="input-group-text">cm</span>
                                    </span>
                                </div>
                            </InputWithError>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="input-height">
                                Yükseklik
                            </label>
                            <InputWithError :errors="store.formErrors.height">
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-ruler-vertical"></i></span>
                                    </span>
                                    <input type="number" step="0" min="0" name="height"
                                        v-model="store.productData.height" placeholder="Yükseklik"
                                        id="input-height-class" class="form-control">
                                    <span class="input-group-append">
                                        <span class="input-group-text">cm</span>
                                    </span>
                                </div>
                            </InputWithError>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label> Ağırlık:</label>
                        <InputWithError :errors="store.formErrors.weight">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-balance-scale"></i></span>
                                </span>
                                <input type="number" step="0.1" min="0" name="weight_class"
                                    v-model="store.productData.weight" placeholder="Ağırlık" id="input-weight-class"
                                    class="form-control">
                                <span class="input-group-append">
                                    <span class="input-group-text">kg</span>
                                </span>
                            </div>
                        </InputWithError>
                    </div>
                </div>
                <div class="col-12">
                    <!-- Hacim -->
                    <div class="form-group">
                        <label for="input-volume">
                            Hacim
                        </label>
                        <InputWithError :errors="store.formErrors.volume">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-box-open"></i></span>
                                </span>
                                <input type="number" step="0.1" min="0" name="volume" v-model="store.productData.volume"
                                    placeholder="Hacim" id="input-volume-class" class="form-control">
                                <span class="input-group-append">
                                    <span class="input-group-text">m<sup>3</sup></span>
                                </span>
                            </div>
                        </InputWithError>
                    </div>
                </div>
                <div class="col-12">
                    <!-- Paket -->
                    <div class="form-group">
                        <label for="input-package">
                            Paket Adedi
                        </label>
                        <InputWithError :errors="store.formErrors.package">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-box"></i></span>
                                </span>
                                <input type="number" step="1" min="0" name="package" v-model="store.productData.package"
                                    placeholder="Paket Adedi" id="input-package-class" class="form-control">
                                <span class="input-group-append">
                                    <span class="input-group-text">Adet</span>
                                </span>
                            </div>
                        </InputWithError>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped></style>