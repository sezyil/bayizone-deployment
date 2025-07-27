<script setup lang="ts">
import { uuid } from 'vue-uuid';
import { IProductOption } from '/@src/shared/product/interface-product-option';
const props = defineProps({
    data: {
        type: Object as PropType<IProductOption>,
        required: false,
        default: () => {
            return {
                id: 0,
                name: '',
                required: false,
                add_to_price: 0,
                values: []
            }
        }
    },
    errors: {
        type: Array as PropType<string[]>,
        default: []
    }
});
const emits = defineEmits(['add-row', 'remove-row', 'remove-option'])
const randomId = 'chk-' + uuid.v4()

const addRow = () => {
    emits('add-row');
}

const selected = ref()

const removeRow = (valueIndex: number) => {
    emits('remove-row', valueIndex)
}

const removeOption = () => {
    emits('remove-option');
}

onMounted(() => {
    $(function () {
        //@ts-ignore
        $('[data-toggle="tooltip"]').tooltip()
    })
})



</script>

<template>
    <div class="card">
        <div class="card-body">

            <fieldset id="option-row-0">
                <!-- is Required Checkbox -->
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" :id="randomId" v-model="data.required">
                        <label class="custom-control-label align-items-center" :for="randomId">Zorunlu
                            <i class="ml-1 fas fa-info-circle" data-toggle="tooltip" data-placement="top"
                                title="Zorunlu seçenekler müşteri tarafından seçilmek zorundadır."> </i>
                        </label>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="option-value0" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <td class="text-left">{{ data.name }}</td>
                                <td class="text-right">Fiyatı</td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-icon border-2"
                                        @click.prevent="removeOption()">
                                        <i class="fas fa-minus-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        </thead>
                        <tbody v-if="data.values.length">
                            <tr :id="`option-value-row_` + data.name + '_' + _index"
                                v-for="(_item, _index) in data.values" :key="_index">
                                <td class="text-left">
                                    <select class="form-control" role="option_value" v-model="_item.option_value_id"
                                        required>
                                        <option value="">Seçiniz</option>
                                        <option :value="__item.id" v-for="(__item, __index) in data.available"
                                            :key="__index">{{ __item.name }}</option>
                                    </select>
                                    <InputWithError :errors="errors" v-if="Array.isArray(errors[_index])" />
                                </td>
                                <td class="text-right">
                                    <!-- input with prepend select -->
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select v-model="_item.add_to_price" class="form-control" required>
                                                <option :value="1">+</option>
                                                <option :value="0">-</option>
                                            </select>
                                        </div>
                                        <input type="number" v-model.number="_item.price" placeholder="Fiyatı" min="0"
                                            required step="0.01" class="form-control">
                                    </div>


                                </td>

                                <td class="text-left">
                                    <button type="button" class="btn btn-danger" @click="removeRow(_index)">
                                        <i class="fa fa-minus-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="3" class="text-center">
                                    <InputWithError :errors="errors" v-if="Array.isArray(errors)" />
                                    <div v-else>
                                        Seçenek Yok
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"></td>
                                <td class="text-left"><button type="button" @click.prevent="addRow()" title=""
                                        class="btn btn-primary"><i class="fa fa-plus-circle"></i></button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </fieldset>

        </div>
    </div>
</template>