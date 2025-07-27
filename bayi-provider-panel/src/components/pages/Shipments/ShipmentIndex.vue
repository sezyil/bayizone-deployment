<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IDataTableCellTrigger, IDataTableColumn, IDataTableColumnNode } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import ShipmentsApi from '/@src/request/request-shipments';
import { CustomerShipmentStatusEnum, IShipmentListResponse, customerShipmentStatusEnumDescription } from '/@src/shared/response/interface-shipment-response';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const { t } = useI18n();
const apiClass = new ShipmentsApi();
const swal = useSwal();
const router = useRouter();
const cardTitle = ref(t('sidemenu.shipments'));

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


const dataContent = ref<IDataTableColumn[]>([
    { data: "created_at", headText: t('common.created_at') },
    { data: "shipment_no", headText: t('components.shipment.shipment_no') },
    { data: "currency_label", headText: t('common.currency') },
    { data: "total_package", headText: t('components.shipment.total_package') },
    { data: "total_volume", headText: t('components.shipment.total_volume') },
    { data: "total_weight", headText: t('components.shipment.total_weight') },
    { data: "status_label", headText: t('common.status') },
    {
        data: "id",
        headText: t('common.actions'),
        render: (row: IShipmentListResponse, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    /* approve */
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-info" + (row.status === CustomerShipmentStatusEnum.DRAFT ? " disabled" : ""),
                        trigger: {
                            type: "click",
                            emitName: "showItem",
                            firstParam: data,
                            secondParam: row.status,
                        }, innerHTML: `<i class="fas fa-eye"></i>`,
                    },
                ]
            }
        }
    }
]);


const _redir = (() => {
    const _tmpUri = "/app/shipments/";
    const _show = (id: string) => router.push(_tmpUri + id + "/show");

    return {
        show: _show
    }
})()


const eventWatcher = (e: IDataTableCellTrigger) => {
    switch (e.name) {
        case "showItem":
            if (e.secondParam === CustomerShipmentStatusEnum.DRAFT) {
                swal.fire(t('common.error'), t('common.data.retrievalError'), "error");
                return;
            }
            _redir.show(e.firstParam);
            break;
        default:
            swal.fire(t('common.error'), t('common.data.retrievalError'), "error");
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