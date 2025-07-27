<script setup lang="ts">
import { useCustomerOrderFormStore } from '/@src/stores/customerOrderFormStore';
import { getCurrencySymbol } from '/@src/utils/currency_helper';

const store = useCustomerOrderFormStore();
const totalPrice = computed(() => {
    let total = 0;
    store.orderForm.lines.forEach((line) => {
        if (!line.product_id) return;
        let withoutTax = line.quantity * line.unit_price;
        //discount
        withoutTax -= withoutTax * line.unit_discount_rate / 100;
        //tax
        let tax = withoutTax * (line.tax_rate / 100);
        total += withoutTax + tax;
    })
    return total.toFixed(2);
})

const totalTax = computed(() => {
    let total = 0;
    store.orderForm.lines.forEach((line) => {
        if (!line.product_id) return;
        let withoutTax = line.quantity * line.unit_price;
        //discount
        withoutTax -= withoutTax * line.unit_discount_rate / 100;
        //tax
        let tax = withoutTax * (line.tax_rate / 100);
        total += tax;
    })
    return total.toFixed(2);
})

const totalVolume = computed(() => {
    let total = 0;
    store.orderForm.lines.forEach((line) => {
        if (!line.product_id) return;
        total += Number(line.quantity) * Number(line.unit_volume);
    })
    return total.toFixed(2);
})

const totalPackage = computed(() => {
    let total = 0;
    store.orderForm.lines.forEach((line) => {
        if (!line.product_id) return;
        total += Number(line.unit_package) * Number(line.quantity);
    })
    return total.toFixed(2);
})
const selectedCurrency = computed(() => {
    return getCurrencySymbol(store.orderForm.detail.currency);
});
</script>
<template>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Ürün Özeti</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- no input only label show -->
                <span class="col-6">Toplam Fiyat</span>
                <span class="col-6 text-right">{{ selectedCurrency }}{{ totalPrice }}</span>
            </div>
            <div class="row" v-if="!store.isInternational">
                <div class="col-6">Toplam KDV</div>
                <div class="col-6 text-right">{{ selectedCurrency }}{{ totalTax }}</div>
            </div>
            <div class="row">
                <!-- no input only label show -->
                <span class="col-6">Toplam Hacim</span>
                <span class="col-6 text-right">{{ totalVolume }} m³</span>
                <span class="col-6">Toplam Paket</span>
                <span class="col-6 text-right">{{ totalPackage }}</span>
            </div>
        </div>
    </div>

</template>


<style scoped></style>