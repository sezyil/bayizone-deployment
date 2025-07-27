<script setup lang="ts">
import useMoment from '/@src/composables/useMoment';
import { useSwal } from '/@src/composables/useSwal';
import PlansApi from '/@src/request/request-plans';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';
interface IResponse {
    id: string
    is_active: boolean
    name: string
    attributes: string[]
    is_featured: boolean
    is_trial: boolean
    trial_days: number
    user_count: number
    sales_management: boolean
    simple_accounting: boolean
    online_store: boolean
    product_count: number
    provider_panel_count: number
    selected_price: number
    prices: Price[]
    monthly_price: number
    yearly_price: number
    divided_price: number
}

export interface Price {
    id: number
    price: number
    month: number
    selected: boolean
    type: "monthly" | "yearly"
}

const alwaysIncluded = {
    proposal_management: 'Teklif yönetimi',
    proforma_invoices: 'Proforma Fatura Oluşturma',
    whatsapp_integration: 'WhatsApp ile teklif gönderme',
}

const planItems = {
    online_store: 'Online Mağaza B2B',
    sales_management: 'Satış yönetimi',
    simple_accounting: 'Basit Muhasebe yönetim'
}
const userSession = useUserSession();
const hasPlan = computed(() => userSession.planData?.plan_name);
const userPlan = computed(() => userSession.planData);
const route = useRoute();
const router = useRouter();
const moment = useMoment();
const { upgrade } = route.query as { upgrade: string | undefined };
const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle('Planlar');
const api = new PlansApi();
const swal = useSwal();
const plansData = ref<IResponse[]>([]);
const planType = ref<Price['type']>('yearly');
const getPlans = async () => {
    viewWrapper.setLoading(true);
    try {
        const response = await api.list();
        const data = response.data.data as IResponse[];
        plansData.value = data.map((plan) => {
            let finded = plan.prices.find((price) => price.selected);
            if (finded) {
                plan.selected_price = finded.id;
            }
            return plan;
        });
    } catch (error) {
        useSwal().fire({
            title: 'Hata',
            text: 'Planlar getirilirken bir hata oluştu. Lütfen tekrar deneyin eğer hata devam ederse destek ekibimizle iletişime geçin.',
            icon: 'error',
        });
    } finally {
        viewWrapper.setLoading(false);
    }
};

const setPlanType = (type: 'monthly' | 'yearly') => {
    planType.value = type;

    //set selected price to first price
    plansData.value.forEach((plan) => {
        let find = plan.prices.find((price) => price.type === type)?.id
        if (find) {
            setCartData(plan, find);
        }
    });
};

const setCartData = (plan: IResponse, price_id: number) => {
    plan.selected_price = price_id;
};

const dataList = computed(() => {
    return plansData.value.filter((plan) => plan.prices.some((price) => price.type === planType.value));
});

const purchasePlan = async (plan: IResponse) => {
    swal.fire({
        title: 'Plan Satın Al',
        text: 'Plan satın almak istediğinize emin misiniz? Ödeme işlemi sonrası planınız aktif olacaktır.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'Hayır',
    }).then(async (result) => {
        if (result.isConfirmed) {
            viewWrapper.setLoading(true);
            try {
                const response = await api.purchase(plan.id, plan.selected_price);
                const data = response.data.data;
                swal.fire({
                    title: 'Başarılı',
                    text: 'Fatura oluşturuldu. Ödeme işlemi için yönlendiriliyorsunuz.',
                    icon: 'success',
                }).then(() => {
                    router.push('/app/payments/:id/pay'.replace(':id', data.order_id));
                });
            } catch (error) {
                let msg = (error as any)?.response?.data?.msg || 'Plan satın alınırken bir hata oluştu. Lütfen tekrar deneyin eğer hata devam ederse destek ekibimizle iletişime geçin.';
                useSwal().fire({
                    title: 'Hata',
                    text: msg,
                    icon: 'error',
                });
            } finally {
                viewWrapper.setLoading(false);
            }
        }
    });
};


onMounted(async () => {
    await getPlans();
    if (upgrade) {
        useSwal().fire({
            title: 'Girmeye çalıştığınız sayfa için planınız yetersiz.',
            text: 'Lütfen planınızı yükseltin.',
            icon: 'warning',
        });
    }
});
</script>

<template>
    <PageContent>
        <!-- if user has a plan warning -->
        <div class="alert alert-warning alert-styled-left alert-dismissible" v-if="1==0">
            <p>Mevcut planınızı değiştiremezsiniz. Dilerseniz Ayarlar->Hizmetlerim sayfasından planınıza ek hizmetler
                alıp deneyiminizi arttırabilirsiniz.</p>
            <RouterLink to="/app/profile?tab=services" class="btn btn-outline-dark btn-labeled btn-labeled-left"><b><i
                        class="icon-cog"></i></b>Hizmetlerim</RouterLink>
        </div>
        <div v-else>
            <div class="alert alert-info alert-styled-left alert-dismissible" v-if="userPlan?.is_trial">
                <p class="m-0">Ücretsiz deneme sürümünüz devam ediyor. Bitiş tarihi:
                    {{ moment(userPlan.trial_ends_at).format('LL') }}
                </p>
            </div>

            <div class="pricing-block">

                <!-- monthly or yearly buttons seperated -->
                <div class="pricing-control">
                   
                    <button type="button" class="btn" @click="setPlanType('yearly')"
                        :class="planType === 'yearly' ? 'btn-secondary' : 'btn-outline-secondary'">Yıllık</button>
                </div>


            </div>

            <div class="row">
                <div class="col-12 col-md-12 col-lg-12 col-xl-11  mx-auto">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-4" v-for="plan in plansData" :key="plan.id">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="mt-2 mb-3">{{ plan.name }}</h4>
                                    <!-- if yearly show monthly price -->
                                    <h1 class="pricing-table-price"><span class="mr-1">$</span>
                                        {{ planType === 'monthly' ? plan.monthly_price : plan.yearly_price }} /
                                        {{ planType === 'monthly' ? 'Ay' : 'Yıl' }}
                                    </h1>
                                    <h5 class="text-muted" v-if="planType === 'yearly'">
                                        <del>${{ plan.monthly_price }}</del> <span class="text-success">${{
                                            plan.divided_price
                                        }} / Ay</span>
                                    </h5>
                                    <!-- select box -->
                                    <ul class="pricing-table-list list-unstyled mb-3 text-left">
                                        <li v-for="(value, key) in alwaysIncluded" :key="key">
                                            <i class="mr-2 icon-checkbox-checked2 text-success"></i>
                                            {{ value }}
                                        </li>
                                        <li v-for="(value, key) in planItems" :key="key">
                                            <i class="mr-2"
                                                :class="plan[key] ? 'icon-checkbox-checked2 text-success' : 'icon-cancel-square2 text-danger'"></i>
                                            {{ value }}
                                        </li>
                                        <li>
                                            <!-- kullanıcı sayısı -->
                                            <i class="mr-2 icon-users text-indigo"></i>
                                            {{ plan.user_count }} Yönetim Kullanıcısı
                                        </li>
                                        <li>
                                            <!-- Bayi Yönetim Paneli Müşterisi -->
                                            <i class="mr-2 fa fa-users text-warning"></i>
                                            {{ plan.provider_panel_count }} Bayi Yönetim Paneli Müşterisi
                                        </li>
                                    </ul>
                                    <button class="btn btn-outline-secondary btn-block"
                                        @click="purchasePlan(plan)">Satın
                                        Al</button>
                                </div>
                                <div class="ribbon-container" v-if="plan.is_featured">
                                    <div class="ribbon bg-secondary text-white">Öne Çıkan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </PageContent>
</template>



<style scoped>
.pricing-block {
    flex-direction: column;
    display: flex;
}

.pricing-control {
    display: flex;
    justify-content: center;
    position: relative;
    width: fit-content;
    margin: 0 auto;
    margin-bottom: 20px;
}

.pricing-control button {
    margin: 0 5px;
    padding: 12px 64px;
}

.pricing-control button.active {
    background-color: #f1f1f1 !important;
    color: white !important;
}
</style>