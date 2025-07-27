<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import CustomerNoteApi from '/@src/request/request-customer-note';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';

interface ITableFilter {
    date: string
}

const permission = useUserPermission().getByName("company_customer");
const canUpdate = computed(() => permission.update);
const canDelete = computed(() => permission.delete);
const apiClass = new CustomerNoteApi();
const props = defineProps({
    company_customer_id: {
        type: String,
    },
})
const emit = defineEmits<{
    (name: "editItem", firstParam: string): void;
}>();

const uriForTable = computed(() => {
    let _uri = API_URIS.CUSTOMERS + "/notes";
    if (props.company_customer_id) _uri = API_URIS.CUSTOMERS + "/" + props.company_customer_id + "/notes";
    return _uri;
});
const swal = useSwal();
const router = useRouter();
const cardTitle = ref("Notlar");
const filter = ref<ITableFilter>({ date: "" });



const refreshTable = ref(false);
const tableData = ref([]);

interface IResponse {
    id: string
    customer_id: string
    company_customer_id: string
    note: string
    created_at: string
    updated_at: string
    company_customer_name: string
}

const dataContentList: IDataTableColumn[] = [
    { data: "note", headText: "Not" },
    { data: "created_at", headText: "Oluşturulma Tarihi" },
    { data: "updated_at", headText: "Güncellenme Tarihi" },
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
            }
        }
    })
}

const clearFilter = () => {
    filter.value = { date: '' };
    refreshTable.value = true;
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    apiClass.setCompanyId(e.secondParam);
    if (e.name == "editItem") emit("editItem", e.firstParam);
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
    <!-- filter -->
    <div class="row pt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="date">Tarih</label>
                                <input type="date" v-model="filter.date" class="form-control" id="date">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                            <VButtonClear @click="clearFilter" />
                            <VButtonSearch @click="refreshTable = true" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <VDatatable :card-title="cardTitle" :request-u-r-l="uriForTable" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable" :filter="filter"
        @event-triggered="eventWatcher" />
</template>
