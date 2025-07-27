<template>
    <nav>
        <ul class="pagination justify-content-end mb-0">
            <li class="page-item">
                <button
                    type="button"
                    class="page-link"
                    @click="onClickPreviousPage"
                    :disabled="isInFirstPage"
                >←</button>
            </li>

            <li class="page-item">
                <button
                    type="button"
                    class="page-link"
                    @click="onClickFirstPage"
                    :disabled="isInFirstPage"
                    v-if="!isInFirstPage && chkGoToFirst"
                >1..</button>
            </li>

            <li v-for="page in pages" class="page-item" :key="page">
                <button
                    type="button"
                    class="page-link"
                    @click="onClickPage(page.name)"
                    :disabled="page.isDisabled"
                    :class="{ active: isPageActive(page.name) }"
                >{{ page.name }}</button>
            </li>

            <li class="page-item">
                <button
                    type="button"
                    class="page-link"
                    @click="onClickLastPage"
                    :disabled="isInLastPage"
                    v-if="!isInLastPage && chkGoToLast"
                >..{{ totalPages }}</button>
            </li>

            <li class="page-item">
                <button
                    type="button"
                    class="page-link"
                    @click="onClickNextPage"
                    :disabled="isInLastPage"
                >→</button>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    props: {
        maxVisibleButtons: {
            type: Number,
            required: false,
            default: 3,
        },
        totalPages: {
            type: Number,
            required: true,
        },
        total: {
            type: Number,
            required: true,
        },
        perPage: {
            type: Number,
            required: true,
        },
        currentPage: {
            type: Number,
            required: true,
        },
    },
    computed: {
        startPage() {
            if (this.currentPage === 1) {
                return 1;
            }

            if (this.currentPage === this.totalPages) {
                const start = this.totalPages - (this.maxVisibleButtons - 1);

                if (start === 0) {
                    return 1;
                } else {
                    return start;
                }
            }

            return this.currentPage - 1;
        },
        endPage() {
            return Math.min(
                this.startPage + this.maxVisibleButtons - 1,
                this.totalPages
            );
        },
        pages() {
            const range = [];

            for (let i = this.startPage; i <= this.endPage; i += 1) {
                range.push({
                    name: i,
                    isDisabled: i === this.currentPage,
                });
            }

            return range;
        },
        isInFirstPage() {
            return this.currentPage === 1;
        },
        isInLastPage() {
            if (this.total === 0) {
                return true;
            }
            return this.currentPage === this.totalPages;
        },
        chkGoToFirst() {
            let x = this.pages.filter(function (page) {
                return page.name == 1;
            })
            return x.length ? false : true;
        },
        chkGoToLast() {
            let _this = this;
            let x = this.pages.filter(function (page) {
                return page.name == _this.totalPages;
            });
            return x.length ? false : true;
        }
    },
    methods: {
        onClickFirstPage() {
            this.$emit("pagechanged", 1);
        },
        onClickPreviousPage() {
            this.$emit("pagechanged", this.currentPage - 1);
        },
        onClickPage(page) {
            this.$emit("pagechanged", page);
        },
        onClickNextPage() {
            this.$emit("pagechanged", this.currentPage + 1);
        },
        onClickLastPage() {
            this.$emit("pagechanged", this.totalPages);
        },
        isPageActive(page) {
            return this.currentPage === page;
        },
        inSight(page) {
            return [].map(function () {

            })
        }
    },
};
</script>

<style scoped>
.pagination {
    display: flex;
    list-style-type: none !important;
}

.pagination-item {
    display: inline-block !important;
}

.page-link {
    font-size: 0.875rem !important;
    display: flex !important;
    width: 36px !important;
    height: 36px !important;
    margin: 0 3px !important;
    padding: 0 !important;
    border-radius: 50% !important;
    align-items: center !important;
    justify-content: center !important;
}

.page-link.active {
    z-index: 1 !important;
    color: #fff !important;
    border-color: #18a4d1 !important;
    background-color: #18a4d1 !important;
}

.page-link:disabled {
    cursor: auto !important;
    pointer-events: none !important;
    color: #8898aa !important;
    border-color: #dee2e6 !important;
    background-color: #fff !important;
}

.justify-content-end {
    justify-content: end !important;
}
</style>
