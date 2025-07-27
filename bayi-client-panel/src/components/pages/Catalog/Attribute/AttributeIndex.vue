<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.ATTRIBUTE" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable" @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import API_URIS from '/@src/utils/api/api_uris';
import { IDataTableColumn, IDataTableCellTrigger } from '/@src/components/Datatable/IDatatable';
import { _delete } from '/@src/request/request-attribute';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { useSwal } from '/@src/composables/useSwal';
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
const cardTitle = ref("Özellik Listesi");

const refreshTable = ref(false);
const tableData = ref([]);

interface DataResponse {
    id: number
    customer_id: string
    attribute_group_id: number
    is_default: number
    group_description: IGroupDescription
    description: IDescriptions
}

interface IGroupDescription {
    id: number
    attribute_group_id: number
    language: string
    name: string
}

interface IDescriptions {
    id: number
    attribute_id: number
    language: string
    name: string
}


const dataContent = ref<IDataTableColumn[]>([
    {
        data: "description", headText: "İsim", render: (row, data: IDescriptions, index) => data.name
    },
    {
        data: "group_description", headText: "Grup", render: (row, data: IGroupDescription, index) => data.name
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
                        class: "btn btn-outline-dark mr-1",
                        nodeActive: props.canUpdate,
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data
                        }, innerHTML: `<i class="fas fa-pencil-alt"></i>`,
                        title: "Düzenle"
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-outline-dark",
                        nodeActive: props.canDelete,
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-trash-alt"></i>`,
                        title: "Sil"
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
        confirmButtonText: "Evet",
        cancelButtonText: "Hayır"
    }).then(async (result) => {
        if (result.isConfirmed) {
            await _delete(id).then((result) => {
                swal.fire("Başarılı", "Kayıt Başarıyla Silindi", "success"), refreshTable.value = true;
            }).catch((err) => {
                catchFieldError(err, (index: any, value: any) => {
                    swal.fire("Hata", value, "error");
                });
            });
        }
    })
}

const _redir = (str: any) => {
    router.push('/app/catalog/attributes/' + str + '/edit');
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};


</script>