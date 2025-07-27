<template>
  <slot></slot>
</template>

<script setup lang="ts">
import $ from "jquery";

//find active menu
const findActiveMenu = () => {
  //find a.router-link-exact-active $
  const activeMenu = $(".router-link-exact-active");
  // nav-item-submenu
  const activeMenuParent = activeMenu.parents(".nav-item-submenu");
  // nav-item-open
  if (activeMenuParent.length) {
    activeMenuParent.addClass("nav-item-open");
    activeMenuParent.children(".nav-group-sub").slideDown(250);
  }
  //if
};
let navClass = "nav-sidebar",
  navItemClass = "nav-item",
  navItemOpenClass = "nav-item-open",
  navLinkClass = "nav-link",
  navSubmenuClass = "nav-group-sub",
  navScrollSpyClass = "nav-scrollspy",
  navSlidingSpeed = 250;

onMounted(() => {
  $("." + navClass + ":not(." + navScrollSpyClass + ")").each(function () {
    $(this)
      .find("." + navItemClass)
      .has("." + navSubmenuClass)
      .children("." + navItemClass + " > " + "." + navLinkClass)
      .not(".disabled")
      .on("click", function (e: Event) {
        e.preventDefault();

        // Simplify stuff
        var $target = $(this);

        // Collapsible
        if ($target.parent("." + navItemClass).hasClass(navItemOpenClass)) {
          $target
            .parent("." + navItemClass)
            .removeClass(navItemOpenClass)
            .children("." + navSubmenuClass)
            .slideUp(navSlidingSpeed);
        } else {
          $target
            .parent("." + navItemClass)
            .addClass(navItemOpenClass)
            .children("." + navSubmenuClass)
            .slideDown(navSlidingSpeed);
        }

        // Accordion
        if ($target.parents("." + navClass).data("nav-type") == "accordion") {
          $target
            .parent("." + navItemClass)
            .siblings(":has(." + navSubmenuClass + ")")
            .removeClass(navItemOpenClass)
            .children("." + navSubmenuClass)
            .slideUp(navSlidingSpeed);
        }
      });
  });
  findActiveMenu();
});
</script>

<style scoped>
a.router-link-exact-active {
  background-color: rgba(255, 255, 255, 0.1);
}
</style>
