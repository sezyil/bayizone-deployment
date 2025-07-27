<script setup lang="ts">
/* paginator props */
const emit = defineEmits(['pageChange'])
const props = defineProps({
    total: {
        type: Number,
        required: true
    },
    limit: {
        type: Number,
        required: true
    },
    componentPage: {
        type: Number,
        required: true
    }
})

const totalPages = computed(() => Math.ceil(props.total / props.limit));
const currentPage = computed(() => props.componentPage);

</script>
<template>
    <div class="d-flex justify-content-center pt-3 mb-3">
        <ul class="pagination">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a href="#" class="page-link" @click.prevent="$emit('pageChange', currentPage - 1)"><i
                        class="icon-arrow-left8"></i></a>
            </li>
            <li class="page-item" :class="{ active: currentPage === page }" v-for="page in totalPages" :key="page">
                <a href="#" class="page-link" @click.prevent="$emit('pageChange', page)">{{ page }}</a>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a href="#" class="page-link" @click.prevent="$emit('pageChange', currentPage + 1)"><i
                        class="icon-arrow-right8"></i></a>
            </li>
        </ul>
    </div>
</template>



<style scoped></style>