<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.PERMISSIONS" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable" @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { remove } from '/@src/request/request-user';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const props = defineProps({
    canUpdate: {
        type: Boolean,
        default: false
    }
})

interface IResponse {
    id: string;
    count: number;
    name: string;
}

const swal = useSwal(); 
const router = useRouter();
const cardTitle = ref("Grup Yetkileri");

const refreshTable = ref(false);
const tableData = ref([]);

const dataContent = ref<IDataTableColumn[]>([
    { data: "name", headText: "Grup" },
    { data: "count", headText: "Kullanıcı Sayısı" },
    {
        data: "id",
        headText: "Eylemler",
        render: (row, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        nodeActive: props?.canUpdate,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data
                        }, innerHTML: `<i class="fas fa-pencil-alt"></i>`,
                        title: "Düzenle"
                    }
                ]
            }
        }
    }
]);



const _redir = (str: any) => {
    router.push('/app/permissions/' + str + '/edit');
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
};

</script>