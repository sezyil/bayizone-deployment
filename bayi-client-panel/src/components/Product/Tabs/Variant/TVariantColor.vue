<script setup lang="ts">
import InputWithError from '/@src/components/Elements/Form/Input/InputWithError.vue';
import { IVariantListItem } from '/@src/request/request-variant';
import { IVariantValue } from '/@src/shared/form/interface-variant-value';
import { useCatalogProductStore } from '/@src/stores/catalog/product';
const store = useCatalogProductStore();
const props = defineProps({
    index: {
        type: Number as PropType<number>,
        required: true
    },
    availableVariants: {
        type: Array as PropType<IVariantListItemTransformed[]>,
        required: true
    }
})
export interface IVariantListItemTransformed extends Omit<IVariantListItem, 'values'> {
    values: IVariantValue[]
}

const findValues = computed(() => {
    return props.availableVariants.find(variant => variant.id === store.productData.colors[props.index].variant_id)
})

const removeVariant = () => {
    store.removeVariant(props.index, 'COLOR');
}

const handleVariantChange = (e: Event) => {
    e.preventDefault();
    e.stopPropagation();
    const target = e.target as HTMLSelectElement;
    const _value = target.value;
    //check store data if has same variant_id, not allow to change
    if (store.productData.colors.some(color => color.variant_id === target.value)) {
        alert('Bu varyasyon daha önce eklenmiş.');
        target.value = '';
        return;
    }

    store.resetVariantValue(props.index, _value, 'COLOR');
}

</script>
<template>
    <div class="row border p-2 position-relative">
        <div class="col-12 col-md-6">
            <InputWithError :errors="store.formErrors.colors[index]?.variant_id">
                <div class="form-group">
                    <label for="input-color">Renk</label>
                    <select name="color" :value="store.productData.colors[props.index].variant_id"
                        @change="handleVariantChange" class="form-control">
                        <option value="">Seçiniz</option>
                        <option v-for="variant in availableVariants" :key="variant.id" :value="variant.id">
                            {{ variant.name }}
                        </option>
                    </select>
                </div>
            </InputWithError>
        </div>
        <div class="col-12 col-md-6">
            <InputWithError :errors="store.formErrors.colors[index]?.variant_value_id">
                <!-- multi checkbox! -->
                <div class="form-group" v-if="findValues?.values">
                    <label for="input-color">Renk Değerleri</label>
                    <div class="d-flex flex-wrap">
                        <div class="form-check form-check-inline" v-for="value in findValues?.values" :key="value.id">
                            <input type="checkbox"
                                :id="store.productData.colors[props.index].variant_id + '-' + value.id"
                                :value="value.id" :name="`color-${store.productData.colors[props.index].variant_id}`"
                                v-model="store.productData.colors[props.index].variant_value_id"
                                class="form-check-input mr-1">
                            <label :for="store.productData.colors[props.index].variant_id + '-' + value.id"
                                class="form-check-label">{{ value.name }}</label>
                        </div>
                    </div>
                </div>
            </InputWithError>
        </div>
        <div class="delete-icon" @click.prevent="removeVariant">X</div> <!-- Silme işareti -->
    </div>

</template>



<style scoped>
.delete-icon {
    position: absolute;
    top: 0;
    right: 0;
    cursor: pointer;
    color: #f00;
    padding-top: 5px;
    padding-right: 5px;
}

.form-group {
    margin-bottom: 0;
}
</style>