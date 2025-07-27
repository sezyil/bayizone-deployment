<script setup lang="ts">
import { initFlowbite } from 'flowbite'
import { AvailableLanguages, LanguageGetter, SelectableLanguages } from '/@src/shared/common/common-language';
import { useI18n } from 'vue-i18n';
const { locale, t } = useI18n()
const defaultLocale = useStorage('locale', 'tr')
const currentLang = computed(() => LanguageGetter(defaultLocale.value as AvailableLanguages))
const languages = SelectableLanguages;
watch(locale, async () => {
    defaultLocale.value = locale.value
})

const setLanguageData = async (lang: string) => {
    defaultLocale.value = lang;
    location.reload();
}

onMounted(() => {
    initFlowbite();

});
</script>
<template>
    <div class="flex items-center space-x-0 rtl:space-x-reverse">
        <button type="button" data-dropdown-toggle="language-dropdown-menu" ref="triggerEl"
            data-dropdown-target="language-dropdown-menu" id="language-dropdown-trigger"
            class="inline-flex items-center font-medium justify-center text-sm text-gray-900 dark:text-white rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
            <img :src="currentLang.image" alt="Turkish" class="w-5 h-5" />
        </button>
        <!-- Dropdown -->
        <div class="z-50 my-4 hidden text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700"
            ref="targetEl" id="language-dropdown-menu">
            <ul class="py-2 font-medium" role="none">
                <li v-for="language in languages" :key="language.value">
                    <button @click="setLanguageData(language.value)"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                        :class="{ 'bg-gray-100 dark:bg-gray-600': language.value === currentLang.value }"
                        role="menuitem">
                        <div class="inline-flex items-center">
                            <img :src="language.image" alt="" class="w-5 h-5 mr-2" />
                            {{ language.description }}
                        </div>
                    </button>
                </li>


            </ul>
        </div>
    </div>
</template>



<style scoped></style>