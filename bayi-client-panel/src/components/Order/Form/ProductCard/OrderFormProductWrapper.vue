<script setup lang="ts">
import { useSwal } from "/@src/composables/useSwal";
import { list } from "/@src/request/request-product";
import VariantsApi, { IVariantListItem } from "/@src/request/request-variant";
import { ICustomerOrderFormProduct } from "/@src/shared/form/interface-customer-order";
import { IProductList } from "/@src/shared/product/interface-product";
import { useCustomerOrderFormStore } from "/@src/stores/customerOrderFormStore";
import { catchFieldError } from "/@src/utils/api/catchFormErrors";
const swal = useSwal();
const store = useCustomerOrderFormStore();
const variantApi = new VariantsApi();
const productData = ref<ICustomerOrderFormProduct[]>([]);

const getProductData = async () => {
    try {
        const response = await list(undefined, {
            withOptions: true,
        });
        let x_data = response.data.data as IProductList[];
        productData.value = x_data.map((product) => {
            return {
                ...product,
                price: 0,
                note: "",
            };
        });
    } catch (error) {
        catchFieldError(error);
    }
};
const productModalCard = ref<{
    active: boolean,
    productIndex: number | null
}>({
    active: false,
    productIndex: null
});
const orderLines = computed(() => {
    return store.orderForm.lines;
})


const addLine = () => {
    //check has empty line
    const emptyLine = store.orderForm.lines.find(line => line.product_id == 0)
    if (emptyLine) {
        //lütfen yeni bir satır açmadan önce mevcut satırı doldurunuz.
        swal.fire({
            title: "Satır Boş",
            text: "Lütfen yeni bir satır açmadan önce mevcut satırı doldurunuz.",
            icon: "warning",
            confirmButtonText: "Tamam",
        });
        return;
    }
    handleTriggerDetail(null);
}

const handleTriggerDetail = (index: number | null) => {
    productModalCard.value = {
        active: true,
        productIndex: index
    }
}

const closeModal = () => {
    productModalCard.value = {
        active: false,
        productIndex: 0
    }
}

const variantList = ref<IVariantListItem[]>([]);
const getVariants = async () => {
    try {
        const { data } = await variantApi.listWithValues();
        variantList.value = data.data
    } catch (error) {
        catchFieldError(error);

    }
}

const dimensionVariants = computed(() => {
    return variantList.value.filter(variant => variant.type === 'DIMENSION');
})

const colorVariants = computed(() => {
    return variantList.value.filter(variant => variant.type === 'COLOR');
})

onMounted(async () => {
    await getProductData();
    await getVariants();
});
</script>
<template>
    <div class="card">
        <div class="card-header bg-transparent header-elements-sm-inline py-sm-0">
            <h6 class="card-title py-sm-3">Ürünler</h6>
            <div class="header-elements">
                <button type="button" class="btn btn-secondary" @click="addLine">Ekle</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row" v-if="orderLines.length > 0">
                <div class="col-12 col-md-4 col-xl-3" v-for="(line, index) in orderLines" :key="index">
                    <OrderFormProductCard :dataIndex="index" :errors="store.formErrors.lines[index] ?? []"
                        :color-list="colorVariants" :dimension-list="dimensionVariants" :productData="productData"
                        :lineData="line" @detail="handleTriggerDetail(index)" @delete="store.deleteLine(index)" />

                </div>
                <!-- blank card to add -->
                <div class="col-12 col-md-4 col-xl-3">
                    <div class="card h-100 border-2 border-dashed border-secondary justify-content-center align-items-center cursor-pointer"
                        @click.prevent="addLine">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                            <i class="fa fa-plus fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-warning" v-else>
                <i class="fa fa-exclamation-triangle"></i>
                Lütfen ürün ekleyiniz.
            </div>
        </div>
        <div class="card-footer" v-if="orderLines.length > 0">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 ml-auto rounded p-1">
                    <OrderFormSummary />
                </div>
            </div>
        </div>

        <OrderFormProductModal :productIndex="productModalCard.productIndex" :active="productModalCard.active"
            :color-list="colorVariants" :dimension-list="dimensionVariants" :product-data="productData"
            @close="closeModal" />

    </div>
</template>


<style scoped></style>