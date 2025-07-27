<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn, IDataTableColumnNode } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import ShipmentsApi from '/@src/request/request-shipments';
import { EnumOfferStatus, getOfferStatusText } from '/@src/shared/form/interface-offer';
import { CustomerShipmentStatusEnum, IShipmentListResponse, customerShipmentStatusEnumDescription } from '/@src/shared/response/interface-shipment-response';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';

const apiClass = new ShipmentsApi();
const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Müşteri Sevkiyatları");

const filter = ref<{
    shipment_no: string,
    customer_name: string,
    status: string
}>({
    shipment_no: "",
    customer_name: "",
    status: ""
});

const refreshTable = ref(false);
const handleSearch = (filterData: any) => {
    filter.value = filterData;
    refreshTable.value = true;
}
const tableData = ref([]);
const approveActiveStatuses = [CustomerShipmentStatusEnum.DRAFT.toString()];
const deletableStatuses = [CustomerShipmentStatusEnum.DRAFT.toString(), CustomerShipmentStatusEnum.PENDING.toString()];

const approveRequest = async (id: string) => {
    swal.fire({
        title: "Onaylama",
        text: "Sevkiyatı işleme almak istediğinize emin misiniz?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Evet",
        cancelButtonText: "Hayır",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await apiClass.approve(id);
                swal.fire("Başarılı", "Sevkiyat başarıyla işleme alındı", "success");
                refreshTable.value = true;
            } catch (error: any) {
                let _msg = "Beklenmeyen bir hata oluştu";
                if (error.response && error.response.status === 422) {
                    _msg = error?.response?.data?.msg || "Beklenmeyen bir hata oluştu";
                }
                swal.fire({
                    title: "Hata",
                    html: _msg,
                    icon: "error",
                });
            }
        }
    });

}



const dataContent = ref<IDataTableColumn[]>([
    { data: "created_at", headText: "Oluşturulma Tarihi" },
    { data: "shipment_no", headText: "Sevk No" },
    { data: "company_customer_name", headText: "Müşteri Adı" },
    { data: "currency_label", headText: "Para Birimi" },
    { data: "total_package", headText: "Toplam Paket" },
    { data: "total_volume", headText: "Toplam Hacim" },
    { data: "total_weight", headText: "Toplam Ağırlık" },
    { data: "status_label", headText: "Durum" },
    {
        data: "id",
        headText: "Eylemler",
        render: (row: IShipmentListResponse, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        nodeActive: row.status === CustomerShipmentStatusEnum.DRAFT,
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-edit"></i>`,
                    },
                    /* approve */
                    {
                        elementType: "button",
                        isRaw: false,
                        nodeActive: approveActiveStatuses.includes(row.status),
                        class: "btn btn-sm btn-outline-dark mr-1",
                        trigger: {
                            type: "click",
                            emitName: "approveItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-check"></i>`,
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark" + (row.status === CustomerShipmentStatusEnum.DRAFT ? " disabled" : ""),
                        trigger: {
                            type: "click",
                            emitName: "showItem",
                            firstParam: data,
                            secondParam: row.status,
                        }, innerHTML: `<i class="fas fa-eye"></i>`,
                    },
                    //delete
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark ml-1",
                        nodeActive: deletableStatuses.includes(row.status),
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-trash"></i>`,
                    },
                ]
            }
        }
    }
]);


const _redir = (() => {
    const _tmpUri = "/app/shipments/";
    const _edit = (id: string) => router.push(_tmpUri + id + "/edit");
    const _preview = (id: string) => router.push(_tmpUri + id + "/show");
    const _show = (id: string) => router.push(_tmpUri + id + "/show");

    return {
        edit: _edit,
        preview: _preview,
        show: _show
    }
})()

const _delete = async (id: string) => {
    swal.fire({
        title: "Silme",
        text: "Sevkiyatı silmek istediğinize emin misiniz?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Evet",
        cancelButtonText: "Hayır",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await apiClass.delete(id);
                swal.fire("Başarılı", "Sevkiyat başarıyla silindi", "success");
                refreshTable.value = true;
            } catch (error: any) {
                catchFieldError(error);
            }
        }
    });
}


const eventWatcher = (e: IDataTableCellTrigger) => {
    switch (e.name) {
        case "editItem":
            _redir.edit(e.firstParam);
            break;
        case "showItem":
            if (e.secondParam === CustomerShipmentStatusEnum.DRAFT) {
                swal.fire("Hata", "Bu sevkiyat henüz işlenmemiştir. İşlem yapabilmek için sevkiyatı onaylayınız.", "error");
                return;
            }
            _redir.show(e.firstParam);
            break;
        case "approveItem":
            approveRequest(e.firstParam);
            break;
        case "deleteItem":
            _delete(e.firstParam);
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
    <!-- shipment_no
customer_name
status -->
    <div>
        <!-- filters -->
        <ShipmentFilter @searchTriggered="handleSearch" />

        <VDatatable :card-title="cardTitle" :request-u-r-l="apiClass.tableUri" :data-content="dataContent"
            :filter="filter" @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
            @event-triggered="eventWatcher" />

    </div>
</template>