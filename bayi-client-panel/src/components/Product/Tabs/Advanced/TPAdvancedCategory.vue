<script setup lang="ts">
import { useCatalogProductStore } from '/@src/stores/catalog/product';
import { ILinks } from '/@src/shared/product/interface-product-links';
import { IProductCategory } from '/@src/shared/product/interface-product-category';
import { SelectedCategories } from '../../Elements/CategorySelector.vue';
const store = useCatalogProductStore();

const disabledCategories = computed(() => store.productData.links.categories.map(e => e.category_id));

const removeRow = (item: IProductCategory) => {
    store.removeCategory(item);
    //remove from selected categories
    selectedCategories.value = selectedCategories.value.filter(e => e.id != item.category_id);
}

const categorySelected = (id: any, data: any) => {
    store.addCategory({
        category_id: data.value,
    })
}

const selectedCategories = ref<SelectedCategories[]>([]);

const handleSelectedCategories = (data: SelectedCategories[]) => {
    selectedCategories.value = data;
}

const getName = (id: number) => {
    let name = '';
    selectedCategories.value.map(e => {
        if (e.id == id)
            name = e.name;
    })
    return name;
}
</script>
<template>
    <div class="form-group mb-3">
        <label class="">Kategoriler</label>
        <CategorySelector @option-selected="categorySelected" :disabled-values="disabledCategories"
            @selected-options="handleSelectedCategories" :with-parent="false" :reset-on-select="true"
            :with-default="true" />
        <div class="form-text">(Otomatik Tamamlama)</div>

        <div class="table-responsive">
            <table id="product-category" class="table table-bordered table-framed table-sm m-0">
                <tbody>
                    <tr :id="`product-category-${item.category_id}`" v-if="selectedCategories.length > 0"
                        v-for="(item, index) in store.productData.links.categories" :key="index">
                        <input type="hidden" v-model="item.category_id">
                        <td>{{ getName(item.category_id) }}</td>
                        <td class="text-right">
                            <button type="button" class="btn btn-danger btn-sm" @click="removeRow(item)">
                                <i class="fas fa-minus-circle"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>



<style scoped></style>