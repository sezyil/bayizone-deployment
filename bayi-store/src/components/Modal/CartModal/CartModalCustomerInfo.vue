<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useShopStore } from '/@src/stores/shopStore';
import VInputCountries from '../../base/Input/VInputCountries.vue';
const { t } = useI18n();
const store = useShopStore();
const customerType = computed(() => store.customerInfo.type);
const nameText = computed(() => customerType.value === 'corporate' ? t('components.cart.form.authorizedPerson') : t('components.cart.form.fullname'));
const formErrors = computed(() => store.cartFormErrors.customer);
</script>
<template>

    <div class="flex flex-col space-y-4">
        <div class="flex flex-row space-x-4 mt-2">
            <div class="flex items-center me-4">
                <label for="inline-radio" class="me-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ t('components.cart.form.customerType.individual') }}
                </label>
                <input id="inline-radio" type="radio" value="individual" v-model="store.customerInfo.type"
                    name="inline-radio-group" required
                    class="w-4 h-4 text-black  focus:ring-black dark:focus:ring-black  dark:bg-gray-700 dark:border-gray-600">
            </div>
            <div class="flex items-center me-4">
                <label for="inline-2-radio" class="me-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ t('components.cart.form.customerType.corporate') }}
                </label>
                <input id="inline-2-radio" type="radio" value="corporate" v-model="store.customerInfo.type"
                    name="inline-radio-group" required
                    class="w-4 h-4 text-black  focus:ring-black dark:focus:ring-black  dark:bg-gray-700 dark:border-gray-600">
            </div>
        </div>
        <div class="flex flex-col space-y-2">
            <VInputErrors v-if="customerType === 'corporate'" class="flex flex-col space-y-1"
                :label="t('components.cart.form.title')" :errors="formErrors?.company_name">
                <input type="text" id="name" v-model="store.customerInfo.company_name" required
                    class="border border-gray-300 p-2 rounded-md focus:outline-none focus:border-blue-400">
            </VInputErrors>

            <VInputErrors class="flex flex-col space-y-1" :label="nameText" :errors="formErrors?.authorized_name">
                <input type="text" id="name" v-model="store.customerInfo.authorized_name" required
                    class="border border-gray-300 p-2 rounded-md focus:outline-none focus:border-blue-400">
            </VInputErrors>
            <VInputErrors class="flex flex-col space-y-1" :label="t('components.cart.form.email')"
                :errors="formErrors?.email">
                <input type="email" id="email" v-model="store.customerInfo.email" required
                    class="border border-gray-300 p-2 rounded-md focus:outline-none focus:border-blue-400">
            </VInputErrors>

            <VInputErrors class="flex flex-col space-y-1" :label="t('components.cart.form.phone')"
                :errors="formErrors?.phone">
                <input type="tel" id="phone" v-model="store.customerInfo.phone" required
                    class="border border-gray-300 p-2 rounded-md focus:outline-none focus:border-blue-400">
            </VInputErrors>
            <!-- Ülke -->
            <VInputCountries :title="t('components.cart.form.country')" class="flex flex-col space-y-1"
                :errors="formErrors?.country_id" v-model="store.customerInfo.country_id" />
            <!-- States -->
            <VInputStates class="flex flex-col space-y-1" :title="t('components.cart.form.city')"
                :errors="formErrors?.state_id" v-model="store.customerInfo.state_id"
                :country_id="store.customerInfo.country_id" />
            <!-- Cities -->
            <VInputCities class="flex flex-col space-y-1" v-model="store.customerInfo.city_id"
                :title="t('components.cart.form.district')" :errors="formErrors?.city_id"
                :state_id="store.customerInfo.state_id" />
            <!-- Adres -->
            <VInputErrors class="flex flex-col space-y-1" :label="t('components.cart.form.address')"
                :errors="formErrors?.address">
                <textarea id="address" rows="2"
                    class="border border-gray-300 p-2 rounded-md focus:outline-none focus:border-blue-400"></textarea>
            </VInputErrors>

            <!-- Currency -->
            <VInputErrors class="flex flex-col space-y-1" :label="t('components.cart.form.currency')"
                :errors="formErrors?.currency">
                <select id="currency" v-model="store.customerInfo.currency"
                    class="border border-gray-300 p-2 rounded-md focus:outline-none focus:border-blue-400">
                    <option value="tl">TL</option>
                    <option value="usd">USD</option>
                    <option value="euro">EURO</option>
                    <option value="gbp">GBP</option>
                </select>
            </VInputErrors>

            <!-- Not -->
            <VInputErrors class="flex flex-col space-y-1" :label="t('components.cart.form.note')"
                :errors="formErrors?.note">
                <textarea id="note" rows="2" v-model="store.customerInfo.note"
                    class="border border-gray-300 p-2 rounded-md focus:outline-none focus:border-blue-400"></textarea>
            </VInputErrors>
        </div>
    </div>

</template>

<style scoped></style>