<script setup lang="ts">
const emit = defineEmits<{
    (e: 'close'): void
}>();
const props = withDefaults(defineProps<{
    active: boolean;
    modalTitle: string;
    wrapperOverrided: boolean;
}>(), {
    active: false,
    modalTitle: '',
    wrapperOverrided: false
})

const handleClose = () => {
    emit('close');
};


watch(() => props.active, (value) => {
    if (value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
});

const computedWrapperClass = computed(() => {
    return props.wrapperOverrided ? '' : 'flex flex-col sm:flex-col md:flex-col lg:flex-row xl:flex-row 2xl:flex-row';
});

</script>
<template>

    <transition name="fade">
        <div v-if="active">
            <div @click="handleClose" class="fixed inset-0 bg-black bg-opacity-70 z-10">
            </div>
            <div class="w-full max-w-lg md:max-w-2xl lg:max-w-3xl xl:max-w-4xl 2xl:max-w-5xl top-3 mx-auto my-auto shadow-lg bg-white z-50 fixed inset-0 m-auto p-3
            overflow-y-auto justify-between flex flex-col h-5/6 sm:h-3/4 md:h-3/4 lg:h-3/4 xl:h-3/4 2xl:h-3/4 rounded
             ">
                <div class="flex flex-col">
                    <div class="flex p-5 flex-row  justify-between border-b">
                        <h2 class="text-2xl font-semibold text-zinc-600">{{ modalTitle }}</h2>
                        <button @click="handleClose" class=" px-1 py-1 text-sm text-black  flex items-center">
                            <span class="pi pi-times"></span>
                        </button>
                    </div>
                    <div :class="computedWrapperClass">
                        <slot></slot>
                    </div>

                </div>

                <slot name="footer"></slot>

            </div>
        </div>
    </transition>
</template>



<style scoped>
/* smooth  */
.fade-enter-active,
.fade-leave-active {
    -moz-transition: opacity .5s;
    -webkit-transition: opacity .5s;
    -o-transition: opacity .5s;
    -ms-transition: opacity .5s;
    transition: opacity .5s;
    /* firefox */
}

.fade-enter-from,
.fade-leave-to {
    -moz-opacity: 0;
    -webkit-opacity: 0;
    -o-opacity: 0;
    -ms-opacity: 0;
    opacity: 0;
}
</style>