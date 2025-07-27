<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useSwal } from '/@src/composables/useSwal';
import { useOfferRequest } from '/@src/stores/offerRequest';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { GLOB_URIS, get_product_image_url } from '/@src/utils/global_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { IProductVariantColorData, IProductVariantDimensionData } from '/@src/request/request-offer-request';
import { IOfferFormProductModalVariants } from './Product/OfferProductModal.vue';
const { t, locale } = useI18n();
const router = useRouter();
const viewWrapper = useViewWrapper();
const checkoutSubmit = ref<HTMLFormElement | null>(null);
interface ITmp {
    id: string;
    name: string;
    model: string;
    image: string;
    note: string;
    quantity: number;
    color: IProductVariantColorData[] | null
    dimension: IProductVariantDimensionData[] | null
}
interface IOfferFormData {
    global_note: string;
    currency: "tl" | "usd" | "euro" | "gbp";
    products: ITmp[];
}
const requestData = ref<IOfferFormData>({
    global_note: "",
    currency: "tl",
    products: []
});
const currentLanguage = computed(() => locale.value);
const store = useOfferRequest();
const itemList = computed(() => store.selectedProductList.map(item => {
    let tmp: any = {
        id: item.id,
        model: item.product?.model,
        name: item.product?.name,
        //@ts-ignore
        image: item.product?.images.length ? get_product_image_url(item.product?.images[0]) : GLOB_URIS.NO_IMAGE,
        quantity: item.quantity,
        note: item.note,
        color: item.color,
        dimension: item.dimension,
    } as ITmp;
    return tmp;
}) as ITmp[]);

const saveForm = async (e: Event) => {
    e.preventDefault();
    viewWrapper.setLoading(true);
    requestData.value.products = itemList.value;
    let tmpData: any = { ...requestData.value };
    console.log(tmpData);
    tmpData.products = tmpData.products.map((item: IOfferFormData['products'][0]) => {
        let _tmpColor: IOfferFormProductModalVariants[] = [];
        let _tmpDimension: IOfferFormProductModalVariants[] = [];
        if (item.color) {
            item.color?.forEach(color => {
                let firstValue = color.value[0];
                if (firstValue) {
                    _tmpColor.push({
                        product_variant_id: color.id,
                        value_id: firstValue.id,
                        variant_id: color.variant_id
                    })
                }

            });
        }

        if (item.dimension) {
            item.dimension?.forEach(dimension => {
                let firstValue = dimension.value[0];
                if (firstValue) {
                    _tmpDimension.push({
                        product_variant_id: dimension.id,
                        value_id: firstValue.id,
                        variant_id: dimension.variant_id
                    })
                }
            });
        }

        return {
            id: item.id,
            note: item.note,
            quantity: item.quantity,
            color: _tmpColor,
            dimension: _tmpDimension
        }
    });
    try {
        const { data } = await store.sendForm(tmpData);
        useSwal().fire({
            icon: "success",
            title: t('common.success'),
            text: t('components.offer_request.create.success')
        }).then(() => {
            router.push('/app/offer_requests')
        });
    } catch (error) {
        catchFieldError(error);
        useSwal().fire({
            icon: "error",
            title: t('common.error'),
            text: t('components.offer_request.create.error')
        });
    }
    viewWrapper.setLoading(false);
}
</script>
<template>
    <div class="card">
        <div class="card-header bg-transparent header-elements-inline">
            <h6 class="card-title">{{ t('components.offer_request.create.summaryText') }}</h6>
            <div class="header-elements text-center text-lg-left mb-3 mb-lg-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-info" @click.prevent="checkoutSubmit?.click()">
                        <i class="icon-floppy-disk mr-2"></i>
                        {{ t('actions.save') }}
                    </button>
                </div>
            </div>
        </div>
        <form @submit.prevent="saveForm" ref="checkoutForm">
            <div class="card-body">

                <!-- Currency -->
                <div class="form-group">
                    <label>{{ t('common.currency') }}</label>
                    <select class="form-control" v-model="requestData.currency" style="width: 100px;">
                        <option value="tl">TL</option>
                        <option value="usd">USD</option>
                        <option value="euro">EURO</option>
                        <option value="gbp">GBP</option>
                    </select>
                </div>

                <!-- global note -->
                <div class="form-group">
                    <label>{{ t('components.offer_request.create.proformaNote') }}</label>
                    <textarea class="form-control" rows="3" cols="3" v-model="requestData.global_note"></textarea>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table text-nowrap">
                    <thead>
                        <tr>
                            <th colspan="2">{{ t('common.product') }}</th>
                            <th>{{ t('common.quantity') }}</th>
                            <th>{{ t('common.variants') }}</th>
                            <th>{{ t('common.productNote') }}</th>
                            <th class="text-center" style="width: 20px;"><i class="icon-arrow-down12"></i></th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr v-for="(product, index) in itemList" :key="index">
                            <td class="pr-0" style="width: 45px;">
                                <a :href="product.image" target="_blank">
                                    <img :src="product.image" height="60" alt="">
                                </a>
                            </td>
                            <td>
                                <a href="#" class="font-weight-semibold">{{ product.name }}</a>
                                <span class="text-muted d-block text-size-small">Model:{{ product.model }}</span>
                            </td>
                            <td>
                                <input type="number" class="form-control" v-model="product.quantity" max="999" min="1">
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-row" :class="{ 'mb-1': product.dimension }"
                                    v-if="product.color">
                                    <span v-for="color in product.color" class="badge bg-primary">
                                        {{ color.name }}
                                        : <span v-for="variant in color.value">{{ variant.name }}</span>
                                    </span>
                                </div>
                                <div class="d-flex gap-1 flex-row" v-if="product.dimension">
                                    <span v-for="dimension in product.dimension" class="badge bg-primary">
                                        {{ dimension.name }}
                                        :
                                        <span v-for="variant in dimension.value">
                                            {{ t('components.offer_request.elements.product.length') }}:
                                            {{ variant.value.length }}cm x
                                            {{ t('components.offer_request.elements.product.width') }}:
                                            {{ variant.value.width }}cm x
                                            {{ t('components.offer_request.elements.product.height') }}:
                                            {{ variant.value.height }}cm
                                        </span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <textarea class="form-control" rows="1" cols="1" v-model="product.note"></textarea>
                            </td>
                            <td class="text-center">
                                <!-- delete button -->
                                <button class="btn btn-danger" @click.prevent="store.removeProduct(index)">
                                    <i class="icon-trash"></i>
                                </button>
                            </td>
                        </tr>


                    </tbody>
                </table>
            </div>
            <!-- hidden btn -->
            <button type="submit" ref="checkoutSubmit" class="d-none"></button>
        </form>

    </div>
</template>

<style scoped>
input {
    width: 100px;
}
</style>