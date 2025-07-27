<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { LanguageGetter, AvailableLanguages, SelectableLanguages } from '/@src/shared/common/common-language';
import { setLanguage } from '/@src/request/request-profile';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const { locale, t } = useI18n()

const defaultLocale = useStorage('locale', 'tr')

const currentLang = computed(() => LanguageGetter(defaultLocale.value as AvailableLanguages))
const selectableLanguages = computed(() => SelectableLanguages)
const viewWrapper = useViewWrapper()
watch(locale, async () => {
    defaultLocale.value = locale.value
})

const setLanguageData = async (lang: string) => {
    viewWrapper.setLoading(true)
    defaultLocale.value = lang
    await setLanguage(lang)
    location.reload()
    viewWrapper.setLoading(false)
}

</script>
<template>
    <li class="nav-item nav-item-dropdown-lg dropdown">
        <a href="#" class="navbar-nav-link navbar-nav-link-toggler dropdown-toggle" data-toggle="dropdown"
            aria-expanded="true">
            <img :src="currentLang.image" class="img-flag" alt="">
            <span class="d-none d-lg-inline-block ml-2">{{ currentLang.description }}</span>
        </a>

        <div class="dropdown-menu dropdown-menu-right">
            <button v-for="lang in selectableLanguages" :key="lang.value" @click="setLanguageData(lang.value)"
                :class="{ active: lang.value === defaultLocale }" class="dropdown-item">
                <img :src="lang.image" class="img-flag" alt="">
                <span class="ml-2">{{ lang.description }}</span>
            </button>
        </div>
    </li>
</template>

<style scoped></style>