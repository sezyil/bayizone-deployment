<script setup lang="ts">
import { ITransactionFilter } from './TransactionFilter.vue'
import { IDataTableCellTrigger, IDataTableColumn, IDataTableColumnNode } from '/@src/components/Datatable/IDatatable';
import TransactionApi, { ITransactionApiGetBalanceResponse } from '/@src/request/request-transaction';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
import { IByPermission, useUserPermission } from '/@src/composables/useUserPermission';
import { EnumTransactionIO } from '/@src/utils/form/transaction_form';
import { ITransactionTableSummary } from './TransactionTableSummary.vue';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useSwal } from '/@src/composables/useSwal';
const permissions = useUserPermission().getByName('transaction');
const apiClass = new TransactionApi();
const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Hareket Listesi");
const props = defineProps({
    company_customer_id: {
        type: String,
        required: false
    },
    currency: {
        type: String,
        required: false
    }
})
const emit = defineEmits(['update']);
const extraTableData = ref<ITransactionTableSummary>();

const refreshTable = ref(false);
const tableData = ref([]);
const initialized = ref(false);
const modalShow = ref(false);
const modalId = ref('');

const setDefault = (): ITransactionFilter => ({
    company_customer_id: props.company_customer_id ? props.company_customer_id : '',
    customer_email: '',
    currency: props.currency ? props.currency : '',
})
const filter = ref<ITransactionFilter>(setDefault())


const handleSearch = (filterData: ITransactionFilter) => {
    initialized.value = true;
    filter.value = filterData;
    refreshTable.value = true;
}

interface IResponse {
    id: string
    company_customer: string
    fiche_no: string
    fiche_type: string
    total: string
    customer_order_id: string | null
    order_number: string | null
    formatted_total_paid: string
    total_due: string
    formatted_total_due: string
    is_paid: boolean
    io_type: boolean
}

const dataContent = ref<IDataTableColumn[]>([
    { data: "fiche_no", headText: "Fiş No" },
    { data: "company_customer", headText: "Müşteri", render: (row: any, data: string) => `<a href="/app/customers/${row.company_customer_id}">${data}</a>` },
    { data: "formatted_amount", headText: "Tutar" },
    { data: "formatted_date", headText: "İşlem Tarihi", render: (row: any, data: string) => data },
    { data: "formatted_due_date", headText: "Vade Tarihi", render: (row: any, data: string) => data ? data : "Vade Tarihi Yok" },
    { data: "fiche_type", headText: "Hareket Tipi", render: (row: any, data: string) => data },
    { data: "is_paid", headText: "Ödeme Durumu", render: (row: any, data: boolean) => data ? "Ödendi" : "Ödenmedi" },
    { data: "io_type", headText: "Hareket Tipi", render: (row: any, data: EnumTransactionIO) => data === EnumTransactionIO.DEBT ? "Borç" : "Alacak" },
    {
        data: "id",
        headText: "İşlemler",
        render: (row: IResponse, data: any, index) => {
            return {
                isRaw: false,
                nodes: [
                    /* mark as paid */
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        nodeActive: permissions.update && !row.is_paid,
                        trigger: {
                            type: "click",
                            emitName: "payItem",
                            firstParam: row.id,
                        },
                        title: "Ödendi Olarak İşaretle",
                        innerHTML: `<i class="fas fa-money-check-alt"></i>`
                    },
                    /* show */
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        nodeActive: permissions.view,
                        trigger: {
                            type: "click",
                            emitName: "showItem",
                            firstParam: data,
                        },
                        title: "Görüntüle",
                        innerHTML: `<i class="fas fa-eye"></i>`
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        nodeActive: permissions.delete,
                        class: "btn btn-sm btn-outline-dark",
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        },
                        title: "Sil",
                        innerHTML: `<i class="fas fa-trash"></i>`
                    },

                ]
            }
        },
    },
]);


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
                await apiClass.delete(id);
                swal.fire("Başarılı", "Silindi", "success"), refreshTable.value = true;
                emit('update');
            } catch (err) {
                swal.fire("Hata", "Silinemedi", "error");
                /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
            }
        }
    })
}

const payItem = async (id: string) => {
    swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlemi geri alamayacaksınız!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, bu işlemin ödemesi yapıldı!',
        cancelButtonText: "Hayır"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await apiClass.payment(id);
                swal.fire("Başarılı", "Ödendi Olarak İşaretlendi", "success"), refreshTable.value = true;
                emit('update');
            } catch (err) {
                swal.fire("Hata", "Ödeme işlemi gerçekleştirilemedi", "error");
                /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
            }
        }
    })
}

const handleModal = {
    open: (id: string) => {
        modalId.value = id;
        modalShow.value = true;
    },
    close: () => {
        modalId.value = '';
        modalShow.value = false;
    }
}
const excelDownloading = ref(false);

const handleExcelDownload = async (eventFilterData: any) => {
    if (!filter.value.currency) {
        swal.fire({
            icon: 'error',
            title: 'Hata',
            text: 'Para birimi seçilmeden excel indirilemez'
        })
        return
    }

    try {
        excelDownloading.value = true;
        const { data } = await apiClass.downloadExcel(eventFilterData);
        const url = window.URL.createObjectURL(new Blob([data]));
        const link = document.createElement('a');
        link.href = url;
        //yyyy-mm-dd-hh-mm-ss
        let time = new Date().toISOString().split('T')[0].replace(/-/g, '').replace(/:/g, '').replace('T', '-');
        link.setAttribute('download', `bayizone-transactions-${time}.xlsx`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (e: any) {
        if (e.response.status === 422) {
            swal.fire("Hata", "Arama kriterlerine uygun veri bulunamadı. Excel çıktısı verilemez", "error");
        } else {
            swal.fire("Hata", "Excel indirilemedi", "error");
        }
    } finally {
        excelDownloading.value = false;
    }


}


const eventWatcher = (e: IDataTableCellTrigger) => {
    switch (e.name) {
        case "deleteItem":
            deleteItem(e.firstParam);
            break;
        case "showItem":
            if (e.firstParam) {
                handleModal.open(e.firstParam);
            }
            break;
        case "payItem":
            payItem(e.firstParam);
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
    <TransactionFilter @searchTriggered="handleSearch" :company_customer_id="company_customer_id"
        :excel-downloading="excelDownloading" @excel-download="handleExcelDownload" :currency="currency" />
    <div v-if="extraTableData">
        <TransactionSummary :data="extraTableData" />
    </div>
    <VDatatable v-if="initialized" :card-title="cardTitle" :request-u-r-l="API_URIS.TRANSACTIONS"
        :data-content="dataContent" :filter="filter" @datachanged="(tableData = $event, refreshTable = false)"
        @extra-data-changed="extraTableData = $event" :refresh="refreshTable" @event-triggered="eventWatcher" />
    <TransactionDetailModal :id="modalId" :show="modalShow" @close="handleModal.close" />

</template>