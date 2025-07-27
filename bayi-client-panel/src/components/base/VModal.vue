<script setup lang="ts">
import { HtmlHTMLAttributes, PropType } from 'vue';
import { uuid } from 'vue-uuid';

const emit = defineEmits(['close', 'submit'])
type IModalSizes = 'sm' | 'md' | 'lg' | 'xl' | null
const modalElement = ref<HTMLElement | null>(null)
const modalContent = ref<HTMLElement | null>(null)
const props = defineProps({
    title: {
        type: String,
        default: 'Modal Title'
    },
    size: {
        type: String as PropType<IModalSizes>,
        default: 'md'
    },
    show: {
        type: Boolean,
        default: true
    },
    scrollable: {
        type: Boolean,
        default: false
    },
    modalContentStyle: {
        type: String as PropType<HtmlHTMLAttributes['style']>,
        default: ''
    },
    loader: {
        type: Boolean,
        default: false
    },
    outsideClick: {
        type: Boolean,
        default: true
    }
})
const modalId = uuid.v4()
const timer = ref<any>(null)

const detectOutsideClick = (e: MouseEvent) => {
    if (modalElement.value && !modalElement.value.contains(e.target as Node)) {
        if (!props.outsideClick) {
            /* add bounce animation */
            modalContent.value?.classList.add('animated', 'shake')
            clearTimeout(timer.value)
            timer.value = setTimeout(() => {
                modalContent.value?.classList.remove('animated', 'shake')
            }, 500)

        } else {
            closeModal()
        }
    }
}

const closeModal = () => {
    emit('close')
}
const submitModal = () => {
    emit('close')
}

const modalElementClass = computed(() => {
    return {
        'modal-sm': props.size === 'sm',
        'modal-md': props.size === 'md',
        'modal-lg': props.size === 'lg',
        'modal-xl': props.size === 'xl',
        'modal-dialog-scrollable': props.scrollable
    }
})

watch(() => props.show, (val) => {
    if (val) {
        document.body.classList.add('modal-open')
    } else {
        document.body.classList.remove('modal-open')
    }
})
</script>
<template>
    <!-- Modal -->
    <!-- Button trigger modal -->
    <div class="modal fade" :id="modalId" tabindex="-1" role="dialog" :aria-labelledby="modalId" aria-hidden="true"
        @click="detectOutsideClick" :style="{ display: show ? 'block' : 'none' }" :class="{ 'show': show }" v-if="show">
        <div class="modal-dialog" role="document" :class="modalElementClass" ref="modalElement">
            <VLoader :active="loader" />
            <div class="modal-content" :style="modalContentStyle" ref="modalContent">
                <div class="modal-header">
                    <h5 class="modal-title">{{ title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        @click.prevent="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <slot />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        @click.prevent="closeModal">Kapat</button>
                    <template v-if="$slots.footer">
                        <slot name="footer" />
                    </template>
                </div>
            </div>
        </div>
    </div>
    <teleport to='body' v-if="show">
        <div class="modal-backdrop fade show"></div>
    </teleport>
</template>



<style scoped>
.modal-footer {
    padding-top: 10px;
    border-top: 1px solid #dcdcdd;
}

/*shake animation*/
@keyframes shake {
    0% {
        transform: translateX(0);
    }

    25% {
        transform: translateX(-10px);
    }

    50% {
        transform: translateX(10px);
    }

    75% {
        transform: translateX(-10px);
    }

    100% {
        transform: translateX(0);
    }
}

/* if outside click is disabled */
.shake {
    animation: shake 0.5s;
}
</style>