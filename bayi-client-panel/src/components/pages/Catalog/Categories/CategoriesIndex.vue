<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { remove } from '/@src/request/request-category';
import { SwalInstance } from '/@src/shared/common/type-swal';
import API_URIS from '/@src/utils/api/api_uris';
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
interface ICategoryFilter {
    name: string;
    uploader: 'system' | 'customer' | '';
}
const filter = ref<ICategoryFilter>({
    name: '',
    uploader: ''
});

const refreshTable = ref(false);
const tableData = ref([]);

const dataContent = ref<IDataTableColumn[]>([
    {
        data: "name", headText: "İsim"
    },
    /* is_default */
    {
        data: "is_default", headText: "Sistem Tanımlı", render: (row) => {
            let badge = ""
            let text = ""
            switch (row.is_default) {
                case true:
                    badge = "badge badge-success"
                    text = "Evet"
                    break;
                case false:
                    badge = "badge badge-danger"
                    text = "Hayır"
                    break;
                default:
                    badge = "badge badge-warning"
                    text = "Bilinmiyor"
                    break;
            }
            return `<span class="${badge}">${text}</span>`
        }
    },
    {
        data: "id",
        headText: "Eylemler",
        render: (row, data, index) => {
            console.log(props.canUpdate && !row.is_default, row.id)
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-outline-dark mr-1",
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data
                        },
                        innerHTML: `<i class="fas fa-pencil-alt"></i>`,
                        nodeActive: props.canUpdate && !row.is_default,
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-outline-dark",
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-trash-alt"></i>`,
                        nodeActive: props.canDelete && !row.is_default
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
        confirmButtonText: 'Sil',
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
    router.push('/app/catalog/categories/' + str + '/edit');
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};

const search = async () => {
    refreshTable.value = true;
}

const clearAndSearch = async () => {
    filter.value = {
        name: '',
        uploader: ''
    }
    refreshTable.value = true;
}


</script>
<template>
    <div>
        <!-- filter - category name & uploader type [user/system] -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Kategori Adı</label>
                                    <input type="text" v-model="filter.name" class="form-control" id="name"
                                        placeholder="Kategori Adı" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="uploader">Sistem Tanımlıları Göster</label>
                                    <select class="form-control" id="uploader" v-model="filter.uploader">
                                        <option value="">Hepsi</option>
                                        <option value="system">Evet</option>
                                        <option value="customer">Hayır</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-end">
                                <VButtonClear @click="clearAndSearch" />
                                <VButtonSearch @click="search" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <VDatatable card-title="Kategori" :request-u-r-l="API_URIS.CATEGORY" :data-content="dataContent"
            @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable" :filter="filter"
            @event-triggered="eventWatcher" />
    </div>
</template>
