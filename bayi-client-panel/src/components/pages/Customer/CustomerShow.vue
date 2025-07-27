<script setup lang="ts">
import { ICompanyCustomerForm } from '/@src/shared/form/interface-company-customer';
import { get } from '/@src/request/request-customer';
import CustomerBankAccountIndex from '/@src/components/pages/Customer/BankAccounts/CustomerBankAccountIndex.vue';
import CustomerWarehouseIndex from '/@src/components/pages/Customer/Warehouse/CustomerWarehouseIndex.vue';
import CustomerNoteIndex from '/@src/components/pages/Customer/Note/CustomerNoteIndex.vue';
import TransactionIndex from '/@src/components/pages/Transaction/TransactionIndex.vue';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { CustomerShowTab } from './Show/CustomerShowNavlist.vue';
import type { CustomerTabType } from './Show/CustomerShowNavlist.vue';
import CustomerNoteModal from './Note/CustomerNoteModal.vue';
const props = defineProps({
    id: {
        type: String,
        required: true
    }
})
const viewWrapper = useViewWrapper();

const customerData = ref<ICompanyCustomerForm>()
const router = useRouter()
const route = useRoute()
const refreshTable = ref(false)

const AvailableTabs: CustomerShowTab[] = [
    //transaction
    { text: 'Hareketler', type: 'transactions', component: TransactionIndex },
    { text: 'Banka Hesapları', type: 'bankAccounts', component: CustomerBankAccountIndex },
    { text: 'Depolar', type: 'warehouses', component: CustomerWarehouseIndex },
    { text: 'Notlar', type: 'notes', component: CustomerNoteIndex }
]

const activeTab = ref<CustomerTabType>();
const activeTabComponent = computed(() => {
    const tab = AvailableTabs.find(tab => tab.type === activeTab.value);
    return tab ? tab.component : null;
});

const getData = async () => {
    viewWrapper.setLoading(true)
    try {
        const { data } = await get(props.id)
        customerData.value = data.data as ICompanyCustomerForm
    } catch (error) {
        catchFieldError(error)
    } finally {
        viewWrapper.setLoading(false)
    }
}

const checkQuery = () => {
    const query = route.query.tab as CustomerTabType
    if (query && AvailableTabs.some(tab => tab.type === query)) {
        activeTab.value = query
    } else {
        activeTab.value = 'transactions'
        router.push({ query: { tab: 'transactions' } })
    }
}

const handleTabChange = (tab: CustomerTabType) => {
    activeTab.value = tab
    router.push({ query: { tab } })
}

const noteModalStatus = ref(false);
const noteModalId = ref<string | undefined>(undefined);

const handleNoteModalTable = (status: boolean, id?: string) => {
    noteModalStatus.value = status;
    noteModalId.value = id;
}
const handleNoteModalUpdate = () => {
    handleNoteModalTable(false)
    refreshTable.value = true
    setTimeout(() => {
        refreshTable.value = false
    }, 100)
}


const noteModalTitle = computed(() => {
    return noteModalId.value ? "Notu Düzenle" : "Not Ekle"
})

onMounted(async () => {
    await getData()
    checkQuery()
})
</script>
<template>
    <div v-if="customerData && activeTab">
        <CustomerShowHeader :customerData="customerData" :id="props.id">
            <template #extra-actions>
                <li class="list-inline-item py-1 py-md-0" v-if="activeTab === 'transactions'">
                    <RouterLink class="btn btn-light border-transparent"
                        :to="`/app/customers/${props.id}/transactions`">
                        <i class="fas fa-list mr-2"></i> Hareketler
                    </RouterLink>
                </li>
                <li class="list-inline-item py-1 py-md-0" v-if="activeTab === 'transactions'">
                    <RouterLink class="btn btn-light border-transparent"
                        :to="`/app/transactions/create?company_customer_id=${props.id}`">
                        <i class="fas fa-plus mr-2"></i> Yeni Hareket Ekle
                    </RouterLink>
                </li>
                <li class="list-inline-item py-1 py-md-0" v-if="activeTab === 'bankAccounts'">
                    <RouterLink class="btn btn-light border-transparent"
                        :to="`/app/customers/${props.id}/bank_accounts/create`">
                        <i class="fas fa-plus mr-2"></i> Yeni Banka Hesabı Ekle
                    </RouterLink>
                </li>
                <li class="list-inline-item py-1 py-md-0" v-else-if="activeTab === 'warehouses'">
                    <RouterLink class="btn btn-light border-transparent"
                        :to="`/app/customers/${props.id}/warehouses/create`">
                        <i class="fas fa-plus mr-2"></i> Yeni Depo Ekle
                    </RouterLink>
                </li>
                <li class="list-inline-item py-1 py-md-0" v-else-if="activeTab === 'notes'">
                    <button class="btn btn-light border-transparent" @click="handleNoteModalTable(true)">
                        <i class="fas fa-plus mr-2"></i> Yeni Not Ekle
                    </button>
                </li>
            </template>
        </CustomerShowHeader>
        <CustomerShowNavlist :tabs="AvailableTabs" :activeTab="activeTab" :id="props.id" @changeTab="handleTabChange" />

        <div class="card p-1 pb-0">
            <div class="card-body p-1 pb-0">
                <div class="tab-content" v-if="activeTabComponent && !refreshTable">
                    <component v-if="activeTab == 'transactions'" :is="activeTabComponent"
                        :company_customer_id="props.id" />
                    <component v-else :is="activeTabComponent" :company_customer_id="props.id"
                        @editItem="handleNoteModalTable(true, $event)" />
                </div>
            </div>
        </div>


        <CustomerNoteModal v-if="activeTab == 'notes'" :title="noteModalTitle" :company_customer_id="props.id"
            :modalStatus="noteModalStatus" :id="noteModalId" @close="handleNoteModalTable(false)"
            @update="handleNoteModalUpdate" />
    </div>
</template>



<style scoped></style>