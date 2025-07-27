<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle("Ürün Listesi");
const router = useRouter();
const permission = useUserPermission().getByName('product');
const activeView = ref<'index' | 'batch'>('index');
if (!permission?.view && !permission?.create) {
    swalPermissionDenied(() => router.push('/app'));
}
</script>
<template>
    <PageContent>
        <template #header v-if="permission?.create">
            <!-- switch button batch upload -->
            <div class="d-flex gap-2 flex-column flex-md-row">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-primary" :class="{ active: activeView === 'index' }"
                        @click="activeView = 'index'">
                        <input type="radio" name="options" id="option1" autocomplete="off" checked> Ürün Listesi
                    </label>
                    <label class="btn btn-danger" :class="{ active: activeView === 'batch' }">
                        <input type="radio" name="options" id="option2" autocomplete="off">
                        <i class="fas fa-upload mr-1"></i> Toplu Ürün Yükle
                        <span class="badge badge-light">Yakında</span>
                    </label>
                </div>
                <button class="btn btn-bayi-red" @click="router.push('/app/catalog/product/create')"
                    v-show="activeView === 'index'">
                    <i class="icon-plus3 mr-1"></i>
                    Yeni Ürün Ekle
                </button>
            </div>
        </template>
        <ProductIndex v-if="permission?.view" v-show="activeView === 'index'" :can-delete="permission.delete"
            :can-update="permission.update" :can-create="permission.create" />
        <!--  <ProductBatchUploadWrapper v-if="permission?.create" v-show="activeView === 'batch'" /> -->
    </PageContent>
</template>


<style scoped></style>