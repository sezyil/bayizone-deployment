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
import { onMounted, ref, watch } from 'vue';
import { list } from '/@src/request/request-attribute';
const emit = defineEmits(['optionSelected']);
const props = defineProps({
    propVal: {
        type: Number,
        default: 0
    },
    disabledValues: {
        type: Array,
        default: () => []
    }
})
const options = ref<any[]>([]);
const onSearch = (_search: any, loading: any) => {
    loading(true);
    search(loading, _search);
}

const selected = ref();

const getData = (_search = '', loading: any = undefined) => list(_search).then((response: any) => {
    let data = response.data.data;
    let values = [];
    values.push({
        value: 0,
        label: "Lütfen bir seçim yapınız",
    });
    data.map((e: any) => {
        if (!props.disabledValues.includes(e.id) || Number(e.id) == props.propVal)
            values.push({
                value: e.id,
                label: e.group_description.name + ' -> ' + e.description.name,
            });
    })
    options.value = values;
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
    if (e !== undefined && e.value === Number(props.propVal)) selected.value = e;
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