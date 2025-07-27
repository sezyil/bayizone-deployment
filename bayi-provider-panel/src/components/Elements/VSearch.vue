<template>
    <v-select label="name" :filterable="false" :options="option" @search="onSearch">
        <template slot="no-options">
            type to search GitHub repositories..
        </template>
        <template slot="option" slot-scope="option">
            <div class="d-center">
                <img :src='option.owner.avatar_url' />
                {{ option.full_name }}
            </div>
        </template>
        <template slot="selected-option" slot-scope="option">
            <div class="selected d-center">
                <img :src='option.owner.avatar_url' />
                {{ option.full_name }}
            </div>
        </template>
    </v-select>
</template>

<script setup>
import _ from 'lodash';
import { ref } from 'vue';
const option = ref([]);
const onSearch = (_search, loading) => {
    if (_search.length) {
        loading(true);
        search(loading, _search);
    }
}
const search = _.debounce((loading, search) => {
    fetch(
        `https://api.github.com/search/repositories?q=${escape(search)}`
    ).then(res => {
        res.json().then(json => (option.value = json.items));
        loading(false);
    });
}, 350)
</script>

<style lang="scss" scoped></style>