<script setup lang="ts">
const props = withDefaults(defineProps<{
    label?: string;
    labelClass?: string;
    errors?: string[];
}>(), {
    label: '---',
    errors: undefined
});
const formwrapper = ref<HTMLElement | null>(null);
const defaultClass = ref('')

const inputErrorClass = 'bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 dark:bg-gray-700 focus:border-red-500 block w-full p-2.5 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500';
const labelErrorClass = 'block mb-2 text-sm font-medium text-red-500 dark:text-red-500';

watch(() => props.errors?.length, (newValue, oldValue) => {
    //find in formwrapper input and add is-invalid class
    const input = formwrapper.value?.querySelector('input, select, textarea, .vs__dropdown-toggle, date');
    if (input) {
        if (newValue) {
            input.className = `${defaultClass.value} ${inputErrorClass}`;
        } else {
            input.className = defaultClass.value;
        }
    }
})

onMounted(() => {
    //get in slot input and add default class
    const input = formwrapper.value?.querySelector('input, select, textarea, .vs__dropdown-toggle, date');
    if (input) {
        defaultClass.value = input.className;
    }
})

const mergedLabelClass = computed(() => {
    return props.errors && props.errors.length > 0 ? labelErrorClass : props.labelClass;
})


</script>
<template>
    <div v-bind="$attrs" ref="formwrapper">
        <label for="error" :class="mergedLabelClass">
            {{ label }}
        </label>
        <slot></slot>
        <div v-if="errors && errors.length > 0">
            <p v-for="error in errors" :key="error" class="mt-2 text-sm text-red-600 dark:text-red-500">
                {{ error }}
            </p>
        </div>

    </div>
</template>



<style scoped></style>