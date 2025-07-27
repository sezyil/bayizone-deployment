<script setup lang="ts">
type TType = 'top-label' | 'left-label' | 'left-icon' | 'right-icon'
interface IInputWithErrorProps {
    errors?: string[];
    icon?: string;
    type?: TType;
    clickable?: boolean;
    errorVisible?: boolean;
    class?: string;
    isFinalClass?: boolean;
}
const emit = defineEmits<{
    (e: 'iconClick'): void
}>()
const formwrapper = ref<HTMLElement | null>(null);
const props = withDefaults(defineProps<IInputWithErrorProps>(), {
    errors: () => [],
    icon: '',
    type: 'top-label',
    clickable: false,
    errorVisible: true
});

const clickTrigger = () => {
    if (props.clickable)
        emit('iconClick');
}

watch(() => props.errors.length, (newValue, oldValue) => {
    //find in formwrapper input and add is-invalid class
    const input = formwrapper.value?.querySelector('input, select, textarea, .vs__dropdown-toggle');
    if (input) {
        if (newValue) {
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    }
})

const wrapperClass = computed(() => {
    const classes = ['bz-i-w', 'form-group', 'form-group-feedback'];
    if (props.isFinalClass && props.class) {
        return props.class;
    }
    if (props.class) classes.push(props.class);

    if (props.type === 'left-icon') classes.push('form-group-feedback-left');
    else if (props.type === 'right-icon') classes.push('form-group-feedback-right');
    else if (props.type === 'left-label' || props.type === 'top-label') classes.push('form-label-group');

    return classes.join(' ');
})


</script>
<template>
    <div class="" :class="wrapperClass" ref="formwrapper">
        <slot></slot>
        <div class="form-control-feedback" v-if="icon && !errors.length" :class="{ 'cursor-pointer': props.clickable }"
            @click="clickTrigger">
            <i :class="icon"></i>
        </div>
        <span v-if="errorVisible" class="invalid-feedback" v-for="(item, index) in  errors" :key="index">{{ item }}</span>
    </div>
</template>



<style>
.invalid-feedback {
    display: inherit !important;
}

.vs__dropdown-toggle.is-invalid {
    border: 1px solid #dc3545 !important;
    border-color: #dc3545 !important;
}
</style>