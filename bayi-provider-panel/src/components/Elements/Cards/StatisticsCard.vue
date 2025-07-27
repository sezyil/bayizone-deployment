<template>
    <div class="card card-body text-black has-bg-image" :style="{ backgroundColor: backgroundColor }"
        @click="$emit('cardClicked', $event)">
        <div class="media">
            <div class="mr-3 align-self-center" v-if="iconPosition == 'left'">
                <i :class="icon" :style="{ color: iconColor }"></i>
            </div>


            <div class="media-body" :class="{ 'text-right': iconPosition == 'left' }">
                <h3 class="mb-0" :style="{ color: textColor }">{{ numberArea }}</h3>
                <span :style="{ color: textColor }" class="text-uppercase font-size-xs">{{ textContent }}</span>
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
    textContent: String,
    backgroundColor: String,
    textColor: String
});
const emit = defineEmits(['cardClicked']);


</script>

<style lang="css" scoped>
.text-black {
    color: #515151;
}
</style>