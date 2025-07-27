<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { remove } from '/@src/request/request-customer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Bekleyen Ürün Listesi");

const refreshTable = ref(false);
const tableData = ref([]);

interface IResponse {
    id: string
    status: string
    created_at: string
    count: number
    errors: string[]
    has_error: boolean
}

const modalStatus = ref(false);
const modalErrors = ref<string[]>([]);

const dataContent = ref<IDataTableColumn[]>([
    { data: "created_at", headText: "Oluşturulma Tarihi" },
    { data: "status", headText: "Durum" },
    { data: "has_error", headText: "Hata Var mı?", render: (row: IResponse) => `<span class="badge ${row.has_error ? 'badge-danger' : 'badge-success'}">${row.has_error ? 'Evet' : 'Hayır'}</span>` },
    { data: "count", headText: "Ürün Sayısı" },
    {
        data: "id",
        headText: "Eylemler",
        render: (row: IResponse, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    /* show errors */
                    {
                        elementType: "button",
                        isRaw: false,
                        nodeActive: row.has_error,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        trigger: {
                            type: "click",
                            emitName: "showErrors",
                            firstParam: row.errors
                        }, innerHTML: `<i class="fas fa-exclamation-triangle"></i>`,
                        title: "Hataları Göster"
                    }
                ]
            }
        }
    }

]);

const eventWatcher = (event: IDataTableCellTrigger) => {
    if (event.name === "showErrors") {
        showErrors(event.firstParam);
    }
}

const modalClose = () => {
    modalStatus.value = false;
    modalErrors.value = [];
}

const showErrors = (errors: string[]) => {
    modalErrors.value = errors;
    modalStatus.value = true;
}


</script>
<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.BATCH_PROCESSES + '?type=product'"
        :data-content="dataContent" @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />

    <VModal ref="modal" title="Hatalar" @close="modalClose" :show="modalStatus">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Açıklama</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(error, index) in modalErrors" :key="index">
                    <td>{{ error }}</td>
                </tr>
            </tbody>
        </table>
    </VModal>
</template>