<template>
  <div v-if="isLoading" class="card-overlay">
    <i style="font-size: 3rem; color: #18a4d1" :class="icon"></i>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  isLoading: Boolean,
  icon: {
    type: String,
    default: "fa fa-sync lds-dual-ring mr-2"
  }
})
</script>

<style scoped>
.card_overlay {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  height: 100%;
  width: 100%;
  opacity: 0;
  visibility: none;
  transition: .5s ease;
  background-color: #393839;
}

.lds-dual-ring {
  display: inline-block;
  width: 80px;
  height: 80px;
}

.lds-dual-ring:after {
  content: " ";
  display: block;
  width: 64px;
  height: 64px;
  margin: 8px;
  border-radius: 50%;
  border: 6px solid #fff;
  border-color: #fff transparent #fff transparent;
  animation: lds-dual-ring 1.2s linear infinite;
}

@keyframes lds-dual-ring {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}</style>
