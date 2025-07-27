<script setup lang="ts">
export interface ISidebarMenuItem {
  uri?: string;
  title: string;
  icon?: string;
}

const props = withDefaults(defineProps<ISidebarMenuItem>(), {
  uri: "/#",
  title: "",
  icon: "",
});
</script>
<template>
  <li class="nav-item">
    <RouterLink :to="uri" class="nav-link routing">
      <i :class="icon" v-if="icon"></i>
      <span>
        {{ title }}
      </span>
    </RouterLink>
  </li>
</template>

<style scoped></style>
