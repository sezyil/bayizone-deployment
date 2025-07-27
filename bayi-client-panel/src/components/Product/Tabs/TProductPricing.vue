<script setup lang="ts">
import { useCatalogProductStore } from '/@src/stores/catalog/product';
import { getCurrencySymbol } from '/@src/utils/currency_helper';
const store = useCatalogProductStore();

</script>
<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <p class="text-warning d-flex flex-col gap-1 align-items-center">
                        <i class="fas fa-info-circle"></i>
                        <small>
                            Ürün fiyatlarının sağ tarafındaki yuvarlak butonlar, ürünün gösteriminde kullanılacak olan
                            varsayılan para birimini belirler.
                        </small>
                    </p>

                </div>
            </div>
            <!-- tl price with append currency symbol -->
            <div class="form-group mt-2">
                <label for="input-tl-price">TL Fiyatı</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ getCurrencySymbol('tl') }}</span>
                    </div>
                    <input type="number" class="form-control" v-model="store.productData.price_tl"
                        placeholder="TL Fiyatı" id="input-tl-price">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <label class="custom-control custom-control-secondary custom-radio">
                                <input type="radio" class="custom-control-input" name="default_currency"
                                    v-model="store.productData.default_currency" value="tl">
                                <span class="custom-control-label p-0"></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>

            <!-- usd price with append currency symbol -->
            <div class="form-group">
                <label for="input-usd-price">USD Fiyatı</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ getCurrencySymbol('usd') }}</span>
                    </div>
                    <input type="number" class="form-control" v-model="store.productData.price_usd"
                        placeholder="USD Fiyatı" id="input-usd-price">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <label class="custom-control custom-control-secondary custom-radio">
                                <input type="radio" class="custom-control-input" name="default_currency"
                                    v-model="store.productData.default_currency" value="usd">
                                <span class="custom-control-label p-0"></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>

            <!-- eur price with append currency symbol -->
            <div class="form-group">
                <label for="input-eur-price">EUR Fiyatı</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ getCurrencySymbol('euro') }}</span>
                    </div>
                    <input type="number" class="form-control" v-model="store.productData.price_euro"
                        placeholder="EUR Fiyatı" id="input-eur-price">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <label class="custom-control custom-control-secondary custom-radio">
                                <input type="radio" class="custom-control-input" name="default_currency"
                                    v-model="store.productData.default_currency" value="euro">
                                <span class="custom-control-label p-0"></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>

            <!-- gbp -->
            <div class="form-group">
                <label for="input-gbp-price">GBP Fiyatı</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ getCurrencySymbol('gbp') }}</span>
                    </div>
                    <input type="number" class="form-control" v-model="store.productData.price_gbp"
                        placeholder="GBP Fiyatı" id="input-gbp-price">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <label class="custom-control custom-control-secondary custom-radio">
                                <input type="radio" class="custom-control-input" name="default_currency"
                                    v-model="store.productData.default_currency" value="gbp">
                                <span class="custom-control-label p-0"></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>


            <hr>


            <div class="form-group">
                <!-- input quantity -->
                <label for="input-quantity">Stok Adedi</label>
                <InputWithError :errors="store.formErrors.quantity">
                    <input type="text" v-model="store.productData.quantity" placeholder="Miktar" id="input-quantity"
                        class="form-control">
                </InputWithError>
            </div>
            <div class="form-group">
                <!-- input minimum -->
                <label for="input-minimum">Minimum sipariş adedi</label>
                <InputWithError :errors="store.formErrors.minimum">
                    <input type="number" v-model="store.productData.minimum" placeholder="Minimum miktar"
                        id="input-minimum" class="form-control">
                </InputWithError>
            </div>

            <div class="form-group">
                <!-- input unit -->
                <label for="input-unit">Birim Türü</label>
                <InputWithError :errors="store.formErrors.unit_id">
                    <ProductUnitSelector v-model="store.productData.unit_id" />
                </InputWithError>
            </div>
        </div>
    </div>
</template>


<style scoped></style>