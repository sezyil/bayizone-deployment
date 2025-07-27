<script setup lang="ts">
type AlertTypes = 'alert-warning' | 'alert-success' | 'alert-danger' | 'alert-info' | 'alert-primary';
type AlertAlign = 'alert-styled-right' | 'alert-styled-left';
interface AlertProps {
    type?: AlertTypes;
    align?: AlertAlign;
    dismissible?: boolean;
}
const props = withDefaults(defineProps<AlertProps>(), {
    type: 'alert-primary',
    align: 'alert-styled-left',
    dismissible: false,
});

const mergedClasses = computed(() => {
    return {
        alert: true,
        [props.type]: true,
        [props.align]: true,
        'alert-dismissible': props.dismissible,
    }
})
</script>
<template>
    <div :class="mergedClasses" role="alert">
        <button v-if="dismissible" type="button" class="close" data-dismiss="alert"><span>×</span></button>
        <slot></slot>
    </div>
</template>

<style scoped></style>