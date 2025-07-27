<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="uriForTable" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import CustomerBankAccountApi from '/@src/request/request-customer-bank-accounts';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
const permission = useUserPermission().getByName("company_customer_bank_account");
const canUpdate = computed(() => permission.update);
const canDelete = computed(() => permission.delete);
const apiClass = new CustomerBankAccountApi();
const props = defineProps({
    company_customer_id: {
        type: String,
    },
})

const uriForTable = computed(() => {
    let _uri = API_URIS.CUSTOMERS + "/bank_accounts";
    if (props.company_customer_id) _uri = API_URIS.CUSTOMERS + "/" + props.company_customer_id + "/bank_accounts";
    return _uri;
});
const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Banka Hesapları");



const refreshTable = ref(false);
const tableData = ref([]);

interface IResponse {
    id: string
    company_customer_id: string
    company_customer_name: string
    bank_name: string
    status: boolean
}

const dataContentList: IDataTableColumn[] = [
    { data: "company_customer_name", headText: "Müşteri Adı" },
    { data: "bank_name", headText: "Banka Adı" },
    { data: "status", headText: "Durum", render: (row: any, data: boolean) => data ? "Aktif" : "Pasif" },
    {
        data: "id",
        headText: "İşlemler",
        render: (row: IResponse, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark mr-1",
                        nodeActive: canUpdate.value,
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data,
                            secondParam: row.company_customer_id
                        }, innerHTML: `<i class="fas fa-pencil-alt"></i>`
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-outline-dark",
                        nodeActive: canDelete.value,
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                            secondParam: row.company_customer_id
                        }, innerHTML: `<i class="fas fa-trash-alt"></i>`
                    }
                ]
            }
        }
    }
];

const dataContent = ref<IDataTableColumn[]>(dataContentList.filter((item) => {
    if (item.data == "company_customer_name" && props.company_customer_id) return false;
    return true;
}));

const deleteItem = async (id: string) => {
    swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlemi geri alamayacaksınız!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, sil!',
        cancelButtonText: "Hayır"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await apiClass.remove(id);
                swal.fire("Başarılı", "Kayıt Silindi", "success"), refreshTable.value = true;
            } catch (err) {
                swal.fire("Hata", "Kayıt Silinemedi", "error");
                /* catchFieldError(err, (a: any, b: any) => console.log(a, b)); */
            }
        }
    })
}

const _redir = (id: any, customer_id: string) => {
    router.push(`/app/customers/${customer_id}/bank_accounts/${id}/edit`);
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    apiClass.setCompanyId(e.secondParam);
    if (e.name == "editItem") _redir(e.firstParam, e.secondParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};

onMounted(async () => {
    if (props.company_customer_id) {
        apiClass.setCompanyId(props.company_customer_id);
        refreshTable.value = true;
    }
})


</script>