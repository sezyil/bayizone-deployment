<script setup lang="ts">
import { IOfferFilter } from './OfferFilter.vue';
import { IDataTableCellTrigger, IDataTableColumn, IDataTableColumnNode } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import CustomerOfferApi from '/@src/request/request-customer-offer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { EnumOfferStatus, getOfferStatusText } from '/@src/shared/form/interface-offer';
import { useUserSession } from '/@src/stores/userSession';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { GLOB_URIS } from '/@src/utils/GLOB_URIS';
const tableUri = API_URIS.CUSTOMERS + "/offers";
const apiClass = new CustomerOfferApi();
const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Müşteri Teklifleri");
const initialized = ref(false);

const wpModalStatus = ref(false);
const planData = computed(() => useUserSession().planData);
const filter = ref<IOfferFilter>({});

const refreshTable = ref(false);
const handleSearch = (filterData: IOfferFilter) => {
    initialized.value = true;
    filter.value = filterData;
    refreshTable.value = true;
}
const tableData = ref([]);

interface IResponse {
    id: string
    company_customer_id: string
    company_customer_name: string
    grand_total: number
    formatted_grand_total: string
    offer_date: string
    offer_due_date: string
    offer_no: string
    currency: string
    currency_name: string
    status: EnumOfferStatus
    mail_notification_date: string | null
    has_order: string | null
}

const dataContent = ref<IDataTableColumn[]>([
    { data: "company_customer_name", headText: "Müşteri Adı" },
    { data: "offer_no", headText: "Teklif No", render: (row: any, data: string) => data ? data : "---" },
    { data: "offer_date", headText: "Teklif Tarihi" },
    { data: "offer_due_date", headText: "Teklif Bitiş Tarihi", render: (row: any, data: string) => data ? data : "Belirsiz" },
    { data: "formatted_grand_total", headText: "Toplam Tutar" },
    { data: "currency_name", headText: "Para Birimi" },
    { data: "status", headText: "Durum", render: (row: any, data: string) => getOfferStatusText(data as EnumOfferStatus) },
    {
        data: "id",
        headText: "İşlemler",
        render: (row: IResponse, data: any, index) => {
            return {
                isRaw: false,
                nodes: statusNodeGenerator(row.status, row, data)
            };
        },
    },
]);

const statusNodeGenerator = (status: EnumOfferStatus, row: IResponse, data: any): IDataTableColumnNode[] => {
    let tmpNodes: IDataTableColumnNode[] = [];
    let editNodeActiveStatuses = [EnumOfferStatus.DRAFT]
    let cancelNodeActiveStatuses = [EnumOfferStatus.DRAFT, EnumOfferStatus.PENDING]
    let commonNodes: IDataTableColumnNode[] = [
        //for preview
        {
            elementType: "button",
            isRaw: false,
            nodeActive: editNodeActiveStatuses.includes(row.status),
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "editItem",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: "Düzenle",
            innerHTML: `<i class="fas fa-pencil-alt"></i>`
        },
        //for cancel
        {
            elementType: "button",
            isRaw: false,
            nodeActive: cancelNodeActiveStatuses.includes(row.status),
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "statusCancel",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: "İptal Et",
            innerHTML: `<i class="fas fa-times"></i>`
        },
        {
            elementType: "button",
            isRaw: false,
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "deleteItem",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: "Sil",
            innerHTML: `<i class="fas fa-trash-alt"></i>`
        },

    ];
    if (status == EnumOfferStatus.DRAFT) {
        tmpNodes.push({
            elementType: "button",
            isRaw: false,
            nodeActive: row.status == EnumOfferStatus.DRAFT,
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "statusPending",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: "Kaydet ve Gönder",
            innerHTML: `<i class="fas fa-save"></i>`
        });
    } else if (status == EnumOfferStatus.PENDING) {
        let _title = row.mail_notification_date ? "Tekrar Gönder" : "Mail Gönder";

        //send mail
        tmpNodes.push({
            elementType: "button",
            isRaw: false,
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "sendMail",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: _title,
            innerHTML: `<i class="fas fa-envelope"></i>`
        });
    } else if (status == EnumOfferStatus.CANCELED) {
        commonNodes = [];
    } else if (status == EnumOfferStatus.APPROVED) {
        //complete
        tmpNodes.push({
            elementType: "button",
            isRaw: false,
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "statusComplete",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: "Tamamla",
            innerHTML: `<i class="fas fa-check"></i>`
        });
    } else if (status == EnumOfferStatus.CLOSED) {
        commonNodes = [];
        //convert to order
        if (planData.value?.sales_management) {
            if (!row.has_order) {
                tmpNodes.push({
                    elementType: "button",
                    isRaw: false,
                    class: "btn btn-sm btn-outline-dark mr-1",
                    trigger: {
                        type: "click",
                        emitName: "convertToOrder",
                        firstParam: data,
                        secondParam: row.company_customer_id
                    },
                    title: "Siparişe Dönüştür",
                    innerHTML: `<i class="fas fa-exchange-alt"></i>`
                });
            } else {
                tmpNodes.push({
                    elementType: "button",
                    isRaw: false,
                    class: "btn btn-sm btn-outline-dark mr-1",
                    trigger: {
                        type: "click",
                        emitName: "goToOrder",
                        firstParam: row.has_order
                    },
                    title: "Siparişe Git",
                    innerHTML: `<i class="fas fa-sign-in-alt"></i>`
                });

            }

        }

    }
    return [
        {
            elementType: "button",
            isRaw: false,
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "previewItem",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: "Görüntüle",
            innerHTML: `<i class="fas fa-eye"></i>`
        },
        {
            elementType: "button",
            isRaw: false,
            class: "btn btn-sm btn-outline-dark mr-1",
            trigger: {
                type: "click",
                emitName: "downloadExcel",
                firstParam: data,
                secondParam: row.company_customer_id
            },
            title: "Excel İndir",
            innerHTML: `<i class="fas fa-file-excel"></i>`
        },
        {
            elementType: "button",
            isRaw: false,
            class: "btn btn-sm btn-outline-dark mr-1",
            nodeActive: planData.value ? true : false,
            trigger: {
                type: "click",
                emitName: "sendWhatsapp",
                firstParam: data,
            },
            title: "Whatsapp Gönder",
            innerHTML: `<i class="fab fa-whatsapp"></i>`
        },
        ...tmpNodes,
        ...commonNodes];
}

const _redir = (() => {
    const _tmpUri = "/app/offers/";
    const _edit = (id: string) => router.push(_tmpUri + id + "/edit");
    const _preview = (id: string) => {
        //get token from local storage
        const { user } = useUserSession()

        let uri = GLOB_URIS.BASE_URL + `proforma-preview/${id}/${user?.id}`
        window.open(uri, '_blank')
    }

    return {
        edit: _edit,
        preview: _preview
    }
})()

const deleteItem = async (id: string) => {
    swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlemi geri alamayacaksınız!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, sil!',
        cancelButtonText: "Hayır"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await apiClass.remove(id);
                swal.fire("Başarılı", "Silindi", "success"), refreshTable.value = true;
            } catch (err) {
                swal.fire("Hata", "Silinemedi", "error");
                /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
            }
        }
    })
}

const statusPending = async (id: string, status: EnumOfferStatus, resendMail: boolean = false) => {
    const __fn = async () => {
        try {
            const { data } = await apiClass.updateStatus(status, id, resendMail);
            let msg = data.msg;
            swal.fire("Başarılı", msg, "success"), refreshTable.value = true;
        } catch (err) {
            swal.fire("Hata", "İşlem gerçekleştirilemedi", "error");
            /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
        }
    }
    let _txt = "Teklif kaydedilecektir. Onaylıyor musunuz?";
    let _warningMsg = " Bu işlemi geri alamazsınız."
    switch (status) {
        case EnumOfferStatus.PENDING:
            _txt = "Teklif kaydedilecek ve müşteriye mail gönderilecektir. Bu aşamadan sonra teklif üzerinde değişiklik yapamayacaksınız. Onaylıyor musunuz?" + _warningMsg;
            if (resendMail) _txt = "Teklif maili müşteriye tekrar gönderilecektir. Onaylıyor musunuz?";
            break;
        case EnumOfferStatus.CANCELED:
            _txt = "Teklif iptal edilecektir. Onaylıyor musunuz?" + _warningMsg;
            break;
        case EnumOfferStatus.CLOSED:
            _txt = "Teklif tamamlanacaktır. Onaylıyor musunuz?" + _warningMsg;
            break;
    }
    //if resend mail is true swal confirm
    swal.fire({
        title: 'Bilgilendirme',
        text: _txt,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: "Hayır",
        confirmButtonText: 'Evet'
    }).then(async (result) => {
        if (result.isConfirmed) {
            await __fn();
        }
    })
}

const wpOfferId = ref<string>()
const wpModalClose = () => {
    console.log('close');
    wpModalStatus.value = false;
    wpOfferId.value = undefined;
};

const setWpOfferId = (id: string) => {
    wpOfferId.value = id;
    wpModalStatus.value = true;
}

const convertToOrder = async (id: string) => {
    swal.fire({
        title: 'Emin misiniz?',
        text: "Bu proforma siparişe dönüştürülecektir!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, dönüştür!',
        cancelButtonText: "Hayır"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await apiClass.convertToOrder(id);
                swal.fire("Başarılı", "Siparişe dönüştürüldü", "success"), refreshTable.value = true;
            } catch (err) {
                swal.fire("Hata", "Dönüştürülemedi", "error");
                /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
            }
        }
    })
}

const downloadExcel = async (offer_id: string) => {
    //swal language select ask
    const { value: _selectedLang } = await swal.fire({
        title: "Dil Seçimi",
        input: "select",
        inputOptions: {
            tr: "Türkçe",
            en: "İngilizce"
        },
        inputPlaceholder: "Excel çıktısını hangi dilde almak istersiniz?",
        showCancelButton: true,
        inputValidator: (value) => {
            return new Promise((resolve) => {
                if (value) {
                    //@ts-ignore
                    resolve();
                } else {
                    resolve('Lütfen bir dil seçiniz');
                }
            });
        },
        /* button texts */
        confirmButtonText: "İndir",
        cancelButtonText: "İptal",
    });
    if (_selectedLang) {
        try {
            const { data } = await apiClass.downloadExcel(offer_id, _selectedLang);
            const url = window.URL.createObjectURL(new Blob([data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `bayizone-teklif-${offer_id}-${_selectedLang}.xlsx`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (e) {
            catchFieldError(e, swal);
        } finally {

        }
    }
}


const eventWatcher = (e: IDataTableCellTrigger) => {
    apiClass.setCompanyId(e.secondParam);
    switch (e.name) {
        case "editItem":
            _redir.edit(e.firstParam);
            break;
        case "deleteItem":
            deleteItem(e.firstParam);
            break;
        case "previewItem":
            _redir.preview(e.firstParam);
            break;
        case "statusPending":
            statusPending(e.firstParam, EnumOfferStatus.PENDING);
            break;
        case "sendMail":
            statusPending(e.firstParam, EnumOfferStatus.PENDING, true);
            break;
        case "statusCancel":
            statusPending(e.firstParam, EnumOfferStatus.CANCELED);
            break;
        case "statusComplete":
            statusPending(e.firstParam, EnumOfferStatus.CLOSED);
            break;
        case "sendWhatsapp":
            setWpOfferId(e.firstParam);
            break;
        case "convertToOrder":
            convertToOrder(e.firstParam);
            break;
        case "downloadExcel":
            downloadExcel(e.firstParam);
            break;
        case "goToOrder":
            router.push('/app/customer_orders/' + e.firstParam + "/show");
            break;
        default:
            swal.fire("Hata", "Beklenmeyen bir hata oluştu(EW_261)", "error");
            break;
    }

};

onMounted(async () => {

})
</script>
<template>
    <OfferFilter @search-triggered="handleSearch" />
    <VDatatable v-if="initialized" :card-title="cardTitle" :request-u-r-l="tableUri" :data-content="dataContent"
        :filter="filter" @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
    <WpMsgModal :modal-active="wpModalStatus" :offer-id="wpOfferId" @close="wpModalClose" />
</template>