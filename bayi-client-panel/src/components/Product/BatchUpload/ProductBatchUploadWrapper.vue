<template>
    <!-- bs tabs -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs nav-tabs-top nav-justified">
                        <li class="nav-item">
                            <a href="#tab1" class="nav-link active" data-toggle="tab">Bekleyen İşlemler</a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab2" class="nav-link" data-toggle="tab">Toplu Ürün Güncelle</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- toplu ürün yükleme -->
                        <div class="tab-pane fade show active" id="tab1">
                            <ProductBatchUploadIndex />
                        </div>
                        <!-- toplu ürün güncelleme -->
                        <div class="tab-pane fade" id="tab2">
                            <ProductBatchUpload />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">

</script>

<style scoped></style>