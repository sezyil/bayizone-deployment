<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import { IServiceResponseRoot, companyGetServices, IAvailableAddon, ISubscription, companyUpdateServices } from '/@src/request/request-company'
import { useUserSession } from '/@src/stores/userSession';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const isLoading = ref<boolean>(false);
const swal = useSwal();
const planData = computed(() => useUserSession().planData);
export interface IAvailableAddonForm extends IAvailableAddon {
    added: boolean;
    amount?: number;
}
const availableAddons = ref<IAvailableAddonForm[]>([]);
const activeSubscription = ref<ISubscription | null>(null);
const router = useRouter();

const getServices = async () => {
    isLoading.value = true;
    try {
        const { data } = await companyGetServices();
        const resp = data as IServiceResponseRoot;
        availableAddons.value = resp.data.available_addons.map((addon) => {
            return {
                ...addon,
                added: false,
                ...addon.is_boolean ? {} : { amount: 1 }
            }
        });
        activeSubscription.value = resp.data.subscription;


    } catch (error) {
        console.log(error);
    }
    isLoading.value = false;
}

const calculateTotal = () => {
    return availableAddons.value.reduce((acc, addon) => {
        return acc + (addon.added ? addon.last_price * (addon.amount || 1) : 0);
    }, 0);
}

interface IActiveAddons {
    name: string;
}

const activeSubscriptionAddons = computed(() => {
    let transformed: IActiveAddons[] = [];
    if (activeSubscription.value) {
        if (activeSubscription.value.left_user_count) {
            transformed.push({ name: `${activeSubscription.value.left_user_count} Kalan Kullanıcı` });
        }
        if (activeSubscription.value.left_provider_panel_count) {
            transformed.push({ name: `${activeSubscription.value.left_provider_panel_count} Kalan Bayi Yönetim Paneli Müşterisi` });
        }
        if (activeSubscription.value.sales_management) {
            transformed.push({ name: 'Satış Yönetimi' });
        }
        if (activeSubscription.value.simple_accounting) {
            transformed.push({ name: 'Basit Muhasebe' });
        }
        if (activeSubscription.value.online_store) {
            transformed.push({ name: 'Online Mağaza B2B' });
        }

    }
    return transformed;

})


const handleSave = async () => {
    const selectedAddons = availableAddons.value.filter((addon) => addon.added);
    if (selectedAddons.length === 0) {
        swal.fire({
            title: 'Hizmet Seçimi',
            text: 'Lütfen en az bir hizmet seçiniz.',
            icon: 'warning',
        });
        return;
    }
    //await swal fire
    const { value } = await swal.fire({
        title: 'Hizmetleri Güncelle',
        text: 'Hizmetlerinizi güncellemek istediğinize emin misiniz? Bu işlemden sonra siparişiniz oluşturulacaktır.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'Hayır',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    });

    if (value) {
        isLoading.value = true;
        try {
            const { data } = await companyUpdateServices(selectedAddons);
            const response = data.data;
            swal.fire({
                title: 'Hizmet Güncelleme',
                text: 'Siparişiniz başarıyla oluşturuldu. Ödeme sayfasına yönlendiriliyorsunuz.',
                icon: 'success',
            }).then(() => {
                router.push(`/app/payments/${response.order_id}/pay`);
            });
        } catch (error: any) {
            swal.fire({
                title: 'Hizmet Güncelleme',
                text: error?.response?.data?.msg || 'Hizmetleriniz güncellenirken bir hata oluştu.',
                icon: 'error',
            });
        }
        isLoading.value = false;
    }
}


onMounted(() => {
    getServices();
})


</script>
<template>
    <div class="alert alert-warning alert-styled-left alert-dismissible" v-if="!planData?.plan_name">
        <p>Mevcut Planınız Bulunmamaktadır. Dilerseniz Abonelik Satın Alarak Hizmetlerimizi Kullanmaya
            Başlayabilirsiniz.</p>
        <RouterLink to="/app/plans" class="btn btn-outline-dark btn-labeled btn-labeled-left">
            <b><i class="icon-cog"></i></b>
            Abonelik Satın Al
        </RouterLink>
    </div>
    <div class="card" v-else>
        <div class="card-body">
            <div class="card text-center">
                <div class="card-header">Mevcut Hizmet</div>
                <div class="card-body text-center">
                    <!-- if has plan -->
                    <div v-if="planData?.plan_name">
                        <h6 class="font-weight-semibold mb-0" v-if="!planData?.is_trial">{{ planData?.plan_name }}</h6>
                        <h6 class="font-weight-semibold mb-0" v-else>Deneme Paketi</h6>
                    </div>
                    <!-- if has no plan -->
                    <div v-else>
                        <h6 class="font-weight-semibold mb-0">Hizmet Bulunamadı</h6>
                        <p class="text-muted">Hizmetiniz bulunmamaktadır. Hizmet almak için lütfen iletişime
                            geçiniz.
                        </p>
                    </div>

                </div>
            </div>
            <div class="card">
                <div class="card-header">Mevcut Hizmet Özellikleri</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4" v-for="addon in activeSubscriptionAddons" :key="addon.name">
                            <div class="card">
                                <div class="card-body">
                                    <div class="media align-items-center">
                                        <div class="mr-3">
                                            <i class="icon-checkmark3 icon-2x text-success"></i>
                                        </div>
                                        <div class="media-body">
                                            <h6 class="media-title font-weight-semibold">{{ addon.name }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card" v-if="!planData?.is_trial">
                <form @submit.prevent="handleSave">
                    <div class="card-header">Satın Alınabilir Hizmetler</div>
                    <div class="card-body">
                        <!-- active  -->

                        <!-- available -->
                        <div class="row">
                            <div class="col-md-6" v-for="addon in availableAddons" :key="addon.id">
                                <!-- iconed card with name -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body d-flex justify-content-between align-items-center">
                                                <div class="info-area" v-if="addon.is_boolean">
                                                    <h6 class="media-title font-weight-semibold">{{ addon.name }}</h6>

                                                    <span class="badge badge-primary badge-pill">
                                                        ({{ addon.price }} $ x {{ activeSubscription?.left_month }}
                                                        Ay) Toplam: {{ addon.last_price }} $
                                                    </span>

                                                </div>
                                                <div class="info-area" v-else>
                                                    <h6 class="media-title font-weight-semibold">{{ addon.name }}</h6>
                                                    <!-- input quantity -->
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" v-model="addon.amount"
                                                            min="1" max="10">
                                                        <div class="input-group-append" v-if="addon.amount">
                                                            <!-- calculated -->
                                                            <span class="input-group-text">{{ addon.price * addon.amount
                                                                }}
                                                                $</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- price -->
                                                <button type="button"
                                                    :class="addon.added ? 'btn btn-danger' : 'btn btn-primary'"
                                                    @click="addon.added = !addon.added">
                                                    <span v-if="addon.added">Hizmeti Kaldır</span>
                                                    <span v-else>Hizmeti Ekle</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- total price end align -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-end align-items-center">
                            <div class="mr-1">
                                <h6 class="font-weight-semibold">Toplam Tutar:</h6>
                            </div>
                            <div>
                                <h6 class="font-weight-semibold">{{ calculateTotal() }} $</h6>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end" v-if="planData?.plan_name">
                            <button type="submit" class="btn btn-bayi-red">
                                <i class="icon-checkmark3 mr-2"></i> Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card" v-else>
                <div class="card-header">Satın Alınabilir Hizmetler</div>
                <div class="card-body">
                    <p class="text-info border p-1 rounded">Deneme Paketi içeriği değiştirilemez. Bu alan sadece ücretli
                        paketler
                        için geçerlidir.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ovveride  router-link */
.router-link-active {
    background-color: #f58646 !important;
    border-color: #f58646 !important;
}
</style>