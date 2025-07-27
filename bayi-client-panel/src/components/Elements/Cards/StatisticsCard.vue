<template>
    <div class="card card-body has-bg-image" :style="{ backgroundColor: backgroundColor }"
        @click="$emit('cardClicked', $event)">
        <div class="media">
            <div class="mr-3 align-self-center" v-if="iconPosition == 'left'">
                <i :class="icon" class="text-secondary" :style="{ color: iconColor }"></i>
            </div>


            <div class="media-body" :class="{ 'text-right': iconPosition == 'left' }">
                <h3 class="mb-0" :style="{ color: textColor }">{{ numberArea }}</h3>
                <span :style="{ color: textColor }" class="text-uppercase font-size-sm">{{ textContent }}</span>
            </div>

            <div class="ml-3 align-self-center" v-if="iconPosition == 'right'">
                <i :class="icon"></i>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    numberArea: {
        type: [String, Number]
    },
    iconPosition: {
        type: String,
        default: "right"
    },
    icon: String,
    iconColor: String,
    textContent: [String, Number],
    backgroundColor: String,
    textColor: String
});
const emit = defineEmits(['cardClicked']);


</script>

<style lang="scss" scoped></style>