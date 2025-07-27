<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import CustomerWarehouseApi from '/@src/request/request-customer-warehouses';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { IWarehouseListResponse } from '/@src/shared/response/interface-warehouse-response';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const swal = useSwal();
const router = useRouter();
const apiClass = new CustomerWarehouseApi();
const props = defineProps({
    company_customer_id: {
        type: String,
        required: true
    },
})
const uriForTable = `${API_URIS.CUSTOMERS}/${props.company_customer_id}/warehouses`;
const permission = useUserPermission().getByName("company_customer_warehouse");
const canUpdate = computed(() => permission.update);
const canDelete = computed(() => permission.delete);
const cardTitle = ref("Depolar");

const refreshTable = ref(false);
const tableData = ref([]);

const dataContentArray: IDataTableColumn[] = [
    { data: "customer_name", headText: "Müşteri Adı" },
    { data: "name", headText: "Depo Adı" },
    { data: "address", headText: "Adres" },
    { data: "state", headText: "Şehir" },
    { data: "city", headText: "İlçe" },
    { data: "country", headText: "Ülke" },
    /* contact_person */
    { data: "contact_person", headText: "Yetkili Adı" },
    {
        data: "id",
        headText: "İşlemler",
        render: (row: IWarehouseListResponse, data: any, index) => {
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
                        },
                        title: "Düzenle",
                        innerHTML: `<i class="fas fa-pencil-alt"></i>`
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
                        },
                        title: "Sil",
                        innerHTML: `<i class="fas fa-trash-alt"></i>`
                    },
                ],
            };
        },
    },
];
const dataContent = ref<IDataTableColumn[]>(dataContentArray.filter((item) => {
    if (item.data == "customer_name" && props.company_customer_id) return false;
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
                swal.fire("Başarılı", "Silindi", "success"), refreshTable.value = true;
            } catch (err) {
                swal.fire("Hata", "Silinemedi", "error");
            }
        }
    })
}

const _redir = (id: any, customer_id: string) => {
    router.push(`/app/customers/${customer_id}/warehouses/${id}/edit`);
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
<template>
    <VDatatable :card-title="cardTitle" :request-u-r-l="uriForTable" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>