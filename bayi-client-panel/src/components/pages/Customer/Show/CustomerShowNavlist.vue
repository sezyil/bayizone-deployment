<script setup lang="ts">
export type CustomerTabType = 'bankAccounts' | 'warehouses' | 'notes' | 'transactions'
export interface CustomerShowTab {
    text: string;
    type: CustomerTabType;
    component: any;
}
const emit = defineEmits<{
    (event: 'changeTab', tab: CustomerTabType): void
}>();
const props = defineProps({
    id: {
        type: String,
        required: true
    },
    tabs: {
        type: Array as PropType<CustomerShowTab[]>,
        required: true
    },
    activeTab: {
        type: String,
        required: true
    }
})



</script>
<template>
    <div class="navbar navbar-expand-lg navbar-light px-0">
        <div class="text-center d-lg-none w-100">
            <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse"
                data-target="#navbar-second">
                <i class="icon-menu7 mr-2"></i>
                Menü Çubuğu
            </button>
        </div>

        <div class="navbar-collapse collapse" id="navbar-second">
            <ul class="nav navbar-nav">
                <li class="nav-item" v-for="(item, index) in tabs" :key="index"
                    :class="{ 'active': activeTab === item.type }">
                    <a href="#" class="navbar-nav-link" @click.prevent="emit('changeTab', item.type)"
                        :class="{ 'active': activeTab === item.type }">
                        <i class="icon-cog3 mr-2"></i>
                        {{ item.text }}
                    </a>
                </li>
            </ul>


        </div>
    </div>
</template>



<style scoped></style>