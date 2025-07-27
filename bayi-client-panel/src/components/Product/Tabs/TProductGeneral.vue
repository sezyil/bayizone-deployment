<script setup lang="ts">
import { useCatalogProductStore } from '/@src/stores/catalog/product';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import { LanguageGetter } from '/@src/shared/common/common-language';
import Avatar from 'primevue/avatar';
import Divider from 'primevue/divider';
import { ECompanyCustomerGroup, getCompanyCustomerGroupDescription } from '/@src/shared/form/interface-company-customer';
const trLang = LanguageGetter('tr');
const enLang = LanguageGetter('en');
const store = useCatalogProductStore();
const errors = computed(() => store.formErrors)
const trErrors = computed(() => {
    //check undefined
    if ((store?.formErrors?.descriptions?.tr ?? []).length > 0 ||
        (store?.formErrors?.names?.tr ?? []).length > 0) {
        return true;
    }
    return false;
})
const groups: Record<string, ECompanyCustomerGroup> = {
    'POTENTIAL': 'POTENTIAL',
    'CUSTOMER': 'CUSTOMER',
    'SUPPLIER': 'SUPPLIER',
};

const enErrors = computed(() => {
    //check undefined
    if ((store?.formErrors?.descriptions?.en ?? []).length > 0 ||
        (store?.formErrors?.names?.en ?? []).length > 0) {
        return true;
    }
    return false;
})
const errorClass = "bg-danger";
watch(trErrors, (value) => {
    let trTabpanel = document.getElementById('trTabpanel')?.closest('.p-tabview-header-action');
    if (trTabpanel) {
        if (value) {
            trTabpanel.classList.add(errorClass);
        } else {
            trTabpanel.classList.remove(errorClass);
        }
    }
})
watch(enErrors, (value) => {
    let enTabpanel = document.getElementById('enTabpanel')?.closest('.p-tabview-header-action');
    if (enTabpanel) {
        if (value) {
            enTabpanel.classList.add(errorClass);
        } else {
            enTabpanel.classList.remove(errorClass);
        }
    }
})
</script>
<template>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title mb-0">Ürün Bilgileri</h2>
        </div>
        <div class="card-body">
            <TabView class="tabview-custom p-1">
                <!-- TR -->
                <TabPanel>
                    <template #header>
                        <!-- error class -->
                        <div class="d-flex align-items-center gap-2" id="trTabpanel">
                            <Avatar :image="trLang.flag" shape="circle" />
                            <span class="white-space-nowrap text-black">{{ trLang.description }}</span>
                        </div>
                    </template>
                    <InputWithError :errors="store.formErrors.names.tr">
                        <label>Ürün Adı:</label>
                        <input type="text" v-model="store.productData.names.tr" placeholder="Ürün Adı" id="input-name"
                            class="form-control">
                    </InputWithError>
                    <div class="form-group">
                        <label>Açıklama:</label>
                        <InputWithError :errors="store.formErrors.descriptions.tr">
                            <textarea v-model="store.productData.descriptions.tr" id="trDescription"
                                class="form-control"></textarea>
                        </InputWithError>
                    </div>
                </TabPanel>
                <!-- EN -->
                <TabPanel>
                    <template #header>
                        <div class="d-flex align-items-center gap-2" id="enTabpanel">
                            <Avatar :image="enLang.flag" shape="circle" size="normal" />
                            <span class="font-bold white-space-nowrap text-black">{{ enLang.description }}</span>
                        </div>
                    </template>
                    <InputWithError :errors="store.formErrors.names.en">
                        <label>Ürün Adı:</label>
                        <input type="text" v-model="store.productData.names.en" placeholder="Ürün Adı" id="input-name"
                            class="form-control">
                    </InputWithError>
                    <div class="form-group">
                        <label>Açıklama:</label>
                        <InputWithError :errors="store.formErrors.descriptions.en">
                            <textarea v-model="store.productData.descriptions.en" id="enDescription"
                                class="form-control"></textarea>
                        </InputWithError>
                    </div>
                </TabPanel>
            </TabView>
            <Divider />
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="input-model">Model</label>
                        <InputWithError :errors="store.formErrors.model">
                            <input type="text" v-model="store.productData.model" placeholder="Model" id="input-model"
                                class="form-control">
                        </InputWithError>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="input-sku">SKU</label>
                        <InputWithError :errors="store.formErrors.sku">
                            <input type="text" v-model="store.productData.sku" placeholder="SKU" id="input-sku"
                                class="form-control">
                        </InputWithError>
                    </div>

                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="input-status">Ürün Durumu</label>
                        <InputWithError :errors="store.formErrors.status">
                            <select name="status" v-model="store.productData.status" id="input-status"
                                class="custom-select">
                                <option value="1">
                                    Aktif
                                </option>
                                <option value="0">
                                    Pasif
                                </option>
                            </select>
                        </InputWithError>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <p class="font-weight-semibold">Müşteri Grupları</p>
                        <div class="border p-3 rounded">
                            <div class="custom-control custom-checkbox custom-control-inline"
                                v-for="(group, key) in groups">
                                <input type="checkbox" class="custom-control-input" :id="'cc_li_' + key"
                                    :checked="store.productData.active_customer_group.includes(group)"
                                    v-model="store.productData.active_customer_group" :value="group">
                                <label class="custom-control-label" :for="'cc_li_' + key">
                                    {{ getCompanyCustomerGroupDescription(group) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="custom-control custom-switch custom-control-success mb-2">
                        <input type="checkbox" class="custom-control-input" id="sc_r_success"
                            :checked="store.productData.store_visibility" v-model="store.productData.store_visibility"
                            :true-value="true" :false-value="false"
                            >
                        <label class="custom-control-label" for="sc_r_success">Mağazada Göster</label>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>

<style scoped>
.p-avatar-image {
    width: 18px;
    height: 18px;
}
</style>
<style>
.p-tabview .p-tabview-panels {
    padding: 0px !important;
}
</style>