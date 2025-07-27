<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { OfferRequestApi } from '/@src/request/request-offer-request';
import { GLOB_URIS, get_product_image_url } from '/@src/utils/global_uris';
import { ICustomerOrderShowColorVariant, ICustomerOrderShowDimensionVariant } from '/@src/shared/response/interface-customer-order-show-response';
const apiClass = new OfferRequestApi();
interface RequestDetail {
    id: string
    global_note: any
    created_at: string
    lines: RequestDetailLine[]
}

interface RequestDetailLine {
    id: number
    product_name: string
    image: string
    model: string
    quantity: number
    note: string
    color: ICustomerOrderShowColorVariant[] | null
    dimension: ICustomerOrderShowDimensionVariant[] | null
}
const emit = defineEmits(['close'])
const props = defineProps({
    id: {
        type: String,
    }
})
const { t, locale } = useI18n()
const currentLanguage = computed(() => locale.value)
const modalActive = ref<boolean>(false);
const modalData = ref<RequestDetail>();
const isLoading = ref<boolean>(false);
const getData = async () => {
    if (!props.id) return;
    isLoading.value = true;
    try {
        const { data } = await apiClass.show(props.id);
        modalData.value = data.data;
        modalActive.value = true;
    } catch (error) {
        console.log(error);
    }
    isLoading.value = false;
}

const modalClosed = () => {
    modalActive.value = false;
    modalData.value = undefined;
    emit('close')
}
watch(() => props.id, async (val) => {
    if (!val) {
        modalData.value = undefined;
        return;
    }

    await getData();
})

</script>
<template>
    <VModal :title="t('components.offerRequestModal.title')" :show="modalActive" @close="modalClosed" size="lg">
        <VLoader :active="isLoading" />
        <div class="table-responsive">
            <table class="table text-nowrap">
                <thead>
                    <tr>
                        <th colspan="2">{{ t('common.product') }}</th>
                        <th>{{ t('common.quantity') }}</th>
                        <th>{{ t('common.variants') }}</th>
                        <th>{{ t('common.productNote') }}</th>
                    </tr>
                </thead>
                <tbody>

                    <tr v-for="(product, index) in modalData?.lines" :key="index">
                        <td class="pr-0" style="width: 45px;">
                            <a :href="product.image ? get_product_image_url(product.image) : GLOB_URIS.NO_IMAGE"
                                target="_blank">
                                <img :src="product.image ? get_product_image_url(product.image) : GLOB_URIS.NO_IMAGE"
                                    height="60" alt="">
                            </a>
                        </td>
                        <td>
                            <a href="#" class="font-weight-semibold">{{ product.product_name }}</a>
                            <span class="text-muted d-block text-size-small">Model:{{ product.model }}</span>
                        </td>
                        <td>
                            <span class="font-weight-semibold">{{ product.quantity }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-column" v-if="product.color"
                                :class="{ 'mb-1': product.dimension }">
                                <span v-for="color in product.color" class="badge bg-secondary text-white">
                                    {{ color.variant.name }}
                                    : {{ color.variant_value.name }}
                                </span>
                            </div>
                            <div class="d-flex gap-1 flex-column" v-if="product.dimension">
                                <span v-for="dimension in product.dimension" class="badge bg-secondary text-white">

                                    {{ dimension.variant.name }}
                                    :
                                    <br />
                                    {{ t('components.offer_request.elements.product.length') }}:
                                    {{ dimension.variant_value.value.length }}cm x
                                    {{ t('components.offer_request.elements.product.width') }}:
                                    {{ dimension.variant_value.value.width }}cm x
                                    {{ t('components.offer_request.elements.product.height') }}:
                                    {{ dimension.variant_value.value.height }}cm
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="font-weight-semibold text-wrap">{{ product.note }}</span>
                        </td>
                    </tr>


                </tbody>
            </table>
        </div>
    </VModal>
</template>

<style scoped></style>