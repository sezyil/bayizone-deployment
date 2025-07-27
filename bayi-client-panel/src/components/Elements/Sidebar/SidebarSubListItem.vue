<script setup lang="ts">
export interface ISidebarSubListItem {
  title: string;
  icon?: string;
}
const hasChildren = ref<boolean>(false);

const parent = ref<HTMLElement | null>(null);
const children = ref<HTMLElement | null>(null);
const props = withDefaults(defineProps<ISidebarSubListItem>(), {
  title: "",
  icon: "",
});
onMounted(() => {
  //find in children .routing class after that find in children .nav-link class hasChildren set true
  if (parent.value) {
    let child = children.value;
    if (child) {
      let routing = child.querySelector(".routing");
      if (routing) {
        hasChildren.value = true;
      }
    }
  }
});
</script>
<template>
  <li
    class="nav-item nav-item-submenu"
    ref="parent"
    v-show="hasChildren"
  >
    <a class="nav-link" href="javascript:void(0)">
      <i :class="icon" v-if="icon"></i>
      <span>
        {{ title }}
      </span>
    </a>

    <template v-if="$slots.default">
      <ul class="nav nav-group-sub" data-submenu-title="Icons" ref="children">
        <slot></slot>
      </ul>
    </template>
  </li>
</template>

<style scoped></style>
