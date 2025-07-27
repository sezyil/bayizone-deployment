<script setup lang="ts">
import { useCatalogProductStore } from '/@src/stores/catalog/product';
import { IProductAttribute } from '/@src/shared/product/interface-product-attribute';
const store = useCatalogProductStore();

const disabledValues = computed(() => store.productData.attributes.map((e: IProductAttribute) => e.attribute_id));

const addRow = () => store.addAttribute();

const rowDelete = (row_index: number) => store.removeAttribute(row_index);

const handleSelected = (value: number, index: number) => {
    store.productData.attributes[index].attribute_id = value;
}


</script>

<template>
    <div class="table-responsive">
        <table id="product-attribute" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <td class="text-left">Özellik</td>
                    <td class="text-left">Açıklama</td>
                    <td></td>
                </tr>
            </thead>
            <tbody v-if="store.productData.attributes.length > 0">
                <tr :id="`attribute-row-${index}`" v-for="(item, index) in store.productData.attributes" :key="index"
                    :class="{ 'border border-danger': store.formErrors.attributes[index] }">
                    <td width="40%">
                        <InputWithError :errors="store.formErrors.attributes[index]">
                            <AttributeSelector :prop-val="item.attribute_id ?? 0"
                                @option-selected="handleSelected($event, index)" :disabled-values="disabledValues" />
                        </InputWithError>

                    </td>
                    <td width="50%">
                        <InputWithError :errors="store.formErrors.attributes[index]" :error-visible="false">
                            <textarea rows="2" placeholder="Açıklama" class="form-control"
                                v-model="item.text"></textarea>
                        </InputWithError>
                    </td>
                    <td class="text-center" width="10%">
                        <button type="button" class="btn btn-danger" @click.prevent="rowDelete(index)">
                            <i class="fas fa-minus-circle"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="3" class="text-center">Özellik bulunmamaktadır.</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-center">
                        <button type="button" id="button-attribute" class="btn btn-primary" @click.prevent="addRow">
                            <i class="fas fa-plus-circle"></i>
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>