<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="API_URIS.BANK_ACCOUNTS" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable" @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { remove } from '/@src/request/request-bank-accounts';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const props = defineProps({
    canDelete: {
        type: Boolean,
        default: false,
    },
    canUpdate: {
        type: Boolean,
        default: false,
    },
});

const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Banka Hesap Listesi");

const refreshTable = ref(false);
const tableData = ref([]);

interface IListResponse {
    id: string;
    bank_name: string;
    branch_name: string;
    account_name: string;
    account_number?: string;
    currency_name: string;
}

const dataContent = ref<IDataTableColumn[]>([
    {
        data: "bank_name",
        headText: "Banka Adı",
    },
    {
        data: "branch_name",
        headText: "Şube Adı",
    },
    {
        data: "currency_name",
        headText: "Para Birimi",
    },
    {
        data: "account_name",
        headText: "Hesap Adı",
    },
    {
        data: "account_number",
        headText: "Hesap Numarası",
    },
    {
        data: "id",
        headText: "Actions",
        render: (row, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        nodeActive: props.canUpdate,
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data
                        }, innerHTML: `<i class="fas fa-pencil-alt"></i>`
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark",
                        nodeActive: props.canDelete,
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
    router.push('/app/bank_accounts/' + str + '/edit');
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};

</script>