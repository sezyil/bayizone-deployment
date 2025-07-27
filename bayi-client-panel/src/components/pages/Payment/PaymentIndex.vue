<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { remove } from '/@src/request/request-bank-accounts';
import PaymentApi from '/@src/request/request-payment';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const api = new PaymentApi();

const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Ödemeler");

const refreshTable = ref(false);
const tableData = ref([]);

interface IListResponse {
    id: string;
    order_no: string;
    total: string;
    is_paid: boolean;
    is_active: boolean;
    created_at: string;
}

const dataContent = ref<IDataTableColumn[]>([
    {
        data: "order_no",
        headText: "Sipariş No",
    },
    {
        data: "total",
        headText: "Toplam",
    },
    {
        data: "is_paid",
        headText: "Ödendi",
        render: (row, data, index) => {
            let _html = '';
            if (!row.is_active && !row.is_paid) {
                _html = `<span class="badge badge-danger">Sistem Tarafından İptal Edildi</span>`;
            } else if (row.is_paid) {
                _html = `<span class="badge badge-success">Ödendi</span>`;
            } else {
                _html = `<span class="badge badge-warning">Ödenmedi</span>`;
            }
            return {
                isRaw: true,
                nodes: [
                    {
                        class: '',
                        elementType: "span",
                        isRaw: true,
                        innerHTML: _html
                    }
                ]
            }
        }
    },
    {
        data: "id",
        headText: "İşlemler",
        render: (row: IListResponse, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        nodeActive: !row.is_paid && row.is_active,
                        trigger: {
                            type: "click",
                            emitName: "makePayment",
                            firstParam: data
                        },
                        innerHTML: `<i class="fas fa-money-bill-wave"></i>`,
                        title: "Ödeme Yap"
                    },
                    //show paid 
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        nodeActive: row.is_paid,
                        trigger: {
                            type: "click",
                            emitName: "showPayment",
                            firstParam: data
                        },
                        innerHTML: `<i class="fas fa-eye"></i>`,
                        title: "Ödeme Detayı"
                    },
                ]
            }
        }
    }
]);

const deleteItem = async (id: any) => {
    swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlem geri alınamaz!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet',
        cancelButtonText: 'Hayır'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await remove(id);
                swal.fire("Başarılı", "Silindi", 'success');
                refreshTable.value = true;
            } catch (err) {
                catchFieldError(err);
            }
        }
    })
}

const _redir = (str: any) => {
    router.push('/app/payments/:id/pay'.replace(':id', str));
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name === "makePayment") {
        _redir(e.firstParam);
    } else if (e.name === "showPayment") {
        router.push('/app/payments/:id/show'.replace(':id', e.firstParam));
    }
};

</script>


<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="api._uri" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>
