<script setup lang="ts">
import InputWithError from '/@src/components/Elements/Form/Input/InputWithError.vue';
import { IVariantListItem } from '/@src/request/request-variant';
import { useCatalogProductStore } from '/@src/stores/catalog/product';
const store = useCatalogProductStore();

const props = defineProps({
    index: {
        type: Number as PropType<number>,
        required: true
    },
    availableVariants: {
        type: Array as PropType<IVariantListItem[]>,
        required: true
    }
})

const currentData = computed(() => {
    return store.productData.dimensions[props.index];
})

const removeVariant = () => {
    store.removeVariant(props.index, 'DIMENSION');
}

const removeVariantValue = (index: number) => {
    store.removeVariantValue(props.index, index, 'DIMENSION');
}

const changeDimension = (e: Event) => {
    e.preventDefault();
    e.stopPropagation();
    let oldValue = store.productData.dimensions[props.index].variant_id;
    const target = e.target as HTMLSelectElement;
    const _value = target.value;
    //if data dimensions has same variant_id, not allow to change
    if (store.productData.dimensions.some(dimension => dimension.variant_id === target.value)) {
        alert('Bu varyasyon daha önce eklenmiş.');
        target.value = oldValue;
        return;
    }

    store.resetVariantValue(props.index, _value, 'DIMENSION');
}

const handleAddVariantValue = () => {
    if (!store.productData.dimensions[props.index].variant_id) {
        alert('Değer eklemek için önce bir ölçü varyasyonu seçmelisiniz.');
        return;
    }
    store.addVariantValue(props.index, 'DIMENSION');
}

</script>
<template>
    <div>
        <div class="row border p-2 position-relative">
            <div class="col-md-12 mb-2">
                <div class="form-group">
                    <!-- with plus button append icon -->
                    <label for="input-dimension">Ölçü</label>
                    <div class="input-group">
                        <InputWithError :errors="store.formErrors.dimensions[props.index]?.variant_id">
                            <select name="dimension" :value="store.productData.dimensions[props.index].variant_id"
                                @change="changeDimension" class="form-control">
                                <option value="">Seçiniz</option>
                                <option v-for="variant in availableVariants" :key="variant.id" :value="variant.id">
                                    {{ variant.name }}
                                </option>
                            </select>
                        </InputWithError>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button"
                                @click.prevent="handleAddVariantValue()">+</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 border p-2" v-for="(value, _index) in currentData.value" :key="_index">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="input-length">Uzunluk</label>
                            <div class="input-group">
                                <InputWithError
                                    :errors="store.formErrors.dimensions[props.index]?.value[_index]?.length">
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-ruler"></i></span>
                                        </span>
                                        <input type="number" step="0" min="0" name="length"
                                            v-model="store.productData.dimensions[props.index].value[_index].length"
                                            placeholder="Uzunluk" id="input-length-class" class="form-control">
                                        <span class="input-group-append">
                                            <span class="input-group-text">cm</span>
                                        </span>
                                    </div>
                                </InputWithError>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="input-width">
                                Genişlik
                            </label>
                            <InputWithError :errors="store.formErrors.dimensions[props.index]?.value[_index]?.width">
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-ruler-horizontal"></i></span>
                                    </span>
                                    <input type="number" step="0" min="0" name="width"
                                        v-model="store.productData.dimensions[props.index].value[_index].width"
                                        placeholder="Genişlik" class="form-control">
                                    <span class="input-group-append">
                                        <span class="input-group-text">cm</span>
                                    </span>
                                </div>
                            </InputWithError>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="input-height">Yükseklik</label>
                            <div class="input-group">
                                <InputWithError
                                    :errors="store.formErrors.dimensions[props.index]?.value[_index]?.height">
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-ruler-vertical"></i></span>
                                        </span>
                                        <input type="number" step="0" min="0" name="height"
                                            v-model="store.productData.dimensions[props.index].value[_index].height"
                                            placeholder="Yükseklik" id="input-height-class" class="form-control">
                                        <span class="input-group-append">
                                            <span class="input-group-text">cm</span>
                                        </span>
                                    </div>
                                </InputWithError>
                            </div>
                        </div>
                    </div>
                    <div class="delete-icon" @click.prevent="removeVariantValue(_index)"><i class="fas fa-trash"></i>
                    </div>
                </div>
            </div>
            <div class="delete-icon" @click.prevent="removeVariant">X</div> <!-- Silme işareti -->
        </div>
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