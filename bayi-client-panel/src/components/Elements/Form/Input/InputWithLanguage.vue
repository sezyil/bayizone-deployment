<script setup lang="ts">
import type { AvailableLanguages } from '/@src/shared/common/common-language';
import { LanguageGetter } from '/@src/shared/common/common-language';
const props = defineProps({
    language: {
        type: String as PropType<AvailableLanguages>,
        default: 'tr',
    },
    withText: {
        type: Boolean as PropType<boolean>,
        default: false,
    }
})

const activeLanguage = computed(() => LanguageGetter(props.language));
const flag = computed(() => `/images/flags/${props.language}_flag.svg`);
</script>
<template>
    <div class="input-group">
        <span class="input-group-prepend">
            <span class="input-group-text">
                <img :src="flag" alt="icon" width="20" height="20">
                <span class="ml-2" v-if="withText">{{ activeLanguage.description }}</span>
            </span>
        </span>
        <slot></slot>
    </div>
</template>



<style scoped></style>