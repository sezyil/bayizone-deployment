<template>
    <v-select v-model="selected" :filterable="false" :options="options" @search="onSearch"
        @option:selected="setSelected">
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
import { attributeGroupAutocomplete } from '/@src/request/request-attribute-group';
const emit = defineEmits(['optionSelected']);
const props = defineProps({
    propVal: {
        type: Number,
    },
    disabledValues: {
        type: Array,
        default: []
    },
    withDefault: {
        type: Boolean,
        default: true
    }
})

interface VSelectItem {
    value: number,
    label: string
}

const options = ref<VSelectItem[]>([]);
const onSearch = (_search: any, loading: any) => {
    loading(true);
    search(loading, _search);
}

const selected = ref();

interface IData {
    id: number,
    description: {
        name: string
    }
}

const getData = (_search = '', loading: any = undefined) => attributeGroupAutocomplete(_search, props.withDefault).then(response => {
    let data = response.data.data as IData[];

    options.value = data.map((e) => {
        if (!props.disabledValues.includes(e.id)) {
            return {
                value: e.id,
                label: e.description.name,
            };
        }
    }).filter(Boolean) as { value: number; label: string; }[];

    setDefault();

    if (loading) loading(false);
});

const search = _.debounce((loading, search) => {
    getData(search, loading)
}, 350)

const setSelected = (value: any, x: any) => {
    emit('optionSelected', value.value);
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

onMounted(() => {
    getData();
});
</script>

<style lang="scss" scoped></style>