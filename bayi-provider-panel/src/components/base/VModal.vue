<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { uuid } from 'vue-uuid';

const emit = defineEmits(['close', 'submit'])
type IModalSizes = 'sm' | 'md' | 'lg' | 'xl';
const modalElement = ref<HTMLElement | null>(null)
const { t } = useI18n()
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
    }
})
const modalId = uuid.v4()

const detectOutsideClick = (e: MouseEvent) => {
    if (modalElement.value && !modalElement.value.contains(e.target as Node)) {
        closeModal()
    }
}

const closeModal = () => {
    emit('close')
}
const submitModal = () => {
    emit('close')
}
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
        <div class="modal-dialog" role="document" :class="`modal-${size}`" ref="modalElement">
            <div class="modal-content">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click.prevent="closeModal">{{
                        t('actions.close') }}</button>
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



<style scoped></style>