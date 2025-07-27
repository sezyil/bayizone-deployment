<template>
    <VDatatable :card-title="cardTitle" request-u-r-l="users" :data-content="dataContent"
        @datachanged="(tableData = $event, refreshTable = false)" :refresh="refreshTable"
        @event-triggered="eventWatcher" />
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IDataTableCellTrigger, IDataTableColumn } from '/@src/components/Datatable/IDatatable';
import { remove } from '/@src/request/request-user';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const { t } = useI18n();
const swal = inject('$swal') as SwalInstance;
const router = useRouter();
const cardTitle = ref(t('components.users.index.title'));

const refreshTable = ref(false);
const tableData = ref([]);

const dataContent = ref<IDataTableColumn[]>([
    { data: "fullname", headText: t('common.fullname') },
    { data: "email", headText: t('common.email') },
    { data: "phone", headText: t('common.phone') },
    {
        data: "id",
        headText: t('common.actions'),
        render: (row, data, index) => {
            return {
                isRaw: false,
                nodes: [
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-primary mr-1",
                        trigger: {
                            type: "click",
                            emitName: "editItem",
                            firstParam: data
                        }, innerHTML: `<i class="fas fa-pencil-alt"></i>`,
                        title: t('actions.edit')
                    },
                    {
                        elementType: "button",
                        isRaw: false,
                        class: "btn btn-sm btn-danger",
                        trigger: {
                            type: "click",
                            emitName: "deleteItem",
                            firstParam: data,
                        }, innerHTML: `<i class="fas fa-trash-alt"></i>`,
                        title: t('actions.delete')
                    }
                ]
            }
        }
    }
]);

const deleteItem = async (id: any) => {
    swal.fire({
        title: t('actions.deleteConfirm'),
        text: t('actions.deleteNotRecoverable'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: t('actions.yes'),
        cancelButtonText: t('actions.no')
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await remove(id);
                swal.fire(t('common.success'), t('actions.deleteSuccess'), 'success');
                refreshTable.value = true;
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