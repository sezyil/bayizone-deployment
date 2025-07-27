<template>
    <VDatatable :card-title="cardTitle" request-u-r-l="users" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable" @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { useSwal } from '/@src/composables/useSwal';
import { remove } from '/@src/request/request-user';
import { useUserSession } from '/@src/stores/userSession';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const userSession = useUserSession();
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
const router = useRouter();
const cardTitle = ref("Kullanıcılar");

const refreshTable = ref(false);
const tableData = ref([]);

const dataContent = ref<IDataTableColumn[]>([
    { data: "fullname", headText: "Ad-Soyad" },
    { data: "email", headText: "Email" },
    {
        data: "role", headText: "Rol",
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
                        nodeActive: props?.canUpdate,
                        class: "btn btn-sm btn-outline-dark mr-1",
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
                        nodeActive: props?.canDelete,
                        class: "btn btn-sm btn-outline-dark",
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
        confirmButtonText: 'Evet',
        cancelButtonText: 'Hayır'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await remove(id);
                swal.fire("Başarılı", "Silindi", 'success');
                refreshTable.value = true;
                userSession.subscriptionUpdater.user.increment();
            } catch (err) {
                catchFieldError(err, undefined, undefined, () => router.push('/app'));
            }
        }
    })
}

const _redir = (str: any) => {
    router.push('/app/users/' + str + '/edit');
}

const eventWatcher = (e: IDataTableCellTrigger) => {
    if (e.name == "editItem") _redir(e.firstParam);
    else if (e.name == "deleteItem") deleteItem(e.firstParam);
};

</script>