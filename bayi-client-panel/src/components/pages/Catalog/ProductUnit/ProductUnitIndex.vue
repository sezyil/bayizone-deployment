<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.PRODUCT_UNITS" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable" @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { remove } from '/@src/request/request-product-unit';
import { SwalInstance } from '/@src/shared/common/type-swal';
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
const cardTitle = "Ürün Birimleri"
const router = useRouter();

const refreshTable = ref(false);
const tableData = ref([]);
interface IProductUnitResponse {
    id: number
    customer_id: any
    name: string
    short_name: string
    is_active: boolean
    is_system: boolean
}


const dataContent = ref<IDataTableColumn[]>([
    {
        data: "name", headText: "İSİM"
    },
    {
        data: "short_name", headText: "KISA İSİM"
    },
    {
        data: "is_active", headText: "AKTİFLİK DURUMU", render: (row: IProductUnitResponse, data: boolean, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "span",
                        isRaw: false,
                        class: !row.is_system ? (data ? "badge badge-success" : "badge badge-danger") : "badge badge-primary",
                        innerHTML: !row.is_system ? (data ? "Aktif" : "Pasif") : "Sistem Tanımlı",
                    }
                ]
            }
        }
    },
    {
        data: "id",
        headText: "Eylemler",
        render: (row: IProductUnitResponse, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-outline-dark mr-1",
                        trigger: !row.is_system ? {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data,
                        } : undefined,
                        innerHTML: `<i class="fas fa-pencil-alt"></i>`,
                        nodeActive: !row.is_system && props.canUpdate
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-outline-dark",
                        trigger: !row.is_system ? {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        } : undefined,
                        innerHTML: `<i class="fas fa-trash-alt"></i>`,
                        nodeActive: !row.is_system && props.canDelete
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
    router.push('/app/catalog/product_units/' + str + '/edit');
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};


</script>