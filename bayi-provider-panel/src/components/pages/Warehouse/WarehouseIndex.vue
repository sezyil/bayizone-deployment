<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import WarehouseApi from '/@src/request/request-warehouses';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const swal = inject('$swal') as SwalInstance;
const { t } = useI18n();
const router = useRouter();
const apiClass = new WarehouseApi();
const cardTitle = ref(t('components.warehouses.index.title'));

const refreshTable = ref(false);
const tableData = ref([]);

/**
 * @description IWarehouseListResponse is the response type of the api
 */
const dataContent = ref<IDataTableColumn[]>([
    { data: "name", headText: t('components.warehouses.index.name') },
    { data: "address", headText: t('common.address') },
    { data: "state", headText: t('common.city') },
    { data: "city", headText: t('common.district') },
    { data: "country", headText: t('common.country') },
    /* contact_person */
    { data: "contact_person", headText: t('components.warehouses.index.authorizedName') },
    {
        data: "id",
        headText: t('common.actions'),
        render: (row, data: any, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-primary mr-1",
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data,
                        },
                        title: t('actions.edit'),
                        innerHTML: `<i class="fas fa-pencil-alt"></i>`
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-danger",
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        },
                        title: t('actions.delete'),
                        innerHTML: `<i class="fas fa-trash-alt"></i>`
                    },
                ],
            };
        },
    },
]);

const deleteItem = async (id: string) => {
    swal.fire({
        title: t('actions.deleteConfirm'),
        text: t('actions.deleteNotRecoverable'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: t('actions.yes'),
        cancelButtonText: t('actions.no')
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await apiClass.remove(id);
                swal.fire(t('common.success'), t('actions.deleteSuccess'), "success"), refreshTable.value = true;
            } catch (err) {
                swal.fire(t('common.error'), t('actions.deleteError'), "error");
            }
        }
    })
}

const _redir = (id: any) => {
    router.push(`/app/warehouses/${id}/edit`);
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};

</script>
<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.WAREHOUSES" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>