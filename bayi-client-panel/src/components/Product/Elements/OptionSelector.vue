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
import { optionAutocomplete } from '/@src/request/request-option';
const emit = defineEmits(['optionSelected']);
const props = defineProps({
    propVal: {
        type: Number,
        default: 0,
    },
    disabledValues: {
        default: []
    },
    withParent: {
        default: true
    },
    resetOnSelect: {
        default: false
    }
})
const options = ref([]);
const onSearch = (_search: any, loading: any) => {
    loading(true);
    search(loading, _search);
}


const search = _.debounce((loading, search) => {
    getData(search, loading)
}, 350)
const selected = ref(null);
const getData = (_search = '', loading = undefined) => optionAutocomplete(_search, true).then(response => {
    let data = response.data.data;
    let values: any = [];
    data.map((e: any) => {
        //@ts-ignore
        if (!props.disabledValues.includes(e.id))
            values.push({
                value: e.id,
                label: e.description.name,
                type: e.type
            })
    });
    options.value = values;
    //@ts-ignore
    if (loading) loading(false);
});

const setSelected = (value: any) => {
    emit('optionSelected', value.value, value);
    if (props.resetOnSelect)
        selected.value = null;
}

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