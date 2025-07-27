<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useOfferRequest } from '/@src/stores/offerRequest';
import { GLOB_URIS, get_product_image_url } from '/@src/utils/global_uris';

const store = useOfferRequest();
const { t } = useI18n();
const cartProducts = computed(() => store.selectedProductList);
const removeProduct = (productId: number | null) => {
    if (productId) {
        store.removeProduct(productId);
    }
}
</script>
<template>
    <li class="nav-item nav-item-dropdown-lg dropdown">
        <a href="#" class="navbar-nav-link navbar-nav-link-toggler" data-toggle="dropdown">
            <i class="icon-cart"></i>
            <span class="badge badge-warning badge-pill ml-auto ml-lg-0">{{ cartProducts.length }}</span>
        </a>

        <div class="dropdown-menu dropdown-menu-right dropdown-content wmin-lg-350">
            <div class="dropdown-content-header">
                <h6 class="font-weight-semibold mb-0">{{ t('components.cart.title') }}</h6>
                <a v-if="cartProducts.length" href="#" @click.prevent="store.setStep('checkout')"
                    class="text-success ml-2" data-popup="tooltip" title="Kaydet">
                    <i class="fa fa-save"></i>
                </a>
            </div>

            <div class="dropdown-content-body dropdown-scrollable">
                <ul class="media-list" v-if="cartProducts.length">
                    <li class="media" v-for="(product, index) in cartProducts" :key="index">
                        <div class="mr-3 position-relative">
                            <img v-if="product.product?.image" :src="get_product_image_url(product?.product?.image)"
                                width="36" height="36" class="rounded-circle" alt="" />
                            <img v-else :src="GLOB_URIS.NO_IMAGE" width="36" height="36" class="rounded-circle"
                                alt="" />
                        </div>

                        <div class="media-body">
                            <div class="media-title">
                                <a href="#">
                                    <span class="font-weight-semibold">{{ product.product?.name }}</span>
                                    <span class="text-muted float-right font-size-sm">{{ t('common.quantity') }}:{{
                                        product.quantity }}</span>
                                    <!-- remove cart -->
                                </a>
                                <a href="#" class="text-danger ml-2" data-popup="tooltip"
                                    :title="t('components.cart.remove')" @click.prevent="removeProduct(index)">
                                    <i class="fa fa-trash"></i>

                                </a>
                            </div>

                            <span class="text-muted">Model:{{ product.product?.model }}</span>
                        </div>
                    </li>
                </ul>
                <div v-else class="text-center">
                    <span>{{ t('components.cart.noItem') }}</span>
                </div>
            </div>
        </div>
    </li>
</template>

<style lang="scss" scoped></style>