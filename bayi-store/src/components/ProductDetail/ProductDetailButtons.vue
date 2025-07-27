<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IStoreCartData } from '/@src/shared/cart';
import { IStoreProduct } from '/@src/shared/product';
const { t } = useI18n();
const emit = defineEmits<{
    (e: 'changeQuantity', data: number): void,
    //void
    (e: 'addItem'): void,
}>();
const props = defineProps<{
    productData: IStoreProduct
}>();
const maxQuantity = 999;
const minQuantity = 1;

const increment = () => {
    if (props.productData.quantity < maxQuantity) {
        changeQuantity(props.productData.quantity + 1);
    }
};

const decrement = () => {
    if (props.productData.quantity > minQuantity) {
        changeQuantity(props.productData.quantity - 1);
    }
};

const quantityValue = computed(() => {
    return props.productData.quantity;
});

const handleInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    //only allow numbers
    target.value = target.value.replace(/[^0-9]/g, '');
    let converted = parseInt(target.value);
    //is nan or not a number
    if (isNaN(converted)) {
        converted = 1;
    } else if (converted < minQuantity) {
        converted = minQuantity;
    } else if (converted > maxQuantity) {
        converted = maxQuantity;
    }

    return converted;
};

const handleOnChangeEvent = (e: Event) => {
    const target = e.target as HTMLInputElement;
    //only allow numbers
    target.value = target.value.replace(/[^0-9]/g, '');
    let converted = parseInt(target.value);
    if (isNaN(converted)) {
        converted = 1;
    } else if (converted < minQuantity) {
        converted = minQuantity;
    } else if (converted > maxQuantity) {
        converted = maxQuantity;
    }

    changeQuantity(converted);
};

const changeQuantity = (data: number) => {
    emit('changeQuantity', data);
};

</script>
<template>
    <div>
        <div class="my-5 flex flex-row justify-between text-center items-center gap-3" v-if="productData">
            <div class="w-2/5">
                <div class="relative flex items-center max-w-[11rem]">
                    <button type="button" id="decrement-button" @click="decrement"
                        data-input-counter-decrement="quantity-input"
                        class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-12 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 1h16" />
                        </svg>
                    </button>
                    <input type="text" id="quantity-input" data-input-counter data-input-counter-min="1"
                        data-input-counter-max="5" aria-describedby="helper-text-explanation" :value="quantityValue"
                        @change="handleOnChangeEvent" @input="handleInput" @keydown="handleInput"
                        class="bg-gray-50 border-x-0 border-gray-300 h-12 items-center font-medium text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="" required />
                    <button type="button" id="increment-button" @click="increment"
                        data-input-counter-increment="quantity-input"
                        class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-12 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 1v16M1 9h16" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="w-3/5">
                <button @click="$emit('addItem')" class="w-full flex justify-center items-center text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center h-12
                        dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-500">
                    {{ t('common.addToCart') }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped></style>