<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { get } from '/@src/request/request-customer';
import CustomerOfferApi from '/@src/request/request-customer-offer';
import TransactionApi, { ITransactionApiGetBalanceResponse } from '/@src/request/request-transaction';
import { CurrencyTypes } from '/@src/shared/response/interface-utils-response';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { getCurrencyName, getCurrencySymbol } from '/@src/utils/currency_helper';
const route = useRoute();
const router = useRouter();
const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle('Müşteri Hareketleri');
//id param
const { id: company_customer_id } = route.params as { id: string };
if (!company_customer_id) router.push("/app/customers");
const permission = useUserPermission().getByName('transaction');
if (!permission.view) {
    swalPermissionDenied(() => router.push('/app'))
}
const balanceData = ref<ITransactionApiGetBalanceResponse['data']>();
const api = new TransactionApi()
const availableCurrencies = ref<CurrencyTypes[]>(['tl', 'usd', 'euro', 'gbp'])
const activeTab = ref<CurrencyTypes>('tl')
const customerData = ref<any>()




const getCustomerInfo = async () => {
    try {
        const { data } = await get(company_customer_id)
        let companyName = data.data.company_name;
        customerData.value = data.data
        viewWrapper.setPageTitle(companyName + ' - Müşteri Hareketleri')
    } catch (error) {
        catchFieldError(error)
    }
}

const handleTabChange = async (tab: CurrencyTypes) => {
    activeTab.value = tab
    await router.push({ query: { tab } })

}

const mountedParams = async () => {
    const query = route.query.tab as CurrencyTypes
    if (query && availableCurrencies.value.includes(query)) {
        activeTab.value = query
    } else {
        activeTab.value = 'tl'
        router.push({ query: { tab: 'tl' } })
    }
}

const loadData = async () => {
    viewWrapper.setLoading(true)
    viewWrapper.setLoading(false)
}

onMounted(async () => {
    await mountedParams()
    await getCustomerInfo()
    await loadData()
})

</script>
<template>
    <PageContent>
        <template #header>
            <div class="d-flex gap-1">
                <button class="btn btn-secondary" @click="$router.push('/app/customers/' + company_customer_id)">
                    <i class="icon-arrow-left7 mr-2"></i>
                    Geri Dön
                </button>
                <VButtonCreate @click="$router.push('/app/transactions/create?company_customer_id=' + company_customer_id)"
                    v-if="permission.create" />
                <!-- refresh -->
                <button class="btn btn-light btn-icon btn-refresh" @click="loadData">
                    <i class="icon-spinner11"></i>
                </button>
            </div>

        </template>
        <div>
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
                        <li class="nav-item" v-for="(item, index) in availableCurrencies" :key="index"
                            :class="{ 'active': activeTab === item }">
                            <a href="#" class="navbar-nav-link" @click.prevent="handleTabChange(item)"
                                :class="{ 'active': activeTab === item }">
                                {{ getCurrencyName(item) }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-1" v-if="!viewWrapper.isLoading">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <TransactionIndex :company_customer_id="company_customer_id" :currency="activeTab"
                                    @update="loadData" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <VLoader :active="true" />
            </div>
        </div>
        <!-- tablist -->



    </PageContent>
</template>


<style scoped></style>