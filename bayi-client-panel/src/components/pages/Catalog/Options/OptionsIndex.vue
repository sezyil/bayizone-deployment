<template>
    <VDatatable :card-title="cardTitle" request-u-r-l="options" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { remove } from '/@src/request/request-option';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const props = defineProps({
    canUpdate: {
        type: Boolean,
        default: false
    },
    canDelete: {
        type: Boolean,
        default: false
    }
})
const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Seçenekler");

const refreshTable = ref(false);
const tableData = ref([]);

const dataContent = ref<IDataTableColumn[]>([
    {
        data: "description", headText: "İsim", render: (row, data, index) => data.name
    },
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
                        nodeActive: props.canUpdate,
                        class: "btn btn-outline-dark mr-1",
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data
                        }, innerHTML: `<i class="fas fa-pencil-alt"></i>`
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        nodeActive: props.canDelete,
                        class: "btn btn-outline-dark",
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-trash-alt"></i>`
                    }
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
    router.push('/app/catalog/options/' + str + '/edit');
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};


</script>