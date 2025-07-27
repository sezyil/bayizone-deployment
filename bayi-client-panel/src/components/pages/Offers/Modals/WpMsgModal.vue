<script setup lang="ts">
import { useApi } from '/@src/composables/useApi';
import { useSwal } from '/@src/composables/useSwal';
import CustomerOfferApi from '/@src/request/request-customer-offer';
const swal = useSwal();
interface IResponse {
    company_customer_id: string
    total_price: string
    total_tax: string
    total_discount: string
    grand_total: string
    offer_date: string
    offer_due_date: string
    offer_no: string
    currency: string
    note: string
    billing_address: string
    billing_city: string
    billing_state: string
    billing_country: string
    billing_zip_code: any
    payment_bank_name: string
    payment_branch_name: string
    payment_account_name: string
    payment_account_number: string
    payment_iban: string
    payment_swift_code: string
    contact_person: any
    contact_email: any
    contact_phone: any
    whatsapp_notification_date: any
    mail_notification_date: string
    offer_uri: string
    password: string
    is_request: number
    international_order: boolean
    status: string
    created_at: string
    updated_at: string
    lines: Line[]
    company_customer: CompanyCustomer
    customer: Customer
}

interface Line {
    id: string
    product_id: number
    product_name: string
    product_code: string
    product_unit: string
    product_image_url: string
    unit_name: string
    quantity: string
    unit_price: string
    tax_rate: string
    unit_discount_price: string
    unit_discount_rate: string
    total_discount_price: string
    total_price: string
    grand_total: string
    note: any
}

interface CompanyCustomer {
    authorized_name: string
    tax_office: string
    tax_identity_no: string
    company_name: string
    phone: string
    fax: string
    email: string
    address: string
    country_id: string
    state_id: string
    city_id: string
    postcode: any
    type: number
}

interface Customer {
    firm_name: string
    tax_no: string
    tax_administration: string
    address: string
    country: string
    state: string
    city: string
    postcode: string
    email: string
    phone: string
}

const emit = defineEmits(['close'])
const api = new CustomerOfferApi();
const props = defineProps({
    modalActive: {
        type: Boolean,
        default: false
    },
    offerId: {
        type: String
    }
});
const isLoading = ref(false);
const responseData = ref<IResponse>();
const messageLanguage = ref<'tr' | 'en'>('tr');
const formData = ref({
    wpNumber: '',
    wpMessage: '',

})

const offerUri = computed(() => {
    if (!responseData.value) return;
    return responseData.value.offer_uri;
})

const copyOfferUri = () => {
    const uri = document.getElementById('wpOfferUri') as HTMLInputElement;
    navigator.clipboard.writeText(uri.value);
}

const getData = async () => {
    if (!props.offerId) return;
    isLoading.value = true;
    try {
        const { data } = await api.preview(props.offerId, messageLanguage.value);
        responseData.value = data.data as IResponse;
        formData.value.wpNumber = '+' + responseData.value.company_customer.phone;
        generateDefaultText();
    } catch (error) {

    }
    isLoading.value = false;
}

const generateDefaultText = () => {
    if (!responseData.value) return;
    const { company_customer, customer } = responseData.value;
    const trText = `Merhaba ${company_customer.authorized_name},\n${customer.firm_name} firması tarafından gönderilen teklifiniz aşağıdaki gibidir.\n\nFirma Adı: ${company_customer.company_name}\nTeklif No: ${responseData.value.offer_no}\nTeklif Tarihi: ${responseData.value.offer_date}\nTeklif Son Tarihi: ${responseData.value.offer_due_date}\nTeklif Linki: ${responseData.value.offer_uri}\n\nÜrünler:\n${responseData.value.lines.map((line) => `${line.product_name} - ${line.quantity} ${line.unit_name}`).join('\n')}\n\n\nİletişim:\n${customer.firm_name}\n${customer.address}\n${customer.country} ${customer.state} ${customer.city} ${customer.postcode}\n${customer.email}\n${customer.phone}\n\nİyi Günler Dileriz.`;
    const enText = `Hello ${company_customer.authorized_name},\nYour offer sent by ${customer.firm_name} company is as follows.\n\nCompany Name: ${company_customer.company_name}\nOffer No: ${responseData.value.offer_no}\nOffer Date: ${responseData.value.offer_date}\nOffer Due Date: ${responseData.value.offer_due_date}\nOffer Link: ${responseData.value.offer_uri}\n\nProducts:\n${responseData.value.lines.map((line) => `${line.product_name} - ${line.quantity} ${line.unit_name}`).join('\n')}\n\n\nContact:\n${customer.firm_name}\n${customer.address}\n${customer.country} ${customer.state} ${customer.city} ${customer.postcode}\n${customer.email}\n${customer.phone}\n\nHave a nice day.`;
    formData.value.wpMessage = messageLanguage.value === 'tr' ? trText : enText;
}

const handlers = {
    async close() {
        emit('close');
    },
    async send() {
        if (!props.offerId) return;
        //new tab 
        const url = `https://api.whatsapp.com/send?phone=${formData.value.wpNumber}&text=${encodeURIComponent(formData.value.wpMessage)}`;
        window.open(url, '_blank');
    }
}

watch(() => props.offerId, async (val) => {
    if (val) {
        await getData()
    }
})

watch(() => messageLanguage.value, () => {
    generateDefaultText();
})
</script>
<template>
    <VModal title="Whatsapp Teklif Gönderme" :show="modalActive" @close="handlers.close()" :loader="isLoading">
        <!-- info msg to user-->
        <div class="wrapper" v-if="responseData">
            <div class="alert alert-info" role="alert">
                <p class="mb-0">Bu alan belirtilen mesaj ve bilgileri whatsapp üzerinden gönderir.</p>
                <p class="mb-0">Kullanım sağlamak için whatsapp uygulaması yüklü olmalıdır.</p>
            </div>
            <!-- show offer uri not input with copy button and icon -->
            <div class="form-group">
                <label for="wpOfferUri">Teklif Linki</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="wpOfferUri" :value="offerUri" placeholder="Teklif Linki"
                        readonly>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="button-addon2"
                            @click.prevent="copyOfferUri">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- number with icon wp icon prepend -->
            <div class="form-group">
                <label for="wpNumber">Whatsapp Numarası</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-success"><i class="fab fa-whatsapp"></i></span>
                    </div>
                    <input type="text" class="form-control" id="wpNumber" v-model="formData.wpNumber"
                        placeholder="Numara" autocomplete="off">
                    <!-- ülke kodunu koymayı unutmayın -->
                </div>
                <small class="form-text text-warning">Ülke kodu ile birlikte yazınız.
                    <b>Örnek: +905555555555</b>
                </small>
            </div>
            <!-- language list tr-en -->
            <div class="form-group">
                <label for="language">Dil</label>
                <select class="form-control" id="language" v-model="messageLanguage">
                    <option value="tr">Türkçe</option>
                    <option value="en">İngilizce</option>
                </select>
            </div>

            <!-- mesaj önizleme -->
            <div class="form-group">
                <label for="wpMessage">Mesaj Önizleme</label>
                <textarea class="form-control" id="wpMessage" rows="3" v-model="formData.wpMessage"
                    placeholder="Örnek: Merhaba, teklifiniz aşağıdaki gibidir."></textarea>
            </div>
        </div>

        <template #footer>
            <button type="button" class="btn btn-primary" @click.prevent="handlers.send">Gönder</button>
        </template>

    </VModal>
</template>



<style scoped></style>