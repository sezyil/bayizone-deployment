<template>
    <!-- Page content -->
    <div class="page-content">
        <PageLoader />
        <!-- Main sidebar -->
        <Sidebar />
        <!-- /main sidebar -->

        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Main navbar -->

            <MainNavbar />

            <!-- /main navbar -->

            <!-- Inner content -->

            <slot />

            <!-- /inner content -->
        </div>
        <!-- /main content -->
    </div>
</template>

<script setup lang="ts">
</script>

<style lang="scss" scoped></style>