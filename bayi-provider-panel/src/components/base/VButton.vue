<script setup lang="ts">
interface Props {
    class?: string;
    icon?: string;
    text: string;
    disabled?: boolean;
    type?: HTMLButtonElement['type'];
}
const emit = defineEmits(['click'])
const props = withDefaults(defineProps<Props>(), {
    class: 'btn-primary',
    text: 'Button',
    disabled: false,
    type: 'button',
})

const mergedIconClass = computed(() => {
    return props.icon ? `${props.icon} mr-1` : ''
})

const mergedClass = computed(() => {
    return `btn ${props.class}`
})

const handleClick = () => {
    if (!props.disabled) {
        emit('click')
    }
}

</script>
<template>
    <button :class="mergedClass" @click.prevent="handleClick" :disabled="props.disabled" :type="props.type">
        <i v-if="icon" :class="mergedIconClass"></i> {{ text }}
    </button>
</template>
<style scoped></style>