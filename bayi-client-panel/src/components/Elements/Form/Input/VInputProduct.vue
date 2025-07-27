<template>
    <v-select v-model="selected" :filterable="false" :options="options" @search="onSearch"
        @option:selected="setSelected" :clear-search-on-select="resetOnSelect">
        <template #no-options="{ search, searching, loading }">
            <div class="d-center">
                <span>Veri Bulunamadı</span>
            </div>
        </template>
        <!-- <template slot="option" slot-scope="option">
            <div class="d-center">
                {{ options.name }}
            </div>
        </template> -->
        <!-- <template slot="selected-option" slot-scope="option">
            <div class="selected d-center">

                {{ options.name }}
            </div>
        </template> -->
    </v-select>
</template>

<script setup lang="ts">
import _ from 'lodash';
import { productAutocomplete } from '/@src/request/request-product';

export interface SelectedCategories {
    id: number;
    name: string;
}

const emit = defineEmits(['optionSelected', 'selectedOptions']);
const props = defineProps({
    propVal: {
        type: Number,
        default: 0,
    },
    disabledValues: {
        type: Array,
        default: []
    },
    resetOnSelect: {
        default: false
    },
    withDefault: {
        default: true
    }
})
const options = ref<any[]>([]);
const onSearch = (_search: any, loading: any) => {
    loading(true);
    search(loading, _search);
}

const selected = ref();

const getData = (_search = '', loading: any = undefined) => productAutocomplete(_search, props.withDefault).then(response => {
    let data = response.data.data;
    console.log(data);

    let values: {
        value: number,
        label: string
    }[] = [];

    values.push({
        value: 0,
        label: 'Ürün Seçiniz'
    });

    data.map((e: any) => {
        values.push({
            value: e.id,
            label: e.name
        });
    })



    options.value = values;
    setDefault();

    if (loading) loading(false);
});

const search = _.debounce((loading, search) => {
    getData(search, loading)
}, 350)

const setSelected = (value: any) => {
    emit('optionSelected', value.value, value);
    if (props.resetOnSelect)
        selected.value = null;
}

const setDefault = () => options.value.forEach((e: any) => {
    if (e.value === Number(props.propVal)) selected.value = e;
})


watch(
    () => props.propVal,
    (current, previous) => {
        setDefault();
    }
);

watch(
    () => props.disabledValues,
    (current, previous) => {
        getData();
    }
);

onMounted(() => {
    getData();
});
</script>

<style lang="scss" scoped></style>