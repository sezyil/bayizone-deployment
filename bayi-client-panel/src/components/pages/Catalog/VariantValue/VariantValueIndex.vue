<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.VARIANT_VALUES" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import VariantValuesApi from '/@src/request/request-variant-values';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const props = defineProps({
    canDelete: {
        type: Boolean,
        default: false
    },
    canUpdate: {
        type: Boolean,
        default: false
    }
})
const swal = useSwal();
const cardTitle = "Varyant Değerleri"
const router = useRouter();
const api = new VariantValuesApi();

const refreshTable = ref(false);
const tableData = ref([]);
interface IResponse {
    id: string
    variant_type: string
    variant_type_label: string
    name: string
    is_default: boolean
}


const dataContent = ref<IDataTableColumn[]>([
    { data: "name", headText: "İSİM" },
    { data: "variant_type_label", headText: "TİP" },
    {
        data: "is_default", headText: "VARSAYILAN", render: (row: IResponse, data: boolean, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "span",
                        isRaw: false,
                        class: data ? "badge badge-primary" : "badge badge-success",
                        innerHTML: data ? "Sistem Tanımlı" : "Kullanıcı",
                    }
                ]
            }
        }
    },
    {
        data: "id",
        headText: "Eylemler",
        render: (row: IResponse, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-outline-dark mr-1",
                        trigger: !row.is_default ? {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data,
                        } : undefined,
                        innerHTML: `<i class="fas fa-pencil-alt"></i>`,
                        nodeActive: !row.is_default && props.canUpdate
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-outline-dark",
                        trigger: !row.is_default ? {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        } : undefined,
                        innerHTML: `<i class="fas fa-trash-alt"></i>`,
                        nodeActive: !row.is_default && props.canDelete
                    }
                ]
            }
        }
    }
]);

const deleteItem = async (id: any) => {
    swal.fire({
        title: 'Bu kaydı silmek istediğinizden emin misiniz?',
        text: "Bu işlem geri alınamaz!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText: 'Hayır'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await api.remove(id);
                swal.fire("Başarılı", "Silindi", 'success');
                refreshTable.value = true;
            } catch (err) {
                catchFieldError(err);
            }
        }
    })
}

const _redir = (str: any) => {
    router.push('/app/catalog/variant_values/' + str + '/edit');
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};


</script>