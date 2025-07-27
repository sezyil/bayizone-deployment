<script setup lang="ts">
</script>
<template>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title mb-0">Gelişmiş</h2>
        </div>
        <div class="card-body">
            <div class="d-lg-flex">
                <ul class="nav nav-tabs nav-tabs-vertical flex-column mr-lg-3 wmin-lg-200 mb-lg-0 border-bottom-0">
                    <li class="nav-item">
                        <a href="#codes-tab" class="nav-link active" data-toggle="tab">
                            <i class="icon-menu7 mr-2"></i> Ürün Kodları</a>
                    </li>
                    <li class="nav-item">
                        <a href="#category-tab" class="nav-link" data-toggle="tab">
                            <i class="icon-mention mr-2"></i> Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a href="#attributes-tab" class="nav-link" data-toggle="tab">
                            <i class="icon-mention mr-2"></i> Özellikler</a>
                    </li>

                </ul>

                <div class="tab-content flex-lg-fill">
                    <div class="tab-pane fade show active" id="codes-tab">
                        <TPAdvancedCodes />
                    </div>

                    <div class="tab-pane fade" id="category-tab">
                        <TPAdvancedCategory />
                    </div>

                    <div class="tab-pane fade" id="attributes-tab">
                        <TPAdvancedAttributes />
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
